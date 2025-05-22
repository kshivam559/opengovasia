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
    global $post;
    $post_id = $post->ID ?? 0;
    $events_data = get_post_meta($post_id, 'events_data', true);

    // Safely get values with proper defaults
    $event_date = isset($events_data['event_date']) ? esc_attr($events_data['event_date']) : '';
    $event_start_time = isset($events_data['event_start_time']) ? esc_attr($events_data['event_start_time']) : '';
    $event_end_time = isset($events_data['event_end_time']) ? esc_attr($events_data['event_end_time']) : '';
    $event_timezone = isset($events_data['event_timezone']) ? esc_attr($events_data['event_timezone']) : '';
    $event_address = isset($events_data['event_address']) ? esc_attr($events_data['event_address']) : '';
    $event_link = isset($events_data['event_link']) ? esc_url($events_data['event_link']) : '';
    $event_description = isset($events_data['event_description']) ? esc_textarea($events_data['event_description']) : '';
    $theme_color = isset($events_data['theme_color']) ? esc_attr($events_data['theme_color']) : '#0c50a8';
    $who_should_attend = isset($events_data['who_should_attend']) && is_array($events_data['who_should_attend']) ? $events_data['who_should_attend'] : [];
    $speakers = isset($events_data['speakers']) && is_array($events_data['speakers']) ? $events_data['speakers'] : [];
    $speakers_heading = isset($events_data['speakers_heading']) ? esc_attr($events_data['speakers_heading']) : '';
    $testimonials = isset($events_data['testimonials']) && is_array($events_data['testimonials']) ? $events_data['testimonials'] : [];
    $topics_covered = isset($events_data['topics_covered']) && is_array($events_data['topics_covered']) ? $events_data['topics_covered'] : [];
    $special_events = isset($events_data['special_events']) && is_array($events_data['special_events']) ? $events_data['special_events'] : [];
    ?>
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Event Details</h3>
        <table style="width:100%; border-spacing: 0 10px;">
            <tr>
                <td style="width: 150px;"><strong>Event Date:</strong></td>
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
                            echo '<optgroup label="' . esc_attr($region_name) . '">';
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


    <!-- Meet Our Distinguished Speakers -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Speakers</h3>
        <div style="flex: 1; max-width: 99%; margin-bottom: 10px;">
            <p><strong>Speakers Tab Heading: (optional)</strong></p>
            <input type="text" name="events_data[speakers_heading]" value="<?php echo $speakers_heading; ?>"
                placeholder="Meet Our Distinguished Speakers" style="width:100%;">
        </div>
        <div id="speakers-list" style="display: flex; flex-wrap: wrap;">
            <?php if (!empty($speakers)): ?>
                <?php foreach ($speakers as $index => $speaker): ?>
                    <div class="speaker-item"
                        style="flex: 1 0 48%; padding: 15px; box-sizing: border-box; border: 1px solid #eee; margin: 5px; position: relative;">
                        <div style="display: flex; flex-wrap: wrap;">
                            <div style="flex: 1 0 70%;">
                                <input type="text" name="events_data[speakers][<?php echo $index; ?>][name]"
                                    value="<?php echo isset($speaker['name']) ? esc_attr($speaker['name']) : ''; ?>"
                                    placeholder="Name" style="width:100%; margin-bottom:5px;">
                                <input type="text" name="events_data[speakers][<?php echo $index; ?>][designation]"
                                    value="<?php echo isset($speaker['designation']) ? esc_attr($speaker['designation']) : ''; ?>"
                                    placeholder="Designation" style="width:100%; margin-bottom:5px;">
                                <input type="text" name="events_data[speakers][<?php echo $index; ?>][organization]"
                                    value="<?php echo isset($speaker['organization']) ? esc_attr($speaker['organization']) : ''; ?>"
                                    placeholder="Organization" style="width:100%; margin-bottom:5px;">
                                <input type="url" name="events_data[speakers][<?php echo $index; ?>][image]"
                                    value="<?php echo isset($speaker['image']) ? esc_url($speaker['image']) : ''; ?>"
                                    placeholder="Image URL" style="width:100%; margin-bottom:5px;" class="speaker-image-url">
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


    <!-- Testimonials -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Testimonials</h3>
        <div id="testimonials-list">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="testimonial-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">
                        <div class="wp-editor-container" style="margin-bottom:10px;">
                            <?php
                            $content = isset($testimonial['content']) ? $testimonial['content'] : '';
                            $editor_id = 'testimonial_content_' . $index;
                            wp_editor_fix($content, $editor_id, array(
                                'textarea_name' => 'events_data[testimonials][' . $index . '][content]',
                                'media_buttons' => false,
                                'tinymce' => true,
                                'textarea_rows' => 6,
                                'editor_height' => 150,
                                'teeny' => false,
                                'quicktags' => true,
                            ));
                            ?>
                        </div>
                        <input type="text" name="events_data[testimonials][<?php echo $index; ?>][author]"
                            value="<?php echo isset($testimonial['author']) ? esc_attr($testimonial['author']) : ''; ?>"
                            placeholder="Author Name" style="width:100%; margin-bottom:5px;">
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
            <h4>Tab Sections</h4>
            <div id="special-events-list">
                <?php if (!empty($special_events)): ?>
                    <?php foreach ($special_events as $index => $event): ?>
                        <div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 10px;">
                            <input type="text" name="events_data[special_events][<?php echo $index; ?>][title]"
                                value="<?php echo isset($event['title']) ? esc_attr($event['title']) : ''; ?>"
                                placeholder="Tab Title" style="width:100%; margin-bottom:10px;">
                            <input type="text" name="events_data[special_events][<?php echo $index; ?>][heading]"
                                value="<?php echo isset($event['heading']) ? esc_attr($event['heading']) : ''; ?>"
                                placeholder="Tab Heading" style="width:100%; margin-bottom:10px;">
                            <input type="url" name="events_data[special_events][<?php echo $index; ?>][video_url]"
                                value="<?php echo isset($event['video_url']) ? esc_url($event['video_url']) : ''; ?>"
                                placeholder="Video URL" style="width:100%; margin-bottom:10px;">

                            <div class="wp-editor-container" style="margin-bottom:10px;">
                                <?php
                                $content = isset($event['content']) ? $event['content'] : '';
                                $editor_id = 'special_event_content_' . $index;
                                wp_editor_fix($content, $editor_id, array(
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
                                <button type="button" class="remove-special-event button button-secondary">Remove Tab</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="add-special-event button button-primary" style="margin-top:10px;">+ Add Tab
                Section</button>
        </div>
    </div>

    <!-- Media Library Image Upload JS -->
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            // Initialize Color Picker
            if ($.fn.wpColorPicker) {
                $('.color-picker').wpColorPicker();
            }

            // Media Library Image Picker for Speakers
            $(document).on('click', '.choose-image', function (e) {
                e.preventDefault();
                var button = $(this);
                var speakerItem = button.closest('.speaker-item');
                var imageField = speakerItem.find('.speaker-image-url');
                var previewContainer = speakerItem.find('.speaker-image-preview');
                var rightColumn = speakerItem.find('div[style*="flex: 1 0 25%"]');

                if (!wp.media) {
                    console.error('WordPress media library not available');
                    return;
                }

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

            // Testimonials functionality
            $('.add-testimonial').on('click', function () {
                var list = $('#testimonials-list');
                var index = list.children().length;
                var editorId = 'testimonial_content_' + index;

                var newItem = $('<div class="testimonial-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">' +
                    '<div class="wp-editor-container" style="margin-bottom:10px;">' +
                    '<textarea id="' + editorId + '" name="events_data[testimonials][' + index + '][content]" rows="6" style="width:100%;"></textarea>' +
                    '</div>' +
                    '<input type="text" name="events_data[testimonials][' + index + '][author]" placeholder="Author Name" style="width:100%; margin-bottom:5px;">' +
                    '<div style="text-align: right;">' +
                    '<button type="button" class="remove-testimonial button button-secondary">Remove</button>' +
                    '</div>' +
                    '</div>');

                list.append(newItem);

                // Initialize the WP Editor with better error handling
                var initAttempts = 0;
                var maxAttempts = 20;

                var initEditor = function () {
                    initAttempts++;

                    if (document.getElementById(editorId) && wp.editor && typeof wp.editor.initialize === 'function') {
                        try {
                            wp.editor.initialize(editorId, {
                                tinymce: {
                                    wpautop: true,
                                    plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                    toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen wp_adv',
                                    toolbar2: 'strikethrough hr forecolor backcolor pastetext removeformat charmap outdent indent undo redo wp_help',
                                    setup: function (editor) {
                                        editor.on('init', function () {
                                            console.log('TinyMCE editor initialized for: ' + editorId);
                                        });
                                        editor.on('change', function () {
                                            editor.save();
                                        });
                                    }
                                },
                                quicktags: true,
                                mediaButtons: false,
                                editor_height: 150
                            });
                            console.log('Editor initialized successfully for: ' + editorId);
                        } catch (error) {
                            console.error('Error initializing editor:', error);
                        }
                    } else if (initAttempts < maxAttempts) {
                        setTimeout(initEditor, 100);
                    } else {
                        console.error('Failed to initialize editor after ' + maxAttempts + ' attempts');
                    }
                };

                setTimeout(initEditor, 100);
            });

            $(document).on('click', '.remove-testimonial', function () {
                var item = $(this).closest('.testimonial-item');
                var editorId = item.find('textarea').attr('id');

                // Save content before removing editor
                var content = '';
                if (editorId && wp.editor) {
                    try {
                        // Try to get content from TinyMCE first, then fallback to textarea
                        if (tinymce && tinymce.get(editorId)) {
                            content = tinymce.get(editorId).getContent();
                        } else {
                            content = $('#' + editorId).val();
                        }
                        wp.editor.remove(editorId);
                    } catch (error) {
                        console.error('Error removing editor:', error);
                        content = $('#' + editorId).val();
                    }
                }

                // Remove the DOM element
                item.remove();

                // Reindex the remaining testimonials and reinitialize editors
                $('#testimonials-list .testimonial-item').each(function (index) {
                    var oldTextarea = $(this).find('textarea');
                    var oldId = oldTextarea.attr('id');
                    var newId = 'testimonial_content_' + index;

                    // Update name attributes for all input fields
                    $(this).find('input, textarea').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[testimonials\]\[\d+\]/, '[testimonials][' + index + ']'));
                        }
                    });

                    // If the ID needs to be updated, reinitialize the editor
                    if (oldId !== newId && wp.editor) {
                        var existingContent = '';

                        try {
                            // Get existing content
                            if (tinymce && tinymce.get(oldId)) {
                                existingContent = tinymce.get(oldId).getContent();
                                wp.editor.remove(oldId);
                            } else {
                                existingContent = oldTextarea.val();
                            }
                        } catch (error) {
                            existingContent = oldTextarea.val();
                        }

                        // Update textarea ID
                        oldTextarea.attr('id', newId);

                        // Set content in textarea first
                        if (existingContent) {
                            oldTextarea.val(existingContent);
                        }

                        // Reinitialize editor
                        var reinitAttempts = 0;
                        var maxReinitAttempts = 10;

                        var reinitEditor = function () {
                            reinitAttempts++;

                            if (document.getElementById(newId) && wp.editor) {
                                try {
                                    wp.editor.initialize(newId, {
                                        tinymce: {
                                            wpautop: true,
                                            plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                            toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen wp_adv',
                                            toolbar2: 'strikethrough hr forecolor backcolor pastetext removeformat charmap outdent indent undo redo wp_help',
                                            setup: function (editor) {
                                                editor.on('init', function () {
                                                    if (existingContent) {
                                                        editor.setContent(existingContent);
                                                    }
                                                });
                                                editor.on('change', function () {
                                                    editor.save();
                                                });
                                            }
                                        },
                                        quicktags: true,
                                        mediaButtons: false,
                                        editor_height: 150
                                    });
                                } catch (error) {
                                    console.error('Error reinitializing editor:', error);
                                }
                            } else if (reinitAttempts < maxReinitAttempts) {
                                setTimeout(reinitEditor, 150);
                            }
                        };

                        setTimeout(reinitEditor, 200);
                    }
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
                    '<input type="text" name="events_data[special_events][' + index + '][title]" placeholder="Tab Title" style="width:100%; margin-bottom:10px;">' +
                    '<input type="text" name="events_data[special_events][' + index + '][heading]" placeholder="Tab Heading" style="width:100%; margin-bottom:10px;">' +
                    '<input type="url" name="events_data[special_events][' + index + '][video_url]" placeholder="Video URL" style="width:100%; margin-bottom:10px;">' +
                    '<div class="wp-editor-container" style="margin-bottom:10px;">' +
                    '<textarea id="' + editorId + '" name="events_data[special_events][' + index + '][content]" rows="10" style="width:100%;"></textarea>' +
                    '</div>' +
                    '<div style="text-align: right;">' +
                    '<button type="button" class="remove-special-event button button-secondary">Remove Tab</button>' +
                    '</div>' +
                    '</div>');

                list.append(newItem);

                // Initialize the WP Editor with better error handling
                var initAttempts = 0;
                var maxAttempts = 20;

                var initEditor = function () {
                    initAttempts++;

                    if (document.getElementById(editorId) && wp.editor && typeof wp.editor.initialize === 'function') {
                        try {
                            wp.editor.initialize(editorId, {
                                tinymce: {
                                    wpautop: true,
                                    plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                    toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen wp_adv',
                                    toolbar2: 'strikethrough hr forecolor backcolor pastetext removeformat charmap outdent indent undo redo wp_help',
                                    setup: function (editor) {
                                        editor.on('init', function () {
                                            console.log('TinyMCE editor initialized for: ' + editorId);
                                        });
                                        editor.on('change', function () {
                                            editor.save();
                                        });
                                    }
                                },
                                quicktags: true,
                                mediaButtons: true,
                                editor_height: 200
                            });
                            console.log('Editor initialized successfully for: ' + editorId);
                        } catch (error) {
                            console.error('Error initializing special event editor:', error);
                        }
                    } else if (initAttempts < maxAttempts) {
                        setTimeout(initEditor, 100);
                    } else {
                        console.error('Failed to initialize special event editor after ' + maxAttempts + ' attempts');
                    }
                };

                setTimeout(initEditor, 100);
            });

            $(document).on('click', '.remove-special-event', function () {
                var item = $(this).closest('.special-event-item');
                var editorId = item.find('textarea').attr('id');

                // Save content before removing editor
                var content = '';
                if (editorId && wp.editor) {
                    try {
                        // Try to get content from TinyMCE first, then fallback to textarea
                        if (tinymce && tinymce.get(editorId)) {
                            content = tinymce.get(editorId).getContent();
                        } else {
                            content = $('#' + editorId).val();
                        }
                        wp.editor.remove(editorId);
                    } catch (error) {
                        console.error('Error removing special event editor:', error);
                        content = $('#' + editorId).val();
                    }
                }

                // Remove the DOM element
                item.remove();

                // Reindex the remaining items and reinitialize editors
                $('#special-events-list .special-event-item').each(function (index) {
                    var oldTextarea = $(this).find('textarea');
                    var oldId = oldTextarea.attr('id');
                    var newId = 'special_event_content_' + index;

                    // Update name attributes for all input fields
                    $(this).find('input, textarea').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[special_events\]\[\d+\]/, '[special_events][' + index + ']'));
                        }
                    });

                    // If the ID needs to be updated, reinitialize the editor
                    if (oldId !== newId && wp.editor) {
                        var existingContent = '';

                        try {
                            // Get existing content
                            if (tinymce && tinymce.get(oldId)) {
                                existingContent = tinymce.get(oldId).getContent();
                                wp.editor.remove(oldId);
                            } else {
                                existingContent = oldTextarea.val();
                            }
                        } catch (error) {
                            existingContent = oldTextarea.val();
                        }

                        // Update textarea ID
                        oldTextarea.attr('id', newId);

                        // Set content in textarea first
                        if (existingContent) {
                            oldTextarea.val(existingContent);
                        }

                        // Reinitialize editor
                        var reinitAttempts = 0;
                        var maxReinitAttempts = 10;

                        var reinitEditor = function () {
                            reinitAttempts++;

                            if (document.getElementById(newId) && wp.editor) {
                                try {
                                    wp.editor.initialize(newId, {
                                        tinymce: {
                                            wpautop: true,
                                            plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                            toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen wp_adv',
                                            toolbar2: 'strikethrough hr forecolor backcolor pastetext removeformat charmap outdent indent undo redo wp_help',
                                            setup: function (editor) {
                                                editor.on('init', function () {
                                                    if (existingContent) {
                                                        editor.setContent(existingContent);
                                                    }
                                                });
                                                editor.on('change', function () {
                                                    editor.save();
                                                });
                                            }
                                        },
                                        quicktags: true,
                                        mediaButtons: true,
                                        editor_height: 200
                                    });
                                } catch (error) {
                                    console.error('Error reinitializing special event editor:', error);
                                }
                            } else if (reinitAttempts < maxReinitAttempts) {
                                setTimeout(reinitEditor, 150);
                            }
                        };

                        setTimeout(reinitEditor, 200);
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
                } else {
                    container.remove();
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

    // Check if this is the correct post type
    if (get_post_type($post_id) !== 'events') {
        return;
    }

    // Get form data
    if (isset($_POST['events_data'])) {
        $events_data = $_POST['events_data'];

        // Sanitize the data before saving
        $sanitized_data = array();

        // Basic fields
        $sanitized_data['event_date'] = sanitize_text_field($events_data['event_date'] ?? '');
        $sanitized_data['event_start_time'] = sanitize_text_field($events_data['event_start_time'] ?? '');
        $sanitized_data['event_end_time'] = sanitize_text_field($events_data['event_end_time'] ?? '');
        $sanitized_data['event_timezone'] = sanitize_text_field($events_data['event_timezone'] ?? '');
        $sanitized_data['event_address'] = sanitize_text_field($events_data['event_address'] ?? '');
        $sanitized_data['event_link'] = esc_url_raw($events_data['event_link'] ?? '');
        $sanitized_data['event_description'] = sanitize_textarea_field($events_data['event_description'] ?? '');
        $sanitized_data['theme_color'] = sanitize_hex_color($events_data['theme_color'] ?? '#0073aa');
        $sanitized_data['speakers_heading'] = sanitize_text_field($events_data['speakers_heading'] ?? '');

        // Who Should Attend
        if (isset($events_data['who_should_attend']) && is_array($events_data['who_should_attend'])) {
            $sanitized_data['who_should_attend'] = array_map('sanitize_text_field', array_filter($events_data['who_should_attend']));
        } else {
            $sanitized_data['who_should_attend'] = [];
        }

        // Speakers
        if (isset($events_data['speakers']) && is_array($events_data['speakers'])) {
            $sanitized_data['speakers'] = [];
            foreach ($events_data['speakers'] as $key => $speaker) {
                if (is_array($speaker)) {
                    $sanitized_data['speakers'][$key] = [
                        'name' => sanitize_text_field($speaker['name'] ?? ''),
                        'designation' => sanitize_text_field($speaker['designation'] ?? ''),
                        'organization' => sanitize_text_field($speaker['organization'] ?? ''),
                        'image' => esc_url_raw($speaker['image'] ?? '')
                    ];
                }
            }
        } else {
            $sanitized_data['speakers'] = [];
        }

        // Testimonials
        if (isset($events_data['testimonials']) && is_array($events_data['testimonials'])) {
            $sanitized_data['testimonials'] = [];
            foreach ($events_data['testimonials'] as $key => $testimonial) {
                if (is_array($testimonial)) {
                    $sanitized_data['testimonials'][$key] = [
                        'content' => wp_kses_post($testimonial['content'] ?? ''),
                        'author' => sanitize_text_field($testimonial['author'] ?? '')
                    ];
                }
            }
        } else {
            $sanitized_data['testimonials'] = [];
        }

        // Topics Covered
        if (isset($events_data['topics_covered']) && is_array($events_data['topics_covered'])) {
            $sanitized_data['topics_covered'] = array_map('sanitize_text_field', array_filter($events_data['topics_covered']));
        } else {
            $sanitized_data['topics_covered'] = [];
        }

        // Special Events
        if (isset($events_data['special_events']) && is_array($events_data['special_events'])) {
            $sanitized_data['special_events'] = [];
            foreach ($events_data['special_events'] as $key => $event) {
                if (is_array($event)) {
                    $sanitized_data['special_events'][$key] = [
                        'title' => sanitize_text_field($event['title'] ?? ''),
                        'heading' => sanitize_text_field($event['heading'] ?? ''),
                        'video_url' => esc_url_raw($event['video_url'] ?? ''),
                        'content' => wp_kses_post($event['content'] ?? '')
                    ];
                }
            }
        } else {
            $sanitized_data['special_events'] = [];
        }

        // Save the data
        update_post_meta($post_id, 'events_data', $sanitized_data);

        // IMPORTANT: Also save event_date separately for efficient querying
        if (!empty($sanitized_data['event_date'])) {
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

    // Enqueue editor scripts for dynamic editors
    wp_enqueue_editor();
}
add_action('admin_enqueue_scripts', 'event_meta_box_scripts');

// Register REST API field for events_data
if (function_exists('register_rest_field')) {
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
}

/*
 * Add custom columns to the events post type for displaying event date, start time, and end time
 */
function add_event_columns($columns)
{
    // Insert event columns after title
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['event_date'] = 'Event Date';
            $new_columns['event_start_time'] = 'Start Time';
            $new_columns['event_end_time'] = 'End Time';
        }
    }
    return $new_columns;
}
add_filter('manage_events_posts_columns', 'add_event_columns');

