<?php
/**
 * Plugin Name: Search by URL
 * Description: Search in the content of posts or in any type of ACF field based on URL or keyword.
 * Version: 1.0
 * Author: Adam Solymosi
 */

/*
    Exit if accessed directly  
*/
if (!defined('ABSPATH')) {
    exit;
}

/*
    Define constants
*/
define( 'VSBU_SEARCH_BY_URL_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__) );
define( 'VSBU_SEARCH_BY_URL_PLUGIN_DIR_URL', plugin_dir_url(__FILE__) );

/*
    Includes
*/
require_once VSBU_SEARCH_BY_URL_PLUGIN_DIR_PATH . 'includes/admin-menu.php';
require_once VSBU_SEARCH_BY_URL_PLUGIN_DIR_PATH . 'includes/enqueue-scripts.php';
require_once VSBU_SEARCH_BY_URL_PLUGIN_DIR_PATH . 'includes/search-functions.php';
require_once VSBU_SEARCH_BY_URL_PLUGIN_DIR_PATH . 'includes/dashboard-widget.php';