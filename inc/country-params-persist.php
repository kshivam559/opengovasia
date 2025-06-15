<?php
/**
 * Cache-friendly country parameter persistence
 * 
 * Uses vanilla JavaScript for client-side URL modification to maintain
 * compatibility with page caching solutions.
 * 
 * @package OpenGovAsia
 */

function oga_add_country_script()
{
    if (!is_admin()) {
        add_action('wp_footer', 'oga_output_country_script', 99);
    }
}
add_action('wp_enqueue_scripts', 'oga_add_country_script');

/**
 * Output JavaScript for country parameter persistence
 */
function oga_output_country_script()
{
    ?>
    <script>
        (function () {
            function getCountryParam() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('c');
            }

            const country = getCountryParam();
            if (!country) return;

            function isInternalLink(url) {
                return url.hostname === window.location.hostname;
            }

            function updateUrls() {
                document.querySelectorAll('a:not([data-oga-processed])').forEach(link => {
                    const url = new URL(link.href, window.location.origin);

                    if (isInternalLink(url) && !url.searchParams.has('c')) {
                        url.searchParams.set('c', country);
                        link.href = url.toString();
                    }
                    link.setAttribute('data-oga-processed', 'true');
                });
            }

            function updateForms() {
                document.querySelectorAll('form:not([data-oga-processed])').forEach(form => {
                    if (![...form.elements].some(input => input.name === 'c')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'c';
                        input.value = country;
                        form.appendChild(input);
                    }
                    form.setAttribute('data-oga-processed', 'true');
                });
            }

            function initMutationObserver() {
                const observer = new MutationObserver(() => {
                    updateUrls();
                    updateForms();
                });
                observer.observe(document.body, { childList: true, subtree: true });
            }

            updateUrls();
            updateForms();
            initMutationObserver();
        })();
    </script>
    <?php
}


/**
 * Display country switching UI
 */
function oga_display_country_switcher()
{
    $countries = get_terms(['taxonomy' => 'country', 'hide_empty' => true]);
    $selected_country = isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '';
    $selected_country_name = 'Global';
    $selected_country_flag_html = '<i class="icon icon-1 unicon-earth-filled"></i>';

    if ($selected_country) {
        $selected_term = get_term_by('slug', $selected_country, 'country');
        if ($selected_term && !is_wp_error($selected_term)) {
            $selected_country_name = esc_html($selected_term->name);
            $flag_url = get_term_meta($selected_term->term_id, 'country_flag', true);
            if (!empty($flag_url)) {
                $selected_country_flag_html = '<img src="' . esc_url($flag_url) . '" alt="' . esc_attr($selected_country_name) . ' Flag" style="width: 20px; height: auto;">';
            }
        }
    }

    ?>
    <!-- Footer Country Switcher -->
    <div class="panel hstack justify-center gap-2 lg:gap-3">
        <div class="footer-lang d-inline-block oga-country-switcher">
            <a href="#" class="hstack gap-1 text-none fw-medium oga-country-toggle">
                <?php echo $selected_country_flag_html; ?>
                <span class="oga-current-country"><?php echo $selected_country_name; ?></span>
                <span data-uc-drop-parent-icon=""></span>
            </a>
            <div class="p-2 bg-white dark:bg-gray-800 border border-opacity-15 shadow-xs rounded w-150px" data-uc-drop="mode: click;">
                <ul class="nav-y gap-1 fw-medium items-end oga-country-list">
                    <?php foreach ($countries as $country): ?>
                        <li>
                            <a href="#" class="oga-country-link" data-country="<?php echo esc_attr($country->slug); ?>">
                                <?php echo esc_html($country->name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedCountry = urlParams.get('c');

            /**
             * Remove pagination from URL path and query parameters
             * This handles both pretty permalinks (/page/3/) and query string (?paged=3)
             */
            function removePaginationFromUrl(url) {
                // Create URL object
                const urlObj = new URL(url);
                
                // Remove pagination from path (pretty permalinks)
                // Handles patterns like: /page/3/, /page/3, /3/ at the end
                urlObj.pathname = urlObj.pathname
                    .replace(/\/page\/\d+\/?$/, '/')
                    .replace(/\/\d+\/?$/, '/')
                    .replace(/\/+$/, '/'); // Clean up multiple trailing slashes
                
                // Remove pagination query parameters
                urlObj.searchParams.delete('paged');
                urlObj.searchParams.delete('page');
                
                return urlObj;
            }

            document.querySelectorAll('.oga-country-switcher').forEach(switcher => {
                const countryDisplay = switcher.querySelector('.oga-current-country');
                const countryLinks = switcher.querySelectorAll('.oga-country-link');

                // Set selected country name in each switcher
                if (selectedCountry) {
                    countryLinks.forEach(link => {
                        if (link.dataset.country === selectedCountry) {
                            countryDisplay.textContent = link.textContent;
                        }
                    });
                }

                // Update URL when a country is selected
                countryLinks.forEach(link => {
                    link.addEventListener('click', function (event) {
                        event.preventDefault(); // Prevent default anchor behavior

                        const newCountry = this.dataset.country;
                        
                        // Get clean URL without pagination
                        const cleanUrl = removePaginationFromUrl(window.location.href);
                        
                        // Set the new country parameter
                        cleanUrl.searchParams.set('c', newCountry);

                        // Redirect to the updated URL
                        window.location.href = cleanUrl.toString();
                    });
                });
            });
        });
    </script>
    <?php
}

