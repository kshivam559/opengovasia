<?php

/**
 * Template for displaying single company posts.
 * 
 * @package OpenGovAsia
 * @since 1.0
 */

if (!defined('ABSPATH'))
    exit;


// Use the existing helper function to get all relationships
$relationships = get_company_relationships(get_the_ID());

// Now you have access to:
// $relationships['sponsored_channels'] - Array of WP_Term objects for sponsored channels
// $relationships['tagged_events'] - Array of WP_Post objects for events where company is partnered
// $relationships['tagged_awards'] - Array of WP_Post objects for awards where company is tagged


$logo = get_the_post_thumbnail_url(get_the_ID(), 'full');
$title = get_the_title();
$excerpt = get_the_excerpt();
$socials = get_custom_meta(get_the_ID(), 'socials', true);


$categories = get_the_category();
$category = null;
$term_id = 0;

// Check if there are categories
if (!empty($categories)):
    // Get the first category (you can modify this logic if needed)
    $category = $categories[0];
    $term_id = $category->term_id;

    if (!empty(get_term_meta($term_id, 'sponsor_link_text', true)) && !empty(get_term_meta($term_id, 'sponsor_link', true))):
        // Retrieve the sponsor link text and link
        $sponsor_link_text = get_term_meta($term_id, 'sponsor_link_text', true);
        $sponsor_link = get_term_meta($term_id, 'sponsor_link', true);
    endif;
endif;

// Retrieve the 'channel_image' meta or use fallback
$channel_image = (!empty($categories) && !empty(get_term_meta($term_id, 'channel_image', true)))
    ? get_term_meta($term_id, 'channel_image', true)
    : get_template_directory_uri() . '/assets/images/demo-three/common/channel-banner.webp';

?>


<div class="og_hero-image" style="background-image: url(<?php echo esc_url($channel_image); ?>);">

    <div class="container max-w-xl position-absolute start-50 translate-middle z-2 text-center" style="top: 35%;">
        <h1 class="h3 lg:h1 text-white "><?php the_title(); ?></h1>

        <?php if (!empty($relationships['sponsored_channels'])): ?>
            <h3 class="single-post sponsor-link text-white h5 fw-medium">
                Powers <span class="opacity-60">•</span>
                <?php
                $links = [];
                foreach ($relationships['sponsored_channels'] as $channel) {
                    if (!is_object($channel) || !isset($channel->term_id) || !isset($channel->name)) {
                        continue; // Skip if not a valid channel object
                    }
                    $links[] = '<a class="text-none" href="' . esc_url(get_category_link($channel->term_id)) . '">' . esc_html($channel->name) . '</a>';
                }
                echo implode(' <span class="opacity-60">•</span> ', $links);
                ?>
            </h3>
        <?php endif; ?>

    </div>
</div>

