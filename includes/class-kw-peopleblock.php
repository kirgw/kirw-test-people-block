<?php

/**
 * The file defines the core plugin class
 *
 * @package    KW\PeopleBlock
 * @subpackage KW\PeopleBlock\Inc
 */

namespace KW\PeopleBlock\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Main plguin class
 *
 * @class KW_PeopleBlock
 */
final class Init
{

    public $version;
    public $plugin_name;
    public $plugin_path;
    public $plugin_url;

    /**
     * Instance of the class
     *
     * @var \KW\PeopleBlock\Inc\Init
     */
    protected static $_instance = null;

    /**
     * Store the main instance (singleton)
     *
     * @return \KW\PeopleBlock\Inc\Init
     */
    public static function instance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {

        // Set the properties
        $this->version = KW_PEOPLEBLOCK_PLUGIN_VERSION;
        $this->plugin_name = KW_PEOPLEBLOCK_PLUGIN_NAME;
        $this->plugin_path = KW_PEOPLEBLOCK_PLUGIN_PATH;
        $this->plugin_url = KW_PEOPLEBLOCK_PLUGIN_URL;

        // Run the setup
        $this->load_classes();
        add_action('init', array($this, 'enqueue_assets'));
        add_shortcode('kw-peopleblock', array($this, 'selected_people_shortcode'));
    }


    /**
     * Load all the needed classes
     *
     * @return void
     */
    public function load_classes() {

        // Define names
        $class_names = array(
            'people-cpt',
            'data-import',
        );

        // Include files
        foreach ($class_names as $class_name) {
            require_once $this->plugin_path . 'includes/class-kw-peopleblock-' . $class_name . '.php';
        }
    }


    /**
     * Enqueue block scripts
     *
     * @return void
     */
    public function enqueue_assets() {

        // Setup script of block here
        wp_register_script(
            'kw-people-block',
            $this->plugin_url . 'build/index.js',
            array('wp-blocks', 'wp-editor', 'wp-components')
        );

        register_block_type('kw-test/people-block', array(

            // Point the registered script
            'editor_script' => 'kw-people-block',

            // render option: render_callback
            'render_callback' => array($this, 'selected_people_render_callback'),
        ));
    }


    /**
     * Call to render selected people || render option: render_callback
     *
     * @param  mixed $atts
     * @return string
     */
    public function selected_people_render_callback($atts) {

        // Pass on the ids of selected people
        return $this->render_selected_people($atts['selectedPeople']);
    }


    /**
     * Call to render selected people || render option: shortcode
     *
     * @param  mixed $atts
     * @return string
     */
    public function selected_people_shortcode($atts) {

        // Make array of people ids
        $people_ids = shortcode_atts(array('ids' => '',), $atts);
        $selected_people_ids = explode(',', $people_ids['ids']);
    
        // Pass on the ids of selected people
        return $this->render_selected_people($selected_people_ids);
    }


    /**
     * Perform the render of selected people
     *
     * @param  mixed $selected_people_ids
     * @return string
     */
    public function render_selected_people($selected_people_ids) {

        // Get the posts
        $people_data = get_posts(array(
            'post_type' => 'people',
            'post__in' => $selected_people_ids,
            'orderby' => 'post__in',
            'posts_per_page' => -1,
        ));

        // Check ACF
        if (!function_exists('get_field')) {
            return 'ACF is not active';
        }

        // Collect the output
        $output = '';

        if (!empty($people_data)) {

            $output .= '<ul>';

            // Iterate selected people
            foreach ($people_data as $person) {

                // Start list item
                $output .= '<li>';

                // Get the data
                $title = get_the_title($person->ID);
                $first_name = get_field('name_first', $person->ID);
                $middle_name = get_field('name_middle', $person->ID);
                $last_name = get_field('name_last', $person->ID);

                // Get the taxonomy
                $terms = get_the_terms( $person->ID, 'people_category' );

                if (!empty($terms) && !is_wp_error($terms)) {
                    $category = $terms[0]->name;
                }
                else {
                    $category = 'none';
                }

                // Set the name format
                $full_name = "$last_name, $first_name $middle_name ";

                // Add full name from 3 fields
                $output .= "<h3>$full_name</h3><p>Category: $category</p>";
                
                // Add a picture if present
                $person_picture = wp_get_attachment_image_src(get_post_thumbnail_id($person->ID), 'medium');

                if ($person_picture) {
                    $output .= '<img style="width:200px;" src="' . esc_url($person_picture[0]) . '" alt="' . esc_attr($full_name) . '">';
                }

                // Add link
                $wikipedia_link = get_field('wikipedia_link', $person->ID);
                $output .= "<p>Link: <a href=" . $wikipedia_link . ">Wikipedia</a></p>";

                // Wrap the list item
                $output .= '</li>';
            }

            $output .= '</ul>';
        }
        
        else {
            $output .= 'No people selected';
        }

        return $output;
    }
}
