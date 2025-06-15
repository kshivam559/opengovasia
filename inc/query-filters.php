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
            // Initialize tax_query if it doesn't exist
            if (!isset($args['tax_query'])) {
                $args['tax_query'] = [];
            }
            
            $args['tax_query'][] = [
                'taxonomy' => 'country',
                'field' => 'slug',
                'terms' => [$country, 'global'],
                'operator' => 'IN',
            ];
        }

        // Try object cache first (Redis/Memcached if available)
        $cache_key = 'country_query_' . md5(maybe_serialize($args));
        $cached_query = wp_cache_get($cache_key, 'country_queries');

        if ($cached_query !== false) {
            parent::__construct([]);
            foreach (['posts', 'post_count', 'found_posts', 'max_num_pages'] as $key) {
                if (isset($cached_query[$key])) {
                    $this->$key = $cached_query[$key];
                }
            }
            return;
        }

        // Execute the main query
        parent::__construct($args);

        // Cache results in object cache (30 minutes, auto-cleanup)
        wp_cache_set($cache_key, [
            'posts' => $this->posts,
            'post_count' => $this->post_count,
            'found_posts' => $this->found_posts,
            'max_num_pages' => $this->max_num_pages
        ], 'country_queries', 30 * MINUTE_IN_SECONDS);
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
            // Get existing tax_query to preserve other taxonomy filters
            $existing_tax_query = $query->get('tax_query');
            if (!is_array($existing_tax_query)) {
                $existing_tax_query = [];
            }
            
            $existing_tax_query[] = [
                'taxonomy' => 'country',
                'field' => 'slug',
                'terms' => [$country, 'global'],
                'operator' => 'IN',
            ];
            
            $query->set('tax_query', $existing_tax_query);
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
    if (!is_admin() && $query->is_main_query() && (is_archive() || is_search())) {
        if (isset($_GET['filter_year']) && is_numeric($_GET['filter_year'])) {
            // Get existing tax_query to preserve other taxonomy filters
            $existing_tax_query = $query->get('tax_query');
            if (!is_array($existing_tax_query)) {
                $existing_tax_query = [];
            }
            
            $existing_tax_query[] = [
                'taxonomy' => 'years',
                'field' => 'name',
                'terms' => intval($_GET['filter_year']),
            ];
            
            $query->set('tax_query', $existing_tax_query);
        }
    }
}
add_action('pre_get_posts', 'filter_archive_by_years_taxonomy');

/**
 * Manage post type filtering and archive displays
 * 
 * @param WP_Query $query The WordPress query object
 */
function manage_post_types_and_archives($query)
{
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
    }
}
add_action('pre_get_posts', 'manage_post_types_and_archives');

/**
 * Dynamic filter form for the OpenGovAsia theme.
 * Optimized for high-traffic websites with efficient caching strategies.
 *
 * @param array $filters Array of filters to display
 * @package OpenGovAsia
 */
function opengovasia_dynamic_filter_form($filters = [])
{
    if ((is_date() || (!is_archive() && !is_search())) && !is_page('upcoming-events')) {
        return;
    }

    $selected_values = [
        'filter_post_type' => isset($_GET['filter_post_type']) ? sanitize_text_field($_GET['filter_post_type']) : '',
        'country' => isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '',
        'filter_year' => isset($_GET['filter_year']) ? sanitize_text_field($_GET['filter_year']) : ''
    ];

    $is_search_page = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    // Static post type options - no database query needed
    $post_type_options = [
        '' => 'Post Type',
        'post' => 'Posts',
        'events' => 'Events',
        'ogtv' => 'OGTV'
    ];

    // Optimized country options with object cache (Redis/Memcached)
    $country_options = get_country_options_cached();

    // Optimized year options with object cache
    $year_options = get_year_options_cached();

    $filter_definitions = [
        'filter_post_type' => [
            'label' => 'Filter by:',
            'options' => $post_type_options,
            'param' => 'filter_post_type',
            'icon' => 'unicon-document-alt'
        ],
        'country' => [
            'label' => 'Filter by:',
            'options' => $country_options,
            'param' => 'c',
            'icon' => ' unicon-earth-filled'
        ],
        'filter_year' => [
            'label' => 'Filter by:',
            'options' => $year_options,
            'param' => 'filter_year',
            'icon' => 'unicon-calendar'
        ]
    ];

    // Get the base URL without pagination
    $current_url = get_base_url_without_pagination();
    
    // Remove existing filter parameters from the base URL
    $params_to_remove = array_keys($filter_definitions);
    $current_url = remove_query_arg($params_to_remove, $current_url);
    ?>

    <form method="get" action="<?php echo esc_url($current_url); ?>" class="hstack items-center gap-2 justify-between">
        <?php if (!empty($is_search_page)): ?>
            <input type="hidden" name="s" value="<?php echo esc_attr($is_search_page); ?>">
        <?php endif; ?>

        <div class="opacity-60 hstack gap-1"><i class="icon icon-1 unicon-settings-adjust-filled"></i>Filter by:</div>

        <div class="hstack gap-2 ">
            <?php foreach (array_intersect(array_keys($filter_definitions), $filters) as $filter):
                $filter_data = $filter_definitions[$filter];
                ?>
                <div>
                    <div class="hstack gap-2 fs-6 justify-between position-relative items-center">
                        <select name="<?php echo esc_attr($filter_data['param']); ?>"
                            class="form-select form-control-xs fs-6 w-150px dark:bg-gray-900 dark:text-white dark:border-gray-700 select-filter-type"
                            onchange="this.form.submit();">
                            <?php foreach ($filter_data['options'] as $value => $name): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_values[$filter] ?? '', $value); ?>>
                                    <?php echo esc_html($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Mobile trigger icon -->
                        <i class="position-absolute filter-select-icon icon icon-2 <?php echo esc_attr($filter_data['icon']); ?> d-none"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
    <?php
}

