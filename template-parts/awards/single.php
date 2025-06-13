<?php
/**
 * Template part for displaying single posts
 *
 * @package OpenGovAsia
 */

if (!defined('ABSPATH'))
    exit;


$categories = get_the_category();

// Check if there are categories
if (!empty($categories)):
    // Get the first category (you can modify this logic if needed)
    $category = $categories[0];
    $term_id = $category->term_id;

    $sponsored_by = get_term_meta($term_id, 'sponsored_by', true);

endif;

// Retrieve the 'channel_image' meta or use fallback
$banner_image = !empty(get_term_meta($term_id, 'channel_image', true))
    ? get_term_meta($term_id, 'channel_image', true)
    : get_archive_banner('awards');

$awards_year = get_the_terms(get_the_ID(), 'years');

$selected_partners = get_custom_meta(get_the_ID(), 'companies', true);

?>


<div class="og_hero-image" style="background-image: url(<?php echo esc_url($banner_image); ?>);">

    <div class="container max-w-xl position-absolute start-50 translate-middle z-2 text-center" style="top: 35%;">
        <h1 class="h3 lg:h1 text-white "><?php the_title(); ?></h1>
        <div class="my-2">

            <span class="single-post cateogry-link text-white fs-6 md:fs-5">
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                    class="text-none text-white"><?php echo esc_html($category->name); ?></a>
            </span>

            <?php if (!empty($sponsored_by)):
                $company = get_post($sponsored_by);
                if ($company && $company->post_status === 'publish'):

                    $sponsor_name = $company->post_title;
                    $sponsor_link = get_permalink($company->ID);
                endif;
                ?>

                <span class="single-post single-post-sep sep text-white fs-6 md:fs-5 opacity-60">
                    •
                </span>

                <span class="single-post sponsor-link text-white fs-6 md:fs-5">
                    Powered by
                    <a class="text-none"
                        href="<?php echo esc_url($sponsor_link); ?>"><?php echo esc_html($sponsor_name); ?></a>
                </span>

            <?php endif; ?>
            <span class="single-post single-post-sep sep text-white fs-6 md:fs-5 opacity-60">•</span>
            <span class="single-post single-post-date text-white fs-6 md:fs-5">
                <i class="unicon-calendar"></i>
                <?php
                if (!empty($awards_year) && !is_wp_error($awards_year)):
                    echo implode(', ', wp_list_pluck($awards_year, 'name'));
                else:
                    echo esc_html(get_the_date('M j, Y'));
                endif;
                ?>
            </span>
        </div>

        <div class="panel vstack items-center xl:mx-auto gap-2 md:gap-3">

            <ul class="post-share-icons nav-x gap-1 text-white">
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank">
                        <i class="unicon-logo-facebook icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                        target="_blank">
                        <i class="unicon-logo-x-filled icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>"
                        target="_blank">
                        <i class="unicon-logo-linkedin icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="mailto:?subject=<?php the_title(); ?>&body=<?php the_permalink(); ?>">
                        <i class="unicon-email icon-1"></i>
                    </a>
                </li>
                <li>
                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-white border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="javascript:void(0);" onclick="sharePage()"><i class="unicon-share-filled icon-1"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<article id="post-<?php the_ID(); ?>" <?php post_class('post type-post single-post py-4 lg:py-6 xl:py-6'); ?>
    data-post-id="<?php the_ID(); ?>">
    <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3 rounded-1"
        style="margin-top: -230px;padding: 16px;">

        <div class="post-header">

            <?php if (has_post_thumbnail()): ?>
                <figure
                    class="featured-image m-0 ratio ratio-2x1 rounded-1 uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                    <?php the_post_thumbnail('full', ['class' => 'media-cover image uc-transition-opaque', 'data-uc-img' => 'loading: lazy']); ?>
                </figure>
            <?php endif; ?>


        </div>
    </div>

    <div class="panel">
        <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3">
            <div class="post-content panel fs-6 md:fs-5">
                <?php the_content(); ?>


                <!-- Partners Section -->
                <?php if (!empty($selected_partners) && is_array($selected_partners)): ?>
                    <div class="partners-section panel">

                        <div class="partners-list mt-3">
                            <?php foreach ($selected_partners as $partner_id):
                                $partner = get_post($partner_id);

                                if ($partner && $partner->post_status === 'publish'):
                                    $logo = get_the_post_thumbnail_url($partner_id, 'full');
                                    $info = wp_strip_all_tags(strip_shortcodes($partner->post_content));
                                    $socials = get_custom_meta($partner_id, 'socials', true);
                                    ?>
                                    <div
                                        class="partner border py-2 px-2 rounded-1 shadow-xs mb-2 dark:bg-gray-900 dark:text-white vstack gap-2 sm:hstack align-items-center">
                                        <?php if (!empty($logo)): ?>
                                            <div>
                                                <a href="<?php echo esc_url(get_permalink($partner_id)); ?>">
                                                    <img class="max-w-100px mr-2" src="<?php echo esc_url($logo); ?>"
                                                        alt="<?php echo esc_attr($partner->post_title); ?>">
                                                </a>

                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <?php if (!empty($partner->post_title)): ?>
                                                <h3 class="h5 my-1 dark:text-white">
                                                    <a href="<?php echo esc_url(get_permalink($partner_id)); ?>"
                                                        class="text-none hover:text-primary">
                                                        <?php echo esc_html($partner->post_title); ?>
                                                    </a>
                                                </h3>
                                            <?php endif; ?>

                                            <?php if (!empty($info)): ?>
                                                <p class="my-1"><?php echo esc_html($info); ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($socials) && is_array($socials)): ?>
                                                <div class="social-links py-2">
                                                    <?php foreach ($socials as $social):
                                                        if (!empty($social['platform']) && !empty($social['url'])):
                                                            $platform = strtolower($social['platform']);
                                                            switch ($platform) {
                                                                case 'facebook':
                                                                    $icon = '<i class="icon icon-1 unicon-logo-facebook"></i>';
                                                                    break;
                                                                case 'twitter':
                                                                    $icon = '<i class="icon icon-1 unicon-logo-x"></i>';
                                                                    break;
                                                                case 'linkedin':
                                                                    $icon = '<i class="icon icon-1 unicon-logo-linkedin"></i>';
                                                                    break;
                                                                case 'instagram':
                                                                    $icon = '<i class="icon icon-1 unicon-logo-instagram"></i>';
                                                                    break;
                                                                case 'youtube':
                                                                    $icon = '<i class="icon icon-1 unicon-logo-youtube"></i>';
                                                                    break;
                                                                case 'website':
                                                                    $icon = '<i class="icon icon-1 unicon-earth-americas"></i>';
                                                                    break;
                                                                default:
                                                                    $icon = '<i class="icon icon-1 unicon-earth-americas"></i>';
                                                            }
                                                            ?>
                                                            <a href="<?php echo esc_url($social['url']); ?>"
                                                                class="text-none hover:text-primary mx-1" target="_blank" rel="noopener">
                                                                <?php echo $icon; ?>
                                                            </a>
                                                        <?php endif;
                                                    endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- End Partners Section -->

            </div>

            <div class="post-footer panel vstack sm:hstack gap-3 justify-between py-3 mt-4">
                <ul class="nav-x gap-narrow text-primary">
                    <li><span class="text-black dark:text-white me-narrow">Channel:</span></li>
                    <?php

                    if ($categories) {
                        foreach ($categories as $index => $category) {
                            echo '<li>';
                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="uc-link gap-0 dark:text-white">';
                            echo esc_html($category->name);
                            echo '<span class="text-black dark:text-white">' . ($index < count($categories) - 1 ? ',' : '') . '</span>';
                            echo '</a>';
                            echo '</li>';
                        }
                    }
                    ?>
                </ul>



                <ul class="post-share-icons nav-x gap-narrow">
                    <li class="me-1"><span class="text-black dark:text-white">Share:</span></li>
                    <li>
                        <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                            href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"
                            target="_blank">
                            <i class="unicon-logo-facebook icon-1"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                            href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                            target="_blank">
                            <i class="unicon-logo-x-filled icon-1"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                            href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>">
                            <i class="unicon-logo-linkedin icon-1"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                            href="mailto:?subject=<?php the_title(); ?>&body=<?php the_permalink(); ?>">
                            <i class="unicon-email icon-1"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                            href="javascript:void(0);" onclick="sharePage()">
                            <i class="unicon-share-filled icon-1"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Author Box -->
            <?php // opengovasia_author_box(); ?>
            <!-- End Author Box -->


            <!-- Related Posts -->
            <?php display_related_posts(get_the_ID(), 'post', 'Latest News in %s:'); ?>
            <?php display_related_posts(get_the_ID(), 'events', 'Latest Events on %s:'); ?>
            <?php display_related_posts(get_the_ID(), 'ogtv', 'Latest Videos in %s:'); ?>
            <!-- End Related Posts -->


            <!-- Comments -->
            <?php

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()):
                comments_template();
            endif;
            ?>
            <!-- End Comments -->


        </div>
    </div>
</article>