<?php

/*
 * Archive Page of Channels (Category)
 *
 * @package OpenGovAsia
 */

get_header();

?>

<?php opengovasia_breadcrumbs(); ?>

<header class="page-header panel vstack text-center">

    <?php

    $category = get_queried_object();
    $term_id = $category->term_id;

    // Retrieve the 'channel_image' meta or use fallback
    $channel_image = !empty(get_term_meta($term_id, 'channel_image', true))
        ? get_term_meta($term_id, 'channel_image', true)
        : get_template_directory_uri() . '/assets/images/demo-three/common/channel-banner.webp';
    ?>

    <div class="og_hero-image" style="background-image: url('<?php echo esc_url($channel_image); ?>');">


        <?php $sponsor_image = get_term_meta($term_id, 'sponsor_image', true); ?>
        <?php $sponsor_link_text = get_term_meta($term_id, 'sponsor_link_text', true); ?>
        <?php $sponsor_link = get_term_meta($term_id, 'sponsor_link', true); ?>

        <?php if ($sponsor_link_text || $sponsor_link): ?>

            <div
                class="sponsor-link z-2 vstack items-end position-absolute top-0 end-0 m-2 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white">
                <div>Powered by
                    <a class="text-none text-primary" href="<?php echo esc_url($sponsor_link); ?>" target="_blank"
                        rel="noopener noreferrer">
                        <?php echo esc_html($sponsor_link_text); ?>
                    </a>
                </div>
                <?php if ($sponsor_image): ?>
                    <div class="sponsor-image m-1">
                        <?php if (!empty($sponsor_link)): ?>
                            <a href="<?php echo esc_url($sponsor_link); ?>" target="_blank" rel="noopener noreferrer">
                                <img width="90px" height="90px" src="<?php echo esc_url($sponsor_image); ?>"
                                    alt="<?php echo esc_attr($category->name); ?>">
                            </a>
                        <?php else: ?>
                            <img width="90px" height="90px" src="<?php echo esc_url($sponsor_image); ?>"
                                alt="<?php echo esc_attr($category->name); ?>">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>


        <div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
            <h1 class="h3 lg:h1 text-white"><?php echo single_cat_title('', false); ?></h1>

            <?php if (get_the_archive_description()): ?>

                <span class="archive-description text-white">
                    <?php echo strip_tags(get_the_archive_description()); ?>
                </span>

            <?php else: ?>

                <?php
                global $wp_query;

                // Total number of posts found by the query
                $total_posts = $wp_query->found_posts;

                // Number of posts displayed on the current page
                $current_post_count = $wp_query->post_count;

                // Get the current term object name
                $channel_name = single_cat_title('', false);

                // Posts per page setting
                $posts_per_page = get_option('posts_per_page');

                echo '<span class="text-white">Showing ' . $current_post_count . ' content out of ' . $total_posts . ' under "' . $channel_name . '" channel.</span>';
                ?>

            <?php endif; ?>

        </div>

    </div>

</header>

<div class="section py-3 sm:py-6 lg:py-6">
    <div class="container max-w-xl">
        <div class="panel vstack gap-3 sm:gap-6 lg:gap-7">


            <div class="section-inner panel vstack gap-4">
                <?php
                $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;
                $filter_post_type = isset($_GET['filter_post_type']) ? sanitize_text_field($_GET['filter_post_type']) : null;


                $post_types = ['post', 'events', 'ogtv'];
                if ($paged > 1 && $filter_post_type) {
                    $post_types = [$filter_post_type]; // Show only selected post type on paginated views
                }

                foreach ($post_types as $post_type):

                    $args = [
                        'post_type' => $post_type,
                        'paged' => $paged,
                        'tax_query' => [
                            [
                                'taxonomy' => 'category',
                                'field' => 'term_id',
                                'terms' => $term_id,
                            ]
                        ]
                    ];

                    $query = new Country_Filtered_Query($args);

                    if ($query->have_posts()):
                        // Headings
                        echo match ($post_type) {
                            'post' => '<div class="block-header panel"><h2 class="h4 -ls-1 xl:-ls-2 text-inherit hstack gap-1">Latest News</h2></div>',
                            'events' => '<div class="block-header panel border-top"><h2 class="h4 -ls-1 xl:-ls-2 text-inherit hstack gap-1 mt-3">Latest Events</h2></div>',
                            'ogtv' => '<div class="block-header panel border-top"><h2 class="h4 -ls-1 xl:-ls-2 text-inherit hstack gap-1 mt-3">Latest Videos</h2></div>',
                            default => '<div class="block-header panel border-top"><h2 class="h4 -ls-1 xl:-ls-2 text-inherit hstack gap-1 mt-3">Latest ' . ucfirst($post_type) . 's</h2></div>',
                        };
                        ?>

                        <div class="row g-4 xl:g-8">
                            <div class="col">
                                <div class="panel">
                                    <div
                                        class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">
                                        <?php
                                        while ($query->have_posts()):
                                            $query->the_post();
                                            match ($post_type) {
                                                'post' => get_template_part('template-parts/archive-classic'),
                                                'events' => get_template_part('template-parts/events/archive'),
                                                'ogtv' => get_template_part('template-parts/ogtv/archive'),
                                                default => get_template_part('template-parts/archive-classic'),
                                            };
                                        endwhile;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // Show View More button if there's a next page
                        if ($query->max_num_pages > $paged):
                            $next_page = $paged + 1;
                            $cat_slug = $category->slug;
                            $view_more_url = home_url("/channel/{$cat_slug}/page/{$next_page}/?filter_post_type={$post_type}");
                            ?>
                            <div class="text-center">
                                <a href="<?php echo esc_url($view_more_url); ?>"
                                    class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border ">
                                    <span>Next page</span>
                                    <i class="icon icon-1 unicon-chevron-right"></i></a>
                            </div>
                        <?php endif; ?>

                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
                <?php endforeach; ?>


            </div>


        </div>
    </div>
</div>

<?php
get_footer();