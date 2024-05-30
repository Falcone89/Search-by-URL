<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
    Get posts/pages by keyword
*/

// Search in Nested and Post Object ACF fields
function vsbu_search_acf_nested_fields($fields, $keyword, &$results, $post_id, $parent_key) {
    foreach ($fields as $key => $value) {
        if (is_string($value) && stripos($value, $keyword) !== false) {
            $results[$post_id][] = array('post_id' => $post_id, 'field' => $parent_key . ' / ' . $key, 'type' => 'acf');
        } elseif (is_array($value) || is_object($value)) {
            // If the field value is an array or object (like the ACF Relationship field with default return format), recursively search within the subfields
            vsbu_search_acf_nested_fields($value, $keyword, $results, $post_id, $parent_key . ' / ' . $key);
        }
    }
}

// Get posts by keyword
function vsbu_get_posts_by_keyword($keyword) {

    echo '<h2>Results in posts - <i>(' . esc_html($keyword) . ')</i></h2>';

    // Get the posts
    // To detect matches with all ACF fields, such as RELATIONSHIP or GALLERY, etc., it is more efficient to iterate through the posts (although it takes more time) instead of using a wpdb query.
    $args = array(
        'post_type' => array('post', 'page', 'product', 'blog-post'),
        'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ),
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);

    $results = array(); // Array for matching posts

    foreach ($posts as $post) {
        $post_id = $post->ID;

        // Check the post content
        if (stripos($post->post_content, $keyword) !== false) {
            $results[$post_id][] = array('post_id' => $post_id, 'field' => 'Content', 'type' => 'content');
        }

        // Check if the ACF plugin is installed and activated
        if (class_exists('ACF')) {
            // Get all ACF fields for the post
            $fields = get_fields($post_id);
            if ($fields) {
                foreach ($fields as $key => $value) {
                    if (is_string($value) && stripos($value, $keyword) !== false) {
                        $results[$post_id][] = array('post_id' => $post_id, 'field' => $key, 'type' => 'acf');
                    } elseif (is_array($value)) {
                        vsbu_search_acf_nested_fields($value, $keyword, $results, $post_id, $key);
                    }
                }
            }
        }
    }

    if (!empty($results)) { ?>
        <p><?php echo count($results) . (count($results) == 1 ? ' item' : ' items'); ?></p>
        <table class="wp-list-table widefat striped fixed" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary"><span>Title</span></th>
                    <th scope="col" class="manage-column">Detected Place</th>
                    <th scope="col" class="manage-column">Post Type</th>
                    <th scope="col" class="manage-column">Status and Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $post_id => $fields) {
                    $post_title = get_the_title($post_id);
                    $edit_link = get_edit_post_link($post_id);
                    $status = get_post_status_object( get_post_status($post_id) )->label;
                    $date_format = 'Y/m/d \a\t g:i a';
                    $date = get_the_date($date_format, $post_id); ?>
                    <tr>
                        <td class="title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a href="<?php echo esc_url($edit_link); ?>"><?php echo esc_html($post_title); ?></a></strong>
                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo esc_url($edit_link); ?>">Edit</a></span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
                        </td>
                        <td data-colname="Detected Place">
                            <ul>
                                <?php foreach ($fields as $field) {
                                    $field_type = "{$field['type']}";
                                    $field_name = "{$field['field']}";

                                    // Extract the root label of the ACF field
                                    if ( $field_type === 'acf' ) {
                                        $field_name_parts = explode(' / ', $field_name);
                                        $field_name_parts_root = $field_name_parts[0];
                                        $field_root_object = get_field_object($field_name_parts_root, $post_id);
                                        if ($field_root_object && isset($field_root_object['label'])) {
                                            $field_root_label = $field_root_object['label'];
                                        } else {
                                            $field_root_label = $field_name_parts_root;
                                        }
                                    } ?>
                                    <li><span class="vsbu-label <?php echo esc_html($field_type); ?>"><?php echo esc_html($field_type === 'acf') ? '<small>ACF field</small>' . esc_html($field_root_label) . ' - <i>' . esc_html($field_name) .'</i>' : esc_html($field_name); ?></span></li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td data-colname="Post Type"><i><?php echo esc_html(get_post_type($post_id)); ?></i></td>
                        <td data-colname="Status and Date"><?php echo esc_html($status); ?><br><?php echo esc_html($date); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else {
        echo '<p>No results found in posts.</p>';
    }
    
}

/*
    Get menus by keyword
*/
function vsbu_get_menus_by_keyword($keyword) {

    echo '<h2>Results in menus - <i>(' . esc_html($keyword) . ')</i></h2>';

    // Get navigation menus
    $menus = wp_get_nav_menus();

    $matching_menus = array(); // Array for matching menus

    // Check WordPress menus
    foreach ($menus as $menu) {
        // Get navigation menu items
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        
        foreach ($menu_items as $menu_item) {
            // Check if the URL or Navigation Label of the menu item contains the keyword
            if (stripos($menu_item->url, $keyword) !== false || stripos($menu_item->title, $keyword) !== false) {
                $menu_name = esc_html($menu->name);
                if (!isset($matching_menus[$menu_name])) {
                    $matching_menus[$menu_name] = array(); // Array for matching menu items
                }
                $matching_menus[$menu_name]['items'][] = esc_html($menu_item->title);
                $matching_menus[$menu_name]['id'] = $menu->term_id; // Menu ID
            }
        }
    }

    if (!empty($matching_menus)) { ?>
        <p><?php echo count($matching_menus) . (count($matching_menus) == 1 ? ' item' : ' items'); ?></p>
        <table class="wp-list-table widefat striped fixed" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary">Menu Name</th>
                    <th scope="col" class="manage-column" colspan="3">Detected Menu Items</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matching_menus as $menu_name => $menu_data) {
                    $edit_url = admin_url('nav-menus.php?action=edit&menu=' . $menu_data['id']); ?>
                    <tr>
                        <td class="title has-row-actions column-primary page-title" data-colname="Menu Name">
                            <strong><a href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($menu_name); ?></a></strong>
                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo esc_url($edit_url); ?>">Edit</a></span>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
                        </td>
                        <td data-colname="Detected Menu Items" colspan="3">
                            <ul>
                                <?php foreach ($menu_data['items'] as $item) { ?>
                                    <li><span class="vsbu-label"><?php echo esc_html($item); ?></span></li>
                                <?php } ?>
                            </ul>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else {
        echo '<p>No results found in menus.</p>';
    }

}