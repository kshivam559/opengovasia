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
                            <div>
                                <div class="post-category hstack gap-narrow fw-semibold">
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)) {
                                        echo '<a class="fw-medium text-none text-primary dark:text-primary-400" href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="sep d-none md:d-block">|</div>
                            <div class="d-none md:d-block">
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
    endif;
}

