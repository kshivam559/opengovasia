<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package OpenGov_Asia
 */

get_header();

opengovasia_breadcrumbs();

?>

<main id="primary">

	<?php

	while (have_posts()):

		the_post();

		switch (get_post_type()) {
			case 'awards':
				get_template_part("template-parts/awards/single", "");
				break;
			case 'events':
				get_template_part("template-parts/events/single", "");
				break;
			case 'ogtv':
				get_template_part("template-parts/ogtv/single", "");
				break;
			case 'company':
				get_template_part("template-parts/company/single", "");
				break;
			default:
				?>

				<div class="single-post-container">
					<div class="single-post-content" data-post-id="<?php get_the_ID(); ?>">
						<?php get_template_part('template-parts/single'); ?>
					</div>
				</div>

				<div id="infinite-scroll-posts" class="border-top"></div> <!-- Container for dynamically loaded posts -->

				<div class="loading-spinner" style="display: none; text-align: center;">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>

				<?php
				break;
		}

	endwhile; // End of the loop.
	
	?>

</main>

<?php

get_footer();
