<?php

/**
 * The taxonomy for OGTV Playlists
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the custom taxonomy for OGTV Playlists
 */

function opengovasia_register_ogtv_playlists_taxonomy()
{
    $labels = array(
        'name' => _x('Playlists', 'taxonomy general name', 'opengovasia'),
        'singular_name' => _x('Playlist', 'taxonomy singular name', 'opengovasia'),
        'search_items' => __('Search Playlists', 'opengovasia'),
        'all_items' => __('All Playlists', 'opengovasia'),
        'parent_item' => __('Parent Playlist', 'opengovasia'),
        'parent_item_colon' => __('Parent Playlist:', 'opengovasia'),
        'edit_item' => __('Edit Playlist', 'opengovasia'),
        'update_item' => __('Update Playlist', 'opengovasia'),
        'add_new_item' => __('Add New Playlist', 'opengovasia'),
        'new_item_name' => __('New Playlist Name', 'opengovasia'),
        'menu_name' => __('Playlists', 'opengovasia'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'show_in_rest' => true,

        // Add the rewrite rules for the taxonomy
        // This will create pretty permalinks for the taxonomy
        // e.g., /playlists/playlist-name/
        // You can customize the slug as needed
        // For example, to use "channels" instead of "playlists", change it to:
        // 'rewrite'          => array('slug' => _x('channels', 'URL slug', 'opengovasia')),
    );

    register_taxonomy('playlists', ['ogtv'], $args);
}

add_action('init', 'opengovasia_register_ogtv_playlists_taxonomy');