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
function add_event_details_meta_box() {
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
function display_event_details_meta_box($post) {
    // Add nonce field for security
    wp_nonce_field('save_event_details', 'event_details_nonce');

    // Retrieve existing meta data
    $events_data = get_post_meta($post->ID, 'events_data', true);

    $event_date      = isset($events_data['event_date']) ? esc_attr($events_data['event_date']) : '';
    $event_start_time = isset($events_data['event_start_time']) ? esc_attr($events_data['event_start_time']) : '';
    $event_end_time  = isset($events_data['event_end_time']) ? esc_attr($events_data['event_end_time']) : '';
    $event_location  = isset($events_data['event_location']) ? esc_attr($events_data['event_location']) : '';
    $event_link      = isset($events_data['event_link']) ? esc_url($events_data['event_link']) : '';

    // Fetch all terms from the 'country' taxonomy
    $countries = get_terms([
        'taxonomy'   => 'country',
        'hide_empty' => false,
    ]);
    ?>
    <table style="width:100%;">
        <tr>
            <td style="width: 150px;"><strong>Event Date:</strong></td>
            <td><input type="date" name="events_data[event_date]" value="<?php echo $event_date; ?>" style="width:100%;"></td>
        </tr>
        <tr>
            <td style="width: 150px;"><strong>Event Start Time:</strong></td>
            <td><input type="time" name="events_data[event_start_time]" value="<?php echo $event_start_time; ?>" style="width:100%;"></td>
        </tr>
        <tr>
            <td style="width: 150px;"><strong>Event End Time:</strong></td>
            <td><input type="time" name="events_data[event_end_time]" value="<?php echo $event_end_time; ?>" style="width:100%;"></td>
        </tr>
        <tr>
            <td style="width: 150px;"><strong>Event Location:</strong></td>
            <td>
                <select name="events_data[event_location]" style="width:100%;">
                    <option value="">Select Country</option>
                    <?php if (!empty($countries)) : ?>
                        <?php foreach ($countries as $country) : ?>
                            <option value="<?php echo esc_attr($country->slug); ?>" <?php selected($event_location, $country->slug); ?>>
                                <?php echo esc_html($country->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 150px;"><strong>Event Link:</strong></td>
            <td><input type="url" name="events_data[event_link]" value="<?php echo $event_link; ?>" placeholder="https://eventbrites.com" style="width:100%;"></td>
        </tr>
    </table>
    <?php
}

// Save Meta Box Data with Nonce Verification
function save_event_details_meta_box($post_id) {
    // Check nonce validity
    if (!isset($_POST['event_details_nonce']) || !wp_verify_nonce($_POST['event_details_nonce'], 'save_event_details')) {
        return;
    }

    // Auto-save check
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // User permission check
    if (!current_user_can('edit_post', $post_id)) return;

    // Sanitize and save data
    if (isset($_POST['events_data']) && is_array($_POST['events_data'])) {
        $sanitized_data = [];
        foreach ($_POST['events_data'] as $key => $value) {
            if ($key === 'event_link') {
                $sanitized_data[$key] = esc_url_raw($value); // Ensure safe URLs
            } else {
                $sanitized_data[$key] = sanitize_text_field($value);
            }
        }
        update_post_meta($post_id, 'events_data', $sanitized_data);

        // Store event date separately for easy query access
        if (!empty($sanitized_data['event_date'])) {
            update_post_meta($post_id, 'event_date', $sanitized_data['event_date']);
        }
    }
    
}
add_action('save_post', 'save_event_details_meta_box');

