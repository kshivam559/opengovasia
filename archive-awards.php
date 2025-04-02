<?php
/**
 * The template for displaying archive pages for Awards
 *
 * @package OpenGovAsia
 */

get_header();
?>

<div class="section py-3 sm:py-6 lg:py-9">
    <div class="container max-w-xl">
        <div class="panel vstack gap-3 sm:gap-6 lg:gap-6">

            <?php opengovasia_dynamic_filter_form(['country', 'filter_year']); ?>

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

                                    get_template_part('template-parts/archive-classic');

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