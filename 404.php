<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package OpenGov_Asia
 */

get_header();

opengovasia_breadcrumbs();

?>

	  <!-- Wrapper start -->
	  <div id="wrapper" class="wrap overflow-x-hidden">
            <div class="section py-6 lg:py-8 xl:py-10">
                <div class="container max-w-xl">
                    <div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center">
                        <h2 class="display-5 sm:display-3 lg:display-2 xl:display-1 text-primary">404</h2>
                        <h1 class="h3 sm:h1 m-0">Page not found</h1>
                        <p class="fs-6 md:fs-5">
                            Sorry, the page you seems looking for, <br>
                            has been moved, redirected or removed permanently.
                        </p>
                        <a href="<?php echo get_home_url(); ?>" class="animate-btn btn btn-md btn-primary text-none gap-0">
                            <span>Go back home</span>
                            <i class="icon icon-narrow unicon-arrow-left fw-bold"></i>
                        </a>
                        <p>Why Not try to search again? <a class="uc-link" href="#uc-search-modal" data-uc-toggle>Search now</a></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wrapper end -->

<?php

get_footer();
