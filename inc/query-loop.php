<?php

/*
 * Custom query loop function
 *
 * @param string $post_type
 * @param int $posts_per_page
 * @param string $term
 *
 * @package OpenGovAsia
 */

function fetch_header_channel_posts($post_type = 'post', $posts_per_page = 2, $term = 'big-data')
{
    // Define the query arguments
    $args = [
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'tax_query' => [
            [
                'taxonomy' => 'category', // Custom taxonomy
                'field' => 'slug',
                'terms' => $term, // Custom term
            ],
        ],
    ];

    // Fetch posts
    $query = new Country_Filtered_Query($args);

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post(); ?>
            <div class="col-3">
                <article class="post type-post panel vstack gap-1">
                    <div class="post-media panel overflow-hidden">
                        <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                            <img class="media-cover image"
                                src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
                                data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>"
                                alt="<?php the_title(); ?>" data-uc-img="loading: lazy">
                        </div>
                        <a href="<?php the_permalink(); ?>" class="position-cover"></a>
                    </div>
                    <div class="post-header panel vstack gap-narrow">
                        <div
                            class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                            <div class="text-truncate">
                                <div class="post-category hstack gap-narrow fw-semibold">
                                    <?php 
                                    $category = get_term_by('slug', $term, 'category');
                                    $category_link = get_term_link($term, 'category');
                                    ?>
                                    <a class="fw-medium text-none text-primary dark:text-primary-400" href="
                                        <?php echo esc_url($category_link); ?>">
                                        <?php echo esc_html($category->name); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="sep d-block">|</div>
                            <div class="d-block text-truncate">
                                <div class="post-date hstack gap-narrow">
                                    <span><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                                </div>
                            </div>
                        </div>
                        <h3 class="post-title h6 m-0 text-truncate-2">
                            <a class="text-none hover:text-primary duration-150"
                                href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    </div>
                </article>
            </div>
        <?php endwhile;
        wp_reset_postdata();
    else:
        // No posts found
        echo '<p class="text-center">No posts found.</p>';
    endif;
}

/**
 * Display related posts with support for different post types
 * 
 * @param int    $post_id       The current post ID
 * @param string $post_type     The post type to query (default: 'post')
 * @param string $display_title The title to display above related posts
 * @param int    $limit         Number of posts to display (default: 3)
 */

function display_related_posts($post_id, $post_type = '', $display_title = "Latest from %s:", $limit = 3)
{
    // Only show on single post pages
    if (!is_single() || empty($post_id)) {
        return;
    }

    if (empty($post_type)) {
        $post_type = get_post_type($post_id); // Get the current post type
    }

    $taxonomies = get_object_taxonomies($post_type); // Get taxonomies for the post type

    if (empty($taxonomies)) {
        return;
    }

    $terms = wp_get_post_terms($post_id, $taxonomies[0]); // Get terms from the first taxonomy

    if (empty($terms)) {
        return;
    }

    // Get the first term name to use in the title
    $term_name = $terms[0]->name;

    // Replace %s in the display title with the term name
    $formatted_title = str_replace('%s', $term_name, $display_title);

    $term_ids = wp_list_pluck($terms, 'term_id');

    $args = [
        'post_type' => $post_type, // Use the same post type
        'posts_per_page' => $limit,
        'post__not_in' => [$post_id], // Exclude current post
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => [
            [
                'taxonomy' => $taxonomies[0], // Use the first taxonomy
                'field' => 'term_id',
                'terms' => $term_ids,
            ],
        ],
    ];

    $related_posts = new Country_Filtered_Query($args);

    if ($related_posts->have_posts()): ?>
        <div class="post-related panel border-top pt-4 mt-4">
            <h4 class="h5 xl:h4 mb-4 xl:mb-4"><?php echo $formatted_title; ?></h4>
            <div class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">
                <?php
                while ($related_posts->have_posts()):

                    $related_posts->the_post();

                    get_template_part('template-parts/archive-classic');

                endwhile;
                ?>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif;
}

/**
 * Display Related Playlists content of OGTV
 * 
 * @param int $post_id The current post ID
 * @param int $limit   Number of posts to display (default: 4)
 * 
 */

function display_related_ogtv_by_playlist($post_id = null, $limit = 4)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $requested_playlist_slug = isset($_GET['playlist']) ? sanitize_text_field($_GET['playlist']) : null;
    $playlist_ids = array();
    $playlist_for_header = null;

    if ($requested_playlist_slug) {
        $requested_playlist = get_term_by('slug', $requested_playlist_slug, 'playlists');

        if ($requested_playlist && !is_wp_error($requested_playlist)) {
            $playlist_ids[] = $requested_playlist->term_id;
            $playlist_for_header = $requested_playlist;
        }
    }

    if (empty($playlist_ids)) {
        $playlists = get_the_terms($post_id, 'playlists');

        if ($playlists && !is_wp_error($playlists)) {
            foreach ($playlists as $playlist) {
                $playlist_ids[] = $playlist->term_id;
            }
            $playlist_for_header = $playlists[0]; // Use first one for header
        }
    }

    if (!empty($playlist_ids)) {
        $related_query = new Country_Filtered_Query(array(
            'post_type' => 'ogtv',
            'post__not_in' => array($post_id),
            'posts_per_page' => $limit,
            'tax_query' => array(
                array(
                    'taxonomy' => 'playlists',
                    'field' => 'term_id',
                    'terms' => $playlist_ids,
                ),
            ),
        ));

        if ($related_query->have_posts()) {
            ?>
            <div class="section panel overflow-hidden swiper-parent uc-dark mt-3">
                <div class="section-outer panel py-5 lg:py-8 bg-gray-25 dark:bg-gray-800 dark:text-white">
                    <div class="container max-w-lg">

                        <div class="section-inner panel vstack gap-4">
                            <?php if ($playlist_for_header): ?>
                                <div class="section-header panel">
                                    <h3 class="h5 lg:h4 fw-medium m-0 text-inherit hstack">
                                        <a href="<?php echo esc_url(get_term_link($playlist_for_header)); ?>"
                                            class="text-none hover:text-primary">
                                            <?php echo 'More from ' . esc_html($playlist_for_header->name); ?>
                                        </a>
                                        <i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>
                                    </h3>
                                </div>
                            <?php endif; ?>

                            <div class="row g-4 xl:g-8">
                                <div class="col">
                                    <div class="panel">
                                        <div
                                            class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">
                                            <?php
                                            while ($related_query->have_posts()) {
                                                $related_query->the_post();
                                                get_template_part('template-parts/ogtv/archive-playlists', null, array(
                                                    'playlist_slug' => $requested_playlist_slug,
                                                ));
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
            wp_reset_postdata();
        }
    }
}
