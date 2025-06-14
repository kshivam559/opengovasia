<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package OpenGov_Asia
 */


/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function opengovasia_pingback_header()
{
	if (is_singular() && pings_open()) {
		printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
	}
}
add_action('wp_head', 'opengovasia_pingback_header');



/**
 * Breadcrumbs functionality for the OpenGovAsia theme.
 *
 * This file contains the opengovasia breadcrumbs function to display breadcrumb navigation
 * on various pages of the OpenGovAsia WordPress theme.
 *
 * @package OpenGovAsia
 */

function opengovasia_breadcrumbs()
{
	if (is_front_page()) {
		return;
	}

	$output = '<div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white" itemscope itemtype="https://schema.org/BreadcrumbList">';
	$output .= '<div class="container max-w-xl">';
	$output .= '<ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0 text-center">';

	// Home Link with Schema
	$output .= '<li itemscope itemtype="https://schema.org/ListItem">
	                <a href="' . esc_url(home_url()) . '" itemprop="item">
	                    <span itemprop="name">Home</span>
	                </a>
	                <meta itemprop="position" content="1" />
	            </li>';

	$separator = '<li><i class="unicon-chevron-right opacity-50"></i></li>';
	$position = 2;

	// Single Post or Custom Post Type
	if (is_single()) {
		$output .= $separator;
		$post_type = get_post_type();
		$post_type_obj = get_post_type_object($post_type);

		// Handle Custom Post Type Archive Link or Label
		if (!in_array($post_type, ['post', 'page'])) {
			if (!empty(get_post_type_archive_link($post_type))) {
				// Show archive link if available
				$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <a href="' . esc_url(get_post_type_archive_link($post_type)) . '" itemprop="item">
                            <span itemprop="name">' . esc_html($post_type_obj->labels->name) . '</span>
                        </a>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
			} else {
				// Show label without link if no archive
				$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">' . esc_html($post_type_obj->labels->name) . '</span>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
			}
			$output .= $separator;
		}

		// Only show categories for default posts
		if ($post_type === 'post') {
			$terms = get_the_terms(get_the_ID(), 'category');
			if (!empty($terms) && is_array($terms)) {
				$first_term = reset($terms);
				$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <a href="' . esc_url(get_term_link($first_term)) . '" itemprop="item">
                            <span itemprop="name">' . esc_html($first_term->name) . '</span>
                        </a>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
				$output .= $separator;
			}
		}

		// Post Title
		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
		                <span itemprop="name">' . esc_html(get_the_title()) . '</span>
		                <meta itemprop="position" content="' . $position++ . '" />
		            </li>';

		// Page Breadcrumbs
	} elseif (is_page()) {
		$output .= $separator;

		// Handle Parent Pages
		$parent_id = wp_get_post_parent_id(get_the_ID());
		if ($parent_id) {
			$parents = [];
			while ($parent_id) {
				$parent = get_post($parent_id);
				$parents[] = '<li itemscope itemtype="https://schema.org/ListItem">
								<a href="' . esc_url(get_permalink($parent_id)) . '" itemprop="item">
									<span itemprop="name">' . esc_html(get_the_title($parent_id)) . '</span>
								</a>
								<meta itemprop="position" content="' . $position++ . '" />
							</li>';
				$parent_id = wp_get_post_parent_id($parent_id);
			}
			$parents = array_reverse($parents);
			$output .= implode($separator, $parents);
			$output .= $separator;
		}

		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
		                <span itemprop="name">' . esc_html(get_the_title()) . '</span>
		                <meta itemprop="position" content="' . $position++ . '" />
		            </li>';

		// Category / Taxonomy Archives
	} elseif (is_category() || is_tax()) {
		$term = get_queried_object();
		$taxonomy = get_taxonomy($term->taxonomy);

		$output .= $separator;
		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
						<span itemprop="name">' . esc_html($taxonomy->labels->singular_name) . '</span>
						<meta itemprop="position" content="' . $position++ . '" />
					</li>';

		$output .= $separator;
		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
						<span itemprop="name">' . esc_html($term->name) . '</span>
						<meta itemprop="position" content="' . $position++ . '" />
					</li>';

		// Custom Post Type Archive Page
	} elseif (is_post_type_archive()) {
		$post_type = get_queried_object()->name; // Fixed issue of displaying "Posts" instead of CPT name
		$post_type_obj = get_post_type_object($post_type);

		$output .= $separator;
		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
		                <span itemprop="name">' . esc_html($post_type_obj->labels->name) . '</span>
		                <meta itemprop="position" content="' . $position++ . '" />
		            </li>';

		// Author Archive
	} elseif (is_author()) {
		$author = get_queried_object();
		if ($author && isset($author->display_name)) {
			$output .= $separator;
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
			                <span itemprop="name">Articles by ' . esc_html($author->display_name) . '</span>
			                <meta itemprop="position" content="' . $position++ . '" />
			            </li>';
		}

		// Search Results
	} elseif (is_search()) {
		$output .= $separator;
		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
		                <span itemprop="name">Search results for "' . esc_html(get_search_query()) . '"</span>
		                <meta itemprop="position" content="' . $position++ . '" />
		            </li>';

		// 404 Page
	} elseif (is_404()) {
		$output .= $separator;
		$output .= '<li itemscope itemtype="https://schema.org/ListItem">
		                <span itemprop="name">404 Not Found</span>
		                <meta itemprop="position" content="' . $position++ . '" />
		            </li>';

		// Date Archives (Year, Month, Day)
	} elseif (is_date()) {
		$output .= $separator;

		if (is_year()) {
			$year = get_the_date('Y');
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">' . esc_html($year) . '</span>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
		} elseif (is_month()) {
			$year = get_the_date('Y');
			$month = get_the_date('F');

			// Add year as a breadcrumb with a link
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <a href="' . esc_url(get_year_link($year)) . '" itemprop="item">
                            <span itemprop="name">' . esc_html($year) . '</span>
                        </a>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
			$output .= $separator;

			// Add month breadcrumb
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">' . esc_html($month) . '</span>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
		} elseif (is_day()) {
			$year = get_the_date('Y');
			$month = get_the_date('F');
			$day = get_the_date('j');

			// Add year breadcrumb with a link
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <a href="' . esc_url(get_year_link($year)) . '" itemprop="item">
                            <span itemprop="name">' . esc_html($year) . '</span>
                        </a>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
			$output .= $separator;

			// Add month breadcrumb with a link
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <a href="' . esc_url(get_month_link($year, get_the_date('m'))) . '" itemprop="item">
                            <span itemprop="name">' . esc_html($month) . '</span>
                        </a>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
			$output .= $separator;

			// Add day breadcrumb
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">' . esc_html($day) . '</span>
                        <meta itemprop="position" content="' . $position++ . '" />
                    </li>';
		}
	}

	$output .= '</ul>';
	$output .= '</div>';
	$output .= '</div>';

	echo $output;
}