/**
 * Display current country name
 */
function get_selected_country_name()
{
    $slug = isset($_GET['c']) ? sanitize_text_field($_GET['c']) : 'global';
    $term = get_term_by('slug', $slug, 'country');
    return $term ? $term->name : 'Global';
}

/**
 * Add hreflang tags for country-specific URLs
 */
function output_hreflangs()
{
    // Fetch all countries (taxonomy terms)
    $countries = get_terms(['taxonomy' => 'country', 'hide_empty' => false]);

    // Use WordPress function to get current URL more reliably
    $current_url = home_url(add_query_arg(null, null));
    
    // Remove pagination parameters from current URL
    $base_url = remove_query_arg(['paged', 'page', 'c'], $current_url);
    
    // Get existing query parameters
    $query_string = parse_url($current_url, PHP_URL_QUERY);
    $query_params = [];
    if ($query_string) {
        parse_str($query_string, $query_params);
        // Remove pagination and country parameters
        unset($query_params['c'], $query_params['paged'], $query_params['page']);
    }

    // Default (global) hreflang
    $global_url = add_query_arg(array_merge($query_params, ['c' => 'global']), $base_url);
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($global_url) . '" />' . "\n";

    // Loop through each country to add hreflang links
    foreach ($countries as $country) {
        // Skip 'global' if it's in the terms
        if ($country->slug == 'global') {
            continue;
        }

        // Determine the hreflang tag format (using 'en-' before the country code for example)
        $hreflang = 'en-' . strtolower($country->slug);

        // Generate the URL for the current country without pagination
        $country_url = add_query_arg(array_merge($query_params, ['c' => $country->slug]), $base_url);

        // Output the hreflang tag
        echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($country_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'output_hreflangs');

/**
 * Add a REST API endpoint to fetch geolocation data
 */
add_action('rest_api_init', function () {
    register_rest_route('opengovasia/v1', '/geolocation', [
        'methods' => 'GET',
        'callback' => function () {
            $user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];

            if (empty($user_ip) || $user_ip === '127.0.0.1' || $user_ip === '::1') {
                return new WP_Error('geo_error', 'Invalid IP address' . $user_ip . '', ['status' => 400]);
                // $user_ip = '106.219.00.100'; // Test IP
            }

            $response = wp_remote_get("https://ipapi.co/{$user_ip}/json/");

            if (is_wp_error($response)) {
                return new WP_Error('geo_error', 'Failed to fetch location', ['status' => 500]);
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (empty($data) || empty($data['country'])) {
                return new WP_Error('geo_error', 'Invalid location data', ['status' => 500]);
            }

            return [
                'country' => strtolower($data['country']),
                'country_name' => $data['country_name'],
            ];
        },
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Add Cloudflare IP country header to the head
 */
add_action('wp_head', function () {
    if (!headers_sent() && isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
        echo '<meta name="user-country" content="' . esc_attr(strtolower($_SERVER['HTTP_CF_IPCOUNTRY'])) . '">' . "\n";
    }
});