/**
 * Get country options with optimized caching
 * Uses object cache (Redis/Memcached) instead of database transients
 */
function get_country_options_cached() {
    // Try object cache first (Redis/Memcached if available)
    $cache_key = 'oga_country_options';
    $country_options = wp_cache_get($cache_key, 'taxonomy_options');
    
    if ($country_options !== false) {
        return $country_options;
    }
    
    // Fallback: check if we can use a static variable for the current request
    static $static_country_options = null;
    if ($static_country_options !== null) {
        return $static_country_options;
    }
    
    // Build options from database
    $country_options = ['' => 'Country'];
    $countries = get_terms([
        'taxonomy' => 'country', 
        'hide_empty' => true,
        'fields' => 'id=>name' // Only get what we need
    ]);
    
    if (!is_wp_error($countries) && !empty($countries)) {
        foreach ($countries as $country_id => $country_name) {
            $country_slug = get_term_by('id', $country_id, 'country')->slug;
            $country_options[$country_slug] = $country_name;
        }
    }
    
    // Cache in object cache (30 minutes, auto-cleanup)
    wp_cache_set($cache_key, $country_options, 'taxonomy_options', 360 * MINUTE_IN_SECONDS);
    
    // Also store in static variable for current request
    $static_country_options = $country_options;
    
    return $country_options;
}

/**
 * Get year options with optimized caching
 * Uses object cache (Redis/Memcached) instead of database transients
 */
function get_year_options_cached() {
    // Try object cache first
    $cache_key = 'oga_year_options';
    $year_options = wp_cache_get($cache_key, 'taxonomy_options');
    
    if ($year_options !== false) {
        return $year_options;
    }
    
    // Fallback: check static variable for current request
    static $static_year_options = null;
    if ($static_year_options !== null) {
        return $static_year_options;
    }
    
    // Build options from database
    $year_options = ['' => 'Year'];
    $years = get_terms([
        'taxonomy' => 'years', 
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'DESC',
        'fields' => 'id=>name' // Only get what we need
    ]);
    
    if (!is_wp_error($years) && !empty($years)) {
        foreach ($years as $year_id => $year_name) {
            $year_slug = get_term_by('id', $year_id, 'years')->slug;
            $year_options[$year_slug] = $year_name;
        }
    }
    
    // Cache in object cache (1 hour, auto-cleanup)
    wp_cache_set($cache_key, $year_options, 'taxonomy_options', HOUR_IN_SECONDS);
    
    // Also store in static variable for current request
    $static_year_options = $year_options;
    
    return $year_options;
}

/**
 * Clear taxonomy options cache when terms are updated
 * This ensures fresh data when terms are added/modified/deleted
 */
function clear_taxonomy_options_cache($term_id, $tt_id, $taxonomy) {
    if (in_array($taxonomy, ['country', 'years'])) {
        wp_cache_delete('oga_country_options', 'taxonomy_options');
        wp_cache_delete('oga_year_options', 'taxonomy_options');
    }
}
add_action('created_term', 'clear_taxonomy_options_cache', 10, 3);
add_action('edited_term', 'clear_taxonomy_options_cache', 10, 3);
add_action('delete_term', 'clear_taxonomy_options_cache', 10, 3);


