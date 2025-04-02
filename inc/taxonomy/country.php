<?php

/**
 * Country Taxonomy Functions
 *
 * This file contains functions related to the 'country' taxonomy.
 *
 * @package OpenGovAsia
 */

// Register Country Taxonomy
function register_country_taxonomy() {
    register_taxonomy('country', ['post', 'awards', 'events', 'ogtv'], [
        'labels'            => [
            'name'          => __('Countries'),
            'singular_name' => __('Country'),
            'menu_name'     => __('Country'),
            'add_new_item'  => __('Add New Country'),
        ],
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => '', 'with_front' => false],
    ]);
}
add_action('init', 'register_country_taxonomy');


// Ensure Default Country Exists
function create_default_country() {
    if (!term_exists('global', 'country')) {
        wp_insert_term('Global', 'country', ['slug' => 'global']);
    }
}
add_action('init', 'create_default_country');

// Assign Default Country if none is set
function assign_default_country($post_id, $post, $update) {
    if (!in_array($post->post_type, ['events', 'awards', 'ogtv', 'post'])) return;

    $default_country = get_term_by('slug', 'global', 'country');
    if (!$default_country) return;

    if (empty(wp_get_post_terms($post_id, 'country', ['fields' => 'ids']))) {
        wp_set_post_terms($post_id, [$default_country->term_id], 'country', false);
    }
}
add_action('save_post', 'assign_default_country', 10, 3);

// Prevent deletion of default country
function prevent_default_country_deletion($term_id, $taxonomy) {
    $default_country = get_term_by('slug', 'global', 'country');
    if ($taxonomy === 'country' && $default_country->term_id == $term_id) {
        wp_die(__('You cannot delete the default country (Global).', 'textdomain'));
    }
}
add_action('pre_delete_term', 'prevent_default_country_deletion', 10, 2);

