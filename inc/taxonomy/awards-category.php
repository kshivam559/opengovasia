<?php

/**
 * Awards Category Taxonomy
 *
 * This file contains the custom cateogry taxonomy for Awards.
 *
 * @package OpenGovAsia
 */

function register_awards_category_taxonomy()
{
    $args = [
        'labels' => [
            'name' => __('Category', 'opengovasia'),
            'singular_name' => __('Category', 'opengovasia'),
            'search_items' => __('Search Categories', 'opengovasia'),
            'all_items' => __('All Categories', 'opengovasia'),
            'parent_item' => __('Parent Category', 'opengovasia'),
            'parent_item_colon' => __('Parent Category:', 'opengovasia'),
            'edit_item' => __('Edit Category', 'opengovasia'),
            'update_item' => __('Update Category', 'opengovasia'),
            'add_new_item' => __('Add New Category', 'opengovasia'),
            'new_item_name' => __('New Category Name', 'opengovasia'),
            'menu_name' => __('Categories', 'opengovasia'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'query_var' => true,
        'show_in_rest' => true,
    ];
    register_taxonomy('awards-category', ['awards'], $args);
}
add_action('init', 'register_awards_category_taxonomy');