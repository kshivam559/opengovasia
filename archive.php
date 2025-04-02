<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OpenGov_Asia
 */

get_header();

?>

<?php opengovasia_breadcrumbs(); ?>

<div class="section py-3 sm:py-6 lg:py-9">
	<div class="container max-w-xl">
		<div class="panel vstack gap-3 sm:gap-6 lg:gap-6">

			<header class="page-header panel vstack text-center">
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
				echo '<h1 class="h3 lg:h1">' . esc_html($title) . '</h1>';
				?>

				<?php if (get_the_archive_description()): ?>

					<span class="archive-description m-0 opacity-60">
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

					if ($term) {
						if (is_category()) {
							$taxonomy_label = 'Channel';
						} elseif (is_tag()) {
							$taxonomy_label = 'Tag';
						} elseif (is_tax() && isset($term->taxonomy)) {
							$taxonomy = get_taxonomy($term->taxonomy);
							$taxonomy_label = $taxonomy ? $taxonomy->label : '';
						}
					}
					?>

					<span class="m-0 opacity-60">
						Showing <?php echo esc_html($current_posts); ?> posts out of <?php echo esc_html($total_posts); ?>
						total under
						<br class="d-block lg:d-none"> "<?php echo esc_html(single_term_title('', false)); ?>"
						<?php echo esc_html($taxonomy_label); ?>.
					</span>

				<?php endif; ?>

			</header>

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
								class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">

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
