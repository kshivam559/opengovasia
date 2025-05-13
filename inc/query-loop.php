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