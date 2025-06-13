<?php
/**
 * Template part for displaying events in company pages.
 *
 * @package OpenGovAsia
 */
if (!defined('ABSPATH')) {
    exit;
}

$event = !empty(isset($args['event'])) ? $args['event'] : $event;
if (!is_object($event) || !isset($event->ID)) {
    return; // Ensure $event is a valid object with an ID
}

?>

<div>
    <article class="post type-post panel vstack gap-2">
        <div class="post-image panel overflow-hidden">
            <figure
                class="featured-image m-0 ratio rounded ratio-16x9 uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                <?php if (has_post_thumbnail($event->ID)): ?>
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                        src="<?php echo get_the_post_thumbnail_url($event->ID, 'full'); ?>" alt="<?php echo esc_html($event->post_title); ?>">
                <?php else: ?>
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                        src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img-fallback.png"
                        alt="No Image Available">
                <?php endif; ?>
                <a href="<?php the_permalink($event->ID); ?>" class="position-cover" data-caption="<?php echo esc_html($event->post_title); ?>"></a>

            </figure>

            <div
                class="has-video-overlay rounded position-absolute top-0 end-0 w-150px h-150px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
            </div>

            <span class="cstack position-absolute top-0 end-0 fs-6 w-40px h-40px text-white">
                <i class="icon-narrow unicon-calendar"></i>
            </span>

        </div>
        <div class="post-header panel vstack gap-1 lg:gap-2 text-center">
            <h3 class="post-title h6 lg:h5 m-0 text-truncate-2 mb-1">
                <a class="text-none"
                    href="<?php the_permalink($event->ID); ?>"><?php echo esc_html($event->post_title); ?></a>
            </h3>
            <div>
                <div
                    class="post-meta panel hstack justify-center fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 md:d-flex">
                    <div class="meta">
                        <div class="hstack gap-2">

                            <?php
                            $events_data = get_custom_meta($event->ID);
                            $event_date = date('F j, Y', strtotime($events_data['event_date'] ?? get_the_date('F j, Y', $event->ID)));
                            $start = format_event_time($events_data['event_start_time'] ?? '');
                            $end = format_event_time($events_data['event_end_time'] ?? '');

                            ?>

                            <div class="event-time hstack gap-narrow">
                                <i class="icon-narrow unicon-time"></i>
                                <span>
                                    <?php
                                    if ($start && $end) {
                                        echo esc_html("$start - $end");
                                    } elseif ($start || $end) {
                                        echo esc_html($start ?: $end);
                                    }
                                    ?>
                                </span>
                            </div>

                            <div>
                                <div class="event-date text-none hstack gap-narrow">
                                    <i class="icon-narrow unicon-calendar"></i>
                                    <span>
                                        <?php echo esc_html($event_date); ?>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>