<article id="post-<?php the_ID(); ?>" <?php post_class('post type-post single-post py-4 lg:py-6 xl:py-6'); ?>
    data-post-id="<?php the_ID(); ?>">
    <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3 rounded-1"
        style="margin-top: -230px;padding: 16px;">

        <div class="post-header">

            <!-- Partners Section -->

            <div class="partners-section panel border pt-2 px-2 rounded-1 shadow-xs">
                <div class="partners-list">
                    <div
                        class="partner px-2 rounded-1 mb-2 dark:bg-gray-900 dark:text-white vstack gap-2 sm:hstack align-items-center">
                        <?php if (!empty($logo)): ?>
                            <div>
                                <img class="max-w-100px mr-2" src="<?php echo esc_url($logo); ?>"
                                    alt="<?php echo esc_attr($title); ?>">
                            </div>
                        <?php endif; ?>
                        <div>
                            <?php if (!empty($title)): ?>
                                <h3 class="h5 my-1 dark:text-white">
                                    <?php echo esc_html($title); ?>
                                </h3>
                            <?php endif; ?>


                            <p class="my-1"><?php the_content(); ?></p>


                            <?php if (!empty($socials) && is_array($socials)): ?>
                                <div class="social-links py-2">
                                    <?php foreach ($socials as $social):
                                        if (!empty($social['platform']) && !empty($social['url'])):
                                            $platform = strtolower($social['platform']);
                                            switch ($platform) {
                                                case 'facebook':
                                                    $icon = '<i class="icon icon-1 unicon-logo-facebook"></i>';
                                                    break;
                                                case 'twitter':
                                                    $icon = '<i class="icon icon-1 unicon-logo-x"></i>';
                                                    break;
                                                case 'linkedin':
                                                    $icon = '<i class="icon icon-1 unicon-logo-linkedin"></i>';
                                                    break;
                                                case 'instagram':
                                                    $icon = '<i class="icon icon-1 unicon-logo-instagram"></i>';
                                                    break;
                                                case 'youtube':
                                                    $icon = '<i class="icon icon-1 unicon-logo-youtube"></i>';
                                                    break;
                                                case 'website':
                                                    $icon = '<i class="icon icon-1 unicon-earth-americas"></i>';
                                                    break;
                                                default:
                                                    $icon = '<i class="icon icon-1 unicon-earth-americas"></i>';
                                            }
                                            ?>
                                            <a href="<?php echo esc_url($social['url']); ?>"
                                                class="text-none hover:text-primary mx-1" target="_blank" rel="noopener">
                                                <?php echo $icon; ?>
                                            </a>
                                        <?php endif;
                                    endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End Partners Section -->

            <!-- Display Sponsored Channels -->

            <?php

            $gradient_classes = [
                'from-indigo-600',
                'from-pink-600',
                'from-purple-600',
                'from-teal-600',
                'from-blue-600',
                'from-green-600',
                'from-red-600',
                'from-lime-600',
                'from-orange-600',
            ];

            ?>

            <?php if (!empty($relationships['sponsored_channels'])): ?>
                <div class="section-inner panel vstack gap-4 pb-4 my-4 border-bottom">
                    <div class="block-header panel">
                        <h2 class="h4 -ls-1 xl:-ls-2 m-0 text-inherit hstack gap-1">
                            Sponsored Channels
                        </h2>
                    </div>

                    <div class="image-links-panel panel overflow-hidden pt-2 swiper-parent">
                        <div class="container max-w-xl">
                            <div class="panel">
                                <div class="swiper overflow-unset"
                                    data-uc-swiper="items: 2.5; gap: 12; freeMode: true; center-bounds: true; disable-class: d-none;"
                                    data-uc-swiper-s="items: 5;" data-uc-swiper-l="items: 6; gap: 12;">
                                    <div class="swiper-wrapper">
                                        <?php foreach ($relationships['sponsored_channels'] as $channel): ?>
                                            <?php

                                            if (!is_object($channel) || !isset($channel->term_id) || !isset($channel->name)):
                                                continue; // Skip if not a valid channel object
                                            endif;

                                            $channel_image = get_term_meta($channel->term_id, 'channel_image', true);

                                            $channel_image = $channel_image ?: get_template_directory_uri() . '/assets/images/demo-three/posts/img-01.jpg'; // Fallback image
                                    
                                            $channel_link = get_term_link($channel);

                                            $random_class = $gradient_classes[array_rand($gradient_classes)]; // Pick a random gradient class
                                    
                                            ?>

                                            <div class="swiper-slide">
                                                <div
                                                    class="panel uc-transition-toggle vstack text-center overflow-hidden rounded border border-white border-opacity-10">
                                                    <figure
                                                        class="featured-image m-0 ratio ratio-3x4 rounded-0 overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                            src="<?php echo esc_url($channel_image); ?>"
                                                            alt="<?php echo esc_attr($channel->name); ?>" loading="lazy">
                                                        <a href="<?php echo esc_url($channel_link); ?>" class="position-cover"
                                                            data-caption="<?php echo esc_attr($channel->name); ?>">
                                                        </a>
                                                    </figure>
                                                    <div class="overlay position-cover z-0 bg-black bg-opacity-50">
                                                    </div>
                                                    <div
                                                        class="position-absolute bottom-0 vstack justify-end gap-1 lg:gap-2 h-3/4 w-100 p-2 bg-gradient-to-t <?php echo esc_attr($random_class); ?> to-transparent">
                                                        <span
                                                            class="fs-5 lg:fs-4 fw-bold text-white m-0"><?php echo esc_html($channel->name); ?>
                                                        </span>
                                                        <a href="<?php echo esc_url($channel_link); ?>"
                                                            class="btn btn-2xs border-white border-opacity-25 fs-7 text-white rounded-1">
                                                            Visit
                                                        </a>
                                                    </div>
                                                    <a class="position-cover text-none z-1"
                                                        href="<?php echo esc_url($channel_link); ?>">
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>


            <!-- Display Partner Events -->
            <?php if (!empty($relationships['tagged_events'])): ?>
                <div class="section-inner panel vstack gap-4 pb-4 my-4 border-bottom">
                    <div class="block-header panel">
                        <h2 class="h4 -ls-1 xl:-ls-2 m-0 text-inherit hstack gap-1">
                            Events Partnered with <?php echo esc_html($title); ?>
                        </h2>

                    </div>
                    <div class="row g-4 xl:g-8">
                        <div class="col">
                            <div class="panel">
                                <div
                                    class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">
                                    <?php foreach ($relationships['tagged_events'] as $event): ?>
                                        <?php

                                        if (!is_object($event) || !isset($event->ID) || !isset($event->post_title)):
                                            continue;
                                        endif;

                                        ?>

                                        <?php get_template_part('template-parts/company/events', null, ['event' => $event]); ?>

                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Display Awards -->
            <?php if (!empty($relationships['tagged_awards'])): ?>
                <div class="section-inner panel vstack gap-4 pb-4 border-bottom">
                    <div class="block-header panel">
                        <h2 class="h4 -ls-1 xl:-ls-2 m-0 text-inherit hstack gap-1">
                            <?php echo esc_html($title); ?> Awards
                        </h2>

                    </div>
                    <div class="row g-4 xl:g-8">
                        <div class="col">
                            <div class="panel">
                                <div
                                    class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">
                                    <?php foreach ($relationships['tagged_awards'] as $award): ?>
                                        <?php

                                        if (!is_object($award) || !isset($award->ID) || !isset($award->post_title)):
                                            continue;
                                        endif;

                                        ?>

                                        <?php get_template_part('template-parts/company/awards', null, ['award' => $award]); ?>

                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


        </div>
    </div>

    <div class="panel">
        <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3">



            <!-- Related Posts -->
            <?php display_related_posts(get_the_ID(), 'post', 'Latest News in %s:'); ?>
            <?php display_related_posts(get_the_ID(), 'events', 'Latest Events on %s:'); ?>
            <?php display_related_posts(get_the_ID(), 'ogtv', 'Latest Videos in %s:'); ?>
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