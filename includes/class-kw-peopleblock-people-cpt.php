<?php

/**
 * The file defines the PeopleCPT class
 *
 * @package    KW\PeopleBlock
 * @subpackage KW\PeopleBlock\Inc
 */

namespace KW\PeopleBlock\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

/**
 * People Custom Post Type class that stores all the people data
 *
 * @class \KW\PeopleBlock\Inc\PeopleCPT
 */
class PeopleCPT
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        add_action('init', array($this, 'create_people_post_type'));
        add_action('init', array($this, 'register_people_category_taxonomy'));
        add_action('acf/init', array($this, 'create_people_acf_fields'));
    }


    /**
     * Create people post type
     *
     * @return void
     */
    public function create_people_post_type() {

        $labels = array(
            'name' => __('People'),
            'singular_name' => __('Person'),
            'menu_name' => __('People'),
            'all_items' => __('All People'),
            'add_new' => __('Add New Person'),
            'add_new_item' => __('Add New Person'),
            'edit_item' => __('Edit Person'),
            'new_item' => __('New Person'),
            'view_item' => __('View Person'),
            'search_items' => __('Search People'),
            'not_found' => __('No people found'),
            'not_found_in_trash' => __('No people found in trash')
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'people'),
            'supports' => array('title', 'thumbnail'),
            'taxonomies' => array('people_category'),
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-groups'
        );

        register_post_type('people', $args);
    }


    /**
     * Create people category taxonomy
     *
     * @return void
     */
    public function register_people_category_taxonomy() {

        $labels = array(
            'name'                       => _x('People Categories', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x('People Category', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __('People Category', 'text_domain'),
            'all_items'                  => __('All People Categories', 'text_domain'),
            'parent_item'                => __('Parent People Category', 'text_domain'),
            'parent_item_colon'          => __('Parent People Category:', 'text_domain'),
            'new_item_name'              => __('New People Category Name', 'text_domain'),
            'add_new_item'               => __('Add New People Category', 'text_domain'),
            'edit_item'                  => __('Edit People Category', 'text_domain'),
            'update_item'                => __('Update People Category', 'text_domain'),
            'view_item'                  => __('View People Category', 'text_domain'),
            'separate_items_with_commas' => __('Separate people categories with commas', 'text_domain'),
            'add_or_remove_items'        => __('Add or remove people categories', 'text_domain'),
            'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
            'popular_items'              => __('Popular People Categories', 'text_domain'),
            'search_items'               => __('Search People Categories', 'text_domain'),
            'not_found'                  => __('Not Found', 'text_domain'),
            'no_terms'                   => __('No people categories', 'text_domain'),
            'items_list'                 => __('People categories list', 'text_domain'),
            'items_list_navigation'      => __('People categories list navigation', 'text_domain'),
        );

        $rewrite = array(
            'slug'                       => 'people-category',
            'with_front'                 => true,
            'hierarchical'               => false,
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'rewrite'                    => $rewrite,
        );

        register_taxonomy('people_category', array('people'), $args);
    }


    /**
     * Create people ACF fields
     *
     * @return void
     */
    public function create_people_acf_fields() {

        if (function_exists('acf_add_local_field_group')) {

            acf_add_local_field_group(array(

                'key' => 'people_fields',
                'title' => __('People Fields'),

                'fields' => array(
                    array(
                        'key' => 'name_first',
                        'label' => __('First Name'),
                        'name' => 'name_first',
                        'type' => 'text'
                    ),
                    array(
                        'key' => 'name_middle',
                        'label' => __('Middle Name'),
                        'name' => 'name_middle',
                        'type' => 'text'
                    ),
                    array(
                        'key' => 'name_last',
                        'label' => __('Last Name'),
                        'name' => 'name_last',
                        'type' => 'text'
                    ),
                    array(
                        'key' => 'wikipedia_link',
                        'label' => __('Wikipedia Link'),
                        'name' => 'wikipedia_link',
                        'type' => 'url'
                    )
                ),

                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'people'
                        ),
                    ),
                ),

                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'active' => true,
                'description' => ''
            ));
        }
    }

}

new PeopleCPT();
