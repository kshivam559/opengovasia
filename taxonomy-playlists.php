<?php

/**
 * The template for displaying archive pages for Playlists
 * 
 * @package OpenGov_Asia
 */

get_header();

opengovasia_breadcrumbs();

$term = get_queried_object();

$term_id = $term->term_id;

?>

<header class="page-header panel vstack text-center">

    <?php

    $banner_image = get_term_meta($term_id, 'channel_image', true);

    ?>

    <div class="og_hero-image" style="background-image: url('<?php echo esc_url($banner_image); ?>');">

        <div class="container max-w-xl position-absolute top-50 start-50 translate-middle z-2">
            <h1 class="h3 lg:h1 text-white"><?php echo esc_html($term->name); ?></h1>

            <?php if (get_the_archive_description()): ?>

                <span class="archive-description text-white">
                    <?php echo strip_tags(get_the_archive_description()); ?>
                </span>

            <?php else: ?>

                <?php
                global $wp_query;
                $total_posts = $wp_query->found_posts;
                $current_posts = $wp_query->post_count;

                // Get the current term object
                $taxonomy_label = '';

                if ($term) {
                    if (is_tax() && isset($term->taxonomy)) {
                        $taxonomy = get_taxonomy($term->taxonomy);
                        $taxonomy_label = $taxonomy ? $taxonomy->label : '';

                    } elseif (is_post_type_archive()) {
                        $taxonomy_label = post_type_archive_title('', false);
                    }
                }

                echo '<span class="text-white">Showing ' . $current_posts . ' video out of ' . $total_posts . ' under "' . esc_html(single_term_title('', false)) . '" ' . strtoLower(esc_html($taxonomy_label)) . '.</span>';
                ?>

            <?php endif; ?>

        </div>
    </div>


</header>

<div id="primary" class="section uc-dark">
    <div class="section-outer panel py-5 lg:py-8 bg-gray-25 dark:bg-gray-800 dark:text-white">
        <div class="container max-w-xl">
            <div class="panel vstack gap-3 sm:gap-6 lg:gap-7">

                <?php opengovasia_dynamic_filter_form(['country']) ?>

                <div class="section-inner panel vstack gap-4">

                    <div class="section-content">

                        <?php if (have_posts()): ?>

                            <div class="row g-4 xl:g-8">
                                <div class="col">
                                    <div class="panel">
                                        <div
                                            class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-3">

                                            <?php while (have_posts()):

                                                the_post();

                                                /*
                                                 * Include the template for the content.
                                                 */

                                                get_template_part('template-parts/ogtv/archive-playlists', null, array(
                                                    'playlist_slug' => $term->slug,
                                                ));

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
        </div>
    </div>
</div>
<?php
get_footer();