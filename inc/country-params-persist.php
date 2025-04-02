<?php
/**
 * Cache-friendly country parameter persistence
 * 
 * Uses vanilla JavaScript for client-side URL modification to maintain
 * compatibility with page caching solutions.
 * 
 * @package OpenGovAsia
 */

/**
 * Set country cookie if country parameter is present
 */
// function oga_set_country_cookie()
// {
//     if (isset($_GET['c']) && !empty($_GET['c'])) {
//         $country = sanitize_text_field($_GET['c']);

//         // Verify this is a valid country term
//         if (term_exists($country, 'country')) {
//             setcookie('oga_country', $country, time() + (86400 * 30), '/', '', is_ssl(), true);
//         }
//     }
// }
// add_action('init', 'oga_set_country_cookie', 5);

/**
 * Add country parameter script to footer
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
            function getCookie(name) {
                return document.cookie.split('; ').reduce((acc, cookie) => {
                    let [key, value] = cookie.split('=');
                    return key === name ? value : acc;
                }, '');
            }

            function getCountryParam() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('c') || getCookie('oga_country');
            }

            const country = getCountryParam();
            if (!country) return;

            function isInternalLink(url) {
                return url.hostname === window.location.hostname; // Ensures it's an internal link
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
    $selected_country_name = 'Select Country';

    if ($selected_country) {
        $selected_term = get_term_by('slug', $selected_country, 'country');
        if ($selected_term) {
            $selected_country_name = esc_html($selected_term->name);
        }
    }

    ?>
    <!-- Footer Country Switcher -->
    <div class="panel hstack justify-center gap-2 lg:gap-3">
        <div class="footer-lang d-inline-block oga-country-switcher">
            <a href="#" class="hstack gap-1 text-none fw-medium oga-country-toggle">
                <i class="icon icon-1 unicon-earth-filled"></i>
                <span class="oga-current-country"><?php echo $selected_country_name; ?></span>
                <span data-uc-drop-parent-icon=""></span>
            </a>
            <div class="p-2 bg-white dark:bg-gray-800 shadow-xs rounded w-150px" data-uc-drop="mode: click;">
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
                        const currentUrl = new URL(window.location.href);

                        currentUrl.searchParams.set('c', newCountry); // Update only 'c' param

                        window.location.href = currentUrl.toString(); // Redirect to updated URL
                    });
                });
            });
        });
    </script>
    <?php
}




/**
 * Get current country
 */
function oga_get_current_country()
{
    return $_GET['c'] ?? $_COOKIE['oga_country'] ?? null;
}