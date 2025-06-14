<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OpenGov_Asia
 */

get_header();

opengovasia_breadcrumbs();

$taxonomy = get_queried_object();
$term_id = 0;

if (is_object($taxonomy) && isset($taxonomy->term_id)) {
	$term_id = $taxonomy->term_id;
}
$sponsored_by = get_term_meta($term_id, 'sponsored_by', true);

?>

<header class="page-header panel vstack text-center">

	<?php

	if (is_category()):

		// Also check category.php for category banner
		$banner_image = !empty(get_term_meta($term_id, 'channel_image', true))
			? get_term_meta($term_id, 'channel_image', true)
			: get_archive_banner('channels');

	elseif (is_tax()):

		$banner_image = !empty(get_term_meta($term_id, 'channel_image', true))
			? get_term_meta($term_id, 'channel_image', true)
			: get_archive_banner();
	else:
		$banner_image = get_archive_banner();
	endif;

	?>

	<div class="og_hero-image" style="background-image: url('<?php echo esc_url($banner_image); ?>');">



		<?php if ($sponsored_by):
			$company = get_post($sponsored_by);
			if ($company && $company->post_status === 'publish') {

				$sponsor_name = $company->post_title;
				$sponsor_link = get_permalink($company->ID);
				$sponsor_image = get_the_post_thumbnail_url($company->ID, 'full');
			}
			?>
			<div
				class="sponsor-link z-2 position-absolute top-0 end-0 m-2 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white">
				<span>Powered by
					<a class="text-none text-primary" href="<?php echo esc_url($sponsor_link); ?>" target="_blank"
						rel="noopener noreferrer">
						<?php echo esc_html($sponsor_name); ?>
					</a>
				</span>
				<?php if ($sponsor_image): ?>
					<div class="sponsor-image m-1">

						<a href="<?php echo esc_url($sponsor_link); ?>">
							<img width="90px" height="90px" src="<?php echo esc_url($sponsor_image); ?>"
								alt="<?php echo esc_attr($sponsor_name); ?>">
						</a>

					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>


		<div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
			<h1 class="h3 lg:h1 text-white">
				<?php
				// Determine the header title dynamically
				$title = 'Archive'; // Default title
				
				if (is_category()) {
					$title = single_cat_title('', false);
				} elseif (is_tag()) {
					$title = single_tag_title('', false);
				} elseif (is_tax()) {
					$title = single_term_title('', false);
				} elseif (is_author()) {
					$title = strip_tags(get_the_archive_title()); // Correct handling of author archives
				} elseif (is_search()) {
					$title = 'Search Results for: ' . get_search_query();
				} elseif (is_post_type_archive()) {
					$title = post_type_archive_title('', false);
				} elseif (is_date()) {
					if (is_year()) {
						$title = get_the_date('Y'); // Yearly archive (e.g., "2025")
					} elseif (is_month()) {
						$title = get_the_date('F Y'); // Monthly archive (e.g., "March 2025")
					} elseif (is_day()) {
						$title = get_the_date('F j, Y'); // Daily archive (e.g., "March 23, 2025")
					} else {
						$title = strip_tags(get_the_archive_title()); // Fallback for date archives
					}
				} elseif (is_archive()) {
					$title = strip_tags(get_the_archive_title());
				}

				// Output the title
				echo esc_html($title);
				?>
			</h1>

			<?php if (get_the_archive_description()): ?>

				<span class="archive-description text-white">
					<?php echo strip_tags(get_the_archive_description()); ?>
				</span>

			<?php else: ?>

				<?php
				global $wp_query;
				$total_posts = $wp_query->found_posts;
				$current_posts = $wp_query->post_count;

				// Get the current term object
				$term = get_queried_object();
				$taxonomy_label = '';
				$term_name = '';

				if ($term) {
					if (function_exists('is_category') && is_category()) {
						$taxonomy_label = 'Channel';
						$term_name = single_term_title('', false);
					} elseif (function_exists('is_tag') && is_tag()) {
						$taxonomy_label = 'Tag';
						$term_name = single_term_title('', false);
					} elseif (function_exists('is_tax') && is_tax() && isset($term->taxonomy)) {
						$taxonomy = function_exists('get_taxonomy') ? get_taxonomy($term->taxonomy) : null;
						$taxonomy_label = $taxonomy ? $taxonomy->label : '';
						$term_name = single_term_title('', false);
					} elseif (function_exists('is_author') && is_author()) {
						$taxonomy_label = 'Author';
						$term_name = get_the_author();
					} elseif (function_exists('is_post_type_archive') && is_post_type_archive()) {
						$taxonomy_label = function_exists('post_type_archive_title') ? post_type_archive_title('', false) : 'Archive';
						$term_name = $taxonomy_label;
					}
				} else {
					$taxonomy_label = 'Archive';
					$term_name = 'All Content';
				}

				echo '<span class="text-white">Showing ' . $current_posts . ' content out of ' . $total_posts . ' under "' . esc_html($term_name) . '" ' . strtolower(esc_html($taxonomy_label)) . '.</span>';
				?>

			<?php endif; ?>

		</div>

	</div>

</header>

<div id="primary" class="section py-3 sm:py-6 lg:py-6">
	<div class="container max-w-xl">
		<div class="panel vstack gap-3 sm:gap-6 lg:gap-7">


			<?php // opengovasia_country_filter_dropdown(); ?>

			<?php if (is_category()): ?>

				<?php opengovasia_dynamic_filter_form(['country', 'filter_post_type']) ?>

			<?php elseif (is_tax('country')): ?>

				<?php opengovasia_dynamic_filter_form(['post_type']) ?>

			<?php else: ?>

				<?php opengovasia_dynamic_filter_form(['country', 'filter_year']) ?>

			<?php endif; ?>

			<?php if (have_posts()): ?>

				<div class="row g-4 xl:g-8">
					<div class="col">
						<div class="panel">
							<div
								class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">

								<?php while (have_posts()):

									the_post();

									/*
									 * Include the Post-Type-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
									 */

									if (get_post_type() === 'awards') {

										get_template_part('template-parts/archive-classic');

									} elseif (get_post_type() === 'events') {

										get_template_part('template-parts/events/archive');

									} elseif (get_post_type() === 'ogtv') {

										get_template_part('template-parts/ogtv/archive');

									} elseif (get_post_type() === 'company') {

										get_template_part('template-parts/company/archive');

									} else {

										get_template_part('template-parts/archive-classic');

									}

								endwhile; ?>

							</div>
						</div>
					</div>
				</div>

				<?php opengovasia_pagination(); ?>

			<?php else: ?>

				<?php get_template_part('template-parts/content', 'none'); ?>

			<?php endif; ?>

		</div>
	</div>
</div>

<?php

get_footer();
