<?php

/**
 * Source Taxonomy
 * 
 * This file contains the custom taxonomy for Source.
 * 
 * @package OpenGovAsia
 * @since 1.0
 */

function register_source_taxonomy()
{
    $args = [
        'labels' => [
            'name' => __('Sources', 'textdomain'),
            'singular_name' => __('Source', 'textdomain'),
            'all_items' => __('All Sources', 'textdomain'),
            'edit_item' => __('Edit Source', 'textdomain'),
            'update_item' => __('Update Source', 'textdomain'),
            'add_new_item' => __('Add New Source', 'textdomain'),
            'new_item_name' => __('New Source Name', 'textdomain'),
            'menu_name' => __('Sources', 'textdomain'),
        ],
       
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'query_var' => true,
        'show_in_rest' => true,
    ];
    register_taxonomy('source', ['post'], $args);
}

add_action('init', 'register_source_taxonomy');