/**
 * Archive pagination function for the OpenGovAsia theme.
 * @param mixed $query
 * @package OpenGovAsia
 */
function opengovasia_pagination($query = null)
{
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    $total_pages = $query->max_num_pages;
    if ($total_pages <= 1) return;

    $pagination_links = paginate_links([
        'prev_text' => '<span class="icon icon-1 unicon-chevron-left"></span>',
        'next_text' => '<span class="icon icon-1 unicon-chevron-right"></span>',
        'type' => 'array',
        'mid_size' => 2,
        'end_size' => 1,
        'total' => $total_pages,
        'current' => max(1, get_query_var('paged', 1)),
        'add_args' => array_filter($_GET),
    ]);

    if (!empty($pagination_links)) {
        echo '<div class="nav-pagination pt-3 mt-3 lg:mt-4 border-top">';
        echo '<ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary">';
        foreach ($pagination_links as $link) {
            echo '<li>' . str_replace('page-numbers', 'page-numbers uc-active', $link) . '</li>';
        }
        echo '</ul></div>';
    }
}

/**
 * Format event time for display.
 *
 * @param string $time The time in 'H:i:s' format.
 * @return string Formatted time in 'h:i A' format or empty string if no time is provided.
 */

function format_event_time($time)
{
	return $time ? DateTime::createFromFormat('H:i:s', $time)->format('h:i') : '';
}

/**
 * Get the banner image URL for the OpenGovAsia theme.
 *
 * This function retrieves the banner image URL based on the provided key or returns a default value.
 *
 * @param string $banner_key The key for the banner image.
 * @return string The URL of the banner image or the default value.
 */

function get_archive_banner($banner_page = '')
{
	$banner_images = get_theme_mod('banner_images', []);

	if (!isset($banner_images[$banner_page]) || empty($banner_images[$banner_page])) {
		// Return default banner if specific key is not set
		return get_template_directory_uri() . '/assets/images/demo-three/common/channel-banner.webp';
	}

	// Return specific banner URL or default
	return $banner_images[$banner_page];
}

/**
 * Get the homepage banner for the OpenGovAsia theme.
 *
 * This function retrieves the homepage banner link based on the device type or returns a default value.
 *
 * @param string $device The device type ('desktop' or 'mobile').
 * @return string The URL of the homepage banner or the default value.
 */

function get_homepage_banner($device = '')
{
	$banner_images = get_theme_mod('homepage_banner', []);

	if ($device == 'desktop') {
		$banner_images = $banner_images['desktop_banner'] ?? '';
	} elseif ($device == 'mobile') {
		$banner_images = $banner_images['mobile_banner'] ?? '';
	} else {
		$banner_images = '';
	}

	return $banner_images;
}

/**
 * Get the homepage banner link for the OpenGovAsia theme.
 *
 * This function retrieves the homepage banner link based on the device type or returns a default value.
 *
 * @param string $device The device type ('desktop' or 'mobile').
 * @return string The URL of the homepage banner link or the default value.
 */


function get_homepage_banner_link($device = '')
{
	$banner_links = get_theme_mod('homepage_banner', []);

	if ($device == 'desktop') {
		$banner_links = $banner_links['desktop_banner_link'] ?? esc_url('https://opengovasia.com/');
	} elseif ($device == 'mobile') {
		$banner_links = $banner_links['mobile_banner_link'] ?? esc_url('https://opengovasia.com/');
	} else {
		$banner_links = esc_url('https://opengovasia.com/');
	}

	// Return specific banner link or default
	if (empty($device)) {
		return esc_url('https://opengovasia.com/');
	}

	// Default banner link
	return $banner_links;
}



/**
 * Get the OpenGovAsia theme color based on post or term meta.
 *
 * This function retrieves the theme color for the current post or term.
 * If no specific color is set, it defaults to a predefined color.
 *
 * @return string The hex color code for the theme.
 */

function get_opengovasia_theme_color()
{
	$selected_country = !empty($_GET['c']) ? sanitize_text_field($_GET['c']) : 'global';
	$term = get_term_by('slug', $selected_country, 'country');
	$color_code = get_term_meta($term->term_id, 'country_color', true) ?: '#0c50a8';


	if (is_singular('events')):
		return get_custom_meta(get_the_ID(), 'theme_color');

	else:
		return $color_code; // Default color
	endif;
}