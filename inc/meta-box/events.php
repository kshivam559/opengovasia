<?php

/**
 * Event Meta Box - Optimized with Fixed wp_editor
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add Meta Box for Event Details
add_action('add_meta_boxes', function() {
    add_meta_box(
        'event_details_meta_box',
        'Event Details',
        'display_event_details_meta_box',
        'events',
        'normal',
        'high'
    );
});

// Display the Meta Box Fields
function display_event_details_meta_box($post)
{
    wp_nonce_field('save_event_details', 'event_details_nonce');
    $events_data = get_post_meta($post->ID, 'events_data', true);
    
    // Extract and sanitize data
    extract(array_map('esc_attr', [
        'event_date' => $events_data['event_date'] ?? date('Y-m-d'),
        'event_start_time' => $events_data['event_start_time'] ?? '08:00',
        'event_end_time' => $events_data['event_end_time'] ?? '11:10',
        'event_timezone' => $events_data['event_timezone'] ?? '',
        'event_address' => $events_data['event_address'] ?? '',
        'event_link' => $events_data['event_link'] ?? '',
        'event_description' => $events_data['event_description'] ?? '',
        'theme_color' => $events_data['theme_color'] ?? '#0c50a8',
        'speakers_heading' => $events_data['speakers_heading'] ?? ''
    ]));
    
    $who_should_attend = $events_data['who_should_attend'] ?? [];
    $speakers = $events_data['speakers'] ?? [];
    $testimonials = $events_data['testimonials'] ?? [];
    $topics_covered = $events_data['topics_covered'] ?? [];
    $special_events = $events_data['special_events'] ?? [];
    ?>
    
    <!-- Event Details -->
    <div class="meta-section">
        <h3>Event Details</h3>
        <table class="form-table">
            <tr><td><strong>Event Date:</strong></td><td><input type="date" name="events_data[event_date]" value="<?php echo $event_date; ?>" style="width:100%;"></td></tr>
            <tr><td><strong>Start Time:</strong></td><td><input type="time" name="events_data[event_start_time]" value="<?php echo $event_start_time; ?>" style="width:100%;"></td></tr>
            <tr><td><strong>End Time:</strong></td><td><input type="time" name="events_data[event_end_time]" value="<?php echo $event_end_time; ?>" style="width:100%;"></td></tr>
            <tr><td><strong>Time Zone:</strong></td><td><?php echo get_timezone_select($event_timezone); ?></td></tr>
            <tr><td><strong>Event Address:</strong></td><td><input type="text" name="events_data[event_address]" value="<?php echo $event_address; ?>" style="width:100%;"></td></tr>
            <tr><td><strong>Event Link:</strong></td><td><input type="url" name="events_data[event_link]" value="<?php echo esc_url($event_link); ?>" style="width:100%;"></td></tr>
            <tr><td><strong>Short Description:</strong></td><td><textarea name="events_data[event_description]" rows="3" style="width:100%;"><?php echo esc_textarea($event_description); ?></textarea></td></tr>
            <tr><td><strong>Theme Color:</strong></td><td><input type="text" name="events_data[theme_color]" value="<?php echo $theme_color; ?>" class="color-picker" data-default-color="#0c50a8"></td></tr>
        </table>
    </div>

    <!-- Speakers -->
    <div class="meta-section">
        <h3>Speakers</h3>
        <p><input type="text" name="events_data[speakers_heading]" value="<?php echo $speakers_heading; ?>" placeholder="Meet Our Distinguished Speakers" style="width:100%; margin-bottom:10px;"></p>
        <div id="speakers-list" style="display: flex; flex-wrap: wrap;">
            <?php foreach ($speakers as $index => $speaker): ?>
                <?php echo get_speaker_html($index, $speaker); ?>
            <?php endforeach; ?>
        </div>
        <button type="button" class="add-speaker button button-primary" style="margin-top: 10px;">+ Add Speaker</button>
    </div>

    <!-- Who Should Attend -->
    <div class="meta-section">
        <h3>Who Should Attend</h3>
        <div id="who-should-attend-list">
            <?php foreach ($who_should_attend as $index => $item): ?>
                <div class="list-item">
                    <input type="text" name="events_data[who_should_attend][<?php echo $index; ?>]" value="<?php echo esc_attr($item); ?>" placeholder="Who Should Attend" style="flex: 1;">
                    <button type="button" class="remove-item button button-secondary">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="add-who-should-attend button button-primary" style="margin-top: 10px;">+ Add Item</button>
    </div>

    <!-- Testimonials -->
    <div class="meta-section">
        <h3>Testimonials</h3>
        <div id="testimonials-list">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-item" style="margin-top:20px; border: 1px solid #eee; padding: 15px;">
                    <input type="text" name="events_data[testimonials][<?php echo $index; ?>][author]" value="<?php echo esc_attr($testimonial['author'] ?? ''); ?>" placeholder="Author Name" style="width:100%; margin-bottom:10px;">
                    
                    <div class="wp-editor-container">
                        <?php
                        wp_editor($testimonial['content'] ?? '', 'testimonial_content_' . $index, [
                            'textarea_name' => 'events_data[testimonials][' . $index . '][content]',
                            'media_buttons' => false,
                            'tinymce' => [
                                'toolbar1' => 'bold,italic,underline,link,unlink',
                                'toolbar2' => '',
                                'menubar' => false,
                                'statusbar' => false
                            ],
                            'textarea_rows' => 5,
                            'quicktags' => true
                        ]);
                        ?>
                    </div>
                    
                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="remove-testimonial button button-secondary">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="add-testimonial button button-primary" style="margin-top: 10px;">+ Add Testimonial</button>
    </div>

    <!-- Topics Covered -->
    <div class="meta-section">
        <h3>Topics Covered</h3>
        <div id="topics-covered-list">
            <?php foreach ($topics_covered as $index => $topic): ?>
                <div class="list-item">
                    <input type="text" name="events_data[topics_covered][<?php echo $index; ?>]" value="<?php echo esc_attr($topic); ?>" placeholder="Topic" style="flex: 1;">
                    <button type="button" class="remove-item button button-secondary">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="add-topic button button-primary" style="margin-top: 10px;">+ Add Topic</button>
    </div>

    <!-- Special Events/Tab Sections -->
    <div class="meta-section">
        <h3>Tab Sections</h3>
        <div id="special-events-list">
            <?php foreach ($special_events as $index => $event): ?>
                <div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 15px;">
                    <input type="text" name="events_data[special_events][<?php echo $index; ?>][title]" value="<?php echo esc_attr($event['title'] ?? ''); ?>" placeholder="Tab Title" style="width:100%; margin-bottom:10px;">
                    <input type="text" name="events_data[special_events][<?php echo $index; ?>][heading]" value="<?php echo esc_attr($event['heading'] ?? ''); ?>" placeholder="Tab Heading" style="width:100%; margin-bottom:10px;">
                    <input type="url" name="events_data[special_events][<?php echo $index; ?>][video_url]" value="<?php echo esc_url($event['video_url'] ?? ''); ?>" placeholder="Video URL" style="width:100%; margin-bottom:10px;">

                    <div class="wp-editor-container">
                        <?php
                        wp_editor($event['content'] ?? '', 'special_event_content_' . $index, [
                            'textarea_name' => 'events_data[special_events][' . $index . '][content]',
                            'media_buttons' => true,
                            'tinymce' => [
                                'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,fullscreen',
                                'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo',
                                'menubar' => false
                            ],
                            'textarea_rows' => 10,
                            'quicktags' => true
                        ]);
                        ?>
                    </div>

                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="remove-special-event button button-secondary">Remove Tab</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="add-special-event button button-primary">+ Add Tab Section</button>
    </div>

    <style>
    .meta-section { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; }
    .list-item { display: flex; margin-top: 10px; gap: 10px; align-items: center; }
    .speaker-item { flex: 1 0 48%; padding: 15px; box-sizing: border-box; border: 1px solid #eee; margin: 5px; }
    .speaker-grid { display: flex; gap: 10px; }
    .speaker-left { flex: 1; }
    .speaker-right { width: 120px; }
    .speaker-preview img { max-width: 100%; height: auto; max-height: 100px; }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Initialize Color Picker
        $('.color-picker').wpColorPicker();

        // Global counter for unique IDs
        let editorCounter = <?php echo max(count($testimonials), count($special_events)) + 1; ?>;

        // Helper function to safely remove editor
        function safeRemoveEditor(editorId) {
            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                tinymce.get(editorId).remove();
            }
            if (typeof quicktags !== 'undefined') {
                quicktags.instances[editorId] = null;
            }
        }

        // Helper function to initialize editor
        function initWpEditor(editorId, settings = {}) {
            const defaultSettings = {
                tinymce: {
                    toolbar1: 'bold,italic,underline,link,unlink',
                    toolbar2: '',
                    menubar: false,
                    statusbar: false,
                    plugins: 'wordpress,wplink,textcolor',
                    setup: function(editor) {
                        editor.on('init', function() {
                            console.log('Editor initialized:', editorId);
                        });
                    }
                },
                quicktags: true,
                mediaButtons: false
            };
            
            const finalSettings = $.extend(true, defaultSettings, settings);
            
            setTimeout(() => {
                if (document.getElementById(editorId)) {
                    wp.editor.initialize(editorId, finalSettings);
                }
            }, 100);
        }

        // Media Library for Speakers
        $(document).on('click', '.choose-image', function(e) {
            e.preventDefault();
            const button = $(this);
            const speakerItem = button.closest('.speaker-item');
            const imageField = speakerItem.find('.speaker-image-url');
            const preview = speakerItem.find('.speaker-preview');

            const mediaFrame = wp.media({
                title: 'Select Speaker Image',
                button: { text: 'Use this image' },
                multiple: false
            });

            mediaFrame.on('select', function() {
                const attachment = mediaFrame.state().get('selection').first().toJSON();
                imageField.val(attachment.url);
                preview.html(`<img src="${attachment.url}" style="max-width: 100%; height: auto; max-height: 100px;">`);
            });

            mediaFrame.open();
        });

        // Add Speaker
        $('.add-speaker').click(function() {
            const index = $('#speakers-list .speaker-item').length;
            $('#speakers-list').append(getSpeakerHtml(index));
        });

        // Remove Speaker
        $(document).on('click', '.remove-speaker', function() {
            $(this).closest('.speaker-item').remove();
            reindexItems('#speakers-list .speaker-item', 'speakers');
        });

        // Simple list items (Who Should Attend, Topics)
        $('.add-who-should-attend').click(() => addListItem('#who-should-attend-list', 'who_should_attend'));
        $('.add-topic').click(() => addListItem('#topics-covered-list', 'topics_covered'));
        
        $(document).on('click', '.remove-item', function() {
            const container = $(this).closest('.list-item').parent();
            $(this).closest('.list-item').remove();
            reindexListItems(container);
        });

        // Testimonials
        $('.add-testimonial').click(function() {
            const index = $('#testimonials-list .testimonial-item').length;
            const editorId = `testimonial_content_${editorCounter++}`;
            
            const html = `
                <div class="testimonial-item" style="margin-top:20px; border: 1px solid #eee; padding: 15px;">
                    <input type="text" name="events_data[testimonials][${index}][author]" placeholder="Author Name" style="width:100%; margin-bottom:10px;">
                    <div class="wp-editor-container">
                        <textarea id="${editorId}" name="events_data[testimonials][${index}][content]" rows="5" style="width:100%;"></textarea>
                    </div>
                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="remove-testimonial button button-secondary">Remove</button>
                    </div>
                </div>`;
            
            $('#testimonials-list').append(html);
            initWpEditor(editorId, { mediaButtons: false });
        });

        $(document).on('click', '.remove-testimonial', function() {
            const editorId = $(this).closest('.testimonial-item').find('textarea').attr('id');
            safeRemoveEditor(editorId);
            $(this).closest('.testimonial-item').remove();
            reindexItems('#testimonials-list .testimonial-item', 'testimonials');
        });

        // Special Events
        $('.add-special-event').click(function() {
            const index = $('#special-events-list .special-event-item').length;
            const editorId = `special_event_content_${editorCounter++}`;
            
            const html = `
                <div class="special-event-item" style="margin-top:20px; border: 1px solid #eee; padding: 15px;">
                    <input type="text" name="events_data[special_events][${index}][title]" placeholder="Tab Title" style="width:100%; margin-bottom:10px;">
                    <input type="text" name="events_data[special_events][${index}][heading]" placeholder="Tab Heading" style="width:100%; margin-bottom:10px;">
                    <input type="url" name="events_data[special_events][${index}][video_url]" placeholder="Video URL" style="width:100%; margin-bottom:10px;">
                    <div class="wp-editor-container">
                        <textarea id="${editorId}" name="events_data[special_events][${index}][content]" rows="10" style="width:100%;"></textarea>
                    </div>
                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="remove-special-event button button-secondary">Remove Tab</button>
                    </div>
                </div>`;
            
            $('#special-events-list').append(html);
            initWpEditor(editorId, {
                tinymce: {
                    toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,fullscreen',
                    toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo',
                    menubar: false
                },
                mediaButtons: true
            });
        });

        $(document).on('click', '.remove-special-event', function() {
            const editorId = $(this).closest('.special-event-item').find('textarea').attr('id');
            safeRemoveEditor(editorId);
            $(this).closest('.special-event-item').remove();
            reindexItems('#special-events-list .special-event-item', 'special_events');
        });

        // Helper functions
        function getSpeakerHtml(index) {
            return `<div class="speaker-item">
                <div class="speaker-grid">
                    <div class="speaker-left">
                        <input type="text" name="events_data[speakers][${index}][name]" placeholder="Name" style="width:100%; margin-bottom:5px;">
                        <input type="text" name="events_data[speakers][${index}][designation]" placeholder="Designation" style="width:100%; margin-bottom:5px;">
                        <input type="text" name="events_data[speakers][${index}][organization]" placeholder="Organization" style="width:100%; margin-bottom:5px;">
                        <input type="url" name="events_data[speakers][${index}][image]" placeholder="Image URL" style="width:100%; margin-bottom:5px;" class="speaker-image-url">
                        <button type="button" class="choose-image button button-secondary">Choose Image</button>
                        <button type="button" class="remove-speaker button button-secondary">Remove</button>
                    </div>
                    <div class="speaker-right">
                        <div class="speaker-preview"></div>
                    </div>
                </div>
            </div>`;
        }

        function addListItem(container, fieldName) {
            const index = $(container + ' .list-item').length;
            const html = `<div class="list-item">
                <input type="text" name="events_data[${fieldName}][${index}]" placeholder="${fieldName.replace('_', ' ')}" style="flex: 1;">
                <button type="button" class="remove-item button button-secondary">Remove</button>
            </div>`;
            $(container).append(html);
        }

        function reindexItems(selector, fieldName) {
            $(selector).each(function(index) {
                $(this).find('input, textarea').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[${fieldName}\]\[\d+\]/, `[${fieldName}][${index}]`));
                    }
                });
            });
        }

        function reindexListItems(container) {
            const fieldName = container.find('input').first().attr('name')?.match(/\[(\w+)\]/)?.[1];
            if (fieldName) {
                container.find('.list-item').each(function(index) {
                    $(this).find('input').attr('name', `events_data[${fieldName}][${index}]`);
                });
            }
        }

        // Update speaker image preview on URL change
        $(document).on('change', '.speaker-image-url', function() {
            const url = $(this).val();
            const preview = $(this).closest('.speaker-item').find('.speaker-preview');
            if (url) {
                preview.html(`<img src="${url}" style="max-width: 100%; height: auto; max-height: 100px;">`);
            }
        });
    });
    </script>
    <?php
}

// Helper function for speaker HTML
function get_speaker_html($index, $speaker) {
    $name = esc_attr($speaker['name'] ?? '');
    $designation = esc_attr($speaker['designation'] ?? '');
    $organization = esc_attr($speaker['organization'] ?? '');
    $image = esc_url($speaker['image'] ?? '');
    
    $preview = $image ? "<img src='$image' style='max-width: 100%; height: auto; max-height: 100px;'>" : '';
    
    return "<div class='speaker-item'>
        <div class='speaker-grid'>
            <div class='speaker-left'>
                <input type='text' name='events_data[speakers][$index][name]' value='$name' placeholder='Name' style='width:100%; margin-bottom:5px;'>
                <input type='text' name='events_data[speakers][$index][designation]' value='$designation' placeholder='Designation' style='width:100%; margin-bottom:5px;'>
                <input type='text' name='events_data[speakers][$index][organization]' value='$organization' placeholder='Organization' style='width:100%; margin-bottom:5px;'>
                <input type='url' name='events_data[speakers][$index][image]' value='$image' placeholder='Image URL' style='width:100%; margin-bottom:5px;' class='speaker-image-url'>
                <button type='button' class='choose-image button button-secondary'>Choose Image</button>
                <button type='button' class='remove-speaker button button-secondary'>Remove</button>
            </div>
            <div class='speaker-right'>
                <div class='speaker-preview'>$preview</div>
            </div>
        </div>
    </div>";
}

// Helper function for timezone select
function get_timezone_select($selected = '') {
    $output = "<select name='events_data[event_timezone]' style='width:100%;'>";
    $output .= "<option value=''>Select Time Zone</option>";
    
    $timezones = timezone_identifiers_list();
    $tzstring = get_option('timezone_string');
    
    if ($tzstring) {
        $sel = selected($selected, $tzstring, false);
        $output .= "<option value='$tzstring' $sel>$tzstring (Site Default)</option>";
    }
    
    $regions = ['Africa', 'America', 'Asia', 'Europe', 'Pacific'];
    foreach ($regions as $region) {
        $output .= "<optgroup label='$region'>";
        foreach ($timezones as $timezone) {
            if (strpos($timezone, $region . '/') === 0) {
                $sel = selected($selected, $timezone, false);
                $display = str_replace('_', ' ', $timezone);
                $output .= "<option value='$timezone' $sel>$display</option>";
            }
        }
        $output .= "</optgroup>";
    }
    
    $output .= "</select>";
    return $output;
}

// Save Meta Box data
add_action('save_post', function($post_id) {
    if (!isset($_POST['event_details_nonce']) || !wp_verify_nonce($_POST['event_details_nonce'], 'save_event_details') || !current_user_can('edit_post', $post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }

    if (!isset($_POST['events_data'])) return;

    $events_data = $_POST['events_data'];
    $sanitized_data = [];

    // Basic fields
    $basic_fields = ['event_date', 'event_start_time', 'event_end_time', 'event_timezone', 'event_address', 'speakers_heading'];
    foreach ($basic_fields as $field) {
        $sanitized_data[$field] = sanitize_text_field($events_data[$field] ?? '');
    }

    $sanitized_data['event_link'] = esc_url_raw($events_data['event_link'] ?? '');
    $sanitized_data['event_description'] = sanitize_textarea_field($events_data['event_description'] ?? '');
    $sanitized_data['theme_color'] = sanitize_hex_color($events_data['theme_color'] ?? '#0c50a8');

    // Arrays
    $array_fields = ['who_should_attend', 'topics_covered'];
    foreach ($array_fields as $field) {
        if (isset($events_data[$field]) && is_array($events_data[$field])) {
            $sanitized_data[$field] = array_map('sanitize_text_field', $events_data[$field]);
        }
    }

    // Speakers
    if (isset($events_data['speakers']) && is_array($events_data['speakers'])) {
        foreach ($events_data['speakers'] as $key => $speaker) {
            $sanitized_data['speakers'][$key] = [
                'name' => sanitize_text_field($speaker['name'] ?? ''),
                'designation' => sanitize_text_field($speaker['designation'] ?? ''),
                'organization' => sanitize_text_field($speaker['organization'] ?? ''),
                'image' => esc_url_raw($speaker['image'] ?? '')
            ];
        }
    }

    // Testimonials
    if (isset($events_data['testimonials']) && is_array($events_data['testimonials'])) {
        foreach ($events_data['testimonials'] as $key => $testimonial) {
            $sanitized_data['testimonials'][$key] = [
                'content' => wp_kses_post($testimonial['content'] ?? ''),
                'author' => sanitize_text_field($testimonial['author'] ?? '')
            ];
        }
    }

    // Special Events
    if (isset($events_data['special_events']) && is_array($events_data['special_events'])) {
        foreach ($events_data['special_events'] as $key => $event) {
            $sanitized_data['special_events'][$key] = [
                'title' => sanitize_text_field($event['title'] ?? ''),
                'heading' => sanitize_text_field($event['heading'] ?? ''),
                'video_url' => esc_url_raw($event['video_url'] ?? ''),
                'content' => wp_kses_post($event['content'] ?? '')
            ];
        }
    }

    update_post_meta($post_id, 'events_data', $sanitized_data);
    if (!empty($sanitized_data['event_date'])) {
        update_post_meta($post_id, 'event_date', $sanitized_data['event_date']);
    }
});

// Enqueue required scripts
add_action('admin_enqueue_scripts', function($hook) {
    global $post;
    if (!$post || !in_array($hook, ['post.php', 'post-new.php']) || get_post_type() != 'events') {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_media();
    
    // Essential for wp_editor in metaboxes
    wp_enqueue_editor();
    wp_enqueue_script('word-count');
    wp_enqueue_script('post');
});

// Register REST API field
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

// Add custom columns
add_filter('manage_events_posts_columns', function($columns) {
    return array_merge($columns, [
        'event_date' => 'Event Date',
        'event_start_time' => 'Start Time',
        'event_end_time' => 'End Time'
    ]);
});

add_action('manage_events_posts_custom_column', function($column, $post_id) {
    $events_data = get_post_meta($post_id, 'events_data', true);
    $fields = ['event_date', 'event_start_time', 'event_end_time'];
    
    if (in_array($column, $fields)) {
        echo !empty($events_data[$column]) ? esc_html($events_data[$column]) : 'N/A';
    }
}, 10, 2);