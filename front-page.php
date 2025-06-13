<?php
/**
 * Template Name: Front Page
 */

get_header(); ?>

<main id="primary">

    <!-- Section start -->

    <!-- Ensure this only show on 1st Page -->

    <?php if (!is_paged()): ?>

        <!-- Hero Section -->

        <?php
        // Query for fetching posts
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => 5, // Adjust the number of posts as needed
        );
        $query = new Country_Filtered_Query($query_args);
        ?>

        <div class="section panel overflow-hidden py-2 bg-gray-25 dark:bg-gray-900 uc-dark">
            <div class="section-outer panel">
                <div class="container container-expand">
                    <div class="section-inner panel vstack gap-4">
                        <div class="section-content">
                            <div class="block-layout grid-overlay-layout">
                                <div class="block-content">
                                    <div class="row child-cols-12 md:child-cols-6 g-1 col-match">
                                        <?php if ($query->have_posts()):
                                            $query->the_post(); ?>
                                            <div>
                                                <div>
                                                    <article
                                                        class="post type-post panel uc-transition-toggle vstack gap-2 lg:gap-3 h-100 rounded overflow-hidden">
                                                        <div class="post-media panel overflow-hidden h-100">
                                                            <div
                                                                class="featured-image bg-gray-25 dark:bg-gray-800 h-100 d-none md:d-block">

                                                                <?php if (has_post_thumbnail()) {
                                                                    the_post_thumbnail('full', array(
                                                                        'class' => 'media-cover image uc-transition-scale-up uc-transition-opaque',
                                                                        'loading' => 'eager',
                                                                        'alt' => esc_attr(get_the_title())
                                                                    ));
                                                                } ?>

                                                            </div>
                                                            <div
                                                                class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9 d-block md:d-none">
                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                    src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
                                                                    data-src="<?php echo get_the_post_thumbnail_url(); ?>"
                                                                    alt="<?php the_title(); ?>" data-uc-img="loading: lazy">
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="position-cover bg-gradient-to-t from-black to-transparent opacity-90">
                                                        </div>
                                                        <div
                                                            class="post-header panel vstack justify-end items-start gap-1 sm:gap-2 p-2 sm:p-4 position-cover text-white">
                                                            <div
                                                                class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                                                                <div>
                                                                    <div class="post-category hstack gap-narrow fw-semibold">
                                                                        <a class="fw-medium text-none text-white"
                                                                            href="<?php echo get_category_link(get_the_category()[0]->term_id); ?>">
                                                                            <?php echo get_the_category()[0]->name; ?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="sep d-none md:d-block">|</div>
                                                                <div class="d-none md:d-block">
                                                                    <div class="post-date hstack gap-narrow">
                                                                        <span><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <h3
                                                                class="post-title h5 sm:h4 xl:h3 m-0 max-w-600px text-white text-truncate-2">
                                                                <a class="text-none text-white"
                                                                    href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                            </h3>
                                                            <div
                                                                class="post-meta panel hstack justify-between fs-7 fw-medium text-white text-opacity-60">
                                                                <div class="meta">
                                                                    <div class="hstack gap-2">
                                                                        <div>
                                                                            <div class="post-author hstack gap-1">
                                                                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
                                                                                    data-uc-tooltip="<?php the_author(); ?>">
                                                                                    <?php echo get_avatar(get_the_author_meta('ID'), 24, '', '', array('class' => 'w-24px h-24px rounded-circle')); ?>
                                                                                </a>

                                                                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
                                                                                    class="text-black dark:text-white text-none fw-bold"><?php the_author(); ?></a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="actions">
                                                                    <div class="hstack gap-1"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="panel">
                                                <div class="row child-cols-6 g-1">
                                                    <?php while ($query->have_posts()):
                                                        $query->the_post(); ?>
                                                        <div>
                                                            <article
                                                                class="post type-post panel uc-transition-toggle vstack gap-2 lg:gap-3 rounded overflow-hidden">
                                                                <div class="post-media panel overflow-hidden">
                                                                    <div
                                                                        class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1 sm:ratio-4x3">
                                                                        <img class="media-cover image image uc-transition-scale-up uc-transition-opaque"
                                                                            src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
                                                                            data-src="<?php echo get_the_post_thumbnail_url(); ?>"
                                                                            alt="<?php the_title(); ?>"
                                                                            data-uc-img="loading: eager">
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="position-cover bg-gradient-to-t from-black to-transparent opacity-90">
                                                                </div>
                                                                <div
                                                                    class="post-header panel vstack justify-start items-start flex-column-reverse gap-1 p-2 position-cover text-white">
                                                                    <h3
                                                                        class="post-title h6 sm:h5 lg:h6 xl:h5 m-0 max-w-600px text-white text-truncate-2">
                                                                        <a class="text-none text-white"
                                                                            href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                                    </h3>
                                                                    <div
                                                                        class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                                                                        <div>
                                                                            <div
                                                                                class="post-category hstack gap-narrow fw-semibold">
                                                                                <a class="fw-medium text-none text-white"
                                                                                    href="<?php echo get_category_link(get_the_category()[0]->term_id); ?>">
                                                                                    <?php echo get_the_category()[0]->name; ?>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="sep d-none md:d-block">|</div>
                                                                        <div class="d-none md:d-block">
                                                                            <div class="post-date hstack gap-narrow">
                                                                                <span><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </article>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>

        <!-- Hero Section End -->

        <!-- Category Section Start -->

        <?php
        $channels = get_terms(array(
            'taxonomy' => 'category', // Assuming "channel" is still using the category taxonomy
            'hide_empty' => false,
        ));

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

        if (!empty($channels) && !is_wp_error($channels)): ?>
            <div class="image-links-panel panel overflow-hidden pt-2 swiper-parent">
                <div class="container max-w-xl">
                    <div class="panel">
                        <div class="swiper overflow-unset"
                            data-uc-swiper="items: 3.25; gap: 8; center: true; freeMode: true; center-bounds: true; disable-class: d-none;"
                            data-uc-swiper-s="items: 6;" data-uc-swiper-l="items: 8; gap: 16;">
                            <div class="swiper-wrapper">
                                <?php foreach ($channels as $channel):
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
                                                    data-caption="<?php echo esc_attr($channel->name); ?>"></a>
                                            </figure>
                                            <div class="overlay position-cover z-0 bg-black bg-opacity-50"></div>
                                            <div
                                                class="position-absolute bottom-0 vstack justify-end gap-1 lg:gap-2 h-3/4 w-100 p-2 bg-gradient-to-t <?php echo esc_attr($random_class); ?> to-transparent">
                                                <span
                                                    class="fs-5 lg:fs-4 fw-bold text-white m-0"><?php echo esc_html($channel->name); ?></span>
                                                <a href="<?php echo esc_url($channel_link); ?>"
                                                    class="btn btn-2xs border-white border-opacity-25 fs-7 text-white rounded-1">Visit</a>
                                            </div>
                                            <a class="position-cover text-none z-1"
                                                href="<?php echo esc_url($channel_link); ?>"></a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cateogry Section End -->


        <!-- Section start -->

        <div class="section panel overflow-hidden">
            <div class="section-outer panel py-4">
                <div class="container max-w-xl">
                    <div class="section-inner">
                        <?php
                        $homepage_banner_desktop = get_homepage_banner('desktop');
                        $homepage_banner_mobile = get_homepage_banner('mobile');
                        $homepage_banner_desktop_link = get_homepage_banner_link('desktop');
                        $homepage_banner_mobile_link = get_homepage_banner_link('mobile');

                        if ($homepage_banner_desktop || $homepage_banner_desktop_link): ?>
                            <a class="text-none" href="<?php echo esc_url($homepage_banner_desktop_link); ?>">
                                <img class="d-none md:d-block" src="<?php echo esc_url($homepage_banner_desktop); ?>"
                                    alt="Homepage Banner">
                            </a>
                        <?php endif; ?>
                        <?php if ($homepage_banner_mobile || $homepage_banner_mobile_link): ?>
                            <a class="text-none d-block md:d-none" href="<?php echo esc_url($homepage_banner_mobile_link); ?>">
                                <img class="d-block md:d-none" src="<?php echo esc_url($homepage_banner_mobile); ?>"
                                    alt="Homepage Banner Mobile">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section end -->

        <!-- Section start -->

        <div class="section panel overflow-hidden">
            <div class="section-outer panel">
                <div class="container max-w-xl">


                    <div class="section-inner mt-4">
                        <div class="row g-4 lg:gx-6 xl:gy-8" data-uc-grid>
                            <div>
                                <div class="block-layout grid-layout vstack gap-3 lg:gap-4 panel overflow-hidden">

                                    <div class="block-content">
                                        <div
                                            class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match g-2 gy-3 md:gx-3 md:gy-4">
                                            <?php
                                            // WordPress Query Arguments
                                            $args = array(
                                                'post_type' => 'post',
                                                'posts_per_page' => 4, // Change this to the number of posts you want to display
                                                'orderby' => 'date',
                                                'order' => 'DESC',
                                                'offset' => 5, // Skip the first post
                                            );

                                            // Custom Query
                                            $query = new Country_Filtered_Query($args);

                                            // Loop Through Posts
                                            if ($query->have_posts()):
                                                while ($query->have_posts()):

                                                    $query->the_post();

                                                    get_template_part('template-parts/archive-classic');

                                                endwhile;
                                                wp_reset_postdata();

                                            endif;
                                            ?>

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Section end -->


        <!-- Section start -->

        <div class="section panel overflow-hidden swiper-parent uc-dark mt-4">
            <div class="section-outer panel py-5 lg:py-8 bg-gray-25 dark:bg-gray-800 dark:text-white">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-4">
                        <div class="section-header panel">
                            <h2 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">
                                <a class="text-none dark:text-white hover:text-primary duration-150" href="/ogtv/">OpenGov
                                    TV</a>
                                <i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>
                            </h2>
                        </div>
                        <div class="section-content">
                            <div class="swiper"
                                data-uc-swiper="items: 1; gap: 16; autoplay: 2500; dots: .dot-nav; next: .nav-next; prev: .nav-prev; disable-class: opacity-40;"
                                data-uc-swiper-s="items: 2;" data-uc-swiper-m="items: 3;" data-uc-swiper-m="gap: 24;"
                                data-uc-swiper-l="items: 4; gap: 32;">
                                <div class="swiper-wrapper">
                                    <?php

                                    $query = new Country_Filtered_Query([
                                        'post_type' => 'ogtv',
                                        'posts_per_page' => 12, // Change as needed
                                    ]);

                                    if ($query->have_posts()):
                                        while ($query->have_posts()):
                                            $query->the_post();

                                            ?>

                                            <div class="swiper-slide">

                                                <?php get_template_part('template-parts/ogtv/archive'); ?>

                                            </div>

                                            <?php

                                        endwhile;
                                        wp_reset_postdata();
                                    else:
                                        echo '<p>No videos found. Maybe try switching your country?</p>';

                                    endif;
                                    ?>


                                </div>
                                <div class="hstack gap-1 mt-4">
                                    <div
                                        class="swiper-nav nav-prev btn btn-alt-primary bg-transparent dark:text-white rounded-0 p-0 border w-32px lg:w-40px h-32px lg:h-40px shadow-sm">
                                        <i class="icon-1 unicon-chevron-left"></i>
                                    </div>
                                    <div
                                        class="swiper-nav nav-next btn btn-alt-primary bg-transparent dark:text-white rounded-0 p-0 border w-32px lg:w-40px h-32px lg:h-40px shadow-sm">
                                        <i class="icon-1 unicon-chevron-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section end -->

    <?php endif; ?>


    <!-- Section start -->

    <div id="latest-news" class="section panel overflow-hidden">
        <div class="section-outer panel mb-4">
            <div class="container max-w-xl">

                <?php if (!is_paged()): ?>

                    <div class="section-inner panel vstack gap-4">
                        <div class="section-header panel mt-4">

                            <h2 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">
                                <a class="text-none dark:text-white hover:text-primary duration-150"
                                    href="/upcoming-events/">Upcoming Events</a>
                                <i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>
                            </h2>
                        </div>

                        <div class="section-content">

                            <?php
                            // WordPress Query Arguments
                            $args = array(
                                'post_type' => 'events',
                                'posts_per_page' => 8,
                                'meta_key' => 'event_date',
                                'orderby' => 'meta_value',
                                'order' => 'ASC',
                                'meta_query' => array(
                                    array(
                                        'key' => 'event_date',
                                        'value' => current_time('Y-m-d'),
                                        'compare' => '>=',
                                        'type' => 'DATE'
                                    )
                                )
                            );


                            // Execute the query with country filtering
                            $events = new Country_Filtered_Query($args);
                            ?>

                            <?php if ($events->have_posts()): ?>

                                <div class="row g-4 xl:g-8">
                                    <div class="col">
                                        <div class="panel">
                                            <div
                                                class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">


                                                <?php while ($events->have_posts()):
                                                    $events->the_post(); ?>

                                                    <?php get_template_part('template-parts/events/archive'); ?>

                                                <?php endwhile; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php wp_reset_postdata(); ?>

                            <?php else: ?>

                                <div>No Upcoming Events found. Maybe try switching your country?</div>

                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="border-top mt-4"></div>

                <?php endif; ?>


                <div class="section-inner panel vstack gap-4 mt-4">


                    <div class="block-header panel">
                        <h2 class="h4 -ls-1 xl:-ls-2 m-0 text-inherit hstack gap-1">
                            Latest News
                        </h2>

                    </div>

                    <?php if (have_posts()): ?>

                        <div class="row g-4 xl:g-8">
                            <div class="col">
                                <div class="panel">
                                    <div
                                        class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">

                                        <?php

                                        while (have_posts()):

                                            the_post();

                                            get_template_part('template-parts/archive-classic');

                                        endwhile;

                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php opengovasia_pagination(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Section end -->

</main>

<?php get_footer(); ?>