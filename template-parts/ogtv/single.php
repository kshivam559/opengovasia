<?php
/**
 * Template part for displaying single posts
 *
 * @package OpenGovAsia
 */

if (!defined('ABSPATH'))
    exit;


$categories = get_the_category();

// Check if there are categories
if (!empty($categories)):
    // Get the first category (you can modify this logic if needed)
    $category = $categories[0];
    $term_id = $category->term_id;

    $sponsor_link_text = get_term_meta($term_id, 'sponsor_link_text', true);
    $sponsor_link = get_term_meta($term_id, 'sponsor_link', true);


endif;

// Retrieve the 'channel_image' meta or use fallback
$channel_image = !empty(get_term_meta($term_id, 'channel_image', true))
    ? get_term_meta($term_id, 'channel_image', true)
    : get_template_directory_uri() . '/assets/images/demo-three/common/channel-banner.webp';

?>


<div class="og_hero-image" style="background-image: url(<?php echo esc_url($channel_image); ?>);">

    <div class="container max-w-xl position-absolute start-50 translate-middle z-2 text-center" style="top:35%;">
        <h1 class="h3 lg:h1 text-white "><?php the_title(); ?></h1>
        <div class="my-2">

            <span class="single-post cateogry-link text-white fs-6 md:fs-5">
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                    class="text-none text-white"><?php echo esc_html($category->name); ?></a>
            </span>
            <?php if (!empty($sponsor_link_text) && !empty($sponsor_link)): ?>
                <span class="single-post single-post-sep sep text-white fs-6 md:fs-5 opacity-60">
                    •
                </span>

                <span class="single-post sponsor-link text-white fs-6 md:fs-5">
                    Powered by
                    <a class="text-none" href="<?php echo esc_url($sponsor_link); ?>" target="_blank"
                        rel="noopener noreferrer"><?php echo esc_html($sponsor_link_text); ?></a>
                </span>

            <?php endif; ?>
            <span class="single-post single-post-sep sep text-white fs-6 md:fs-5 opacity-60">
                •
            </span>
            <span class="single-post single-post-date text-white fs-6 md:fs-5">
                <i class="unicon-calendar"></i>
                <?php echo get_the_date(); ?>
            </span>
        </div>

        <div class="panel vstack items-center xl:mx-auto gap-2 md:gap-3">

            <ul class="post-share-icons nav-x gap-1 text-white">
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank">
                        <i class="unicon-logo-facebook icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                        target="_blank">
                        <i class="unicon-logo-x-filled icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>"
                        target="_blank">
                        <i class="unicon-logo-linkedin icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="mailto:?subject=<?php the_title(); ?>&body=<?php the_permalink(); ?>">
                        <i class="unicon-email icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="javascript:void(0);" onclick="sharePage()"><i class="unicon-link icon-1"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<article id="post-<?php the_ID(); ?>" <?php post_class('post type-post single-post py-4 lg:py-6 xl:py-6'); ?>
    data-post-id="<?php the_ID(); ?>">
    <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3 rounded-1"
        style="margin-top: -230px;padding: 16px;">

        <div class="post-header">

            <?php if (has_post_thumbnail()): ?>
                <figure
                    class="featured-image m-0 ratio ratio-2x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">

                    <?php
                    $video_url = get_post_meta(get_the_ID(), 'ogtv_details', true)['video_url'] ?? '';

                    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/|.*[?&]v=))([a-zA-Z0-9_-]{11})/', $video_url, $matches)) {

                        $video_id = $matches[1];

                        echo '<div class="video-container" style="aspect-ratio: 16 / 9;">';

                        echo '<iframe class="youtube-player" src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" style="height: 100%; width: 100%;" frameborder="0" scrolling="no" loading="lazy" allowfullscreen></iframe>';
                        
                        echo '</div>';

                    } else {

                        the_post_thumbnail('full', ['class' => 'media-cover image uc-transition-opaque', 'data-uc-img' => 'loading: eager']);
                    }
                    ?>
                </figure>
            <?php endif; ?>

        </div>
    </div>

    <div class="panel">
        <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3">
            <div class="post-content panel fs-6 md:fs-5" data-uc-lightbox="animation: scale">
                <?php the_content(); ?>
            </div>
        </div>

        <?php
        $current_post_id = get_the_ID();
        $playlists = get_the_terms($current_post_id, 'playlists');

        if ($playlists && !is_wp_error($playlists)) {

            $playlist_ids = array();
            foreach ($playlists as $playlist) {
                $playlist_ids[] = $playlist->term_id;
            }

            $related_query = new Country_Filtered_Query(array(
                'post_type' => 'ogtv',

                'post__not_in' => array($current_post_id), // Exclude current post
                'tax_query' => array(
                    array(
                        'taxonomy' => 'playlists',
                        'field' => 'term_id',
                        'terms' => $playlist_ids,
                    ),
                ),
            ));

            if ($related_query->post_count > 0) {

                ?>
                <div class="section panel overflow-hidden swiper-parent uc-dark mt-3">
                    <div class="section-outer panel py-5 lg:py-8 bg-gray-25 dark:bg-gray-800 dark:text-white">
                        <div class="container max-w-lg">

                            <div class="section-inner panel vstack gap-4">

                                <?php

                                echo '<div class="section-header panel">';
                                echo '<h3 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">';
                                echo '<a href="' . esc_url(get_term_link($playlist)) . '" class="text-none hover:text-primary">';
                                echo 'More from ' . esc_html($playlist->name) . '';
                                echo '</a>';
                                echo '<i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>';
                                echo '</h3>';
                                echo '</div>';

                                echo '<div class="row g-4 xl:g-8">';
                                echo '<div class="col">';
                                echo '<div class="panel">';
                                echo '<div class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">';
                                ?>

                                <?php
                                while ($related_query->have_posts()):
                                    $related_query->the_post();
                                    // Include your template part for displaying videos
                                    get_template_part('template-parts/ogtv/archive');

                                endwhile;

                                echo '  </div>';
                                echo '  </div>';
                                echo '  </div>';
                                echo '  </div>';

                                ?>
                            </div>

                        </div>
                    </div>
                </div>
                <?php
                // Restore original post data
                wp_reset_postdata();
            }
        }
        ?>

        <div class="container max-w-lg">
            <div class="post-footer panel vstack sm:hstack gap-3 justify-between py-3 mt-4">
                <ul class="nav-x gap-narrow text-primary">
                    <li><span class="text-black dark:text-white me-narrow">Channel:</span></li>
                    <?php

                    if ($categories) {
                        foreach ($categories as $index => $category) {
                            echo '<li>';
                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="uc-link gap-0 dark:text-white">';
                            echo esc_html($category->name);
                            echo '<span class="text-black dark:text-white">' . ($index < count($categories) - 1 ? ',' : '') . '</span>';
                            echo '</a>';
                            echo '</li>';
                        }
                    }
                    ?>
                </ul>



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

            <!-- Author Box -->
            <?php // opengovasia_author_box(); ?>
            <!-- End Author Box -->


            <!-- Related Posts -->
            <?php display_related_posts(get_the_ID(), 'post', 'Latest News from %s:'); ?>
            <?php display_related_posts(get_the_ID(), 'events', 'Latest Events on %s:'); ?>
            <?php display_related_posts(get_the_ID(), 'ogtv', 'Latest Videos from %s:'); ?>
            <!-- End Related Posts -->


            <!-- Comments -->
            <?php

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()):
                comments_template();
            endif;
            ?>
            <!-- End Comments -->


        </div>
    </div>
</article>