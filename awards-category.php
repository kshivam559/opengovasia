<?php

/*
 * Archive Page of Channels (Category)
 *
 * @package OpenGovAsia
 */

get_header();

opengovasia_breadcrumbs();

// $category = get_queried_object();
// $term_id = $category->term_id;

$banner_image = get_archive_banner('awards_category');

$get_theme_mod = get_theme_mod('text_content', []);
$page_title = (!empty($get_theme_mod['awards_category_title'])) ? $get_theme_mod['awards_category_title'] : __('Awards Category', 'opengovasia');
$page_description = (!empty($get_theme_mod['awards_category_description'])) ? $get_theme_mod['awards_category_description'] : __('Explore our Awards Category for the latest winners and updates.', 'opengovasia');



?>

<header class="page-header panel vstack text-center">
    <div class="og_hero-image" style="background-image: url('<?php echo esc_url($banner_image); ?>');">

        <!-- <div class="og_hero-image" style="background: linear-gradient( cyan, transparent), linear-gradient( -45deg, magenta, transparent), linear-gradient( 45deg, yellow, transparent), url(<?php echo esc_url($banner_image); ?>) center center / cover no-repeat;
    background-blend-mode: overlay;"> -->

        <div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
            <h1 class="h3 lg:h1 text-white">
                <?php echo $page_title; ?>
            </h1>
            <div class="archive-description text-white">
                <?php echo $page_description; ?>
            </div>
        </div>
    </div>
</header>

