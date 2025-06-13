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
    : get_archive_banner('past_events');

// Fetch Event Data

$events_data = get_custom_meta(get_the_ID());
$selected_partners = get_custom_meta(get_the_ID(), 'companies', true);

$event_date = $events_data['event_date'] ?? '';
$event_start_time = $events_data['event_start_time'] ?? '';
$event_end_time = $events_data['event_end_time'] ?? '';

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
                if ($company && $company->post_status === 'publish') {

                    $sponsor_name = $company->post_title;
                    $sponsor_link = get_permalink($company->ID);
                }

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
                echo (!empty($event_date)) ? date('F j, Y', strtotime($event_date)) : get_the_date();
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


<article id="post-<?php the_ID(); ?>" <?php post_class('post type-post single-post py-4 lg:py-4 xl:py-4'); ?>
    data-post-id="<?php the_ID(); ?>">
    <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3 rounded-1"
        style="margin-top: -230px;padding: 16px;">

        <div class="post-header position-relative panel vstack gap-1 lg:gap-2">

            <?php if (has_post_thumbnail()): ?>
                <figure
                    class="featured-image m-0 ratio ratio-2x1 rounded-1 uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                    <?php the_post_thumbnail('full', ['class' => 'media-cover image uc-transition-opaque', 'data-uc-img' => 'loading: lazy']); ?>
                </figure>
            <?php endif; ?>

            <div class="vstack gap-narrow position-absolute bottom-0 start-0 end-0 p-1 text-white overflow-hidden"
                style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.9));">
                <?php if (!empty($events_data['event_date'])): ?>
                    <div class="hstack gap-narrow">
                        <i class="icon-narrow unicon-calendar"></i>
                        <?php echo date('F j, Y', strtotime($events_data['event_date'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($events_data['event_start_time']) || !empty($events_data['event_end_time'])): ?>
                    <div class="hstack gap-narrow"><i class="icon-narrow unicon-time"></i>
                        <?php
                        if (!empty($events_data['event_start_time'])) {
                            echo date('g:i A', strtotime($events_data['event_start_time']));
                        }
                        if (!empty($events_data['event_end_time'])) {
                            echo ' - ' . date('g:i A', strtotime($events_data['event_end_time']));
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($events_data['event_address'])): ?>
                    <div class="hstack gap-narrow">
                        <i class="icon-narrow unicon-map"></i>
                        <?php echo $events_data['event_address']; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

    <div class="panel">
        <div class="container max-w-lg position-relative bg-white dark:bg-gray-900 z-3">

            <?php if (!empty($events_data['event_description'])): ?>

                <div class="post-header panel vstack gap-1 lg:gap-2 ">

                    <div class="post-content">
                        <?php echo wpautop($events_data['event_description']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="dark:bg-gray-900 rounded-1 p-2 sm:p-3 lg:p-4 xl:p-4 border border-gray-900 border-opacity-15">

                <?php

                $event_date = $events_data['event_date'] ?? '';
                $event_start_time = $events_data['event_start_time'] ?? '';
                $event_end_time = $events_data['event_end_time'] ?? '';
                $event_timezone = $events_data['event_timezone'] ?? '';

                // Determine timezone
                $timezone = (!empty($event_timezone) && in_array($event_timezone, timezone_identifiers_list()))
                    ? new DateTimeZone($event_timezone)
                    : wp_timezone();

                if ($event_date && $event_start_time):
                    $event_datetime = new DateTime($event_date . ' ' . $event_start_time, $timezone);
                    $current_datetime = new DateTime('now', $timezone);
                    $event_datetime_iso = $event_datetime->format('c');

                    $event_ended = false;
                    if ($event_end_time):
                        $event_end_datetime = new DateTime($event_date . ' ' . $event_end_time, $timezone);
                        $event_ended = $current_datetime > $event_end_datetime;
                    endif;

                    if ($event_ended):
                        ?>
                        <div class="post-header panel vstack gap-1 lg:gap-2 text-center">
                            <h4 class="post-title h6 lg:h4 m-2">
                                The Event has Ended!
                            </h4>
                        </div>
                        <?php
                    else:
                        ?>
                        <div class="post-header panel vstack gap-1 lg:gap-2 text-center">
                            <h4 class="post-title h6 lg:h4 m-2">
                                Event Countdown
                            </h4>


                            <div class="panel vstack justify-center items-center py-3 gap-2 sm:gap-4 lg:gap-8 text-center">
                                <div class="row child-cols-3 items-center justify-center g-1 sm:g-2 uc-countdown"
                                    data-uc-countdown="date: <?php echo esc_attr($event_datetime_iso); ?>" role="timer">

                                    <div>
                                        <div>
                                            <div class="h2 lg:h1 w-72px sm:min-w-96px py-2 border rounded uc-countdown-days">
                                                <span>0</span><span>0</span>
                                            </div>
                                            <div class="uc-countdown-label mt-1 text-center d-block opacity-60">
                                                Days
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <div class="h2 lg:h1 w-72px sm:min-w-96px py-2 border rounded uc-countdown-hours">
                                                <span>0</span><span>6</span>
                                            </div>
                                            <div class="uc-countdown-label mt-1 text-center d-block opacity-60">
                                                Hours
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <div class="h2 lg:h1 w-72px sm:min-w-96px py-2 border rounded uc-countdown-minutes">
                                                <span>3</span><span>8</span>
                                            </div>
                                            <div class="uc-countdown-label mt-1 text-center d-block opacity-60">
                                                Minutes
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <div class="h2 lg:h1 w-72px sm:min-w-96px py-2 border rounded uc-countdown-seconds">
                                                <span>1</span><span>7</span>
                                            </div>
                                            <div class="uc-countdown-label mt-1 text-center d-block opacity-60">
                                                Seconds
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($events_data['event_link'])): ?>
                            <div class="mt-2">
                                <a href="<?php echo $events_data['event_link']; ?>"
                                    class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-primary text-white border w-full"
                                    target="_blank">
                                    <span>RESERVE A SEAT NOW!</span>
                                    <i class="icon icon-1 unicon-chevron-right"></i>
                                </a>
                            </div>

                            <p class="text-center mt-2">
                                <small class="text-gray-900 dark:text-white text-opacity-60">
                                    *Click the button to reserve your seat for the event.
                                </small>
                            </p>
                        <?php endif; ?>

                        <?php
                    endif;
                endif;
                ?>

            </div>

            <div class="event-container">

                <style>
                    .og-event-tab {
                        border: none;
                        padding: 12px 18px;
                        text-align: center;
                        border-top-left-radius: 5px;
                        border-top-right-radius: 5px;
                        transition: 0.3s;
                    }

                    .partners-list .partner .social-links a {
                        margin: 0 5px;
                    }
                </style>

                <div class="event-tab d-flex vstack sm:hstack gap-narrow my-2">

                    <button
                        class="og-event-tab text-truncate dark:text-white dark:border-gray-700 w-100 dark:bg-gray-900"
                        onclick="openPage('details', this)" id="defaultOpen">Overview</button>

                    <?php if (!empty($events_data['speakers'])): ?>
                        <button
                            class="og-event-tab text-truncate dark:bg-gray-900 dark:text-white dark:border-gray-700 w-100"
                            onclick="openPage('speakers', this)">Speakers</button>
                    <?php endif; ?>

                    <?php if (!empty($events_data['who_should_attend'])): ?>
                        <button
                            class="og-event-tab text-truncate dark:bg-gray-900 dark:text-white dark:border-gray-700 w-100"
                            onclick="openPage('who_should_attend', this)">Who Should Attend</button>
                    <?php endif; ?>

                    <?php if (!empty($events_data['topics_covered'])): ?>
                        <button
                            class="og-event-tab text-truncate dark:bg-gray-900 dark:text-white dark:border-gray-700 w-100"
                            onclick="openPage('topics_covered', this)">Topics Covered</button>
                    <?php endif; ?>

                    <?php if (!empty($selected_partners) && is_array($selected_partners)): ?>
                        <button
                            class="og-event-tab text-truncate dark:bg-gray-900 dark:text-white dark:border-gray-700 w-100"
                            onclick="openPage('partners', this)">Partners</button>
                    <?php endif; ?>

                    <?php if (!empty($events_data['special_events'])): ?>
                        <?php foreach ($events_data['special_events'] as $special_event): ?>
                            <button
                                class="og-event-tab text-truncate dark:bg-gray-900 dark:text-white dark:border-gray-700 w-100"
                                onclick="openPage('<?php echo str_replace(" ", "-", $special_event['title']); ?>', this)">
                                <?php if (!empty($special_event['title'])): ?>
                                    <?php echo $special_event['title']; ?>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

                <div class="event-content">

                    <div id="details" class="og-event-tab-content">

                        <div class="post-content panel fs-6 md:fs-5">
                            <?php the_content(); ?>
                        </div>

                        <!-- Testimonials Section -->
                        <?php if (!empty($events_data['testimonials'])): ?>
                            <div class="testimonials-section mt-2">
                                <h4 class="h5">Testimonials</h4>
                                <div
                                    class="testimonials-list row child-cols-12 sm:child-cols-6 md:child-cols-4 col-match gy-3 gx-2 h-100">
                                    <?php foreach ($events_data['testimonials'] as $testimonial): ?>
                                        <div>
                                            <div
                                                class="testimonial border border-gray-900 border-opacity-15 py-2 px-2 rounded-1 shadow-xs dark:bg-gray-900 dark:text-white">
                                                <?php if (!empty($testimonial['image'])): ?>
                                                    <div class="testimonial-image">
                                                        <img src="<?php echo $testimonial['image']; ?>"
                                                            alt="<?php echo $testimonial['author']; ?>"
                                                            class="media-cover image uc-transition-opaque"
                                                            data-uc-img="loading: lazy">
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($testimonial['content'])): ?>
                                                    <div class="testimonial-content">

                                                        <i class="icon icon-2 unicon-quotes"></i>
                                                        <?php echo $testimonial['content']; ?>

                                                    </div>

                                                <?php endif; ?>

                                                <?php if (!empty($testimonial['author'])): ?>
                                                    <div class="author">
                                                        <p class="fw-bold m-0 mt-2">- <?php echo $testimonial['author']; ?></p>

                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div id="speakers" class="og-event-tab-content">

                        <!-- Speakers Section -->
                        <?php if (!empty($events_data['speakers'])): ?>
                            <div class="speakers-section panel">
                                <h4 class="h4">
                                    <?php echo !empty($events_data['speakers_heading']) ? $events_data['speakers_heading'] : 'Meet Our Distinguished Speakers'; ?>
                                </h4>

                                <div
                                    class="speakers-list row child-cols-12 sm:child-cols-6 md:child-cols-4 col-match gy-3 gx-2 h-100">
                                    <?php foreach ($events_data['speakers'] as $speaker): ?>
                                        <div class="speaker position-relative overflow-hidden ">
                                            <?php if (!empty($speaker['image'])): ?>
                                                <div class="speaker-image align-items-center">
                                                    <div class="ratio ratio-1x1 overflow-hidden">
                                                        <img src="<?php echo $speaker['image']; ?>"
                                                            alt="<?php echo $speaker['name']; ?>"
                                                            class="media-cover image uc-transition-opaque"
                                                            data-uc-img="loading: lazy">

                                                    </div>
                                                </div>

                                            <?php endif; ?>

                                            <div class="speaker-content vstack gap-narrow position-absolute bottom-0 start-0 end-0 p-2 text-white overflow-hidden"
                                                style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.9));margin: 0 8px;">
                                                <?php if (!empty($speaker['name'])): ?>
                                                    <h4 class="h5 text-white m-0 mt-2"><?php echo $speaker['name']; ?></h4>
                                                <?php endif; ?>

                                                <?php if (!empty($speaker['designation'])): ?>

                                                    <div class="fs-7">
                                                        <?php echo $speaker['designation']; ?>
                                                    </div>

                                                <?php endif; ?>

                                                <?php if (!empty($speaker['organization'])): ?>

                                                    <div class="fw-bold fs-6">
                                                        <?php echo $speaker['organization']; ?>
                                                    </div>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div id="who_should_attend" class="og-event-tab-content">
                        <!-- Who Should Attend Section -->
                        <?php if (!empty($events_data['who_should_attend'])): ?>
                            <div class="who-should-attend">
                                <h4 class="h4">Who Should Attend</h4>
                                <div class="row child-cols-12 md:child-cols-6 col-match g-2 gy-2">

                                    <?php foreach ($events_data['who_should_attend'] as $item): ?>
                                        <div>
                                            <div
                                                class="hstack gap-narrow align-items-center dark:bg-gray-900 dark:text-white p-1 border border-gray-900 border-opacity-15 rounded-1 shadow-xs">
                                                <i class="unicon-caret-right icon-3"></i>
                                                <div><?php echo $item; ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="topics_covered" class="og-event-tab-content">
                        <!-- Topics Covered Section -->
                        <?php if (!empty($events_data['topics_covered'])): ?>
                            <div class="topics-section">
                                <h4 class="h4">Topics Covered</h4>
                                <div class="row child-cols-12 md:child-cols-6 lg:child-cols-4 col-match g-2 gy-2">
                                    <?php foreach ($events_data['topics_covered'] as $topic): ?>
                                        <div>
                                            <div
                                                class="hstack gap-1 border py-2 px-2 rounded-1 shadow-xs dark:bg-gray-900 dark:text-white">
                                                <i class="icon icon-2 unicon-checkbox-checked-filled"></i><?php echo $topic; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="partners" class="og-event-tab-content">

                        <!-- Partners Section -->
                        <?php if (!empty($selected_partners) && is_array($selected_partners)): ?>
                            <div class="partners-section panel">
                                <h4 class="h5"><?php _e('In Collaboration With', 'opengovasia'); ?></h4>

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
                                                                        class="text-none hover:text-primary mx-1" target="_blank"
                                                                        rel="noopener">
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

                    <!-- Special Events Sections -->
                    <?php if (!empty($events_data['special_events'])): ?>
                        <div class="special-events-section">
                            <?php foreach ($events_data['special_events'] as $special_event): ?>

                                <div id="<?php echo str_replace(" ", "-", $special_event['title']); ?>"
                                    class="og-event-tab-content special-event">
                                    <?php if (!empty($special_event['heading'])): ?>
                                        <h4 class="h4"><?php echo $special_event['heading']; ?></h4>
                                    <?php endif; ?>

                                    <?php if (!empty($special_event['video_url'])): ?>
                                        <div class="video-container mb-2" style="aspect-ratio: 16 / 9;">

                                            <iframe src="<?php echo $special_event['video_url']; ?>"
                                                style='height: 100%; width: 100%;' frameborder="0" scrolling="no" loading="lazy"
                                                allowfullscreen></iframe>

                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($special_event['content'])): ?>
                                        <div class="post-content">
                                            <?php echo wpautop($special_event['content']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>

                <script>
                    function openPage(pageName, elmnt) {
                        var i, og_event_tab_content, og_event_tabs;

                        og_event_tab_content = document.getElementsByClassName("og-event-tab-content");
                        for (i = 0; i < og_event_tab_content.length; i++) {
                            og_event_tab_content[i].style.display = "none";
                        }

                        og_event_tabs = document.getElementsByClassName("og-event-tab");
                        for (i = 0; i < og_event_tabs.length; i++) {
                            og_event_tabs[i].classList.remove("active", "bg-primary", "text-white");
                        }

                        document.getElementById(pageName).style.display = "block";

                        elmnt.classList.add("uc-active", "bg-primary", "text-white");
                    }
                    document.getElementById("defaultOpen").click();
                </script>

            </div>

            <div class="post-footer panel vstack sm:hstack gap-3 justify-between pt-3 mt-4">
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