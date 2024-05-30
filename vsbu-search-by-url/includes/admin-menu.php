<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
    Add WordPress Admin Tools submenu
*/
function vsbu_add_admin_menu() {
    add_submenu_page(
        'tools.php',
        'Search by URL',
        'Search by URL',
        'manage_options',
        'vsbu-search',
        'vsbu_render_search_page'
    );
}
add_action('admin_menu', 'vsbu_add_admin_menu');

// Display search layout
function vsbu_render_search_page() { ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Search by URL</h1>
        <div id="search-preloader" class="preloader<?php echo !isset($_GET['searched_url']) && !isset($_GET['searched_keyword']) ? ' hidden' : ''; ?>">
            <img src="<?php echo esc_url( get_admin_url() . 'images/wpspin_light-2x.gif' ); ?>" />
        </div>
        <div id="wrapper"<?php echo isset($_GET['searched_url']) || isset($_GET['searched_keyword']) ? ' class="hidden"' : ''; ?>>
            <div class="container">
                <div class="box">
                    <h2 class="wp-heading-inline">URL</h2>
                    <form id="vsbu-search-by-url-form" class="vsbu-search-form" method="get" action="">
                        <input type="hidden" name="page" value="vsbu-search" />
                        <input type="url" name="searched_url" placeholder="Enter URL..." value="<?php echo isset($_GET['searched_url']) ? esc_url($_GET['searched_url']) : ''; ?>" required />
                        <input type="submit" value="Search URL" class="button button-primary" />
                    </form>
                    <p>Searching for URLs pointing to <strong>pages or files</strong> within the content and navigation</p>
                </div>
                <div class="box">
                    <h2 class="wp-heading-inline">Keyword</h2>
                    <form id="vsbu-search-by-keyword-form" class="vsbu-search-form" method="get" action="">
                        <input type="hidden" name="page" value="vsbu-search" />
                        <input type="text" name="searched_keyword" placeholder="Enter keyword..." value="<?php echo isset($_GET['searched_keyword']) ? esc_html($_GET['searched_keyword']) : ''; ?>" required />
                        <input type="submit" value="Search Keyword" class="button" />
                    </form>
                    <p>Free-text keyword-based search within the content and navigation</p>
                </div>
            </div>
            <?php // Get keyword input
            if ( isset($_GET['searched_url']) && $_GET['searched_url'] !== '' ) {
                $keyword = sanitize_url($_GET['searched_url']);
            } elseif ( isset($_GET['searched_keyword']) && $_GET['searched_keyword'] !== '' ) {
                $keyword = sanitize_text_field($_GET['searched_keyword']);
            } else {
                $keyword = null;
            }

            if ($keyword !== null) {
                vsbu_get_posts_by_keyword($keyword);
                vsbu_get_menus_by_keyword($keyword);
            } ?>
        </div>

    </div>
<?php }