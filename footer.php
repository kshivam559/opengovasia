<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package OpenGov_Asia
 */

?>

</div>

<!-- Wrapper end -->

<!-- Footer start -->
<footer id="uc-footer" class="uc-footer panel uc-dark">
	<div class="footer-outer py-4 lg:py-6 xl:py-9 bg-white dark:bg-gray-900 dark:text-white">
		<div class="container max-w-xl">
			<div class="footer-inner vstack gap-4 lg:gap-6 xl:gap-9">
				<div class="uc-footer-top">
					<div class="row child-cols col-match gx-4 gy-6">
						<div class="col d-none lg:d-block">
							<div class="widget links-widget vstack gap-3">
								<div class="widgt-title">
									<h4
										class="fs-7 fw-medium text-uppercase m-0 text-dark dark:text-white text-opacity-50">
										Latest topics
									</h4>
								</div>
								<div class="widget-content">
									<?php
									wp_nav_menu(array(
										'theme_location' => 'latest-topic-menu',
										'menu_class' => 'nav-y gap-2 fs-6 fw-medium text-dark dark:text-white',
										'container' => false,
										'fallback_cb' => false,
									));
									?>
								</div>

							</div>
						</div>
						<div class="col-6 md:col">
							<div class="widget links-widget vstack gap-3">
								<div class="widgt-title">
									<h4
										class="fs-7 fw-medium text-uppercase m-0 text-dark dark:text-white text-opacity-50">
										Explore
									</h4>
								</div>
								<div class="widgt-content">
									<?php
									wp_nav_menu(array(
										'theme_location' => 'explore-menu',
										'menu_class' => 'nav-y gap-2 fs-6 fw-medium text-dark dark:text-white',
										'container' => false,
										'fallback_cb' => false,
									));
									?>
								</div>
							</div>
						</div>
						<div class="col-6 md:col">
							<div class="widget links-widget vstack gap-3">
								<div class="widgt-title">
									<h4
										class="fs-7 fw-medium text-uppercase m-0 text-dark dark:text-white text-opacity-50">
										About
									</h4>
								</div>
								<div class="widgt-content">
									<?php
									wp_nav_menu(array(
										'theme_location' => 'about-menu',
										'menu_class' => 'nav-y gap-2 fs-6 fw-medium text-dark dark:text-white',
										'container' => false,
										'fallback_cb' => false,
									));
									?>
								</div>
							</div>
						</div>
						<div class="col-12 md:col-5">
							<div class="widget newsletter-widget vstack gap-3">
								<div class="widgt-title">
									<h4 class="h4 lg:h3 lg:-ls-2 m-0">
										Keep up to date with the latest updates & news
									</h4>
								</div>
								<div class="widgt-content">
									<form class="hstack">
										<input
											class="form-control form-control-sm fs-6 fw-medium h-40px rounded-end-0 bg-white dark:bg-gray-800 dark:bg-gray-800 dark:border-white dark:border-opacity-15 dark:border-opacity-15"
											type="email" placeholder="Your email" required="" />
										<button class="btn btn-sm btn-primary rounded-start-0 min-w-100px"
											type="submit">
											Sign up
										</button>
									</form>
									<p class="fs-7 fw-medium text-dark dark:text-white text-opacity-50 mt-2">
										By pressing the Subscribe button, you confirm that you
										have read and are agreeing to our
										<a href="/privacy-policy/" class="uc-link dark:text-white">Privacy Policy</a>
										and
										<a href="/terms-and-conditions/" class="uc-link dark:text-white">Terms and
											Conditions</a>
									</p>
									<ul class="footer-social nav-x gap-2 mt-2 lg:mt-4">
										<li>
											<a class="hover:text-gray-900 dark:hover:text-white duration-150"
												href="https://www.facebook.com/opengovasia/" target="_blank"><i
													class="icon icon-2 unicon-logo-facebook"></i></a>
										</li>
										<li>
											<a class="hover:text-gray-900 dark:hover:text-white duration-150"
												href="https://x.com/opengov_asia/" target="_blank"><i
													class="icon icon-2 unicon-logo-x-filled"></i></a>
										</li>
										<li>
											<a class="hover:text-gray-900 dark:hover:text-white duration-150"
												href="https://www.linkedin.com/company/opengovasia/" target="_blank"><i
													class="icon icon-2 unicon-logo-linkedin"></i></a>
										</li>
										<li>
											<a class="hover:text-gray-900 dark:hover:text-white duration-150"
												href="https://www.youtube.com/channel/UCLrWe43CA--jOtzpa2sssWQ/"
												target="_blank"><i class="icon icon-2 unicon-logo-youtube"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr class="m-0" />
				<div
					class="uc-footer-bottom panel vstack lg:hstack gap-4 justify-between fs-7 text-center lg:text-start">
					<div class="vstack lg:hstack gap-2">
						<div class="footer-logo text-center">
							<?php
							$custom_logo_id = get_theme_mod('custom_logo');
							$logo = wp_get_attachment_image_src($custom_logo_id, 'full');

							if ($logo) {
								echo '<img class="uc-logo w-100px text-gray-900 dark:text-white" src="' . esc_url($logo[0]) . '" alt="' . esc_attr(get_bloginfo('name')) . '" data-uc-svg />';
							} else {
								echo '<h4 class="text-gray-900 dark:text-white">' . get_bloginfo('name') . '</h4>'; // Fallback if no logo is set
							}
							?>
						</div>

						<div class="vr mx-2 d-none lg:d-inline-flex"></div>
						<p class="footer-copyrights">
							<?php echo get_bloginfo('name'); ?>
							© <?php echo date('Y'); ?>, All rights reserved.
						</p>
						<ul class="footer-site-links nav-x gap-2 fw-medium justify-center lg:justify-start">
							<li>
								<a class="uc-link hover:text-gray-900 dark:hover:text-white duration-150"
									href="/privacy-policy/">Privacy notice</a>
							</li>
							<li>
								<a class="uc-link hover:text-gray-900 dark:hover:text-white duration-150"
									href="/terms-and-conditions/">Terms and Conditions</a>
							</li>

						</ul>
					</div>

					<?php echo oga_display_country_switcher(); ?>

				</div>

			</div>
		</div>
	</div>
	</div>