function display_event_columns($column, $post_id)
{
    $events_data = get_post_meta($post_id, 'events_data', true);

    switch ($column) {
        case 'event_date':
            echo !empty($events_data['event_date']) ? esc_html(date('M j, Y', strtotime($events_data['event_date']))) : 'N/A';
            break;
        case 'event_start_time':
            echo !empty($events_data['event_start_time']) ? esc_html(date('g:i A', strtotime($events_data['event_start_time']))) : 'N/A';
            break;
        case 'event_end_time':
            echo !empty($events_data['event_end_time']) ? esc_html(date('g:i A', strtotime($events_data['event_end_time']))) : 'N/A';
            break;
    }
}
add_action('manage_events_posts_custom_column', 'display_event_columns', 10, 2);

// Make event columns sortable
function make_event_columns_sortable($columns)
{
    $columns['event_date'] = 'event_date';
    return $columns;
}
add_filter('manage_edit-events_sortable_columns', 'make_event_columns_sortable');

// Handle sorting for event columns
function handle_event_column_sorting($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'event_date') {
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'handle_event_column_sorting');

// Custom editor fix function
function wp_editor_fix($content, $editor_id, $settings = array())
{
    ob_start();
    wp_editor($content, $editor_id, $settings);
    $editor_html = ob_get_clean();

    echo '<div id="' . esc_attr($editor_id) . '-container">';
    echo $editor_html;
    echo '</div>';

    // JavaScript initialization fix
    echo '<script>
    jQuery(document).ready(function($) {
        setTimeout(function() {
            if(typeof tinyMCE !== "undefined") {
                tinyMCE.execCommand("mceAddEditor", false, "' . esc_js($editor_id) . '");
                $("#" + "' . esc_js($editor_id) . '" + "-tmce").trigger("click");
            }
        }, 300);
    });
    </script>';
}