<?php
/**
 * Template part for displaying single posts
 *
 * @package OpenGovAsia
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post type-post single-post py-4 lg:py-6 xl:py-9'); ?>
    data-post-id="<?php the_ID(); ?>">
    <div class="container max-w-xl">

        <div class="post-header">
            <div class="panel vstack gap-4 md:gap-6 xl:gap-8 text-center">
                <div class="panel vstack items-center xl:mx-auto gap-2 md:gap-3">
                    <h1 class="h4 sm:h2 lg:h1 xl:display-6"><?php the_title(); ?></h1>

                    <ul class="post-share-icons nav-x gap-1 dark:text-white">
                        <li>
                            <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"
                                target="_blank">
                                <i class="unicon-logo-facebook icon-1"></i>
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                                target="_blank">
                                <i class="unicon-logo-x-filled icon-1"></i>
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>"
                                target="_blank">
                                <i class="unicon-logo-linkedin icon-1"></i>
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                href="mailto:?subject=<?php the_title(); ?>&body=<?php the_permalink(); ?>">
                                <i class="unicon-email icon-1"></i>
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                href="javascript:void(0);" onclick="sharePage()"><i class="unicon-link icon-1"></i></a>
                        </li>
                    </ul>
                </div>


                <?php if (has_post_thumbnail()): ?>
                    <figure
                        class="featured-image m-0 ratio ratio-2x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                        <?php the_post_thumbnail('full', ['class' => 'media-cover image uc-transition-opaque', 'data-uc-img' => 'loading: lazy']); ?>
                    </figure>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel mt-4 lg:mt-6 xl:mt-9">
        <div class="container max-w-lg">
            <div class="post-content panel fs-6 md:fs-5" data-uc-lightbox="animation: scale">
                <?php the_content(); ?>
            </div>

            <div class="post-footer panel vstack sm:hstack gap-3 justify-between border-top py-4 mt-4 xl:py-9 xl:mt-9">
                <ul class="nav-x gap-narrow text-primary">
                    <li><span class="text-black dark:text-white me-narrow">Channel:</span></li>
                    <?php
                    $categories = get_the_category();
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
                            <i class="unicon-link icon-1"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- author box -->

            <?php opengovasia_author_box(); ?>

            <?php display_related_posts(get_the_ID(), 4); ?>


            <?php

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()):
                comments_template();
            endif;
            ?>


        </div>
    </div>
</article>