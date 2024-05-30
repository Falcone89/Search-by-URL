<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
    Dashboard Widget
*/
function vsbu_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'dashboard_keyword_search_widget',
        'Search by URL',
        'vsbu_render_dashboard_widget',
        null,
        null,
        'normal',
        'high'
    );
}
add_action('wp_dashboard_setup', 'vsbu_add_dashboard_widget');

// Display dashboard widget search layout
function vsbu_render_dashboard_widget() { ?>
    <p>Please provide the URL to search within the posts.</p>
    <form method="get" action="<?php echo esc_url(admin_url('tools.php')); ?>">
        <input type="hidden" name="page" value="vsbu-search" />
        <input type="url" name="searched_url" placeholder="Enter URL..." value="<?php echo isset($_GET['searched_url']) ? esc_html($_GET['searched_url']) : ''; ?>" required />
        <input type="submit" value="Search URL" class="button" />
    </form>
    <?php
}