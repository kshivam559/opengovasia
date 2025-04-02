<?php
/**
 * Template part for displaying the awards archive page
 *
 * @package OpenGovAsia
 */

if (!defined('ABSPATH'))
    exit;
?>

<div>
    <article class="post type-post panel vstack gap-2">
        <div class="post-image panel overflow-hidden">
            <figure
                class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                <?php if (has_post_thumbnail()): ?>
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                        src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>">
                <?php else: ?>
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                        src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
                        alt="No Image Available">
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="position-cover" data-caption="<?php the_title(); ?>"></a>
            </figure>
            <!-- <?php
            $categories = get_the_category();
            if (!empty($categories)):
                echo '<div
                class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">';
                echo '<a class="text-none" href="' . esc_url(get_category_link($categories[0]->term_id)) . '?post_type=events">' . esc_html($categories[0]->name) . '</a>';
                echo '</div>';
            endif;
            ?> -->
            <span class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                <i class="icon-narrow unicon-trophy-filled"></i>
            </span>
        </div>
        <div class="post-header panel vstack gap-1 lg:gap-2">
            <h3 class="post-title h6 sm:h5 m-0 text-truncate-2 m-0">
                <a class="text-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <div>
                <div
                    class="post-meta panel hstack justify-center fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex">
                    <div class="meta">
                        <div class="hstack gap-2">
                            <!-- <div class="post-author hstack gap-1">
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
                                    data-uc-tooltip="<?php the_author(); ?>">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                </a>
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
                                    class="text-black dark:text-white text-none fw-bold"><?php the_author(); ?></a>
                            </div> -->
                            <div class="post-date hstack gap-narrow">
                                <i class="icon-narrow unicon-calendar"></i>
                                <span><?php echo get_the_date('M j, Y'); ?></span>
                            </div>
                            <div>
                                <a href="<?php the_permalink(); ?>#comments"
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