</footer>

<!-- Footer end -->

<!--  Search modal -->
<div id="uc-search-modal" class="uc-modal-full uc-modal" data-uc-modal="overlay: true">
	<div class="uc-modal-dialog d-flex justify-center bg-white text-dark dark:bg-gray-900 dark:text-white"
		data-uc-height-viewport="">
		<button
			class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
			type="button">
			<i class="unicon-close"></i>
		</button>
		<div class="panel w-100 sm:w-500px px-2 py-10">
			<h3 class="h1 text-center">Search</h3>
			<form class="" action="<?php echo esc_url(home_url('/')); ?>">
				<div class="hstack gap-1 mt-4 border-bottom p-narrow dark:border-gray-700">
					<span
						class="d-inline-flex justify-center items-center w-24px sm:w-40 h-24px sm:h-40px opacity-50"><i
							class="unicon-search icon-3"></i></span>
					<input type="search" name="s"
						class="form-control-plaintext ms-1 fs-6 sm:fs-5 w-full dark:text-white"
						placeholder="Type your keyword.." aria-label="Search" autofocus required />
				</div>

				<select name="filter_post_type" id="post_type" class="form-select mt-2" style="padding:0.75rem">
					<option value="">All</option>
					<option value="post" <?php selected(is_array($post_type) ? in_array('post', $post_type) : $post_type, 'post'); ?>>Posts</option>
					<option value="events" <?php selected(is_array($post_type) ? in_array('events', $post_type) : $post_type, 'events'); ?>>Events</option>
					<option value="awards" <?php selected(is_array($post_type) ? in_array('awards', $post_type) : $post_type, 'awards'); ?>>Awards</option>
					<option value="ogtv" <?php selected(is_array($post_type) ? in_array('ogtv', $post_type) : $post_type, 'ogtv'); ?>>OGTV</option>
				</select>


				<button class="btn btn-primary w-full mt-2" type="submit">Search</button>
			</form>
		</div>
	</div>
</div>

