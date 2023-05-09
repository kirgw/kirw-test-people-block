<?php

/**
 * The file defines the Data import class
 *
 * @package    KW\PeopleBlock
 * @subpackage KW\PeopleBlock\Inc
 */

namespace KW\PeopleBlock\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Data import class that stores all related methods
 *
 * @class KW\PeopleBlock\Inc\DataImport
 */
class DataImport
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        add_action('init', array($this, 'csv_check'));
        add_action('cron_schedules', array($this, 'cron_add_fiveminutely'));
        add_action('init', array($this, 'scheduler_setup'));
    }


    /**
     * Check if import was called
     *
     * @return void
     */
    public function csv_check() {

        if (isset($_GET['csv_people_import'])) {
            $this->import_people_data();
        }
    }


    /**
     * Import data from CSV
     *
     * @return void
     */
    public function import_people_data() {

        // Get and check the file with sample data
        $file_path = plugin_dir_path(__FILE__) . '../people.csv';

        if (!file_exists($file_path)) {
            return;
        }

        $file_data = file_get_contents($file_path);
        $rows = str_getcsv($file_data, "\n");

        // Skip the headers
        unset($rows[0]);

        // Set the taxonomy
        $taxonomy = 'people_category';

        // Parse the file
        foreach ($rows as $row) {

            $columns = str_getcsv($row, ",");

            $first_name = $columns[0];
            $middle_name = $columns[1];
            $last_name = $columns[2];
            $category = $columns[3];
            $wikipedia_link = $columns[4];

            // Create the post title
            $post_title = $last_name . ', ' . $first_name . ' ' . $middle_name;

            // Check if post already exists
            $existing_post = get_posts(array(
                'post_type'   => 'people',
                'post_status' => 'publish',
                'title'       => $post_title,
            ));

            // If post doesn't exist - create new post
            if (!isset($existing_post[0])) {

                $post_id = wp_insert_post(array(
                    'post_title'  => $post_title,
                    'post_type'   => 'people',
                    'post_status' => 'publish'
                ));
            }

            else {
                $post_id = $existing_post[0]->ID;
            }

            // Check if the term exists in the taxonomy
            $term = term_exists($category, $taxonomy);

            // If term exists, assign it
            if ($term !== 0 && $term !== null) {
                wp_set_post_terms($post_id, array($category), $taxonomy);
            }

            // If not, create it and then assign it
            else {
                $term = wp_insert_term($category, $taxonomy);
                wp_set_post_terms($post_id, array($term['term_id']), $taxonomy);
            }

            // Set ACF fields (if ACF active)
            if (function_exists('update_field')) {
                update_field('name_first', $first_name, $post_id);
                update_field('name_middle', $middle_name, $post_id);
                update_field('name_last', $last_name, $post_id);
                update_field('wikipedia_link', $wikipedia_link, $post_id);
            }

        }
    }


    /**
     * Cron: add fiveminutely
     *
     * @param  mixed $schedules
     * @return void
     */
    function cron_add_fiveminutely($schedules) {

        $schedules['fiveminutely'] = array(
            'interval' => 5 * 60,
            'display' => __('FiveMins')
        );

        return $schedules;
    }


    /**
     * Scheduler setup - to run every 5 minutes
     *
     * @return void
     */
    public function scheduler_setup() {

        $hook = 'kw_peopleblock_cron';

        add_action($hook, array($this, 'process_people_posts'));

        if (!wp_next_scheduled($hook)) {
            wp_schedule_event(time(), 'fiveminutely', $hook);
        }
    }


    /**
     * Process people posts and add Wiki images to them
     *
     * @return void
     */
    public function process_people_posts() {

        // Check based on meta saved previously
        // Try to take 30 posts
        // But even if timeout comes before, it'll pickup later based on meta
        $args = array(
            'post_type' => 'people',
            'posts_per_page' => 30,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'wiki_processed',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'wiki_processed',
                    'value' => 'error'
                )
            )
        );

        $people_posts = get_posts($args);

        // If no posts yet or no ACF, just skip
        if (count($people_posts) > 0 || !function_exists('get_field')) {

            foreach ($people_posts as $post) {

                // Check the link
                $wiki_link = get_field('wikipedia_link', $post->ID);

                if (!empty($wiki_link)) {

                    // Specifically get the image
                    $image_url = $this->get_wiki_image_url($wiki_link);

                    // Save if found
                    if (!empty($image_url)) {

                        $attachment_id = $this->import_image_attachment($image_url, $post->post_title);

                        // Found and saved
                        if ($attachment_id) {
                            set_post_thumbnail($post->ID, $attachment_id);
                            update_post_meta($post->ID, 'wiki_processed', 'yes');
                        }

                        // Error (not attached)
                        else {
                            update_post_meta($post->ID, 'wiki_processed', 'error');
                        }
                    }

                    // Error (not found)
                    else {
                        update_post_meta($post->ID, 'wiki_processed', 'error');
                    }
                }

                // Error (no link)
                else {
                    update_post_meta($post->ID, 'wiki_processed', 'error');
                }
            }
        }

    }


    /**
     * Get image from Wiki
     *
     * @param  mixed $wiki_link
     * @return void
     */
    public function get_wiki_image_url($wiki_link) {

        $html = file_get_contents($wiki_link);

        if (preg_match('/<meta property="og:image" content="([^"]+)"/', $html, $matches)) {
            return $matches[1];
        }

        return '';
    }


    /**
     * Import image as attachment
     *
     * @param  mixed $image_url
     * @param  mixed $post_title
     * @return void
     */
    public function import_image_attachment($image_url, $post_title) {

        // Get image
        $image_data = file_get_contents($image_url, false, stream_context_create([
            'http' => [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            ],
        ]));

        if ($image_data == false) {
            error_log('Error downloading image from URL: ' . $image_url);
            return false;
        }

        $filename = sanitize_file_name($post_title) . '.' . pathinfo($image_url, PATHINFO_EXTENSION);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'] . '/' . $filename;

        // Try to upload and save it
        if (!file_exists($upload_path)) {

            if (file_put_contents($upload_path, $image_data)) {

                $attachment_params = array(
                    'post_title'     => sanitize_file_name($post_title),
                    'post_mime_type' => mime_content_type($upload_path),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                );

                $attachment_id = wp_insert_attachment($attachment_params, $upload_path);

                if (is_wp_error($attachment_id)) {
                    error_log('Error importing image: ' . $attachment_id->get_error_message());
                    return false;
                }

                // Maybe include missing functions
                if (!function_exists('wp_crop_image')) {
                    include(ABSPATH . 'wp-admin/includes/image.php');
                }

                // Set all the needed metadata
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                // Success - return id
                return $attachment_id;
            }

            else {
                error_log('Error saving image to server.');
                return false;
            }
        }

        else {
            return false;
        }
        
    }
}

new DataImport();
