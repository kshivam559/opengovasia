<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OpenGov_Asia
 */

?>

<!-- Wrapper start -->
<div id="wrapper" class="wrap overflow-x-hidden">
	<div class="section">

		<div class="container max-w-xl">
			<div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center my-3">

				<h1 class="h3 sm:h1 m-0">No results found</h1>
				<p class="fs-6 md:fs-5">
					Try adjusting the filter or search keyword to find what you're looking for.
				</p>
				<a href="<?php echo get_home_url(); ?>" class="animate-btn btn btn-md btn-primary text-none gap-0">
					<span>Go back home</span>
					<i class="icon icon-narrow unicon-arrow-left fw-bold"></i>
				</a>
				<p>Try to search? <a class="uc-link" href="#uc-search-modal" data-uc-toggle>Search now</a>
				</p>
			</div>
		</div>
	</div>
</div>

<!-- Wrapper end -->