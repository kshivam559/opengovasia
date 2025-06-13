<?php
/**
 * Template part for displaying the awards archive page
 *
 * @package OpenGovAsia
 */

if (!defined('ABSPATH'))
    exit;

$awards_year = get_the_terms(get_the_ID(), 'years');
?>

<div>
    <article class="post type-post panel vstack gap-2">
        <div class="post-image panel overflow-hidden">
            <figure
                class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                <?php if (has_post_thumbnail()): ?>
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                        src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title(); ?>">
                <?php else: ?>
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                        src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
                        alt="No Image Available">
                <?php endif; ?>

            </figure>
            <div
                class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
            </div>

            <span class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                <i class="icon-narrow unicon-trophy-filled"></i>
            </span>
            <a href="<?php the_permalink(); ?>" class="position-cover" data-caption="<?php the_title(); ?>"></a>
            <?php
            $categories = get_the_category();
            if (!empty($categories)):
                echo '<div
                class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">';
                echo '<a class="text-none" href="' . esc_url(get_category_link($categories[0]->term_id)) . '?post_type=events">' . esc_html($categories[0]->name) . '</a>';
                echo '</div>';
            endif;
            ?>
        </div>
        <div class="post-header panel vstack gap-1 lg:gap-2">
            <h3 class="post-title h6 sm:h5 m-0 text-truncate-2 m-0">
                <a class="text-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <div>
                <div
                    class="post-meta panel hstack justify-center fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                    <div class="meta">
                        <div class="hstack gap-2">

                            <div class="post-date hstack gap-narrow">
                                <i class="icon-narrow unicon-calendar"></i>
                                <span class="text-none">
                                    <?php

                                    if (!empty($awards_year) && !is_wp_error($awards_year)):

                                        echo implode(', ', wp_list_pluck($awards_year, 'name'));

                                    else:
                                        echo esc_html(get_the_date('M j, Y'));
                                    endif;

                                    ?>
                                </span>
                            </div>
                            <!-- <div>
                                <a href="<?php the_permalink(); ?>#comments"
                                    class="post-comments text-none hstack gap-narrow">
                                    <i class="icon-narrow unicon-chat"></i>
                                    <span><?php echo get_comments_number(); ?></span>
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>