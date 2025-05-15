<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OpenGov_Asia
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post type-post single-post pb-4 lg:pb-6 xl:pb-9'); ?>
	data-post-id="<?php the_ID(); ?>">

	<div class="container-full">

		<?php if (has_post_thumbnail()): ?>
			<figure
				class="featured-image m-0 ratio ratio-2x1 uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
				<?php the_post_thumbnail('full', ['class' => 'media-cover image uc-transition-opaque', 'data-uc-img' => 'loading: eager']); ?>
			</figure>
		<?php endif; ?>

		<div class="post-content-wrap panel">


			<div class="container max-w-900px">
				<div class="post-header my-4 lg:mt-6 xl:mt-8">
					<div class="panel vstack gap-2 md:gap-3 mx-auto">
						<h1 class="h4 sm:h3 xl:h1 m-0"><?php the_title(); ?></h1>
					</div>
				</div>

				<div class="post-content panel fs-6 md:fs-5">
					<?php the_content(); ?>
				</div>

				<?php

				// If comments are open or we have at least one comment, load up the comment template.
				if (comments_open() || get_comments_number()):
					comments_template();
				endif;
				?>

			</div>
		</div>
</article>