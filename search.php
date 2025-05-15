<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package OpenGov_Asia
 */

get_header();

opengovasia_breadcrumbs();	// Breadcrumbs

$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : ''; // Get selected post type

?>

<div class="section py-3 sm:py-6 lg:py-9">
	<div class="container max-w-xl">
		<div class="panel vstack gap-3 sm:gap-6 lg:gap-6">
			<header class="page-header panel vstack text-center">
				<h1 class="h3 lg:h1">
					<?php echo 'Search Results for: ' . get_search_query(); ?>
				</h1>

				<?php if (is_search()): ?>
					<?php
					$total_posts = $wp_query->found_posts; // Total posts in this search
					$current_posts = $wp_query->post_count; // Posts displayed on the current page
					?>
					<span class="m-0 opacity-60">
						Showing <?php echo $current_posts; ?> posts out of <?php echo $total_posts; ?> total
						<br class="d-block lg:d-none"> "<?php echo get_search_query(); ?>" results.
					</span>
				<?php endif; ?>

			</header>


			<?php opengovasia_dynamic_filter_form(['country', 'filter_post_type']); ?>

			<?php if (have_posts()): ?>

				<div class="row g-4 xl:g-8">
					<div class="col">
						<div class="panel text-center">
							<div
								class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">

								<?php
								while (have_posts()):
									the_post();

									if (get_post_type() === 'awards'):

										get_template_part('template-parts/awards/archive');

									elseif (get_post_type() === 'events'):

										get_template_part('template-parts/events/archive');

									elseif (get_post_type() === 'ogtv'):

										get_template_part('template-parts/ogtv/archive-modern');

									else:

										get_template_part('template-parts/archive');

									endif;

								endwhile;
								?>

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
