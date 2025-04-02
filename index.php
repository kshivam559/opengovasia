<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OpenGov_Asia
 */

get_header();
?>

<!-- Section start -->
<div id="latest-news" class="latest-news section panel overflow-hidden">
	<div class="section-outer panel py-3 lg:py-6">
		<div class="container max-w-xl">
			<div class="section-inner panel vstack gap-4">
				<div class="section-header panel vstack items-center justify-center text-center gap-1">
					<h3 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">
						<span>Latest News</span>
					</h3>

				</div>

				<?php if (have_posts()): ?>

					<div class="row g-4 xl:g-8">
						<div class="col">
							<div class="panel text-center">
								<div
									class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">

									<?php

									while (have_posts()):

										the_post();

										?>


										<div>
											<article class="post type-post panel vstack gap-2">
												<div class="post-image panel overflow-hidden">
													<figure
														class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
														<?php if (has_post_thumbnail()): ?>
															<img class="media-cover image uc-transition-scale-up uc-transition-opaque"
																src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>"
																alt="<?php the_title(); ?>">
														<?php else: ?>
															<img class="media-cover image uc-transition-scale-up uc-transition-opaque"
																src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
																alt="No Image Available">
														<?php endif; ?>
														<a href="<?php the_permalink(); ?>" class="position-cover"
															data-caption="<?php the_title(); ?>"></a>
													</figure>
													<!-- <?php // if (is_paged()): ?>
														<div
															class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">
															<?php
															$categories = get_the_category();
															if (!empty($categories)):
																echo '<a class="text-none" href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
															endif;
															?>
														</div>
													<?php // endif; ?> -->
												</div>
												<div class="post-header panel vstack gap-1 lg:gap-2">
													<h2 class="post-title h6 sm:h5 xl:h5 m-0 text-truncate-2 m-0">
														<a class="text-none"
															href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
													</h2>
													<div>
														<div
															class="post-meta panel hstack justify-center fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 md:d-flex">
															<div class="meta">
																<div class="hstack gap-2">

																	<div class="post-date hstack gap-narrow">
																		<i class="icon-narrow unicon-calendar"></i>
																		<span><?php echo get_the_date('M j, Y'); ?></span>
																	</div>
																	<div>
																		<a href="<?php the_permalink(); ?>#blog-comment"
																			class="post-comments text-none hstack gap-narrow">
																			<i class="icon-narrow unicon-chat"></i>
																			<span><?php echo get_comments_number(); ?></span>
																		</a>
																	</div>
																</div>
															</div>

														</div>
													</div>
												</div>
											</article>
										</div>


										<?php

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
</div>
<!-- Section end -->

<?php

get_footer();
