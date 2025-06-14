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


/**
 * Enqueue Infinite Scroll script and pass data to JS
 */
function opengovasia_enqueue_infinite_scroll_script_rest()
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
        'rest_url' => esc_url_raw(rest_url('opengovasia/v1/infinite-posts/')),
        'nonce' => wp_create_nonce('wp_rest'),
        'post_id' => $post->ID,
        'post_date' => get_the_date('Y-m-d H:i:s', $post->ID),
        'posts_per_load' => $posts_per_load,
        'country' => isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '',
        'loading_text' => __('Loading next article...', 'opengovasia'),
        'no_more_text' => __('No more articles available', 'opengovasia'),
    ]);
}
add_action('wp_enqueue_scripts', 'opengovasia_enqueue_infinite_scroll_script_rest');

/**
 * Register a custom REST API endpoint for infinite scroll posts
 */
add_action('rest_api_init', function () {
    register_rest_route('opengovasia/v1', '/infinite-posts/', [
        'methods' => 'POST',
        'callback' => 'opengovasia_rest_infinite_posts',
        'permission_callback' => '__return_true',
        'args' => [
            'post_id' => ['required' => true, 'type' => 'integer'],
            'loaded_post_ids' => ['required' => false, 'type' => 'array'],
            'posts_per_load' => ['required' => false, 'type' => 'integer'],
            'country' => ['required' => false, 'type' => 'string'],
        ]
    ]);
});

function opengovasia_rest_infinite_posts($request)
{
    $post_id = absint($request['post_id']);
    $posts_per_load = min(absint($request['posts_per_load'] ?? 1), 4);
    $loaded_post_ids = array_map('absint', (array) ($request['loaded_post_ids'] ?? [$post_id]));
    $country = sanitize_text_field($request['country'] ?? '');

    if (!$post_id) {
        return new WP_REST_Response([
            'success' => false,
            'data' => ['message' => 'Invalid post ID.']
        ], 400);
    }

    $current_post = get_post($post_id);
    if (!$current_post) {
        return new WP_REST_Response([
            'success' => false,
            'data' => ['message' => 'Current post not found']
        ], 400);
    }

    $category_terms = get_the_category($post_id);
    $category_ids = $category_terms ? wp_list_pluck($category_terms, 'term_id') : [];

    // Optimized cache key - shorter and more predictable
    $cache_key_data = [
        'p' => $post_id,
        'l' => count($loaded_post_ids), // Use count instead of full array
        'c' => $country,
        'cat' => !empty($category_ids) ? md5(implode(',', $category_ids)) : '',
        'n' => $posts_per_load
    ];
    
    $cache_key = 'is_' . md5(json_encode($cache_key_data));

    // Try object cache first (Redis/Memcached), fallback to transients
    $cached_response = false;
    if (function_exists('wp_cache_get')) {
        $cached_response = wp_cache_get($cache_key, 'infinite_scroll');
    }
    
    if (false === $cached_response) {
        $cached_response = get_transient($cache_key);
    }
    
    if (false !== $cached_response) {
        return new WP_REST_Response([
            'success' => true,
            'data' => $cached_response
        ], 200);
    }

    $base_args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_load,
        'post__not_in' => $loaded_post_ids,
        'no_found_rows' => true,
        'update_post_meta_cache' => false, // Optimize query
        'update_post_term_cache' => false, // Optimize query
    ];

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

    $newer_query = ($country === 'global')
        ? new WP_Query($newer_args)
        : new Country_Filtered_Query(array_merge($newer_args, ['country' => $country]));

    if ($newer_query->have_posts()) {
        $posts_to_use = $newer_query;
        $is_newer = true;
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

        $older_query = ($country === 'global')
            ? new WP_Query($older_args)
            : new Country_Filtered_Query(array_merge($older_args, ['country' => $country]));

        if ($older_query->have_posts()) {
            $posts_to_use = $older_query;
            $is_newer = false;
        } else {
            $response_data = [
                'content' => '',
                'ids' => [],
                'has_more' => false,
                'is_newer' => false,
                'message' => 'No more posts found.'
            ];
            
            // Cache "no results" for shorter time
            opengovasia_set_cache($cache_key, $response_data, 2 * MINUTE_IN_SECONDS);
            
            return new WP_REST_Response([
                'success' => true,
                'data' => $response_data
            ], 200);
        }
    }

    $response_data = [
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

        $response_data['content'] = ob_get_clean();
        $response_data['ids'] = $loaded_ids;
        $response_data['has_more'] = count($loaded_ids) === $posts_per_load;

        // Cache successful results for longer
        opengovasia_set_cache($cache_key, $response_data, 10 * MINUTE_IN_SECONDS);
        
        return new WP_REST_Response([
            'success' => true,
            'data' => $response_data
        ], 200);
    } else {
        $response_data['message'] = 'No more posts found.';
        opengovasia_set_cache($cache_key, $response_data, 2 * MINUTE_IN_SECONDS);
        
        return new WP_REST_Response([
            'success' => false,
            'data' => $response_data
        ], 200);
    }
}

