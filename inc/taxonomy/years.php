<?php

/**
 * Years Taxonomy
 *
 * This file contains the custom taxonomy for Years.
 *
 * @package OpenGovAsia
 */

function register_years_taxonomy()
{
    $args = [
        'labels' => [
            'name' => __('Years', 'textdomain'),
            'singular_name' => __('Year', 'textdomain'),
            'all_items' => __('All Years', 'textdomain'),
            'edit_item' => __('Edit Year', 'textdomain'),
            'update_item' => __('Update Year', 'textdomain'),
            'add_new_item' => __('Add New Year', 'textdomain'),
            'new_item_name' => __('New Year Name', 'textdomain'),
            'menu_name' => __('Years', 'textdomain'),
        ],
        'rewrite' => false,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'query_var' => true,
        'show_in_rest' => true,
    ];
    register_taxonomy('years', ['events', 'awards'], $args);
}
add_action('init', 'register_years_taxonomy');