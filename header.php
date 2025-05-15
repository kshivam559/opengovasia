<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package OpenGov_Asia
 */


?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="ltr">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(array("uni-body panel bg-white text-gray-900 dark:bg-black dark:text-gray-200 overflow-x-hidden")); ?>>
    <?php wp_body_open(); ?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text"
            href="#primary"><?php esc_html_e('Skip to content', 'opengovasia'); ?></a>

        <!-- Header start -->
        <header class="uc-header header-one uc-navbar-sticky-wrap z-999"
            data-uc-sticky="sel-target: .uc-navbar-container; cls-active: uc-navbar-sticky; cls-inactive: uc-navbar-transparent; end: !*;">
            <nav class="uc-navbar-container fs-6 z-1">
                <div
                    class="uc-top-navbar panel z-3 min-h-32px lg:min-h-48px overflow-hidden bg-gray-800 text-white uc-dark d-none md:d-block">
                    <div class="position-cover blend-color"
                        data-src="/wp-content/themes/opengovasia/assets/images/demo-three/topbar-abstract.jpg"
                        data-uc-img></div>
                    <div class="container max-w-xl">
                        <div class="hstack panel z-1">
                            <div class="uc-navbar-left gap-2 lg:gap-3">
                                <div class="trending-ticker panel swiper-parent max-w-600px">
                                    <div class="swiper hstack gap-1 min-h-40px"
                                        data-uc-swiper="items: 1; autoplay: 3000; parallax: true; pause-mouse: false; reverse: false; prev: .swiper-prev; next: .swiper-next; effect: fade; fade: true;">
                                        <div class="hstack gap-narrow">
                                            <i class="icon-1 unicon-fire text-warning"></i>
                                            <span class="fs-6 fw-bold dark:text-white">Trending:</span>
                                        </div>
                                        <div class="swiper-wrapper">
                                            <?php
                                            $query_args = array(
                                                'post_type' => 'post',
                                                'posts_per_page' => 10,
                                                'orderby' => 'date',
                                                'order' => 'DESC',
                                            );

                                            // Use our new Country_Filtered_Query instead of WP_Query
                                            // This automatically handles country filtering and fallback
                                            $recent_posts = new Country_Filtered_Query($query_args);

                                            // Or alternatively, use the helper function:
                                            // $recent_posts = get_country_filtered_posts($query_args);
                                            
                                            if ($recent_posts->have_posts()):
                                                while ($recent_posts->have_posts()):
                                                    $recent_posts->the_post(); ?>
                                                    <div class="swiper-slide">
                                                        <article class="post type-post">
                                                            <h6 class="post-title fs-6 ft-primary fw-medium m-0 opacity-75 dark:text-white"
                                                                data-swiper-parallax-y="-24">
                                                                <a class="text-none"
                                                                    href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                            </h6>
                                                        </article>
                                                    </div>
                                                <?php endwhile;
                                                wp_reset_postdata();
                                            else: ?>
                                                <div class="swiper-slide">
                                                    <p class="text-white opacity-75">No posts found.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="uc-navbar-right gap-2 lg:gap-3">
                                <div class="uc-navbar-item">
                                    <ul class="uc-social-header nav-x gap-1 d-none lg:d-flex dark:text-white">
                                        <li>
                                            <a href="https://www.facebook.com/opengovasia/"
                                                class="w-32px h-32px cstack border rounded-circle hover:bg-primary transition-colors duration-200"
                                                target="_blank"><i class="icon icon-1 unicon-logo-facebook"></i></a>
                                        </li>
                                        <li>
                                            <a href="https://x.com/opengov_asia/"
                                                class="w-32px h-32px cstack border rounded-circle hover:bg-primary transition-colors duration-200"
                                                target="_blank"><i class="icon icon-1 unicon-logo-x"></i></a>
                                        </li>
                                        <li>
                                            <a href="https://www.linkedin.com/company/opengovasia/"
                                                class="w-32px h-32px cstack border rounded-circle hover:bg-primary transition-colors duration-200"
                                                target="_blank"><i class="icon icon-1 unicon-logo-linkedin"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="uc-navbar-item">
                                    <a class="uc-account-trigger btn btn-sm border-0 p-0 duration-0 dark:text-white"
                                        href="#opengov-newsletter" data-uc-toggle>
                                        <i class="icon icon-2 fw-medium unicon-email"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="uc-center-navbar panel z-2 bg-primary dark:bg-gray-900"
                    data-uc-navbar=" animation: uc-animation-slide-top-small; duration: 150;">
                    <div class="container max-w-xl">
                        <div class="uc-navbar min-h-72px lg:min-h-80px text-white dark:text-white">
                            <div class="uc-navbar-left">
                                <div class="d-block lg:d-none">
                                    <a class="uc-menu-trigger text-white" href="#uc-menu-panel" data-uc-toggle></a>
                                </div>
                                <div class="uc-logo d-none md:d-block text-white dark:text-white">
                                    <a href="<?php echo esc_url(home_url('/')); ?>">
                                        <?php if (has_custom_logo()): ?>

                                            <?php
                                            $custom_logo_id = get_theme_mod('custom_logo');
                                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                            ?>

                                            <img src="<?php echo esc_url($logo[0]); ?>"
                                                class="w-80px text-white dark:text-white" alt="<?php bloginfo('name'); ?>"
                                                data-uc-svg>

                                        <?php else: ?>
                                            <img class="w-80px text-white dark:text-white"
                                                src="<?php echo get_template_directory_uri(); ?>/assets/images/demo-three/common/logo.svg"
                                                alt="<?php bloginfo('name'); ?>" data-uc-svg>
                                        <?php endif; ?>
                                    </a>
                                </div>

                                <ul class="uc-navbar-nav gap-3 ft-tertiary fs-5 fw-medium ms-4 d-none lg:d-flex"
                                    style="--uc-nav-height: 80px">
                                    <li class="fw-bold">
                                        <a href="#">Latest <span data-uc-navbar-parent-icon></span></a>
                                        <div class="uc-navbar-dropdown ft-primary text-unset border border-gray-900 border-opacity-15 p-3 pb-4 hide-scrollbar shadow-xs"
                                            data-uc-drop=" offset: 0; boundary: !.uc-navbar; stretch: x; animation: uc-animation-slide-top-small; duration: 150;">
                                            <div class="row child-cols col-match g-3">
                                                <div class="col-2">
                                                    <div
                                                        class="uc-navbar-switcher-nav p-1 rounded bg-gray-25 dark:bg-gray-800">
                                                        <ul class="uc-tab-left fs-5 text-end"
                                                            data-uc-tab="connect: #uc-navbar-switcher-tending; animation: uc-animation-slide-right-small, uc-animation-slide-left-small">
                                                            <?php
                                                            // Get top 5 categories dynamically
                                                            $categories = get_categories(array(
                                                                'orderby' => 'count',
                                                                'order' => 'DESC',
                                                                'number' => 5, // Get top 5 categories
                                                            ));

                                                            // Loop through categories and create tabs
                                                            foreach ($categories as $index => $category) {
                                                                echo '<li><a href="#" data-category="' . $category->slug . '">' . $category->name . '</a></li>';
                                                            }
                                                            ?>

                                                        </ul>
                                                    </div>
                                                    <div class="mt-2">
                                                        <a href="/channels/"
                                                            class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-full">
                                                            <span>View all</span>
                                                            <i class="icon icon-1 unicon-chevron-right"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-10">
                                                    <div id="uc-navbar-switcher-tending"
                                                        class="uc-navbar-switcher uc-switcher">
                                                        <?php
                                                        // Loop through the same categories to create content panels
                                                        foreach ($categories as $index => $category) {
                                                            ?>
                                                            <div>
                                                                <div class="row child-cols col-match g-2">
                                                                    <?php
                                                                    // Fetch 4 posts from this category
                                                                    fetch_header_channel_posts('post', 4, $category->slug);
                                                                    ?>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <a href="/channel/<?php echo $category->slug; ?>/"
                                                                        class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-full">
                                                                        <span>See more in
                                                                            <?php echo $category->name; ?></span>
                                                                        <i class="icon icon-1 unicon-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="fw-bold">
                                        <a href="#">Events <span data-uc-navbar-parent-icon></span></a>
                                        <div class="uc-navbar-dropdown ft-primary text-unset border border-gray-900 border-opacity-15 p-3 pb-4 hide-scrollbar shadow-xs"
                                            data-uc-drop=" offset: 0; boundary: !.uc-navbar; stretch: x; animation: uc-animation-slide-top-small; duration: 150;">
                                            <div class="row child-cols col-match g-3">
                                                <div
                                                    class="section-header panel vstack items-center justify-center text-center gap-1">
                                                    <h3 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">
                                                        Upcoming Events
                                                    </h3>
                                                </div>

                                                <div class="col-10">
                                                    <div class="row child-cols col-match g-3">

                                                        <?php

                                                        $args = array(
                                                            'post_type' => 'events',
                                                            'posts_per_page' => 4,
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
                                                        $events_query = new Country_Filtered_Query($args);

                                                        if ($events_query->have_posts()):
                                                            while ($events_query->have_posts()):
                                                                $events_query->the_post();
                                                                $event_date = get_post_meta(get_the_ID(), 'events_data[event_date]', true);
                                                                $event_category = get_the_category(); // Get category if needed
                                                                ?>

                                                                <div class="col-3">
                                                                    <article class="post type-post panel vstack gap-1">
                                                                        <div class="post-media panel overflow-hidden">
                                                                            <div
                                                                                class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                                                                                <img class="media-cover image"
                                                                                    src="/wp-content/themes/opengovasia/assets/images/common/img-fallback.png"
                                                                                    data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>"
                                                                                    alt="<?php the_title(); ?>"
                                                                                    data-uc-img="loading: lazy">
                                                                            </div>

                                                                            <div
                                                                                class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                                            </div>

                                                                            <span
                                                                                class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                                                                                <i class="icon-narrow unicon-calendar"></i>
                                                                            </span>
                                                                            <a href="<?php the_permalink(); ?>"
                                                                                class="position-cover"></a>
                                                                        </div>
                                                                        <div class="post-header panel vstack gap-narrow">
                                                                            <div
                                                                                class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                                                                                <div>
                                                                                    <div
                                                                                        class="post-category hstack gap-narrow fw-semibold">
                                                                                        <?php if (!empty($event_category)): ?>
                                                                                            <a class="fw-medium text-none text-primary dark:text-primary-400"
                                                                                                href="<?php echo get_category_link($event_category[0]->term_id); ?>">
                                                                                                <?php echo esc_html($event_category[0]->name); ?>
                                                                                            </a>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="sep d-none md:d-block">|</div>
                                                                                <div class="d-none md:d-block">
                                                                                    <div class="post-date hstack gap-narrow">
                                                                                        <span>
                                                                                            <?php

                                                                                            $events_data = get_post_meta(get_the_ID(), 'events_data', true);

                                                                                            $event_date = isset($events_data['event_date']) ? esc_html($events_data['event_date']) : '';

                                                                                            echo esc_html(date('F j, Y', strtotime($event_date))); ?>

                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <h3 class="post-title h6 m-0 text-truncate-2">
                                                                                <a class="text-none hover:text-primary duration-150"
                                                                                    href="<?php the_permalink(); ?>">
                                                                                    <?php the_title(); ?>
                                                                                </a>
                                                                            </h3>
                                                                        </div>
                                                                    </article>
                                                                </div>

                                                            <?php endwhile;
                                                            wp_reset_postdata();
                                                        else:
                                                            echo '<p class="justify-center">No Upcoming Events found.</p>';
                                                        endif;
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="col-2">
                                                    <a href="/upcoming-events/"
                                                        class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-full">
                                                        <span>View all</span>
                                                        <i class="icon icon-1 unicon-chevron-right"></i>
                                                    </a>
                                                </div>

                                            </div>
                                            <a href="/events/"
                                                class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-full mt-2">
                                                <span>See all Past Events</span>
                                                <i class="icon icon-1 unicon-chevron-right"></i>
                                            </a>
                                        </div>
                                    </li>

                                    <li class="fw-bold"><a href="/awards/">Awards</a></li>
                                    <li class="fw-bold"><a href="/ogtv/">OGTV</a></li>
                                    <?php
                                    wp_nav_menu(array(
                                        'theme_location' => 'primary-menu',
                                        'container' => false,
                                        'items_wrap' => '%3$s', // to exclude <ul>
                                        'walker' => new Primary_Menu_Nav_Walker(),
                                        'fallback_cb' => false,
                                    ));
                                    ?>

                                </ul>
                            </div>
                            <div class="uc-navbar-center">
                                <div class="uc-logo d-block md:d-none text-white dark:text-white w-80px">
                                    <a href="<?php echo get_home_url(); ?>">
                                        <?php if (has_custom_logo()): ?>

                                            <?php
                                            $custom_logo_id = get_theme_mod('custom_logo');
                                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                            ?>

                                            <img src="<?php echo esc_url($logo[0]); ?>"
                                                class="w-80px h-72px text-white dark:text-white"
                                                alt="<?php bloginfo('name'); ?>" data-uc-svg>

                                        <?php else: ?>
                                            <img class="w-80px h-72px text-white dark:text-white"
                                                src="<?php echo get_template_directory_uri(); ?>/assets/images/demo-three/common/logo.svg"
                                                alt="<?php bloginfo('name'); ?>" data-uc-svg>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="uc-navbar-right">

                                <?php
                                $countries = get_terms(['taxonomy' => 'country', 'hide_empty' => true]);
                                $selected_country = isset($_GET['c']) ? sanitize_text_field($_GET['c']) : '';
                                $selected_country_name = 'Select Country';
                                $selected_flag = '<i class="icon icon-1 unicon-earth-filled"></i>';

                                if ($selected_country) {
                                    $selected_term = get_term_by('slug', $selected_country, 'country');
                                    if ($selected_term && !is_wp_error($selected_term)) {
                                        $selected_country_name = esc_html($selected_term->name);
                                        $flag_url = get_term_meta($selected_term->term_id, 'country_flag', true);
                                        if (!empty($flag_url)) {
                                            $selected_flag = '<img src="' . esc_url($flag_url) . '" alt="' . esc_attr($selected_country_name) . ' Flag" style="width: 26px; height: auto;">';
                                        }
                                    }
                                }
                                ?>

                                <ul class="uc-navbar-nav gap-3 fs-5 fw-medium ms-4 d-none md:d-flex"
                                    style="--uc-nav-height: 80px">
                                    <li class="fw-medium oga-country-switcher">
                                        <a href="#" class="oga-country-toggle hstack gap-1" title="Select Country">
                                            <?php echo $selected_flag; ?>
                                            <span
                                                class="oga-current-country"><?php echo $selected_country_name; ?></span>
                                            <span data-uc-navbar-parent-icon></span>
                                        </a>
                                        <div class="uc-navbar-dropdown p-3 border border-gray-900 border-opacity-15 bg-white dark:bg-gray-800 shadow-xs rounded"
                                            data-uc-drop="mode: click; boundary: !.uc-navbar;">
                                            <div class="vstack gap-1 fw-medium items-end oga-country-list">
                                                <?php foreach ($countries as $country): ?>
                                                    <span>
                                                        <a class="text-none fw-normal oga-country-link hover:text-primary"
                                                            href="#" data-country="<?php echo esc_attr($country->slug); ?>">
                                                            <?php echo esc_html($country->name); ?>
                                                        </a>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </li>
                                </ul>


                                <div class="uc-navbar-item">
                                    <a class="uc-search-trigger icon-2 cstack text-none text-white dark:text-white"
                                        href="#uc-search-modal" data-uc-toggle>
                                        <i class="icon icon-2 fw-bold unicon-search"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Header end -->

        <!-- Wrapper start -->
        <div id="wrapper" class="wrap overflow-hidden-x">