/**
 * Add custom query vars for pagination with filters
 */
function add_custom_query_vars($vars) {
    $vars[] = 'filter_post_type';
    $vars[] = 'filter_year';
    $vars[] = 'c';
    return $vars;
}
add_filter('query_vars', 'add_custom_query_vars');

/**
 * Modify pagination links to preserve filter parameters
 */
function preserve_filters_in_pagination($link) {
    $filter_params = [];
    
    // Collect active filter parameters
    if (isset($_GET['filter_post_type']) && !empty($_GET['filter_post_type'])) {
        $filter_params['filter_post_type'] = sanitize_text_field($_GET['filter_post_type']);
    }
    
    if (isset($_GET['c']) && !empty($_GET['c'])) {
        $filter_params['c'] = sanitize_text_field($_GET['c']);
    }
    
    if (isset($_GET['filter_year']) && !empty($_GET['filter_year'])) {
        $filter_params['filter_year'] = sanitize_text_field($_GET['filter_year']);
    }
    
    if (isset($_GET['s']) && !empty($_GET['s'])) {
        $filter_params['s'] = sanitize_text_field($_GET['s']);
    }
    
    // Add filter parameters to pagination link
    if (!empty($filter_params)) {
        $link = add_query_arg($filter_params, $link);
    }
    
    return $link;
}
add_filter('paginate_links', 'preserve_filters_in_pagination');
add_filter('get_pagenum_link', 'preserve_filters_in_pagination');

/**
 * Get base URL without pagination for filter forms
 * This handles both pretty permalinks and query string pagination
 */
function get_base_url_without_pagination() {
    global $wp;
    
    // Get current URL
    $current_url = home_url($wp->request);
    
    // Handle pretty permalink pagination patterns
    $current_url = preg_replace('/\/page\/\d+\/?$/', '/', $current_url);
    $current_url = preg_replace('/\/\d+\/?$/', '/', $current_url);
    
    // Clean up multiple trailing slashes
    $current_url = preg_replace('/\/+$/', '/', $current_url);
    
    // Add query string if it exists, excluding pagination parameters
    if (!empty($_SERVER['QUERY_STRING'])) {
        $query_params = [];
        parse_str($_SERVER['QUERY_STRING'], $query_params);
        
        // Remove pagination parameters
        unset($query_params['paged'], $query_params['page']);
        
        if (!empty($query_params)) {
            $current_url = add_query_arg($query_params, $current_url);
        }
    }
    
    return $current_url;
}


/**
 * Prevent redirecting to custom post type archives
 * 
 * @param string $template The path of the template to include
 * @return string The path of the template to include
 */
function prevent_custom_archive_page_redirect($template)
{
    // Check if we're on a category page with a filter
    if (
        (is_category() || is_tax('country') || is_date()) &&
        isset($_GET['filter_post_type']) && !empty($_GET['filter_post_type'])
    ) {
        // Use archive.php or index.php template instead of forcing specific post type archive
        $template = locate_template(['archive.php', 'index.php']);
    }
    return $template;
}
add_filter('template_include', 'prevent_custom_archive_page_redirect', 99);

/**
 * Custom query for Past Events
 * 
 * This code filters the main query to show only past events
 * based on a custom meta field 'event_date'.
 */

add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('events')) {
        $today = date('Y-m-d');

        // Set context for HybridMeta filters
        HybridMeta::set_main_query_context('events', [
            [
                'key' => 'event_date',
                'value' => $today,
                'compare' => '<'
            ]
        ]);

        // Set orderby
        $query->set('orderby', 'event_date');
        $query->set('order', 'ASC');

    }
});

// Simple cleanup - just clear context after main query
add_action('wp', function () {
    // Clear context after main query is done
    if (is_post_type_archive(['events', 'awards', 'company', 'testimonials', 'ogtv'])) {
        HybridMeta::clear_main_query_context();
    }
});

// Include ogtv custom post type in playlists taxonomy queries

function include_ogtv_in_playlists_query($query)
{
    // Only modify the main query on the frontend
    if (!is_admin() && $query->is_main_query()) {
        // If it's a taxonomy archive for playlists
        if ($query->is_tax('playlists')) {
            // Set post_type to include your CPT
            $query->set('post_type', ['ogtv']);
        }
    }
}
add_action('pre_get_posts', 'include_ogtv_in_playlists_query');