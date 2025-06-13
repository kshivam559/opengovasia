<?php
/**
 * Archive Past Events Template
 *
 * This template is used to display the archive page for events.
 *
 * @package OpenGovAsia
 * @since 1.0.0
 */

get_header();

opengovasia_breadcrumbs();

$banner_image = get_archive_banner('past_events');

$get_theme_mod = get_theme_mod('text_content', []);
$page_title = (!empty($get_theme_mod['past_events_title'])) ? $get_theme_mod['past_events_title'] : __('Past Events', 'opengovasia');
$page_description = (!empty($get_theme_mod['past_events_description'])) ? $get_theme_mod['past_events_description'] : __('Be on a lookout for our content rich and engaging events across ASEAN and register now to get informed and empowered.', 'opengovasia');

?>

<header class="page-header panel vstack text-center">

    <div class="og_hero-image" style="background-image: url('<?php echo esc_url($banner_image); ?>');">

        <div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
            <h1 class="h3 lg:h1 text-white"><?php echo $page_title; ?></h1>

            <div class="archive-description text-white">
                <?php echo $page_description; ?>
            </div>

        </div>
    </div>

</header>

<div id="primary" class="section py-3 sm:py-6 lg:py-6">
    <div class="container max-w-xl">
        <div class="panel vstack gap-3 sm:gap-6 lg:gap-7">

            <?php opengovasia_dynamic_filter_form(['country', 'filter_year', '']); ?>

            <?php if (have_posts()): ?>

                <div class="row g-4 xl:g-8">
                    <div class="col">
                        <div class="panel">
                            <div
                                class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">

                                <?php while (have_posts()):

                                    the_post();

                                    /*
                                     * Include the Post-Type-specific template for the content.
                                     * If you want to override this in a child theme, then include a file
                                     * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                                     */

                                    get_template_part('template-parts/events/archive');

                                endwhile; ?>

                            </div>
                        </div>
                    </div>
                </div>

                <?php opengovasia_pagination(); ?>

            <?php else: ?>

                <?php get_template_part('template-parts/content', 'none'); ?>

            <?php endif; ?>

        </div>
    </div>
</div>


<?php

get_footer();