<?php

/**
 * OpenGovAsia Theme
 *
 * @package OpenGovAsia
 * @since 1.0.0
 *
 * Template Name: OGTV Archive
 *
 * This template displays the archive page for OGTV, including playlists and latest videos.
 */

get_header();

opengovasia_breadcrumbs();

$channel_image = get_archive_banner('ogtv');

$get_theme_mod = get_theme_mod('text_content', []);
$page_title = (!empty($get_theme_mod['ogtv_title'])) ? $get_theme_mod['ogtv_title'] : __('OGTV', 'opengovasia');
$page_description = (!empty($get_theme_mod['ogtv_description'])) ? $get_theme_mod['ogtv_description'] : __('Explore our OGTV channel for the latest videos and playlists.', 'opengovasia');

?>

<header class="page-header panel vstack text-center">

    <div class="og_hero-image" style="background-image: url('<?php echo esc_url($channel_image); ?>');">

        <div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
            <h1 class="h3 lg:h1 text-white">
                <?php echo esc_html($page_title); ?>
            </h1>
            <div class="archive-description text-white">
                <?php echo $page_description; ?>
            </div>
        </div>

    </div>
</header>

<div id="primary" class="section panel overflow-hidden swiper-parent uc-dark">
    <div class="section-outer panel py-5 lg:py-8 bg-gray-25 dark:bg-gray-800 dark:text-white">
        <div class="container max-w-xl">

            <div class="section-inner panel vstack gap-4">

                <?php
                // Get all playlist terms
                $terms = get_terms([
                    'taxonomy' => 'playlists',
                    'hide_empty' => false, // Get all terms, we'll filter them manually
                ]);

                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        // Set up WP_Query for posts in this term
                        $args = [
                            'post_type' => 'ogtv', // Or your custom post type
                            'tax_query' => [
                                [
                                    'taxonomy' => 'playlists',
                                    'field' => 'term_id',
                                    'terms' => $term->term_id,
                                ]
                            ],
                            'posts_per_page' => 1, // We only need to check if any posts exist
                        ];

                        // Use your custom query that applies country filtering
                        $query = new Country_Filtered_Query($args);

                        // Only display this playlist section if there are posts for the current country
                        if ($query->have_posts()) {
                            $term_link = get_term_link($term);

                            echo '<div class="section-header panel">';
                            echo '<h2 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">';
                            echo '<a href="' . esc_url($term_link) . '" class="text-none hover:text-primary">';
                            echo esc_html($term->name);
                            echo '</a>';
                            echo '<i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>';
                            echo '</h2>';
                            echo '</div>';

                            // Reset the query args to get all posts for the slider
                            $args['posts_per_page'] = -1; // Get all posts for this term and country
                            $query = new Country_Filtered_Query($args);

                            echo '<div class="section-content">';
                            echo '<div class="swiper"
            data-uc-swiper="items: 1; gap: 16; autoplay: 2500; dots: .dot-nav; next: .nav-next; prev: .nav-prev; disable-class: opacity-40;" data-uc-swiper-s="items: 2;"
            data-uc-swiper-m="items: 3;" data-uc-swiper-m="gap: 24;" data-uc-swiper-l="items: 4; gap: 32;">';
                            echo '<div class="swiper-wrapper">';

                            while ($query->have_posts()) {
                                $query->the_post();
                                ?>
                                <div class="swiper-slide">
                                    <?php get_template_part(
                                        'template-parts/ogtv/archive-playlists',
                                        null,
                                        array(
                                            'playlist_slug' => $term->slug,
                                        )
                                    ); ?>
                                </div>
                                <?php
                            }

                            echo '</div>'; // Close swiper-wrapper
                
                            echo '<div class="hstack gap-1 mt-4">';
                            echo '<div class="swiper-nav nav-prev btn btn-alt-primary bg-transparent dark:text-white rounded-0 p-0 border w-32px lg:w-40px h-32px lg:h-40px shadow-sm">';
                            echo '<i class="icon-1 unicon-chevron-left"></i>';
                            echo '</div>';
                            echo '<div class="swiper-nav nav-next btn btn-alt-primary bg-transparent dark:text-white rounded-0 p-0 border w-32px lg:w-40px h-32px lg:h-40px shadow-sm">';
                            echo '<i class="icon-1 unicon-chevron-right"></i>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>'; // Close swiper
                            echo '</div>'; // Close section-content
                            echo '<div class="border-top mt-3"></div>';

                            wp_reset_postdata();
                        }
                    }
                }
                ?>
            </div>

            <div class="section-inner panel vstack gap-4">
                <div class="section-header panel">
                    <h2 class="h5 lg:h4 fw-medium m-0 mt-3 text-inherit hstack">
                        Latest Videos
                    </h2>
                </div>
                <div class="section-content">

                    <?php

                    if (have_posts()):

                        echo '<div class="row g-4 xl:g-8">';
                        echo '<div class="col">';
                        echo '<div class="panel">';
                        echo '<div class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">';

                        while (have_posts()):
                            the_post();

                            get_template_part('template-parts/ogtv/archive');

                        endwhile;

                        echo '  </div>';
                        echo '  </div>';
                        echo '  </div>';
                        echo '  </div>';

                        opengovasia_pagination();

                    endif;
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>



<?php

get_footer();