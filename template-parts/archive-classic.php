<?php

/**
 * Template part for displaying the archive content in Classical style
 *
 * @package OpenGovAsia
 */

if (!defined('ABSPATH'))
    exit;


if (is_category()):
    // On category archive page
    $category = get_queried_object();
    $category_name = esc_html($category->name);
    $category_link = esc_url(get_category_link($category->term_id));
else:
    // On single post or other pages
    $categories = get_the_category();
    if (!empty($categories)):
        $category_name = esc_html($categories[0]->name);
        $category_link = esc_url(get_category_link($categories[0]->term_id));
    else:
        $category_name = 'Uncategorized';
        $category_link = '#';
    endif;
endif;

$post_thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full') ?: get_template_directory_uri() . '/assets/images/common/img-fallback.png';
$post_link = get_permalink();
$post_title = get_the_title();
$post_date = get_the_date('M j, Y');
?>
<div>
    <article class="post type-post panel uc-transition-toggle vstack gap-1 lg:gap-2">
        <div class="post-media panel overflow-hidden">
            <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                    src="<?php echo esc_url($post_thumbnail); ?>" alt="<?php echo esc_attr($post_title); ?>"
                    loading="lazy">
            </div>

            <?php if (get_post_type() === 'ogtv'): ?>
                <div
                    class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                </div>
                <span class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                    <i class="icon-narrow unicon-play-filled-alt"></i>
                </span>
            <?php elseif (get_post_type() === 'events'): ?>
                <div
                    class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                </div>
                <span class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                    <i class="icon-narrow unicon-calendar"></i>
                </span>

            <?php elseif (get_post_type() === 'awards'): ?>
                <div
                    class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                </div>
                <span class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                    <i class="icon-narrow unicon-trophy-filled"></i>
                </span>
            <?php else: ?>

            <?php endif; ?>

            <a href="<?php echo esc_url($post_link); ?>" class="position-cover"></a>
        </div>
        <div class="post-header panel vstack gap-1">
            <div
                class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 z-1">
                <div>
                    <div class="post-category hstack gap-narrow fw-semibold">
                        <a class="fw-medium text-none text-primary dark:text-primary-400"
                            href="<?php echo esc_url($category_link); ?>">
                            <?php echo esc_html($category_name); ?>
                        </a>
                    </div>
                </div>
                <div class="sep">|</div>
                <div>
                    <div class="post-date hstack gap-narrow">
                        <?php if (get_post_type() === 'events'): ?>
                            <?php
                            $events_data = get_custom_meta(get_the_ID());
                            $event_date = isset($events_data['event_date']) ? esc_html($events_data['event_date']) : '';
                            ?>

                            <span><?php echo esc_html(!empty($event_date) ? date('M j, Y', strtotime($event_date)) : $post_date); ?></span>
                        <?php elseif (get_post_type() === 'awards'): ?>

                            <?php
                            $awards_year = get_the_terms(get_the_ID(), 'years');
                            if (!empty($awards_year) && !is_wp_error($awards_year)):
                                echo '<span>' . implode(', ', wp_list_pluck($awards_year, 'name')) . '</span>';
                            else:
                                echo '<span>' . esc_html($post_date) . '</span>';
                            endif;
                            ?>

                        <?php else: ?>
                            <span><?php echo esc_html($post_date); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <h3 class="post-title h6 lg:h5 m-0 text-truncate-2 mb-1">
                <a class="text-none hover:text-primary duration-150" href="<?php echo esc_url($post_link); ?>">
                    <?php echo esc_html($post_title); ?>
                </a>
            </h3>
        </div>
    </article>
</div>