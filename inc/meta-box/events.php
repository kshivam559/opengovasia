<?php

/**
 * Event Meta Box
 *
 * This file adds a meta box to the 'events' post type for entering event details.
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add Meta Box for Event Details
function add_event_details_meta_box()
{
    add_meta_box(
        'event_details_meta_box',
        'Event Details',
        'display_event_details_meta_box',
        'events',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_event_details_meta_box');


// Display the Meta Box Fields
function display_event_details_meta_box($post)
{
    wp_nonce_field('save_event_details', 'event_details_nonce');
    $events_data = get_post_meta($post->ID, 'events_data', true);
    $event_date = esc_attr($events_data['event_date'] ?? '');
    $event_start_time = esc_attr($events_data['event_start_time'] ?? '');
    $event_end_time = esc_attr($events_data['event_end_time'] ?? '');
    $event_timezone = esc_attr($events_data['event_timezone'] ?? '');
    $event_address = esc_attr($events_data['event_address'] ?? '');
    $event_link = esc_url($events_data['event_link'] ?? '');
    $event_description = esc_textarea($events_data['event_description'] ?? '');
    $theme_color = esc_attr($events_data['theme_color'] ?? '#0c50a8');
    $who_should_attend = $events_data['who_should_attend'] ?? [];
    $attendees = $events_data['attendees'] ?? [];
    $speakers = $events_data['speakers'] ?? [];
    $partners = $events_data['partners'] ?? [];
    $testimonials = $events_data['testimonials'] ?? [];
    $topics_covered = $events_data['topics_covered'] ?? [];
    $special_events = $events_data['special_events'] ?? [];
    ?>
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Event Details</h3>
        <table style="width:100%; border-spacing: 0 10px;">
            <tr>
                <td><strong>Event Date:</strong></td>
                <td><input type="date" name="events_data[event_date]" value="<?php echo $event_date; ?>"
                        style="width:100%;"></td>
            </tr>
            <tr>
                <td><strong>Start Time:</strong></td>
                <td><input type="time" name="events_data[event_start_time]" value="<?php echo $event_start_time; ?>"
                        style="width:100%;"></td>
            </tr>
            <tr>
                <td><strong>End Time:</strong></td>
                <td><input type="time" name="events_data[event_end_time]" value="<?php echo $event_end_time; ?>"
                        style="width:100%;"></td>
            </tr>
            <tr>
                <td><strong>Time Zone:</strong></td>
                <td>
                    <select name="events_data[event_timezone]" style="width:100%;">
                        <option value="" <?php selected($event_timezone, ''); ?>>Select Time Zone</option>
                        <?php
                        $timezones = timezone_identifiers_list();
                        $current_offset = get_option('gmt_offset');
                        $tzstring = get_option('timezone_string');

                        // Add WordPress site's timezone as the first option
                        if (!empty($tzstring)) {
                            $selected = selected($event_timezone, $tzstring, false);
                            echo '<option value="' . esc_attr($tzstring) . '" ' . $selected . '>' . esc_html($tzstring) . ' (Site Default)</option>';
                        } elseif ($current_offset == 0) {
                            $selected = selected($event_timezone, 'UTC', false);
                            echo '<option value="UTC" ' . $selected . '>UTC (Site Default)</option>';
                        } else {
                            $offset_name = ($current_offset < 0) ? 'UTC' . $current_offset : 'UTC+' . $current_offset;
                            $selected = selected($event_timezone, $offset_name, false);
                            echo '<option value="' . esc_attr($offset_name) . '" ' . $selected . '>' . esc_html($offset_name) . ' (Site Default)</option>';
                        }

                        // Group timezones by continent
                        $timezone_regions = array(
                            'Africa' => DateTimeZone::AFRICA,
                            'America' => DateTimeZone::AMERICA,
                            'Antarctica' => DateTimeZone::ANTARCTICA,
                            'Arctic' => DateTimeZone::ARCTIC,
                            'Asia' => DateTimeZone::ASIA,
                            'Atlantic' => DateTimeZone::ATLANTIC,
                            'Australia' => DateTimeZone::AUSTRALIA,
                            'Europe' => DateTimeZone::EUROPE,
                            'Indian' => DateTimeZone::INDIAN,
                            'Pacific' => DateTimeZone::PACIFIC
                        );

                        foreach ($timezone_regions as $region_name => $region) {
                            echo '<optgroup label="' . $region_name . '">';
                            foreach ($timezones as $timezone) {
                                if (strpos($timezone, $region_name . '/') !== false) {
                                    $selected = selected($event_timezone, $timezone, false);
                                    echo '<option value="' . esc_attr($timezone) . '" ' . $selected . '>' . esc_html(str_replace('_', ' ', $timezone)) . '</option>';
                                }
                            }
                            echo '</optgroup>';
                        }

                        // Add UTC
                        $selected = selected($event_timezone, 'UTC', false);
                        echo '<option value="UTC" ' . $selected . '>UTC</option>';
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Event Address:</strong></td>
                <td><input type="text" name="events_data[event_address]" value="<?php echo $event_address; ?>"
                        style="width:100%;"></td>
            </tr>
            <tr>
                <td><strong>Event Link (Reserve a Seat):</strong></td>
                <td><input type="url" name="events_data[event_link]" value="<?php echo $event_link; ?>" style="width:100%;">
                </td>
            </tr>
            <tr>
                <td><strong>Event Short Description:</strong></td>
                <td><textarea name="events_data[event_description]" rows="3"
                        style="width:100%;"><?php echo $event_description; ?></textarea></td>
            </tr>
            <tr>
                <td><strong>Theme Color:</strong></td>
                <td><input type="text" name="events_data[theme_color]" value="<?php echo $theme_color; ?>"
                        class="color-picker" data-default-color="#0c50a8"></td>
            </tr>
        </table>
    </div>

    <!-- Who Should Attend -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Who Should Attend</h3>
        <div id="who-should-attend-list">
            <?php if (!empty($who_should_attend)): ?>
                <?php foreach ($who_should_attend as $index => $item): ?>
                    <div class="who-should-attend-item" style="display: flex; margin-top:10px; justify-content: space-between;">
                        <input type="text" name="events_data[who_should_attend][<?php echo $index; ?>]"
                            value="<?php echo esc_attr($item); ?>" placeholder="Who Should Attend" style="flex: 1;">
                        <button type="button" class="remove-who-should-attend button button-secondary"
                            style="margin-left:10px;">Remove</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="add-who-should-attend button button-primary" style="margin-top:10px;">+ Add
            Item</button>
    </div>

    <!-- Meet Our Distinguished Speakers -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Meet Our Distinguished Speakers</h3>
        
        <div id="speakers-list" style="display: flex; flex-wrap: wrap;">
            <?php if (!empty($speakers)): ?>
                <?php foreach ($speakers as $index => $speaker): ?>
                    <div class="speaker-item"
                        style="flex: 1 0 48%; padding: 15px; box-sizing: border-box; border: 1px solid #eee; margin: 5px; position: relative;">
                        <div style="display: flex; flex-wrap: wrap;">
                            <div style="flex: 1 0 70%;">
                                <input type="text" name="events_data[speakers][<?php echo $index; ?>][name]"
                                    value="<?php echo esc_attr($speaker['name'] ?? ''); ?>" placeholder="Name"
                                    style="width:100%; margin-bottom:5px;">
                                <input type="text" name="events_data[speakers][<?php echo $index; ?>][designation]"
                                    value="<?php echo esc_attr($speaker['designation'] ?? ''); ?>" placeholder="Designation"
                                    style="width:100%; margin-bottom:5px;">
                                <input type="text" name="events_data[speakers][<?php echo $index; ?>][organization]"
                                    value="<?php echo esc_attr($speaker['organization'] ?? ''); ?>" placeholder="Organization"
                                    style="width:100%; margin-bottom:5px;">
                                <input type="url" name="events_data[speakers][<?php echo $index; ?>][image]"
                                    value="<?php echo esc_url($speaker['image'] ?? ''); ?>" placeholder="Image URL"
                                    style="width:100%; margin-bottom:5px;" class="speaker-image-url">
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <button type="button" class="choose-image button button-secondary"
                                        data-index="<?php echo $index; ?>">Choose Image</button>
                                    <button type="button" class="remove-speaker button button-secondary">Remove Speaker</button>
                                </div>
                            </div>
                            <div style="flex: 1 0 25%; padding-left: 10px;">
                                <?php if (!empty($speaker['image'])): ?>
                                    <div class="speaker-image-preview" style="margin-bottom: 10px;">
                                        <img src="<?php echo esc_url($speaker['image']); ?>"
                                            style="max-width: 100%; height: auto; max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="add-speaker button button-primary" style="margin-top:10px;">+ Add Speaker</button>
    </div>


    <!-- In Collaboration With Partners -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>In Collaboration With</h3>
        
        <div id="partners-list">
            <?php if (!empty($partners)): ?>
                <?php foreach ($partners as $index => $partner): ?>
                    <div class="partner-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">
                        <div style="display: flex; flex-wrap: wrap;">
                            <div style="flex: 1 0 70%;">
                                <input type="text" name="events_data[partners][<?php echo $index; ?>][name]"
                                    value="<?php echo esc_attr($partner['name'] ?? ''); ?>" placeholder="Partner Name"
                                    style="width:100%; margin-bottom:5px;">
                                <textarea name="events_data[partners][<?php echo $index; ?>][info]"
                                    placeholder="Partner Description" rows="3"
                                    style="width:100%; margin-bottom:5px;"><?php echo esc_textarea($partner['info'] ?? ''); ?></textarea>
                                <input type="url" name="events_data[partners][<?php echo $index; ?>][logo]"
                                    value="<?php echo esc_url($partner['logo'] ?? ''); ?>" placeholder="Partner Logo URL"
                                    style="width:100%; margin-bottom:5px;" class="partner-logo-url">
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <button type="button" class="choose-partner-logo button button-secondary"
                                        data-index="<?php echo $index; ?>">Choose Logo</button>
                                    <button type="button" class="remove-partner button button-secondary">Remove Partner</button>
                                </div>
                            </div>
                            <div style="flex: 1 0 25%; padding-left: 10px;">
                                <?php if (!empty($partner['logo'])): ?>
                                    <div class="partner-logo-preview" style="margin-bottom: 10px;">
                                        <img src="<?php echo esc_url($partner['logo']); ?>"
                                            style="max-width: 100%; height: auto; max-height: 80px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="partner-socials">
                            <h4>Social Links</h4>
                            <?php
                            $socials = $partner['socials'] ?? [];
                            if (!empty($socials)) {
                                foreach ($socials as $social_index => $social) {
                                    ?>
                                    <div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">
                                        <select
                                            name="events_data[partners][<?php echo $index; ?>][socials][<?php echo $social_index; ?>][platform]"
                                            style="width: 120px; margin-right: 10px;">
                                            <option value="">Select Platform</option>
                                            <option value="facebook" <?php selected($social['platform'] ?? '', 'facebook'); ?>>Facebook
                                            </option>
                                            <option value="twitter" <?php selected($social['platform'] ?? '', 'twitter'); ?>>Twitter
                                            </option>
                                            <option value="linkedin" <?php selected($social['platform'] ?? '', 'linkedin'); ?>>LinkedIn
                                            </option>
                                            <option value="instagram" <?php selected($social['platform'] ?? '', 'instagram'); ?>>Instagram
                                            </option>
                                            <option value="youtube" <?php selected($social['platform'] ?? '', 'youtube'); ?>>YouTube
                                            </option>
                                            <option value="website" <?php selected($social['platform'] ?? '', 'website'); ?>>Website
                                            </option>
                                        </select>
                                        <input type="url"
                                            name="events_data[partners][<?php echo $index; ?>][socials][<?php echo $social_index; ?>][url]"
                                            value="<?php echo esc_url($social['url'] ?? ''); ?>" placeholder="Social URL" style="flex: 1;">
                                        <button type="button" class="remove-social button button-secondary"
                                            style="margin-left:10px;">Remove</button>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <button type="button" class="add-social button button-secondary"
                            data-partner-index="<?php echo $index; ?>">+ Add Social</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="add-partner button button-primary" style="margin-top:10px;">+ Add Partner</button>
    </div>

    <!-- Testimonials -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Testimonials</h3>
        <div id="testimonials-list">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="testimonial-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">
                        <textarea name="events_data[testimonials][<?php echo $index; ?>][content]" rows="3"
                            placeholder="Testimonial Content"
                            style="width:100%; margin-bottom:10px;"><?php echo esc_textarea($testimonial['content'] ?? ''); ?></textarea>
                        <input type="text" name="events_data[testimonials][<?php echo $index; ?>][author]"
                            value="<?php echo esc_attr($testimonial['author'] ?? ''); ?>" placeholder="Author Name"
                            style="width:100%; margin-bottom:5px;">
                        <div style="text-align: right;">
                            <button type="button" class="remove-testimonial button button-secondary">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="add-testimonial button button-primary" style="margin-top:10px;">+ Add
            Testimonial</button>
    </div>

    <!-- Special Events Field -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Special Events</h3>

        <!-- Topics Covered -->
        <div style="margin-bottom: 20px;">
            <h4>Topics Covered</h4>
            <div id="topics-covered-list">
                <?php if (!empty($topics_covered)): ?>
                    <?php foreach ($topics_covered as $index => $topic): ?>
                        <div class="topic-item" style="display: flex; margin-top:10px; justify-content: space-between;">
                            <input type="text" name="events_data[topics_covered][<?php echo $index; ?>]"
                                value="<?php echo esc_attr($topic); ?>" placeholder="Topic" style="flex: 1;">
                            <button type="button" class="remove-topic button button-secondary"
                                style="margin-left:10px;">Remove</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="add-topic button button-primary" style="margin-top:10px;">+ Add Topic</button>
        </div>

        <!-- Multiple Content Editor Sections -->
        <div>
            <h4>Content Sections</h4>
            <div id="special-events-list">
                <?php if (!empty($special_events)): ?>
                    <?php foreach ($special_events as $index => $event): ?>
                        <div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 10px;">
                            <input type="text" name="events_data[special_events][<?php echo $index; ?>][title]"
                                value="<?php echo esc_attr($event['title'] ?? ''); ?>" placeholder="Section Title"
                                style="width:100%; margin-bottom:10px;">
                            <input type="url" name="events_data[special_events][<?php echo $index; ?>][video_url]"
                                value="<?php echo esc_url($event['video_url'] ?? ''); ?>" placeholder="Video URL"
                                style="width:100%; margin-bottom:10px;">

                            <div class="wp-editor-container" style="margin-bottom:10px;">
                                <?php
                                $content = $event['content'] ?? '';
                                $editor_id = 'special_event_content_' . $index;
                                wp_editor($content, $editor_id, array(
                                    'textarea_name' => 'events_data[special_events][' . $index . '][content]',
                                    'media_buttons' => true,
                                    'tinymce' => true,
                                    'textarea_rows' => 10,
                                    'editor_height' => 200,
                                    'teeny' => false,
                                    'quicktags' => true,
                                ));
                                ?>
                            </div>

                            <div style="text-align: right;">
                                <button type="button" class="remove-special-event button button-secondary">Remove Section</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="add-special-event button button-primary" style="margin-top:10px;">+ Add Content
                Section</button>
        </div>
    </div>

    <!-- Media Library Image Upload JS -->
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            // Initialize Color Picker
            $('.color-picker').wpColorPicker();

            // Media Library Image Picker for Speakers
            $(document).on('click', '.choose-image', function (e) {
                e.preventDefault();
                var button = $(this);
                var speakerItem = button.closest('.speaker-item');
                var imageField = speakerItem.find('.speaker-image-url');
                var previewContainer = speakerItem.find('.speaker-image-preview');
                var rightColumn = speakerItem.find('div[style*="flex: 1 0 25%"]');

                var mediaFrame = wp.media({
                    title: 'Select Speaker Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });

                mediaFrame.on('select', function () {
                    var attachment = mediaFrame.state().get('selection').first().toJSON();
                    imageField.val(attachment.url);

                    // Update preview or create it if it doesn't exist
                    if (previewContainer.length === 0) {
                        $('<div class="speaker-image-preview" style="margin-bottom: 10px;"><img src="' + attachment.url + '" style="max-width: 100%; height: auto; max-height: 100px;"></div>').prependTo(rightColumn);
                    } else {
                        previewContainer.find('img').attr('src', attachment.url);
                    }
                });

                mediaFrame.open();
            });

            // Media Library Image Picker for Partner Logo
            $(document).on('click', '.choose-partner-logo', function (e) {
                e.preventDefault();
                var button = $(this);
                var partnerItem = button.closest('.partner-item');
                var logoField = partnerItem.find('.partner-logo-url');
                var previewContainer = partnerItem.find('.partner-logo-preview');
                var rightColumn = partnerItem.find('div[style*="flex: 1 0 25%"]');

                var mediaFrame = wp.media({
                    title: 'Select Partner Logo',
                    button: { text: 'Use this logo' },
                    multiple: false
                });

                mediaFrame.on('select', function () {
                    var attachment = mediaFrame.state().get('selection').first().toJSON();
                    logoField.val(attachment.url);

                    // Update preview or create it if it doesn't exist
                    if (previewContainer.length === 0) {
                        $('<div class="partner-logo-preview" style="margin-bottom: 10px;"><img src="' + attachment.url + '" style="max-width: 100%; height: auto; max-height: 80px;"></div>').prependTo(rightColumn);
                    } else {
                        previewContainer.find('img').attr('src', attachment.url);
                    }
                });

                mediaFrame.open();
            });

            // "Who Should Attend" functionality
            $('.add-who-should-attend').on('click', function () {
                var list = $('#who-should-attend-list');
                var index = list.children().length;
                var newItem = $('<div class="who-should-attend-item" style="display: flex; margin-top:10px; justify-content: space-between;">' +
                    '<input type="text" name="events_data[who_should_attend][' + index + ']" placeholder="Who Should Attend" style="flex: 1;">' +
                    '<button type="button" class="remove-who-should-attend button button-secondary" style="margin-left:10px;">Remove</button>' +
                    '</div>');
                list.append(newItem);
            });

            $(document).on('click', '.remove-who-should-attend', function () {
                $(this).closest('.who-should-attend-item').remove();
                // Reindex the remaining items
                $('#who-should-attend-list .who-should-attend-item').each(function (index) {
                    $(this).find('input').attr('name', 'events_data[who_should_attend][' + index + ']');
                });
            });

            // Add Speaker dynamically
            $('.add-speaker').on('click', function () {
                var speakersList = $('#speakers-list');
                var index = speakersList.children().length;
                var newSpeaker = $('<div class="speaker-item" style="flex: 1 0 48%; padding: 15px; box-sizing: border-box; border: 1px solid #eee; margin: 5px; position: relative;">' +
                    '<div style="display: flex; flex-wrap: wrap;">' +
                    '<div style="flex: 1 0 70%;">' +
                    '<input type="text" name="events_data[speakers][' + index + '][name]" placeholder="Name" style="width:100%; margin-bottom:5px;">' +
                    '<input type="text" name="events_data[speakers][' + index + '][designation]" placeholder="Designation" style="width:100%; margin-bottom:5px;">' +
                    '<input type="text" name="events_data[speakers][' + index + '][organization]" placeholder="Organization" style="width:100%; margin-bottom:5px;">' +
                    '<input type="url" name="events_data[speakers][' + index + '][image]" placeholder="Image URL" style="width:100%; margin-bottom:5px;" class="speaker-image-url">' +
                    '<div style="display: flex; gap: 10px; margin-bottom: 10px;">' +
                    '<button type="button" class="choose-image button button-secondary" data-index="' + index + '">Choose Image</button>' +
                    '<button type="button" class="remove-speaker button button-secondary">Remove Speaker</button>' +
                    '</div>' +
                    '</div>' +
                    '<div style="flex: 1 0 25%; padding-left: 10px;">' +
                    '</div>' +
                    '</div>' +
                    '</div>');
                speakersList.append(newSpeaker);
            });


            // Remove Speaker
            $(document).on('click', '.remove-speaker', function () {
                $(this).closest('.speaker-item').remove();
                // Reindex the remaining speakers
                $('#speakers-list .speaker-item').each(function (index) {
                    $(this).find('input').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[speakers\]\[\d+\]/, '[speakers][' + index + ']'));
                        }
                    });
                    $(this).find('.choose-image').attr('data-index', index);
                });
            });

            // Add Partner dynamically
            $('.add-partner').on('click', function () {
                var partnersList = $('#partners-list');
                var index = partnersList.children().length;
                var newPartner = $('<div class="partner-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">' +
                    '<div style="display: flex; flex-wrap: wrap;">' +
                    '<div style="flex: 1 0 70%;">' +
                    '<input type="text" name="events_data[partners][' + index + '][name]" placeholder="Partner Name" style="width:100%; margin-bottom:5px;">' +
                    '<textarea name="events_data[partners][' + index + '][info]" placeholder="Partner Description" rows="3" style="width:100%; margin-bottom:5px;"></textarea>' +
                    '<input type="url" name="events_data[partners][' + index + '][logo]" placeholder="Partner Logo URL" style="width:100%; margin-bottom:5px;" class="partner-logo-url">' +
                    '<div style="display: flex; gap: 10px; margin-bottom: 10px;">' +
                    '<button type="button" class="choose-partner-logo button button-secondary" data-index="' + index + '">Choose Logo</button>' +
                    '<button type="button" class="remove-partner button button-secondary">Remove Partner</button>' +
                    '</div>' +
                    '</div>' +
                    '<div style="flex: 1 0 25%; padding-left: 10px;">' +
                    '</div>' +
                    '</div>' +
                    '<div class="partner-socials">' +
                    '<h4>Social Links</h4>' +
                    '</div>' +
                    '<button type="button" class="add-social button button-secondary" data-partner-index="' + index + '">+ Add Social</button>' +
                    '</div>');
                partnersList.append(newPartner);
            });

            // Remove Partner
            $(document).on('click', '.remove-partner', function () {
                $(this).closest('.partner-item').remove();
                // Reindex the remaining partners
                $('#partners-list .partner-item').each(function (index) {
                    $(this).find('input').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[partners\]\[\d+\]/, '[partners][' + index + ']'));
                        }
                    });
                    $(this).find('.choose-partner-logo').attr('data-index', index);
                    $(this).find('.add-social').attr('data-partner-index', index);

                    // Update social indices
                    $(this).find('.social-item').each(function (social_index) {
                        $(this).find('select, input').each(function () {
                            var name = $(this).attr('name');
                            if (name) {
                                $(this).attr('name', name.replace(/\[partners\]\[\d+\]\[socials\]\[\d+\]/, '[partners][' + index + '][socials][' + social_index + ']'));
                            }
                        });
                    });
                });
            });

            // Add Social Link dynamically
            $(document).on('click', '.add-social', function () {
                var partnerIndex = $(this).data('partner-index');
                var socialsDiv = $(this).prev('.partner-socials');
                var socialIndex = socialsDiv.children('.social-item').length;

                var newSocial = $('<div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">' +
                    '<select name="events_data[partners][' + partnerIndex + '][socials][' + socialIndex + '][platform]" style="width: 120px; margin-right: 10px;">' +
                    '<option value="">Select Platform</option>' +
                    '<option value="facebook">Facebook</option>' +
                    '<option value="twitter">Twitter</option>' +
                    '<option value="linkedin">LinkedIn</option>' +
                    '<option value="instagram">Instagram</option>' +
                    '<option value="youtube">YouTube</option>' +
                    '<option value="website">Website</option>' +
                    '</select>' +
                    '<input type="url" name="events_data[partners][' + partnerIndex + '][socials][' + socialIndex + '][url]" placeholder="Social URL" style="flex: 1;">' +
                    '<button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>' +
                    '</div>');
                socialsDiv.append(newSocial);
            });

            // Remove Social Link
            $(document).on('click', '.remove-social', function () {
                var socialItem = $(this).closest('.social-item');
                var partnerItem = socialItem.closest('.partner-item');
                var partnerIndex = partnerItem.index();

                socialItem.remove();

                // Reindex the remaining socials
                partnerItem.find('.social-item').each(function (socialIndex) {
                    $(this).find('select, input').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[partners\]\[\d+\]\[socials\]\[\d+\]/, '[partners][' + partnerIndex + '][socials][' + socialIndex + ']'));
                        }
                    });
                });
            });

            // Testimonials functionality
            $('.add-testimonial').on('click', function () {
                var list = $('#testimonials-list');
                var index = list.children().length;
                var newItem = $('<div class="testimonial-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">' +
                    '<textarea name="events_data[testimonials][' + index + '][content]" rows="3" placeholder="Testimonial Content" style="width:100%; margin-bottom:10px;"></textarea>' +
                    '<input type="text" name="events_data[testimonials][' + index + '][author]" placeholder="Author Name" style="width:100%; margin-bottom:5px;">' +
                    '<div style="text-align: right;">' +
                    '<button type="button" class="remove-testimonial button button-secondary">Remove</button>' +
                    '</div>' +
                    '</div>');
                list.append(newItem);
            });

            $(document).on('click', '.remove-testimonial', function () {
                $(this).closest('.testimonial-item').remove();
                // Reindex the remaining testimonials
                $('#testimonials-list .testimonial-item').each(function (index) {
                    $(this).find('textarea, input').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[testimonials\]\[\d+\]/, '[testimonials][' + index + ']'));
                        }
                    });
                });
            });

            // Topics Covered functionality
            $('.add-topic').on('click', function () {
                var list = $('#topics-covered-list');
                var index = list.children().length;
                var newItem = $('<div class="topic-item" style="display: flex; margin-top:10px; justify-content: space-between;">' +
                    '<input type="text" name="events_data[topics_covered][' + index + ']" placeholder="Topic" style="flex: 1;">' +
                    '<button type="button" class="remove-topic button button-secondary" style="margin-left:10px;">Remove</button>' +
                    '</div>');
                list.append(newItem);
            });

            $(document).on('click', '.remove-topic', function () {
                $(this).closest('.topic-item').remove();
                // Reindex the remaining topics
                $('#topics-covered-list .topic-item').each(function (index) {
                    $(this).find('input').attr('name', 'events_data[topics_covered][' + index + ']');
                });
            });

            // Special Events Content Section functionality
            $('.add-special-event').on('click', function () {
                var list = $('#special-events-list');
                var index = list.children().length;
                var editorId = 'special_event_content_' + index;

                var newItem = $('<div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 10px;">' +
                    '<input type="text" name="events_data[special_events][' + index + '][title]" placeholder="Section Title" style="width:100%; margin-bottom:10px;">' +
                    '<input type="url" name="events_data[special_events][' + index + '][video_url]" placeholder="Video URL" style="width:100%; margin-bottom:10px;">' +
                    '<div class="wp-editor-container" style="margin-bottom:10px;">' +
                    '<textarea id="' + editorId + '" name="events_data[special_events][' + index + '][content]" rows="10" style="width:100%;"></textarea>' +
                    '</div>' +
                    '<div style="text-align: right;">' +
                    '<button type="button" class="remove-special-event button button-secondary">Remove Section</button>' +
                    '</div>' +
                    '</div>');

                list.append(newItem);

                // Initialize the WP Editor - need to use setTimeout to make sure the element is added to DOM first
                var initEditor = function () {
                    if (document.getElementById(editorId)) {
                        wp.editor.initialize(editorId, {
                            tinymce: {
                                wpautop: true,
                                plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen wp_adv',
                                toolbar2: 'strikethrough hr forecolor backcolor pastetext removeformat charmap outdent indent undo redo wp_help'
                            },
                            quicktags: true,
                            mediaButtons: true,
                            editor_height: 200
                        });
                    } else {
                        setTimeout(initEditor, 50); // Try again in 50ms
                    }
                };
                initEditor();
            });

            $(document).on('click', '.remove-special-event', function () {
                var item = $(this).closest('.special-event-item');
                var editorId = item.find('textarea').attr('id');

                // Remove the TinyMCE instance first
                if (editorId && wp.editor) {
                    wp.editor.remove(editorId);
                }

                // Then remove the DOM element
                item.remove();

                // Reindex the remaining items and reinitialize editors
                $('#special-events-list .special-event-item').each(function (index) {
                    var oldId = $(this).find('textarea').attr('id');
                    var newId = 'special_event_content_' + index;

                    // Update name attributes for all input fields
                    $(this).find('input, textarea').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[special_events\]\[\d+\]/, '[special_events][' + index + ']'));
                        }
                    });

                    // If the ID needs to be updated, reinitialize the editor
                    if (oldId !== newId) {
                        var content = wp.editor.getContent(oldId);
                        wp.editor.remove(oldId);
                        $(this).find('textarea').attr('id', newId);

                        setTimeout(function () {
                            wp.editor.initialize(newId, {
                                tinymce: true,
                                quicktags: true,
                                mediaButtons: true,
                                editor_height: 200
                            });
                            if (content) {
                                wp.editor.setContent(newId, content);
                            }
                        }, 100);
                    }
                });
            });

            // Update image previews when URL is manually changed
            $(document).on('change', '.speaker-image-url', function () {
                var url = $(this).val();
                var container = $(this).closest('.speaker-item').find('.speaker-image-preview');
                var rightColumn = $(this).closest('.speaker-item').find('div[style*="flex: 1 0 25%"]');

                if (url) {
                    if (container.length === 0) {
                        $('<div class="speaker-image-preview" style="margin-bottom: 10px;"><img src="' + url + '" style="max-width: 100%; height: auto; max-height: 100px;"></div>').prependTo(rightColumn);
                    } else {
                        container.find('img').attr('src', url);
                    }
                }
            });

            $(document).on('change', '.partner-logo-url', function () {
                var url = $(this).val();
                var container = $(this).closest('.partner-item').find('.partner-logo-preview');
                var rightColumn = $(this).closest('.partner-item').find('div[style*="flex: 1 0 25%"]');

                if (url) {
                    if (container.length === 0) {
                        $('<div class="partner-logo-preview" style="margin-bottom: 10px;"><img src="' + url + '" style="max-width: 100%; height: auto; max-height: 80px;"></div>').prependTo(rightColumn);
                    } else {
                        container.find('img').attr('src', url);
                    }
                }
            });
        });
    </script>
    <?php
}

// Save Meta Box data
function save_event_details_meta_box_data($post_id)
{
    // Check if our nonce is set and verify it
    if (!isset($_POST['event_details_nonce']) || !wp_verify_nonce($_POST['event_details_nonce'], 'save_event_details')) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Get form data
    if (isset($_POST['events_data'])) {
        $events_data = $_POST['events_data'];

        // Sanitize the data before saving

        // Basic fields
        $sanitized_data = array();
        $sanitized_data['event_date'] = sanitize_text_field($events_data['event_date'] ?? '');
        $sanitized_data['event_start_time'] = sanitize_text_field($events_data['event_start_time'] ?? '');
        $sanitized_data['event_end_time'] = sanitize_text_field($events_data['event_end_time'] ?? '');
        $sanitized_data['event_timezone'] = sanitize_text_field($events_data['event_timezone'] ?? '');
        $sanitized_data['event_address'] = sanitize_text_field($events_data['event_address'] ?? '');
        $sanitized_data['event_link'] = esc_url_raw($events_data['event_link'] ?? '');
        $sanitized_data['event_description'] = sanitize_textarea_field($events_data['event_description'] ?? '');
        $sanitized_data['theme_color'] = sanitize_hex_color($events_data['theme_color'] ?? '#0073aa');

        // Who Should Attend
        if (isset($events_data['who_should_attend']) && is_array($events_data['who_should_attend'])) {
            $sanitized_data['who_should_attend'] = array_map('sanitize_text_field', $events_data['who_should_attend']);
        }

        // Speakers
        if (isset($events_data['speakers']) && is_array($events_data['speakers'])) {
            foreach ($events_data['speakers'] as $key => $speaker) {
                $sanitized_data['speakers'][$key]['name'] = sanitize_text_field($speaker['name'] ?? '');
                $sanitized_data['speakers'][$key]['designation'] = sanitize_text_field($speaker['designation'] ?? '');
                $sanitized_data['speakers'][$key]['organization'] = sanitize_text_field($speaker['organization'] ?? '');
                $sanitized_data['speakers'][$key]['image'] = esc_url_raw($speaker['image'] ?? '');
            }
        }

        // Partners
        if (isset($events_data['partners']) && is_array($events_data['partners'])) {
            foreach ($events_data['partners'] as $key => $partner) {
                $sanitized_data['partners'][$key]['name'] = sanitize_text_field($partner['name'] ?? '');
                $sanitized_data['partners'][$key]['info'] = sanitize_text_field($partner['info'] ?? '');
                $sanitized_data['partners'][$key]['logo'] = esc_url_raw($partner['logo'] ?? '');

                // Partner Social Links
                if (isset($partner['socials']) && is_array($partner['socials'])) {
                    foreach ($partner['socials'] as $social_key => $social) {
                        $sanitized_data['partners'][$key]['socials'][$social_key]['platform'] = sanitize_text_field($social['platform'] ?? '');
                        $sanitized_data['partners'][$key]['socials'][$social_key]['url'] = esc_url_raw($social['url'] ?? '');
                    }
                }
            }
        }

        // Testimonials
        if (isset($events_data['testimonials']) && is_array($events_data['testimonials'])) {
            foreach ($events_data['testimonials'] as $key => $testimonial) {
                $sanitized_data['testimonials'][$key]['content'] = sanitize_textarea_field($testimonial['content'] ?? '');
                $sanitized_data['testimonials'][$key]['author'] = sanitize_text_field($testimonial['author'] ?? '');
            }
        }

        // Topics Covered
        if (isset($events_data['topics_covered']) && is_array($events_data['topics_covered'])) {
            $sanitized_data['topics_covered'] = array_map('sanitize_text_field', $events_data['topics_covered']);
        }

        // Special Events
        if (isset($events_data['special_events']) && is_array($events_data['special_events'])) {
            foreach ($events_data['special_events'] as $key => $event) {
                $sanitized_data['special_events'][$key]['title'] = sanitize_text_field($event['title'] ?? '');
                $sanitized_data['special_events'][$key]['video_url'] = esc_url_raw($event['video_url'] ?? '');
                $sanitized_data['special_events'][$key]['content'] = wp_kses_post($event['content'] ?? '');
            }
        }

        // Save the data
        update_post_meta($post_id, 'events_data', $sanitized_data);

        // IMPORTANT: Also save event_date separately for efficient querying
        if (isset($events_data['event_date']) && !empty($events_data['event_date'])) {
            update_post_meta($post_id, 'event_date', $sanitized_data['event_date']);
        }
    }
}
add_action('save_post', 'save_event_details_meta_box_data');

// Enqueue required scripts
function event_meta_box_scripts($hook)
{
    global $post;

    if (!$post || ($hook != 'post.php' && $hook != 'post-new.php') || get_post_type() != 'events') {
        return;
    }

    // Enqueue WordPress color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    // Enqueue WordPress media uploader
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'event_meta_box_scripts');


// Register REST API field for events_data

register_rest_field('events', 'events_data', [
    'get_callback' => function ($object) {
        return get_post_meta($object['id'], 'events_data', true);
    },
    'schema' => [
        'type' => 'object',
        'context' => ['view'],
        'description' => 'Event details from the custom meta box.',
    ]
]);

/*
 * Add custom columns to the events post type for displaying event date, start time, and end time
 */

function add_event_columns($columns)
{
    $columns['event_date'] = 'Event Date';
    $columns['event_start_time'] = 'Start Time';
    $columns['event_end_time'] = 'End Time';
    return $columns;
}
add_filter('manage_events_posts_columns', 'add_event_columns');

function display_event_columns($column, $post_id)
{
    $events_data = get_post_meta($post_id, 'events_data', true);

    if ($column === 'event_date') {
        echo !empty($events_data['event_date']) ? esc_html($events_data['event_date']) : 'N/A';
    }

    if ($column === 'event_start_time') {
        echo !empty($events_data['event_start_time']) ? esc_html($events_data['event_start_time']) : 'N/A';
    }

    if ($column === 'event_end_time') {
        echo !empty($events_data['event_end_time']) ? esc_html($events_data['event_end_time']) : 'N/A';
    }
}
add_action('manage_events_posts_custom_column', 'display_event_columns', 10, 2);