<!--  Menu panel -->
<div id="uc-menu-panel" data-uc-offcanvas="overlay: true;">
	<div class="uc-offcanvas-bar bg-white text-dark dark:bg-gray-900 dark:text-white">
		<header class="uc-offcanvas-header hstack justify-between items-center pb-4 bg-white dark:bg-gray-900">
			<div class="uc-logo">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="h5 text-none text-gray-900 dark:text-white">
					<?php
					$custom_logo_id = get_theme_mod('custom_logo');
					$logo = wp_get_attachment_image_src($custom_logo_id, 'full');

					if ($logo) { ?>
						<img class="w-32px" src="<?php echo esc_url($logo[0]); ?>" alt="<?php bloginfo('name'); ?>"
							data-uc-svg />
					<?php } else { ?>
						<span><?php bloginfo('name'); ?></span>
					<?php } ?>
				</a>
			</div>
			<button
				class="uc-offcanvas-close p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
				type="button">
				<i class="unicon-close"></i>
			</button>
		</header>


		<div class="panel">
			<form id="search-panel" class="form-icon-group vstack gap-1 mb-3" data-uc-sticky="" method="get" action="/">
				<input type="text" name="s" class="form-control form-control-md fs-6" placeholder="Search.." />
				<span class="form-icon text-gray">
					<i class="unicon-search icon-1"></i>
				</span>
			</form>

			<?php

			wp_nav_menu(array(
				'theme_location' => 'mobile_menu',
				'container' => false,
				'menu_class' => 'nav-y gap-narrow fw-bold fs-5',
				'walker' => new Mobile_Nav_Walker(),
				'items_wrap' => '<ul id="%1$s" class="%2$s" data-uc-nav>%3$s</ul>',
			));
			?>

			<ul class="social-icons nav-x mt-4">
				<li>
					<a href="https://www.facebook.com/opengovasia/" target="_blank"><i
							class="unicon-logo-facebook icon-2"></i></a>
					<a href="https://x.com/opengov_asia/" target="_blank"><i
							class="unicon-logo-x-filled icon-2"></i></a>
					<a href="https://www.linkedin.com/company/opengovasia/" target="_blank
					"><i class="unicon-logo-linkedin icon-2"></i></a>
					<a href="https://www.youtube.com/channel/UCLrWe43CA--jOtzpa2sssWQ/" target="_blank"><i
							class="unicon-logo-youtube icon-2"></i></a>
				</li>
			</ul>

			<div class="py-2 hstack gap-2 mt-4 bg-white dark:bg-gray-900" data-uc-sticky="position: bottom">
				<div class="vstack gap-1">
					<span class="fs-7 opacity-60">Select theme:</span>
					<div class="darkmode-trigger" data-darkmode-switch="">
						<label class="switch">
							<input type="checkbox" />
							<span class="slider fs-5"></span>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!--  Newsletter modal -->
<div id="opengov-newsletter" data-uc-modal="overlay: true">
	<div class="uc-modal-dialog w-800px bg-white text-dark dark:bg-gray-900 dark:text-white rounded overflow-hidden">
		<button
			class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
			type="button">
			<i class="unicon-close"></i>
		</button>
		<div class="row md:child-cols-6 col-match g-0">
			<div class="d-none md:d-flex">
				<div class="position-relative w-100 ratio-1x1">
					<img class="media-cover"
						src="/wp-content/themes/opengovasia/assets/images/demo-three/common/newsletter.jpg"
						alt="Newsletter image" />
				</div>
			</div>
			<div>
				<div class="panel vstack self-center p-4 md:py-8 text-center">
					<h3 class="h3 md:h2">Subscribe to the Newsletter</h3>
					<p class="ft-tertiary">
						Join 10k+ people to get notified about new posts, news and tips.
					</p>
					<div class="panel mt-2 lg:mt-4">
						<form class="vstack gap-1">
							<input type="email"
								class="form-control form-control-sm w-full fs-6 bg-white dark:border-white dark:border-gray-700 dark:text-dark"
								placeholder="Your email address.." required />
							<button type="submit" class="btn btn-sm btn-primary">
								Sign up
							</button>
						</form>
						<p class="fs-7 mt-2">Do not worry we don't spam!</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--  GDPR modal -->
<div id="uc-gdpr-notification" class="uc-gdpr-notification uc-notification uc-notification-bottom-left lg:m-2">
	<div class="uc-notification-message">
		<div id="uc-close-gdpr-notification" class="uc-notification-close" data-uc-close></div>
		<h2 class="h5 ft-primary fw-bold -ls-1 m-0">GDPR Compliance</h2>
		<p class="fs-7 mt-1 mb-2">
			We use cookies to ensure you get the best experience on our website.
			By continuing to use our site, you accept our use of cookies,
			<a href="/privacy-policy/" class="uc-link text-underline">Privacy Policy</a>, and
			<a href="/terms-and-conditions/" class="uc-link text-underline">Terms and Conditions</a>.
		</p>
		<button class="btn btn-sm btn-primary" id="uc-accept-gdpr">
			Accept
		</button>
	</div>
</div>

