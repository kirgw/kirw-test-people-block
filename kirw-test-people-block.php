<?php
/*
* Plugin Name: Kirw Test People Block
* Description: A plugin to create custom post type "People", custom fields for it and a Gutenberg block.
* Author: Kirill G.
* Version: 0.1.0
* License: GPLv2 or later
* Text Domain: kw-peopleblock
*/

namespace KW\PeopleBlock;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

// Setup the constants
define('KW_PEOPLEBLOCK_PLUGIN_NAME', plugin_basename(__FILE__));
define('KW_PEOPLEBLOCK_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KW_PEOPLEBLOCK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KW_PEOPLEBLOCK_PLUGIN_VERSION', '0.1.0');


// Deactivation function
function kw_peopleblock_deactivate() {

    // Clear wp-cron
    wp_clear_scheduled_hook('kw_peopleblock_cron');
}
register_deactivation_hook(__FILE__, 'KW\PeopleBlock\kw_peopleblock_deactivate');


// Add the main file of the plugin
require_once KW_PEOPLEBLOCK_PLUGIN_PATH . 'includes/class-kw-peopleblock.php';

// Start the plugin
\KW\PeopleBlock\Inc\Init::instance();
