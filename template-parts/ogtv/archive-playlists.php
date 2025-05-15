<?php

/**
 * Template part for displaying the OGTV archive content
 *
 * @package OpenGovAsia
 */

$post_id = get_the_ID();

$category = get_the_category();

$category_link = !empty($category) ? get_category_link($category[0]->term_id) : '#';

$category_name = !empty($category) ? $category[0]->name : 'No Channel';

$featured_image = !empty(get_the_post_thumbnail_url($post_id, 'full')) ? get_the_post_thumbnail_url($post_id, 'full') : get_template_directory_uri() . '/assets/images/common/img-fallback.png';

$media_url = $featured_image;

$post_url = !empty(isset($args['playlist_slug'])) ? get_permalink($post_id) . '?playlist=' . $args['playlist_slug'] : get_permalink($post_id);


?>


<div>
    <article class="post type-post panel vstack gap-1 lg:gap-2">
        <div class="post-media panel overflow-hidden">
            <div class="featured-video bg-gray-700 ratio ratio-16x9">

                <img src="<?php echo esc_url($media_url); ?>" alt="<?php the_title(); ?>" class="img-fluid">

            </div>
            <div
                class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
            </div>
            <span class="cstack has-video-icon position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                <i class="icon-narrow unicon-play-filled-alt"></i>
            </span>
            <a href="<?php echo $post_url; ?>" class="position-cover"></a>
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
                        <span><?php echo get_the_date('M j, Y'); ?></span>
                    </div>
                </div>
            </div>
            <h3 class="post-title h6 lg:h5 m-0 text-truncate-2 mb-1">
                <a class="text-none hover:text-primary duration-150" href="<?php echo $post_url; ?>">
                    <?php the_title(); ?>
                </a>
            </h3>
        </div>
    </article>
</div>