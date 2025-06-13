<?php

/**
 * Event Meta Box with Quill Editor
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

    $events_data = get_custom_meta($post->ID) ?: [];

    $event_date = esc_attr($events_data['event_date'] ?? '');
    $event_start_time = esc_attr($events_data['event_start_time'] ?? '');
    $event_end_time = esc_attr($events_data['event_end_time'] ?? '');
    $event_timezone = esc_attr($events_data['event_timezone'] ?? '');
    $event_address = esc_attr($events_data['event_address'] ?? '');
    $event_link = esc_url($events_data['event_link'] ?? '');
    $event_description = esc_textarea($events_data['event_description'] ?? '');
    $theme_color = esc_attr($events_data['theme_color'] ?? '#0c50a8');
    $speakers_heading = esc_attr($events_data['speakers_heading'] ?? '');
    $who_should_attend = $events_data['who_should_attend'] ?? '';
    $speakers = $events_data['speakers'] ?? [];
    $testimonials = $events_data['testimonials'] ?? [];
    $topics_covered = $events_data['topics_covered'] ?? [];
    $special_events = $events_data['special_events'] ?? [];

    ?>

    <!-- Include Quill CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>

    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">

        <h3>Event Details</h3>
        <table style="width:100%; border-spacing: 0 10px;">
            <tr>
                <td><strong>Event Date:</strong></td>
                <td>
                    <input type="date" name="events_data[event_date]"
                        value="<?php echo !empty($event_date) ? $event_date : date('Y-m-d'); ?>" style="width:100%;">
                </td>
            </tr>
            <tr>
                <td><strong>Start Time:</strong></td>
                <td>
                    <input type="time" name="events_data[event_start_time]"
                        value="<?php echo !empty($event_start_time) ? $event_start_time : '08:00'; ?>" style="width:100%;">
                </td>
            </tr>
            <tr>
                <td><strong>End Time:</strong></td>
                <td>
                    <input type="time" name="events_data[event_end_time]"
                        value="<?php echo !empty($event_end_time) ? $event_end_time : '11:10'; ?>" style="width:100%;">
                </td>
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

    <!-- Testimonials with Quill Editor -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
        <h3>Testimonials</h3>
        <div id="testimonials-list">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="testimonial-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">
                        <label><strong>Testimonial Content:</strong></label>
                        <div id="testimonial-editor-<?php echo $index; ?>" class="quill-editor"
                            style="height: 150px; margin-bottom: 10px;">
                            <?php echo wp_kses_post($testimonial['content'] ?? ''); ?>
                        </div>
                        <textarea name="events_data[testimonials][<?php echo $index; ?>][content]"
                            id="testimonial-content-<?php echo $index; ?>"
                            style="display: none;"><?php echo esc_textarea($testimonial['content'] ?? ''); ?></textarea>
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

        <!-- Multiple Content Editor Sections with Quill -->
        <div>
            <h4>Tab Sections</h4>
            <div id="special-events-list">
                <?php if (!empty($special_events)): ?>
                    <?php foreach ($special_events as $index => $event): ?>
                        <div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 10px;">
                            <input type="text" name="events_data[special_events][<?php echo $index; ?>][title]"
                                value="<?php echo esc_attr($event['title'] ?? ''); ?>" placeholder="Tab Title"
                                style="width:100%; margin-bottom:10px;">
                            <input type="text" name="events_data[special_events][<?php echo $index; ?>][heading]"
                                value="<?php echo esc_attr($event['heading'] ?? ''); ?>" placeholder="Tab Heading"
                                style="width:100%; margin-bottom:10px;">
                            <input type="url" name="events_data[special_events][<?php echo $index; ?>][video_url]"
                                value="<?php echo esc_url($event['video_url'] ?? ''); ?>" placeholder="Video URL"
                                style="width:100%; margin-bottom:10px;">

                            <label><strong>Content:</strong></label>
                            <div id="special-event-editor-<?php echo $index; ?>" class="quill-editor"
                                style="height: 200px; margin-bottom: 10px;">
                                <?php echo wp_kses_post($event['content'] ?? ''); ?>
                            </div>
                            <textarea name="events_data[special_events][<?php echo $index; ?>][content]"
                                id="special-event-content-<?php echo $index; ?>"
                                style="display: none;"><?php echo esc_textarea($event['content'] ?? ''); ?></textarea>

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

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            // Initialize Color Picker
            $('.color-picker').wpColorPicker();

            // Quill Editor Configuration
            var quillOptions = {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['blockquote', 'code-block'],
                            ['link', 'image'],
                            ['clean']
                        ],
                        handlers: {
                            'image': function () {
                                // 'this' refers to the toolbar, we need to get the quill instance
                                var quill = this.quill;
                                selectLocalImage(quill);
                            }
                        }
                    }
                }
            };

            // Custom image handler for WordPress Media Library
            function selectLocalImage(quill) {
                if (typeof wp !== 'undefined' && wp.media) {
                    var mediaFrame = wp.media({
                        title: 'Select Image',
                        button: { text: 'Insert Image' },
                        multiple: false,
                        library: { type: 'image' }
                    });

                    mediaFrame.on('select', function () {
                        var attachment = mediaFrame.state().get('selection').first().toJSON();
                        var range = quill.getSelection();
                        var index = range ? range.index : quill.getLength();

                        // Insert image at cursor position
                        quill.insertEmbed(index, 'image', attachment.url);

                        // Move cursor after the image
                        quill.setSelection(index + 1);

                        // Get the editor container ID to find corresponding textarea
                        var editorContainer = quill.container;
                        var editorId = editorContainer.id;
                        var textareaId = editorId.replace('-editor-', '-content-');

                        // Sync content with hidden textarea
                        var html = quill.root.innerHTML;
                        document.getElementById(textareaId).value = html;

                        // Also trigger using jQuery if available
                        if (typeof $ !== 'undefined') {
                            $('#' + textareaId).val(html).trigger('change');
                        }
                    });

                    mediaFrame.open();
                } else {
                    // Fallback to URL input if WordPress media is not available
                    var url = prompt('Enter image URL:');
                    if (url) {
                        var range = quill.getSelection();
                        var index = range ? range.index : quill.getLength();
                        quill.insertEmbed(index, 'image', url);
                        quill.setSelection(index + 1);

                        // Sync with textarea
                        var editorContainer = quill.container;
                        var editorId = editorContainer.id;
                        var textareaId = editorId.replace('-editor-', '-content-');
                        var html = quill.root.innerHTML;
                        document.getElementById(textareaId).value = html;

                        if (typeof $ !== 'undefined') {
                            $('#' + textareaId).val(html).trigger('change');
                        }
                    }
                }
            }

            // Initialize existing Quill editors
            var quillEditors = {};

            function initializeQuillEditor(editorId, textareaId) {
                if (document.getElementById(editorId) && !quillEditors[editorId]) {
                    var quill = new Quill('#' + editorId, quillOptions);
                    quillEditors[editorId] = quill;

                    // Sync content with hidden textarea on any change
                    quill.on('text-change', function () {
                        var html = quill.root.innerHTML;
                        $('#' + textareaId).val(html);
                    });

                    // Also sync on selection change (for image insertions)
                    quill.on('selection-change', function () {
                        var html = quill.root.innerHTML;
                        $('#' + textareaId).val(html);
                    });

                    return quill;
                }
                return quillEditors[editorId];
            }

            // Initialize existing testimonial editors
            $('.testimonial-item').each(function (index) {
                var editorId = 'testimonial-editor-' + index;
                var textareaId = 'testimonial-content-' + index;
                initializeQuillEditor(editorId, textareaId);
            });

            // Initialize existing special event editors
            $('.special-event-item').each(function (index) {
                var editorId = 'special-event-editor-' + index;
                var textareaId = 'special-event-content-' + index;
                initializeQuillEditor(editorId, textareaId);
            });

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
                $('#who-should-attend-list .who-should-attend-item').each(function (index) {
                    $(this).find('input').attr('name', 'events_data[who_should_attend][' + index + ']');
                });
            });

            // Testimonials functionality with Quill
            $('.add-testimonial').on('click', function () {
                var list = $('#testimonials-list');
                var index = list.children().length;
                var editorId = 'testimonial-editor-' + index;
                var textareaId = 'testimonial-content-' + index;

                var newItem = $('<div class="testimonial-item" style="margin-top:10px; border: 1px solid #eee; padding: 10px;">' +
                    '<label><strong>Testimonial Content:</strong></label>' +
                    '<div id="' + editorId + '" class="quill-editor" style="height: 150px; margin-bottom: 10px;"></div>' +
                    '<textarea name="events_data[testimonials][' + index + '][content]" id="' + textareaId + '" style="display: none;"></textarea>' +
                    '<input type="text" name="events_data[testimonials][' + index + '][author]" placeholder="Author Name" style="width:100%; margin-bottom:5px;">' +
                    '<div style="text-align: right;">' +
                    '<button type="button" class="remove-testimonial button button-secondary">Remove</button>' +
                    '</div>' +
                    '</div>');

                list.append(newItem);

                setTimeout(function () {
                    initializeQuillEditor(editorId, textareaId);
                }, 100);
            });

            $(document).on('click', '.remove-testimonial', function () {
                var item = $(this).closest('.testimonial-item');
                var editorId = item.find('.quill-editor').attr('id');

                // Remove Quill instance
                if (quillEditors[editorId]) {
                    delete quillEditors[editorId];
                }

                item.remove();

                // Reindex remaining testimonials
                $('#testimonials-list .testimonial-item').each(function (index) {
                    var oldEditorId = $(this).find('.quill-editor').attr('id');
                    var oldTextareaId = $(this).find('textarea').attr('id');
                    var newEditorId = 'testimonial-editor-' + index;
                    var newTextareaId = 'testimonial-content-' + index;

                    // Update IDs and names
                    $(this).find('.quill-editor').attr('id', newEditorId);
                    $(this).find('textarea').attr('id', newTextareaId);

                    $(this).find('textarea, input').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[testimonials\]\[\d+\]/, '[testimonials][' + index + ']'));
                        }
                    });

                    // Reinitialize Quill if ID changed
                    if (oldEditorId !== newEditorId) {
                        var content = '';
                        if (quillEditors[oldEditorId]) {
                            content = quillEditors[oldEditorId].root.innerHTML;
                            delete quillEditors[oldEditorId];
                        }

                        setTimeout(function () {
                            var quill = initializeQuillEditor(newEditorId, newTextareaId);
                            if (content && quill) {
                                quill.root.innerHTML = content;
                            }
                        }, 100);
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
                $('#topics-covered-list .topic-item').each(function (index) {
                    $(this).find('input').attr('name', 'events_data[topics_covered][' + index + ']');
                });
            });

            // Special Events Content Section functionality with Quill
            $('.add-special-event').on('click', function () {
                var list = $('#special-events-list');
                var index = list.children().length;
                var editorId = 'special-event-editor-' + index;
                var textareaId = 'special-event-content-' + index;

                var newItem = $('<div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 10px;">' +
                    '<input type="text" name="events_data[special_events][' + index + '][title]" placeholder="Tab Title" style="width:100%; margin-bottom:10px;">' +
                    '<input type="text" name="events_data[special_events][' + index + '][heading]" placeholder="Tab Heading" style="width:100%; margin-bottom:10px;">' +
                    '<input type="url" name="events_data[special_events][' + index + '][video_url]" placeholder="Video URL" style="width:100%; margin-bottom:10px;">' +
                    '<label><strong>Content:</strong></label>' +
                    '<div id="' + editorId + '" class="quill-editor" style="height: 200px; margin-bottom: 10px;"></div>' +
                    '<textarea name="events_data[special_events][' + index + '][content]" id="' + textareaId + '" style="display: none;"></textarea>' +
                    '<div style="text-align: right;">' +
                    '<button type="button" class="remove-special-event button button-secondary">Remove Tab</button>' +
                    '</div>' +
                    '</div>');

                list.append(newItem);

                setTimeout(function () {
                    initializeQuillEditor(editorId, textareaId);
                }, 100);
            });

            $(document).on('click', '.remove-special-event', function () {
                var item = $(this).closest('.special-event-item');
                var editorId = item.find('.quill-editor').attr('id');

                // Remove Quill instance
                if (quillEditors[editorId]) {
                    delete quillEditors[editorId];
                }

                item.remove();

                // Reindex remaining special events
                $('#special-events-list .special-event-item').each(function (index) {
                    var oldEditorId = $(this).find('.quill-editor').attr('id');
                    var oldTextareaId = $(this).find('textarea').attr('id');
                    var newEditorId = 'special-event-editor-' + index;
                    var newTextareaId = 'special-event-content-' + index;

                    // Update IDs and names
                    $(this).find('.quill-editor').attr('id', newEditorId);
                    $(this).find('textarea').attr('id', newTextareaId);

                    $(this).find('input, textarea').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[special_events\]\[\d+\]/, '[special_events][' + index + ']'));
                        }
                    });

                    // Reinitialize Quill if ID changed
                    if (oldEditorId !== newEditorId) {
                        var content = '';
                        if (quillEditors[oldEditorId]) {
                            content = quillEditors[oldEditorId].root.innerHTML;
                            delete quillEditors[oldEditorId];
                        }

                        setTimeout(function () {
                            var quill = initializeQuillEditor(newEditorId, newTextareaId);
                            if (content && quill) {
                                quill.root.innerHTML = content;
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

            // Form submission handler to sync Quill content
            $('form').on('submit', function () {
                // Sync all Quill editors content to textareas before form submission
                Object.keys(quillEditors).forEach(function (editorId) {
                    var quill = quillEditors[editorId];
                    var textareaId = editorId.replace('-editor-', '-content-');
                    var html = quill.root.innerHTML;
                    $('#' + textareaId).val(html);
                });
            });
        });
    </script>
    <?php
}

// Save Meta Box data
function save_event_details_meta_box($post_id)
{
    // Security checks
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    if (!wp_verify_nonce($_POST['event_details_nonce'] ?? '', 'save_event_details'))
        return;


    $post_type = get_post_type($post_id);
    if ($post_type !== 'events')
        return;

    // Check if events_data is posted
    if (!isset($_POST['events_data']) || !is_array($_POST['events_data']))
        return;

    $events_data = $_POST['events_data'];

    // Prepare data for saving
    $data_to_save = [];

    // Handle simple meta fields
    $simple_fields = [
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_timezone',
        'event_address',
        'event_link',
        'event_description',
        'theme_color',
        'speakers_heading'
    ];

    foreach ($simple_fields as $field) {
        if (isset($events_data[$field])) {
            $data_to_save[$field] = sanitize_text_field($events_data[$field]);
        }
    }

    // Handle URL field separately
    if (isset($events_data['event_link'])) {
        $data_to_save['event_link'] = esc_url_raw($events_data['event_link']);
    }

    // Handle textarea field separately  
    if (isset($events_data['event_description'])) {
        $data_to_save['event_description'] = sanitize_textarea_field($events_data['event_description']);
    }

    // Save simple meta fields first
    update_custom_meta($post_id, $data_to_save);

    // Handle complex relationship fields
    $relationship_data = [];

    // Handle speakers (complex array with multiple fields)
    if (isset($events_data['speakers']) && is_array($events_data['speakers'])) {
        $speakers = [];
        foreach ($events_data['speakers'] as $speaker_data) {
            if (is_array($speaker_data) && !empty(array_filter($speaker_data))) {
                $speakers[] = [
                    'name' => sanitize_text_field($speaker_data['name'] ?? ''),
                    'designation' => sanitize_text_field($speaker_data['designation'] ?? ''),
                    'organization' => sanitize_text_field($speaker_data['organization'] ?? ''),
                    'image' => esc_url_raw($speaker_data['image'] ?? '')
                ];
            }
        }
        $relationship_data['speakers'] = $speakers;
    }

    // Handle who_should_attend (simple array)
    if (isset($events_data['who_should_attend']) && is_array($events_data['who_should_attend'])) {
        $who_should_attend = [];
        foreach ($events_data['who_should_attend'] as $item) {
            if (!empty(trim($item))) {
                $who_should_attend[] = sanitize_text_field($item);
            }
        }
        $relationship_data['who_should_attend'] = $who_should_attend;
    }

    // Handle testimonials (complex array with content and author)
    if (isset($events_data['testimonials']) && is_array($events_data['testimonials'])) {
        $testimonials = [];
        foreach ($events_data['testimonials'] as $testimonial_data) {
            if (is_array($testimonial_data) && !empty(array_filter($testimonial_data))) {
                $testimonials[] = [
                    'content' => wp_kses_post($testimonial_data['content'] ?? ''),
                    'author' => sanitize_text_field($testimonial_data['author'] ?? '')
                ];
            }
        }
        $relationship_data['testimonials'] = $testimonials;
    }

    // Handle topics_covered (simple array)
    if (isset($events_data['topics_covered']) && is_array($events_data['topics_covered'])) {
        $topics_covered = [];
        foreach ($events_data['topics_covered'] as $topic) {
            if (!empty(trim($topic))) {
                $topics_covered[] = sanitize_text_field($topic);
            }
        }
        $relationship_data['topics_covered'] = $topics_covered;
    }

    // Handle special_events (complex array with multiple fields)
    if (isset($events_data['special_events']) && is_array($events_data['special_events'])) {
        $special_events = [];
        foreach ($events_data['special_events'] as $special_event_data) {
            if (is_array($special_event_data) && !empty(array_filter($special_event_data))) {
                $special_events[] = [
                    'title' => sanitize_text_field($special_event_data['title'] ?? ''),
                    'heading' => sanitize_text_field($special_event_data['heading'] ?? ''),
                    'video_url' => esc_url_raw($special_event_data['video_url'] ?? ''),
                    'content' => wp_kses_post($special_event_data['content'] ?? '')
                ];
            }
        }
        $relationship_data['special_events'] = $special_events;
    }

    // Save relationship data
    foreach ($relationship_data as $key => $value) {
        update_custom_meta($post_id, $key, $value);
    }
}

// Hook the function to save_post
add_action('save_post', 'save_event_details_meta_box', 10, 1);

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
        return get_custom_meta($object['id'], 'events_data', true);
    },
    'schema' => [
        'type' => 'object',
        'context' => ['view'],
        'description' => 'Event details from the custom meta box.',
    ]
]);

// Add custom columns to the events post type
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
    $events_data = get_custom_meta($post_id);

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