<!--  Country modal -->
<div id="uc-country-notification"
	class="uc-notification uc-notification-bottom-right lg:m-2 border border-gray-200 dark:border-gray-700 rounded shadow-sm"
	style="visibility: hidden; opacity: 0;transform: translateY(8px);transition: all 300ms ease;">
	<div class="uc-notification-message bg-white dark:bg-gray-900 dark:text-white">
		<div class="uc-notification-icon">

		</div>
		<div id="uc-close-country-notification" class="uc-notification-close" data-uc-close></div>
		<h2 class="h5 ft-primary fw-bold -ls-1 m-0 hstack gap-narrow"><i class="unicon-globe icon-1"></i> Switch
			Country?</h2>
		<p class="fs-7 mt-1 mb-2">
			It looks like you're viewing content tailored for <strong
				id="current-country"><?php echo get_selected_country_name() ?></strong>. Would you like to switch to
			content that's more relevant to your current location?
		</p>
		<p class="fs-7 text-gray-900 dark:text-white text-opacity-60">
			You can change this later at the bottom of the page.
		</p>
		<div class="mt-3">
			<button id="redirect-yes" class="btn btn-sm btn-primary">
				Yes, Show me
			</button>
			<button id="redirect-no" class="btn btn-sm dark:text-white">
				No, let me stay
			</button>
		</div>
	</div>
</div>

<!--  Bottom Actions Sticky -->
<div class="backtotop-wrap position-fixed bottom-0 end-0 z-99 m-2 vstack">

	<a class="btn btn-sm bg-primary text-white w-40px h-40px rounded-circle" href="to_top" data-uc-backtotop>
		<i class="icon-2 unicon-chevron-up"></i>
	</a>
</div>

<!-- <div class="position-fixed bg-white dark:bg-gray-900 dark:text-white" id="country-redirect-popup"
	style="display:none; right:0px; bottom:20px; box-shadow:0 0 10px rgba(0,0,0,0.2); z-index:9999;border-radius:5px;padding:20px;margin:0 20px;">
	<div class="">
		<p class="fs-6">
			It looks like you're viewing content tailored for <strong
				id="current-country"><?php echo get_selected_country_name() ?></strong>. Would you like to switch to
			<br>
			content that's more relevant to your current location?
		</p>

		<p class="fs-7 text-gray-900 dark:text-white text-opacity-60">
			You can change this later at the bottom of the page.
		</p>

		<div class="mt-3">
			<button id="redirect-yes" class="btn btn-sm btn-primary">
				Yes, Show me
			</button>
			<button id="redirect-no" class="btn btn-sm dark:text-white">
				No, let me stay
			</button>
		</div>
	</div>
</div> -->


<?php wp_footer(); ?>

<script>
	// Schema toggle via URL
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	const getSchema = urlParams.get("schema");
	if (getSchema === "dark") {
		setDarkMode(1);
	} else if (getSchema === "light") {
		setDarkMode(0);
	}
</script>

<script>
	(async () => {
		if (sessionStorage.getItem("countryPopupDismissed")) return;

		let userCountry = localStorage.getItem("userCountry");
		let userCountryName = localStorage.getItem("userCountryName");

		// Attempt to get country from Cloudflare header via meta tag
		if (!userCountry) {
			const metaTag = document.querySelector('meta[name="cf-ipcountry"]');
			if (metaTag) {
				userCountry = metaTag.content.toLowerCase();
				localStorage.setItem("userCountry", userCountry);
			}
		}

		// Fallback to API if Cloudflare header not available
		if (!userCountry) {
			try {
				const res = await fetch("/wp-json/opengovasia/v1/geolocation");
				const data = await res.json();
				userCountry = data.country.toLowerCase();
				userCountryName = data.country_name;
				localStorage.setItem("userCountry", userCountry);
				localStorage.setItem("userCountryName", userCountryName);
			} catch (e) {
				console.warn("Geo fallback failed", e);
				return;
			}
		}

		const url = new URL(window.location.href);
		const params = url.searchParams;
		const currentCountry = params.get("c");

		if (!userCountry || userCountry === currentCountry) return;

		// const popup = document.getElementById("country-redirect-popup");
		const popup = document.getElementById("uc-country-notification");
		if (!popup) return;

		const closeBtn = document.getElementById("uc-close-country-notification");


		closeBtn.addEventListener("click", function () {
			popup.style.transform = "translateY(400px)";
			popup.style.visibility = "hidden";

		});

		setTimeout(() => {
			popup.style.visibility = "visible";
			popup.style.transform = "translateY(0)";
			popup.style.opacity = "1";
		}, 2000);


		document
			.getElementById("redirect-yes")
			.addEventListener("click", function () {
				params.set("c", userCountry);
				window.location.href = url.pathname + "?" + params.toString();
			});

		document
			.getElementById("redirect-no")
			.addEventListener("click", function () {
				popup.style.transform = "translateY(400px)";
				popup.style.visibility = "hidden";
				sessionStorage.setItem("countryPopupDismissed", "true");
			});
	})();

</script>

</body>

</html>