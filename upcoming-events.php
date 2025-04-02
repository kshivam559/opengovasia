<?php
/**
 * The template for displaying event archives
 *
 * @package opengovasia
 */
get_header();

?>

<div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
    <div class="container max-w-xl">
        <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
            <li><i class="unicon-chevron-right opacity-50"></i></li>
            <li>Upcoming Events</li>
        </ul>
    </div>
</div>
<div class="section py-3 sm:py-6 lg:py-9">
    <div class="container max-w-xl">
        <div class="panel vstack gap-3 sm:gap-6 lg:gap-7">
            <header class="page-header panel vstack text-center">
                <h1 class="h3 lg:h1">Upcoming Events</h1>
            </header>

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
                <p>No Upcoming Events found. Try switching your country.</p>
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