/**
 * Set cache in both object cache and transients (with cleanup)
 */
function opengovasia_set_cache($key, $data, $expiration) {
    // Try object cache first
    if (function_exists('wp_cache_set')) {
        wp_cache_set($key, $data, 'infinite_scroll', $expiration);
    }
    
    // Set transient as fallback
    set_transient($key, $data, $expiration);
}

/**
 * Optimized cache cleanup - only clean old infinite scroll transients
 */
function opengovasia_clear_rest_infinite_scroll_cache($post_id, $post)
{
    if ($post->post_type !== 'post') {
        return;
    }

    // Clear object cache group if available
    if (function_exists('wp_cache_flush_group')) {
        wp_cache_flush_group('infinite_scroll');
    }

    // Only clean transients older than 1 hour to avoid performance issues
    global $wpdb;
    $wpdb->query($wpdb->prepare(
        "DELETE FROM $wpdb->options 
         WHERE option_name LIKE %s 
         AND option_name LIKE %s 
         AND CAST(SUBSTRING(option_value, 1, 10) AS UNSIGNED) < %d",
        '_transient_timeout_is_%',
        '_transient_is_%',
        time() - 3600
    ));
}
add_action('save_post', 'opengovasia_clear_rest_infinite_scroll_cache', 10, 2);
add_action('delete_post', 'opengovasia_clear_rest_infinite_scroll_cache', 10, 2);

/**
 * Scheduled cleanup of old transients (runs daily)
 */
function opengovasia_cleanup_old_infinite_scroll_transients() {
    global $wpdb;
    
    // Delete expired infinite scroll transients
    $wpdb->query($wpdb->prepare(
        "DELETE FROM $wpdb->options 
         WHERE (option_name LIKE %s OR option_name LIKE %s)
         AND CAST(SUBSTRING(option_value, 1, 10) AS UNSIGNED) < %d",
        '_transient_timeout_is_%',
        '_transient_is_%',
        time()
    ));
    
    // Clean up orphaned timeout entries
    $wpdb->query(
        "DELETE t1 FROM $wpdb->options t1
         LEFT JOIN $wpdb->options t2 ON t1.option_name = CONCAT('_transient_timeout_', SUBSTRING(t2.option_name, 12))
         WHERE t1.option_name LIKE '_transient_timeout_is_%' AND t2.option_name IS NULL"
    );
}
add_action('opengovasia_daily_cleanup', 'opengovasia_cleanup_old_infinite_scroll_transients');

/**
 * Schedule daily cleanup if not already scheduled
 */
function opengovasia_schedule_cleanup() {
    if (!wp_next_scheduled('opengovasia_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'opengovasia_daily_cleanup');
    }
}
add_action('wp', 'opengovasia_schedule_cleanup');

/**
 * Clean up scheduled event on deactivation (add this to your theme/plugin deactivation)
 */
function opengovasia_cleanup_scheduled_events() {
    wp_clear_scheduled_hook('opengovasia_daily_cleanup');
}
// Add this to your theme's functions.php or plugin deactivation hook

function opengovasia_default_posts_per_load($number)
{
    return 4;
}
add_filter('opengovasia_posts_per_load', 'opengovasia_default_posts_per_load');
