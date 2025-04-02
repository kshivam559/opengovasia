<?php
/**
 * Country filtering functionality for OpenGovAsia
 * 
 * Provides consistent country filtering across both main WordPress query
 * and custom queries through an extended WP_Query class.
 * 
 * @package OpenGovAsia
 */

/**
 * Wrapper for WP_Query that automatically applies country filtering
 */
class Country_Filtered_Query extends WP_Query
{
    /**
     * Constructor
     * 
     * @param array $args Query arguments
     */
    public function __construct($args = [])
    {
        $country = isset($args['country']) ? sanitize_text_field($args['country']) : (isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '');
        $term_exists = $country ? term_exists($country, 'country') : false;

        if (!empty($country) && $country !== 'global' && is_array($term_exists)) {
            $args['tax_query'][] = [
                'taxonomy' => 'country',
                'field' => 'slug',
                'terms' => $country,
            ];
        }

        // Generate a unique cache key for query results
        $cache_key = 'country_query_' . md5(maybe_serialize($args));
        $cached_query = get_transient($cache_key);

        if ($cached_query !== false) {
            parent::__construct([]);
            foreach (['posts', 'post_count', 'found_posts', 'max_num_pages'] as $key) {
                if (isset($cached_query[$key])) {
                    $this->$key = $cached_query[$key];
                }
            }
        }

        // Execute the main query
        parent::__construct($args);

        // Store results in cache to improve performance
        set_transient($cache_key, [
            'posts' => $this->posts,
            'post_count' => $this->post_count,
            'found_posts' => $this->found_posts,
            'max_num_pages' => $this->max_num_pages
        ], HOUR_IN_SECONDS);
    }
}

/**
 * Filter main query to apply country filtering
 *
 * @param WP_Query $query The main query
 */
function filter_posts_by_country_query($query)
{
    if (is_admin() || !$query->is_main_query() || $query->is_404() || $query->is_page()) {
        return;
    }

    $country = isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '';
    if (!empty($country) && $country !== 'global') {
        $term_exists = term_exists($country, 'country');

        if (!empty($term_exists) && is_array($term_exists)) {
            $query->set('tax_query', [
                [
                    'taxonomy' => 'country',
                    'field' => 'slug',
                    'terms' => $country,
                ]
            ]);
        }
    }
}
add_action('pre_get_posts', 'filter_posts_by_country_query');

/**
 * Helper function to get filtered posts by country
 * 
 * @param array $args Query arguments
 * @param string $country Optional country to override URL parameter
 * @return Country_Filtered_Query Query object with results
 */
function get_country_filtered_posts($args = [], $country = '')
{
    if (!empty($country)) {
        $args['country'] = $country;
    }

    $cache_key = 'country_filtered_' . md5(maybe_serialize($args));
    $cached_query = get_transient($cache_key);

    if ($cached_query !== false) {
        return $cached_query;
    }

    $query = new Country_Filtered_Query($args);

    set_transient($cache_key, $query, HOUR_IN_SECONDS);

    return $query;
}

/**
 * Year filtering functionality for OpenGovAsia
 *  
 * Provides consistent year filtering across both main WordPress query
 * and custom queries through an extended WP_Query class.
 *  
 * @package OpenGovAsia
 */

// Filter archive by Years taxonomy
function filter_archive_by_years_taxonomy($query)
{
    if (!is_admin() && $query->is_main_query() && is_archive()) {
        if (isset($_GET['filter_year']) && is_numeric($_GET['filter_year'])) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'years',
                    'field' => 'name',
                    'terms' => intval($_GET['filter_year']),
                ),
            ));
        }
    }
}
add_action('pre_get_posts', 'filter_archive_by_years_taxonomy');


/**
 * Manage post type filtering and archive displays
 * 
 * @param WP_Query $query The WordPress query object
 */
function manage_post_types_and_archives($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Handle post type filtering via URL parameter
        if ((is_archive() || is_search()) && isset($_GET['filter_post_type']) && !empty($_GET['filter_post_type'])) {
            $query->set('post_type', sanitize_text_field($_GET['filter_post_type']));
        }
        
        // Handle category and taxonomy archives
        elseif ($query->is_category() || $query->is_tax('country')) {
            // Include all custom post types in category pages
            $query->set('post_type', ['post', 'events', 'ogtv']);
        }
        
        // Handle events archive - Only filter future events on the events archive page
        elseif ($query->is_post_type_archive('events')) {
            $current_date = current_time('Y-m-d');
            $meta_query = [
                [
                    'key' => 'event_date',
                    'value' => $current_date,
                    'compare' => '<', // Only show past events
                    'type' => 'DATE',
                ],
            ];
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'manage_post_types_and_archives');

/**
 * Prevent redirecting to custom post type archives
 * 
 * @param string $template The path of the template to include
 * @return string The path of the template to include
 */
function prevent_custom_archive_page_redirect($template) {
    // Check if we're on a category page with a filter
    if ((is_category() || is_tax('country') || is_date()) && 
        isset($_GET['filter_post_type']) && !empty($_GET['filter_post_type'])) {
        // Use archive.php or index.php template instead of forcing specific post type archive
        $template = locate_template(['archive.php', 'index.php']);
    }
    return $template;
}
add_filter('template_include', 'prevent_custom_archive_page_redirect', 99);