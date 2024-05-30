<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
    Enqueue CSS and JS for admin pages
*/
function vsbu_enqueue_admin_assets($hook_suffix) {
    if ($hook_suffix == 'tools_page_vsbu-search') {
        wp_enqueue_style('vsbu-admin-styles', VSBU_SEARCH_BY_URL_PLUGIN_DIR_URL . 'assets/css/vsbu-admin.css', null, '1.0');
        wp_enqueue_script('vsbu-admin-scripts', VSBU_SEARCH_BY_URL_PLUGIN_DIR_URL . 'assets/js/vsbu-admin.js', null, '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'vsbu_enqueue_admin_assets');