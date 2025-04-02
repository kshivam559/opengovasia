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
			<aside class="post-share-float d-none lg:d-block" data-uc-sticky="bottom: .post-author;">
				<div class="vstack justify-center items-center gap-2 position-absolute top-0 end-0 m-4 xl:m-9">
					<span class="ft-secondary">Share</span>
					<ul class="social-icons nav-y justify-center gap-2 text-gray-900 dark:text-white">

							<li>
								<a class="w-40px xl:w-48px h-40px xl:h-48px d-inline-flex justify-center items-center rounded-circle border transition-all duration-200 ease-in hover:scale-110 hover:border-primary hover:bg-primary hover:text-white"
									href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"
									target="_blank">
									<i class="unicon-logo-facebook icon-1"></i>
								</a>
							</li>
							<li>
								<a class="w-40px xl:w-48px h-40px xl:h-48px d-inline-flex justify-center items-center rounded-circle border transition-all duration-200 ease-in hover:scale-110 hover:border-primary hover:bg-primary hover:text-white"
									href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
									target="_blank">
									<i class="unicon-logo-x-filled icon-1"></i>
								</a>
							</li>
							<li>
								<a class="w-40px xl:w-48px h-40px xl:h-48px d-inline-flex justify-center items-center rounded-circle border transition-all duration-200 ease-in hover:scale-110 hover:border-primary hover:bg-primary hover:text-white"
									href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>"
									target="_blank">
									<i class="unicon-logo-linkedin icon-1"></i>
								</a>
							</li>
							<li>
								<a class="w-40px xl:w-48px h-40px xl:h-48px d-inline-flex justify-center items-center rounded-circle border transition-all duration-200 ease-in hover:scale-110 hover:border-primary hover:bg-primary hover:text-white"
									href="mailto:?subject=<?php the_title(); ?>&body=<?php the_permalink(); ?>">
									<i class="unicon-email icon-1"></i>
								</a>
							</li>
							<li>
								<a class="w-40px xl:w-48px h-40px xl:h-48px d-inline-flex justify-center items-center rounded-circle border transition-all duration-200 ease-in hover:scale-110 hover:border-primary hover:bg-primary hover:text-white"
									href="javascript:void(0);" onclick="sharePage()"><i
										class="unicon-link icon-1"></i></a>
							</li>
						</ul>

				</div>
			</aside>

			<div class="container max-w-900px">
				<div class="post-header m-4 lg:mt-6 xl:mt-8">
					<div
						class="panel vstack items-center gap-2 md:gap-3 text-center max-w-400px sm:max-w-500px xl:max-w-md mx-auto">
						<h1 class="h4 sm:h3 xl:h1 m-0"><?php the_title(); ?></h1>
					</div>
				</div>
				
				<div class="post-content panel fs-6 md:fs-5" data-uc-lightbox="animation: scale">
					<?php the_content(); ?>
				</div>
				<div
					class="post-footer panel vstack sm:hstack gap-3 justify-between border-top py-4 mt-4 xl:py-9 xl:mt-9">
			
					<ul class="post-share-icons nav-x gap-narrow">
						<li class="me-1"><span class="text-black dark:text-white">Share:</span></li>
						<li>
							<a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
								href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"
								target="_blank">
								<i class="unicon-logo-facebook icon-1"></i>
							</a>
						</li>
						<li>
							<a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
								href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
								target="_blank">
								<i class="unicon-logo-x-filled icon-1"></i>
							</a>
						</li>
						<li>
							<a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
								href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>">
								<i class="unicon-logo-linkedin icon-1"></i>
							</a>
						</li>
						<li>
							<a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
								href="mailto:?subject=<?php the_title(); ?>&body=<?php the_permalink(); ?>">
								<i class="unicon-email icon-1"></i>
							</a>
						</li>
						<li>
							<a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
								href="javascript:void(0);" onclick="sharePage()">
								<i class="unicon-link icon-1"></i>
							</a>
						</li>
					</ul>
				</div>

				<!-- author box -->

				<?php opengovasia_author_box(); ?>

				<?php display_related_posts(get_the_ID(), 4); ?>


				<?php

				// If comments are open or we have at least one comment, load up the comment template.
				if (comments_open() || get_comments_number()):
					comments_template();
				endif;
				?>

			</div>
		</div>
</article>

