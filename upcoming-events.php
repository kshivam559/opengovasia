<?php
/**
 * The template for displaying event archives
 *
 * @package opengovasia
 */

get_header();

?>

<?php opengovasia_breadcrumbs(); ?>

<header class="page-header panel vstack text-center">

    <?php

    $channel_image = get_template_directory_uri() . '/assets/images/demo-three/common/events-banner.webp';

    ?>

    <div class="og_hero-image" style="background-image: url('<?php echo esc_url($channel_image); ?>');">

        <div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
            <h1 class="h3 lg:h1 text-white">Upcoming Events</h1>

            <div class="archive-description text-white">
                Be on a lookout for our content rich and engaging events across ASEAN and register now to get informed
                and empowered.
            </div>

        </div>

    </div>

</header>

<div id="primary" class="section py-3 sm:py-6 lg:py-6">
    <div class="container max-w-xl">
        <div class="panel vstack gap-3 sm:gap-6 lg:gap-7">


            <!-- Show only country filter for upcoming events -->
            <?php opengovasia_dynamic_filter_form(['country']); ?>

            <?php
            $paged = max(1, get_query_var('paged', 1)); // Ensure correct pagination
            
            $args = array(
                'post_type' => 'events',
                'posts_per_page' => 12, // Set the number of posts per page
                'meta_key' => 'event_date',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'paged' => $paged,
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
                                class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                <?php while ($events->have_posts()):
                                    $events->the_post(); ?>

                                    <?php get_template_part('template-parts/events/archive'); ?>

                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php opengovasia_pagination($events); ?>

                <?php wp_reset_postdata(); ?>

            <?php else: ?>
                <p class="text-center">No Upcoming Events found. Try switching your country.</p>
            <?php endif; ?>

            <div class="section-footer cstack lg:mt-2">
                <a href="/events/"
                    class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-100 md:w-auto">
                    <span>See all Past Events</span>
                    <i class="icon icon-1 unicon-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php

get_footer();