<div id="primary" class="section py-3 sm:py-6 lg:py-6">
    <div class="container max-w-xl">
        <div class="panel vstack gap-3 sm:gap-6 lg:gap-7">

            <?php

            $categories = get_terms([
                'taxonomy' => 'awards-category',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false
            ]);

            $first_category = reset($categories); // Get first category
            if ($first_category):

                $args = array(
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'awards-category',
                            'field' => 'term_id',
                            'terms' => $first_category->term_id,
                        ),
                    ),
                    'post_type' => array('awards'),
                    'posts_per_page' => 6, // 7 for first category (1 hero + 6 grid)
                    'orderby' => 'date',
                    'order' => 'DESC'
                );

                $category_query = new Country_Filtered_Query($args);

                if ($category_query->have_posts()): ?>
                    <div class="block-layout grid-layout vstack gap-3 lg:gap-4 panel overflow-hidden">

                        <div class="block-header panel hstack items-center">
                            <div class="vstack justify-center">
                                <h2 class="h4 xl:h3 -ls-1 xl:-ls-2 text-inherit hstack gap-1">
                                    <a class="text-none hover:text-primary duration-150"
                                        href="<?php echo get_category_link($first_category->term_id); ?>"><?php echo $first_category->name; ?>
                                    </a>
                                    <i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>
                                </h2>
                            </div>

                        </div>
                        <div class="block-content">
                            <div class="panel row child-cols-12 md:child-cols gy-4 md:gx-3 xl:gx-4">

                                <div class="col-12 md:col-6 lg:col-6">
                                    <?php
                                    // Get the first post as the hero
                                    $category_query->the_post();
                                    $awards_year = get_the_terms(get_the_ID(), 'years');
                                    ?>
                                    <div>
                                        <article class="post type-post panel vstack gap-1 lg:gap-2">
                                            <div class="post-media panel uc-transition-toggle overflow-hidden">
                                                <div
                                                    class="featured-image bg-gray-25 dark:bg-gray-800 overflow-hidden ratio ratio-16x9">
                                                    <?php if (has_post_thumbnail()) {
                                                        the_post_thumbnail('full', array(
                                                            'class' => 'media-cover image uc-transition-scale-up uc-transition-opaque',
                                                            'loading' => 'eager',
                                                            'alt' => esc_attr(get_the_title())
                                                        ));
                                                    } ?>
                                                </div>
                                                <a href="<?php the_permalink(); ?>" class="position-cover"></a>
                                                <div
                                                    class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                </div>

                                                <span
                                                    class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                                                    <i class="icon-narrow unicon-trophy-filled"></i>
                                                </span>

                                            </div>
                                            <div class="post-header panel vstack gap-1">
                                                <div
                                                    class="post-meta panel hstack justify-start gap-1 fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 z-1">
                                                    <div>
                                                        <div class="post-category hstack gap-narrow fw-medium">
                                                            <a class="text-none text-primary dark:text-white"
                                                                href="<?php echo get_category_link($first_category->term_id); ?>"><?php echo $first_category->name; ?></a>
                                                        </div>
                                                    </div>
                                                    <div class="sep">❘</div>
                                                    <div>
                                                        <div class="post-date hstack gap-narrow">
                                                            <?php if (!empty($awards_year) && !is_wp_error($awards_year)): ?>
                                                                <span>
                                                                    <?php echo implode(', ', wp_list_pluck($awards_year, 'name')); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span>
                                                                    <?php echo get_the_date('M j, Y'); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h3 class="post-title h5 lg:h4 m-0 text-truncate-2">
                                                    <a class="text-none hover:text-primary duration-150"
                                                        href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                <p class="fs-6 opacity-60 text-truncate-2 my-1">
                                                    <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                                                </p>

                                            </div>
                                        </article>
                                    </div>
                                </div>
                                <div class="col-12 md:col-6 lg:col-6">
                                    <div class="row child-cols-12 g-4 sep-x">
                                        <?php
                                        $count = 0;
                                        while ($category_query->have_posts() && $count < 6):
                                            $category_query->the_post();
                                            $count++;
                                            $awards_year = get_the_terms(get_the_ID(), 'years');
                                            ?>
                                            <div>
                                                <article class="post type-post panel">
                                                    <div class="row child-cols g-2 uc-grid" data-uc-grid="">
                                                        <div class="col-auto uc-first-column">
                                                            <div
                                                                class="post-media panel uc-transition-toggle overflow-hidden max-w-150px min-w-100px lg:min-w-150px">
                                                                <div
                                                                    class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                                                                    <?php if (has_post_thumbnail()) {
                                                                        the_post_thumbnail('full', array(
                                                                            'class' => 'media-cover image uc-transition-scale-up uc-transition-opaque',
                                                                            'loading' => 'eager',
                                                                            'alt' => esc_attr(get_the_title())
                                                                        ));
                                                                    } ?>
                                                                </div>
                                                                <a href="<?php the_permalink(); ?>" class="position-cover"></a>

                                                                <div
                                                                    class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                                </div>

                                                                <span
                                                                    class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                                                                    <i class="icon-narrow unicon-trophy-filled"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="post-header panel vstack justify-between gap-1">
                                                                <div
                                                                    class="post-meta panel hstack justify-start gap-1 fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 z-1">
                                                                    <div>
                                                                        <div class="post-category hstack gap-narrow fw-medium">
                                                                            Awards
                                                                        </div>
                                                                    </div>
                                                                    <div class="sep">❘</div>
                                                                    <div>
                                                                        <div class="post-date hstack gap-narrow">
                                                                            <?php if (!empty($awards_year) && !is_wp_error($awards_year)): ?>
                                                                                <span>
                                                                                    <?php echo implode(', ', wp_list_pluck($awards_year, 'name')); ?>
                                                                                </span>
                                                                            <?php else: ?>
                                                                                <span>
                                                                                    <?php echo get_the_date('M j, Y'); ?>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h3 class="post-title h6 lg:h5 m-0 text-truncate-2">
                                                                    <a class="text-none hover:text-primary duration-150"
                                                                        href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <a href="<?php echo get_category_link($first_category->term_id); ?>"
                                        class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-100 mt-4">
                                        <span>See all <?php echo $first_category->name; ?> Awards</span>
                                        <i class="icon icon-1 unicon-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    // Reset post data
                    wp_reset_postdata();
                    ?>
                <?php endif; // End if have_posts() ?>
            <?php endif; // End first category ?>


            <div class="section-inner">
                <div class="row child-cols-12 lg:child-cols-6 g-4 gx-8 uc-grid" data-uc-grid="">
                    <?php
                    // Skip first category and process remaining categories
                    foreach (array_slice($categories, 1) as $category) {

                        // Set up the query arguments
                        $args = [
                            'tax_query' => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'awards-category',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                ),
                            ),
                            'post_type' => ['awards'],
                            'posts_per_page' => 6,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ];

                        // The Query
                        $category_query = new Country_Filtered_Query($args);

                        if ($category_query->have_posts()): ?>
                            <div>
                                <div class="block-layout grid-layout vstack gap-3 lg:gap-4 panel overflow-hidden">
                                    <div class="border-top"></div>
                                    <div class="block-header panel vstack sm:hstack justify-between sm:items-center">
                                        <h2 class="h4 xl:h3 -ls-1 xl:-ls-2 my-1 text-inherit hstack gap-1">

                                            <a class="text-none hover:text-primary duration-150"
                                                href="<?php echo get_category_link($category->term_id); ?>"><?php echo $category->name; ?>

                                            </a>
                                            <i class="icon-2 lg:icon-3 unicon-chevron-right opacity-40"></i>
                                        </h2>


                                    </div>
                                    <div class="block-content">
                                        <div class="row child-cols-12 g-2 gy-3 md:gx-3 md:gy-4">
                                            <?php while ($category_query->have_posts()):
                                                $category_query->the_post();
                                                $award_year = get_the_terms(get_the_ID(), 'years');
                                                ?>
                                                <div>
                                                    <article class="post type-post panel">
                                                        <div class="row child-cols g-2 uc-grid" data-uc-grid="">
                                                            <div class="col-auto uc-first-column">
                                                                <div
                                                                    class="post-media panel uc-transition-toggle overflow-hidden max-w-150px min-w-100px lg:min-w-150px">
                                                                    <div
                                                                        class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                                                                        <?php if (has_post_thumbnail()) {
                                                                            the_post_thumbnail('full', array(
                                                                                'class' => 'media-cover image uc-transition-scale-up uc-transition-opaque',
                                                                                'loading' => 'lazy',
                                                                                'alt' => esc_attr(get_the_title())
                                                                            ));
                                                                        } ?>
                                                                    </div>
                                                                    <div
                                                                        class="has-video-overlay position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                                    </div>
                                                                    <span
                                                                        class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                                                                        <i class="icon-narrow unicon-trophy-filled"></i>
                                                                    </span>
                                                                    <a href="<?php the_permalink(); ?>" class="position-cover"></a>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="post-header panel vstack justify-between gap-1">
                                                                    <div
                                                                        class="post-meta panel hstack justify-start gap-1 fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 z-1">
                                                                        <div>
                                                                            <div class="post-category hstack gap-narrow fw-medium">
                                                                                Awards
                                                                            </div>
                                                                        </div>
                                                                        <div class="sep">❘</div>
                                                                        <div>
                                                                            <div class="post-date hstack gap-narrow">
                                                                                <?php if (!empty($award_year) && !is_wp_error($award_year)): ?>
                                                                                    <span>
                                                                                        <?php echo implode(', ', wp_list_pluck($award_year, 'name')); ?>
                                                                                    </span>
                                                                                <?php else: ?>
                                                                                    <span>
                                                                                        <?php echo get_the_date('M j, Y'); ?>
                                                                                    </span>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <h3 class="post-title h6 lg:h5 m-0 text-truncate-2">
                                                                        <a class="text-none hover:text-primary duration-150"
                                                                            href="<?php the_permalink(); ?>">
                                                                            <?php the_title(); ?>
                                                                        </a>
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                    <div class="block-footer cstack lg:mt-2">
                                        <a href="<?php echo get_category_link($category->term_id); ?>"
                                            class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent dark:text-white border w-100">
                                            <span>See all <?php echo $category->name; ?></span>
                                            <i class="icon icon-1 unicon-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            // Reset post data
                            wp_reset_postdata();
                            ?>
                        <?php endif; // End if have_posts() ?>
                    <?php } // End foreach for remaining categories ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
get_footer();