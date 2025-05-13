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

		// Handle Custom Post Type Archive Link
		if (!in_array($post_type, ['post', 'page']) && !empty(get_post_type_archive_link($post_type))) {
			$output .= '<li itemscope itemtype="https://schema.org/ListItem">
			                <a href="' . esc_url(get_post_type_archive_link($post_type)) . '" itemprop="item">
			                    <span itemprop="name">' . esc_html($post_type_obj->labels->name) . '</span>
			                </a>
			                <meta itemprop="position" content="' . $position++ . '" />
			            </li>';
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
	if ($total_pages <= 1)
		return; // Don't show pagination if only one page

	$pagination_links = paginate_links([
		'prev_text' => '<span class="icon icon-1 unicon-chevron-left"></span>',
		'next_text' => '<span class="icon icon-1 unicon-chevron-right"></span>',
		'type' => 'array', // Returns an array to format in <ul>
		'mid_size' => 2,
		'end_size' => 1,
		'total' => $total_pages,
		'current' => max(1, get_query_var('paged', 1)), // Ensure current page is correct
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
 * Content filter dropdown for the OpenGovAsia theme.
 *
 * This file contains the opengovasia_content_filter_dropdown function to display a dropdown
 * to filter content on various pages of the OpenGovAsia WordPress theme.
 *
 * @package OpenGovAsia
 */

function opengovasia_content_filter_dropdown()
{
	if (!is_archive() && !is_search()) {
		return;
	}

	$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
	?>
	<!-- Search Filter Dropdown -->
	<div class="">
		<form method="get" action="<?php echo esc_url(home_url('/')); ?>"
			class="search-filter-form d-flex items-center justify-between">
			<input type="hidden" name="s" value="<?php echo get_search_query(); ?>">

			<div class="col-6 lg:col-2 md:col-4">
				<label class="form-label me-2 m-0" for="post_type">Filter by Type:</label>
			</div>

			<div class="col-6 lg:col-2 md:col-4">
				<select name="post_type" id="post_type" class="form-select p-1" onchange="this.form.submit();">
					<option value="">All</option>
					<option value="post" <?php selected($post_type, 'post'); ?>>Posts</option>
					<option value="events" <?php selected($post_type, 'events'); ?>>Events</option>
					<option value="awards" <?php selected($post_type, 'awards'); ?>>Awards</option>
					<option value="ogtv" <?php selected($post_type, 'ogtv'); ?>>OGTV</option>
				</select>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Country filter dropdown for the OpenGovAsia theme.
 *
 * This file contains the opengovasia_country_filter_dropdown function to display a dropdown
 * to filter content by country on various pages of the OpenGovAsia WordPress theme.
 *
 * @package OpenGovAsia
 */

function opengovasia_country_filter_dropdown()
{


	$countries = get_terms(['taxonomy' => 'country', 'hide_empty' => false]);

	if (empty($countries) || is_wp_error($countries)) {
		return '';
	}

	$selected_country = isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '';
	$selected_post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
	$is_search_page = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

	?>
	<!-- Country Filter Dropdown -->
	<div class="">
		<form method="get" class="country-filter-form d-flex items-center justify-between">


			<!-- Preserve Post Type Selection Only If It Exists -->
			<?php if (!empty($selected_post_type)): ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr($selected_post_type); ?>">
			<?php endif; ?>

			<?php if (!empty($is_search_page)): ?>
				<input type="hidden" name="s" value="<?php echo esc_attr($is_search_page); ?>">
			<?php endif; ?>

			<div class="col-6 lg:col-2 md:col-4">
				<label class="form-label me-2 m-0" for="country">Filter by Country:</label>
			</div>

			<div class="col-6 lg:col-2 md:col-4">
				<select name="c" id="country" class="form-select p-1" onchange="this.form.submit();">
					<option value="">All (Global)</option>
					<?php foreach ($countries as $country): ?>
						<option value="<?php echo esc_attr($country->slug); ?>" <?php selected($selected_country, $country->slug); ?>>
							<?php echo esc_html($country->name); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Dynamic filter form for the OpenGovAsia theme.
 *
 * This file contains the opengovasia_dynamic_filter_form function to display a dynamic filter form
 * to filter content on various pages of the OpenGovAsia WordPress theme.
 *
 * @param array $filters Array of filters to display
 * @package OpenGovAsia
 */

// Dynamic filter form content and taxonomy
function opengovasia_dynamic_filter_form($filters = [])
{
	if ((is_date() || (!is_archive() && !is_search())) && !is_page('upcoming-events')) {
		return;
	}

	$selected_values = [
		'filter_post_type' => filter_input(INPUT_GET, 'filter_post_type', FILTER_SANITIZE_STRING) ?? '',
		'country' => filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING) ?? '',
		'filter_year' => filter_input(INPUT_GET, 'filter_year', FILTER_SANITIZE_STRING) ?? ''
	];

	$is_search_page = filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING) ?? '';

	$post_type_options = [
		'' => 'Post Type',
		'post' => 'Posts',
		'events' => 'Events',
		'ogtv' => 'OGTV'
	];

	$country_options = get_transient('opengovasia_country_terms_filter') ?: ['' => 'Country'];
	if ($country_options === ['' => 'Country']) {
		$countries = get_terms(['taxonomy' => 'country', 'hide_empty' => false]);
		if (!is_wp_error($countries)) {
			foreach ($countries as $country) {
				$country_options[$country->slug] = $country->name;
			}
			set_transient('opengovasia_country_terms', $country_options, 1 * HOUR_IN_SECONDS);
		}
	}

	// Retrieve Years from taxonomy instead of static range
	$year_options = ['' => 'Year'];
	$years = get_terms(['taxonomy' => 'years', 'hide_empty' => false]);
	if (!is_wp_error($years)) {
		foreach ($years as $year) {
			$year_options[$year->slug] = $year->name;
		}
	}

	$filter_definitions = [
		'filter_post_type' => [
			'label' => 'Filter by:',
			'options' => $post_type_options,
			'param' => 'filter_post_type',
			'icon' => 'unicon-document-alt' // Unicon icon for post type
		],
		'country' => [
			'label' => 'Filter by:',
			'options' => $country_options,
			'param' => 'c',
			'icon' => ' unicon-earth-filled' // Unicon icon for country
		],
		'filter_year' => [
			'label' => 'Filter by:',
			'options' => $year_options,
			'param' => 'filter_year',
			'icon' => 'unicon-calendar' // Unicon icon for year
		]
	];

	$current_url = remove_query_arg(array_keys($filter_definitions), add_query_arg(null, null));
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
						<i
							class="position-absolute filter-select-icon icon icon-2 <?php echo esc_attr($filter_data['icon']); ?> d-none"></i>

					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</form>
	<?php
}
