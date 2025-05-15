<?php

/**
 * Infinite Scroll for OpenGovAsia
 * Version: 1.0
 * Author: Shivam Kumar
 * Author URI: https://www.github.com/kshivam559/
 *
 * Text Domain: opengovasia
 *
 * Description: This file contains the implementation of infinite scroll functionality for the theme.
 * It allows users to load more posts dynamically as they scroll down the page.
 * The script is compatible with WordPress and follows best practices for theme development.
 * 
 * This file is part of the OpenGovAsia theme.
 *
 * The script also includes a filter to set the number of posts loaded per request.
 * The default is set to 1, but can be changed to 4 by modifying the filter.
 * 
 * Usage: opengovasia_default_posts_per_load($number);
 *
 * @package OpenGovAsia
 * @since 1.0
 * 
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue necessary scripts
function opengovasia_enqueue_infinite_scroll_script()
{
    if (!is_singular('post')) {
        return;
    }

    global $post;

    // Get posts per load from filter (default 1, can be set to 4)
    $posts_per_load = apply_filters('opengovasia_posts_per_load', 1);

    // Enqueue the script with automatic versioning
    wp_enqueue_script(
        'infinite-scroll',
        get_template_directory_uri() . '/assets/js/infinite-scroll.js',
        array('jquery'),
        filemtime(get_template_directory() . '/assets/js/infinite-scroll.js'),
        true
    );

    // Pass data to script
    wp_localize_script('infinite-scroll', 'infiniteScrollData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_posts_nonce'),
        'post_id' => $post->ID,
        'post_date' => get_the_date('Y-m-d H:i:s', $post->ID),
        'posts_per_load' => $posts_per_load,
        'loading_text' => __('Loading next article...', 'opengovasia'),
        'no_more_text' => __('No more articles available', 'opengovasia'),
    ]);
}
add_action('wp_enqueue_scripts', 'opengovasia_enqueue_infinite_scroll_script');

// Secure AJAX Handler for Loading More Posts
function opengovasia_load_more_single_posts()
{
    // Verify AJAX request security
    check_ajax_referer('load_more_posts_nonce', 'nonce');

    // Validate and sanitize inputs
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $posts_per_load = isset($_POST['posts_per_load']) ? absint($_POST['posts_per_load']) : 1;
    $current_date = isset($_POST['current_date']) ? sanitize_text_field($_POST['current_date']) : '';
    $loaded_post_ids = isset($_POST['loaded_post_ids']) ? array_map('absint', (array) $_POST['loaded_post_ids']) : [$post_id];

    // Set reasonable limits for security (adjust to 4 as needed)
    $posts_per_load = min($posts_per_load, 4); // Maximum 4 posts per request

    if (!$post_id) {
        wp_send_json_error(['message' => 'Invalid post ID.']);
    }

    // Get country taxonomy terms of the current post
    $country_terms = get_the_terms($post_id, 'country');
    if (!$country_terms || is_wp_error($country_terms)) {
        wp_send_json_error(['message' => 'No country terms found.']);
    }

    $country_ids = wp_list_pluck($country_terms, 'term_id');

    // Get categories (optional)
    $category_terms = get_the_category($post_id);
    $category_ids = $category_terms ? wp_list_pluck($category_terms, 'term_id') : [];

    // Generate unique cache key based on query parameters
    $cache_key = 'infinite_scroll_' . md5(json_encode([
        'post_id' => $post_id,
        'loaded_ids' => $loaded_post_ids,
        'country' => $country_ids,
        'category' => $category_ids,
        'per_load' => $posts_per_load
    ]));

    // Try to get cached results first
    $cached_response = get_transient($cache_key);
    if (false !== $cached_response) {
        wp_send_json_success($cached_response);
        return;
    }

    // Common query arguments
    $common_args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_load,
        'post__not_in' => $loaded_post_ids,
        'tax_query' => [
            [
                'taxonomy' => 'country',
                'field' => 'term_id',
                'terms' => $country_ids,
            ],
        ],
        'no_found_rows' => true, // Performance improvement
    ];

    // Add category filter if available (optional)
    if (!empty($category_ids)) {
        $common_args['tax_query'][] = [
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => $category_ids,
            'operator' => 'IN',
        ];
    }

    // Get the current post to determine next posts
    $current_post = get_post($post_id);
    if (!$current_post) {
        wp_send_json_error(['message' => 'Current post not found']);
    }

    // First, try to get NEWER posts (published after the current post)
    $newer_args = $common_args;
    $newer_args['date_query'] = [
        [
            'after' => get_the_date('Y-m-d H:i:s', $current_post),
            'inclusive' => false,
        ],
    ];
    $newer_args['orderby'] = 'date';
    $newer_args['order'] = 'ASC'; // Get the closest newer posts first

    $newer_query = new WP_Query($newer_args);

    // If we found newer posts, use them
    if ($newer_query->have_posts()) {
        $posts_to_use = $newer_query;
        $is_newer = true;
    } else {
        // Otherwise, get OLDER posts (published before the current post)
        $older_args = $common_args;
        $older_args['date_query'] = [
            [
                'before' => get_the_date('Y-m-d H:i:s', $current_post),
                'inclusive' => false,
            ],
        ];
        $older_args['orderby'] = 'date';
        $older_args['order'] = 'DESC'; // Get the closest older posts first

        $older_query = new WP_Query($older_args);
        $posts_to_use = $older_query;
        $is_newer = false;
    }

    // Process results
    $response = [
        'has_more' => false,
        'content' => '',
        'ids' => [],
        'is_newer' => $is_newer
    ];

    if ($posts_to_use->have_posts()) {
        ob_start();
        $loaded_ids = [];

        // If we're showing newer posts, we need to reverse the order to show oldest first
        if ($is_newer) {
            $posts = array_reverse($posts_to_use->posts);
            $posts_to_use->rewind_posts();
            $posts_to_use->posts = $posts;
        }

        while ($posts_to_use->have_posts()) {
            $posts_to_use->the_post();
            $current_id = get_the_ID();

            $loaded_ids[] = $current_id;

            ?>
            <div class="single-post-content" data-post-id="<?php echo esc_attr($current_id); ?>"
                data-post-date="<?php echo esc_attr(get_the_date('Y-m-d H:i:s')); ?>">
                <?php get_template_part('template-parts/single'); ?>
            </div>
            <?php
        }

        wp_reset_postdata();

        $response['content'] = ob_get_clean();
        $response['ids'] = $loaded_ids;
        $response['has_more'] = count($loaded_ids) === $posts_per_load;

        // Cache results for 10 minutes (can be adjusted)
        set_transient($cache_key, $response, 10 * MINUTE_IN_SECONDS);

        wp_send_json_success($response);
    } else {
        $response['message'] = 'No more posts found.';
        // Cache negative results for 5 minutes
        set_transient($cache_key, $response, 5 * MINUTE_IN_SECONDS);
        wp_send_json_error($response);
    }
}
add_action('wp_ajax_load_more_single_posts', 'opengovasia_load_more_single_posts');
add_action('wp_ajax_nopriv_load_more_single_posts', 'opengovasia_load_more_single_posts');

// Smart cache invalidation when a post is published, updated, or deleted
function opengovasia_clear_infinite_scroll_cache($post_id, $post)
{
    if ($post->post_type !== 'post') {
        return;
    }

    // Get affected country terms
    $country_terms = get_the_terms($post_id, 'country');
    if (!$country_terms || is_wp_error($country_terms)) {
        return;
    }

    // Get transient keys related to these countries
    global $wpdb;
    $country_ids = wp_list_pluck($country_terms, 'term_id');

    // This approach is more targeted - only clear relevant caches
    foreach ($country_ids as $country_id) {
        $like_query = "_transient_infinite_scroll_%{$country_id}%";
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $wpdb->options WHERE option_name LIKE %s",
            $like_query
        ));
    }
}
add_action('save_post', 'opengovasia_clear_infinite_scroll_cache', 10, 2);
add_action('delete_post', 'opengovasia_clear_infinite_scroll_cache', 10, 2);

// Add filter for posts per load
function opengovasia_default_posts_per_load($number)
{
    return 1; // Change to 4 to load 4 posts at once
}
add_filter('opengovasia_posts_per_load', 'opengovasia_default_posts_per_load');