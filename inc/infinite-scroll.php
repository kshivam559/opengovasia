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
    $posts_per_load = apply_filters('opengovasia_posts_per_load', 1);

    wp_enqueue_script(
        'infinite-scroll',
        get_template_directory_uri() . '/assets/js/infinite-scroll.js',
        array('jquery'),
        filemtime(get_template_directory() . '/assets/js/infinite-scroll.js'),
        true
    );

    wp_localize_script('infinite-scroll', 'infiniteScrollData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_posts_nonce'),
        'post_id' => $post->ID,
        'post_date' => get_the_date('Y-m-d H:i:s', $post->ID),
        'posts_per_load' => $posts_per_load,
        'country' => isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '',
        'loading_text' => __('Loading next article...', 'opengovasia'),
        'no_more_text' => __('No more articles available', 'opengovasia'),
    ]);
}
add_action('wp_enqueue_scripts', 'opengovasia_enqueue_infinite_scroll_script');

function opengovasia_load_more_single_posts()
{
    check_ajax_referer('load_more_posts_nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $posts_per_load = min(absint($_POST['posts_per_load'] ?? 1), 4);
    $loaded_post_ids = isset($_POST['loaded_post_ids']) ? array_map('absint', (array) $_POST['loaded_post_ids']) : [$post_id];
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';

    if (!$post_id) {
        wp_send_json_error(['message' => 'Invalid post ID.']);
    }

    $current_post = get_post($post_id);
    if (!$current_post) {
        wp_send_json_error(['message' => 'Current post not found']);
    }

    // Get current post categories
    $category_terms = get_the_category($post_id);
    $category_ids = $category_terms ? wp_list_pluck($category_terms, 'term_id') : [];

    // Generate cache key
    $cache_key = 'infinite_scroll_' . md5(json_encode([
        'post_id' => $post_id,
        'loaded_ids' => $loaded_post_ids,
        'country' => $country,
        'categories' => $category_ids,
        'per_load' => $posts_per_load
    ]));

    $cached_response = get_transient($cache_key);
    if (false !== $cached_response) {
        wp_send_json_success($cached_response);
        return;
    }

    // Base query arguments
    $base_args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_load,
        'post__not_in' => $loaded_post_ids,
        'country' => $country,
        'no_found_rows' => true,
    ];

    // Add category filter if categories exist
    if (!empty($category_ids)) {
        $base_args['category__in'] = $category_ids;
    }

    // Try newer posts first
    $newer_args = array_merge($base_args, [
        'date_query' => [
            [
                'after' => get_the_date('Y-m-d H:i:s', $current_post),
                'inclusive' => false,
            ],
        ],
        'orderby' => 'date',
        'order' => 'ASC',
    ]);

    $newer_query = new Country_Filtered_Query($newer_args);
    
    if ($newer_query->have_posts()) {
        $posts_to_use = $newer_query;
        $is_newer = true;
        // Reverse order to show oldest newer posts first
        $posts_to_use->posts = array_reverse($posts_to_use->posts);
    } else {
        // Try older posts
        $older_args = array_merge($base_args, [
            'date_query' => [
                [
                    'before' => get_the_date('Y-m-d H:i:s', $current_post),
                    'inclusive' => false,
                ],
            ],
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $posts_to_use = new Country_Filtered_Query($older_args);
        $is_newer = false;
    }

    $response = [
        'has_more' => false,
        'content' => '',
        'ids' => [],
        'is_newer' => $is_newer
    ];

    if ($posts_to_use->have_posts()) {
        ob_start();
        $loaded_ids = [];

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

        set_transient($cache_key, $response, 1440 * MINUTE_IN_SECONDS);
        wp_send_json_success($response);
    } else {
        $response['message'] = 'No more posts found.';
        set_transient($cache_key, $response, 240 * MINUTE_IN_SECONDS);
        wp_send_json_error($response);
    }
}
add_action('wp_ajax_load_more_single_posts', 'opengovasia_load_more_single_posts');
add_action('wp_ajax_nopriv_load_more_single_posts', 'opengovasia_load_more_single_posts');

function opengovasia_clear_infinite_scroll_cache($post_id, $post)
{
    if ($post->post_type !== 'post') {
        return;
    }

    global $wpdb;
    $wpdb->query(
        "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_infinite_scroll_%'"
    );
}
add_action('save_post', 'opengovasia_clear_infinite_scroll_cache', 10, 2);
add_action('delete_post', 'opengovasia_clear_infinite_scroll_cache', 10, 2);

function opengovasia_default_posts_per_load($number)
{
    return 4;
}
add_filter('opengovasia_posts_per_load', 'opengovasia_default_posts_per_load');