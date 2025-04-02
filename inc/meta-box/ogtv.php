<?php
/**
 * OGTV Meta Box
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register Meta Box

function ogtv_add_meta_box() {
    add_meta_box(
        'ogtv_meta_box',            // Unique ID
        'OGTV Video URL',           // Box Title
        'ogtv_meta_box_callback',   // Content callback function
        'ogtv',                     // Post type (change if needed)
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ogtv_add_meta_box');

// Display Meta Box Content

function ogtv_meta_box_callback($post) {
    // Retrieve stored values
    $ogtv_details = get_post_meta($post->ID, 'ogtv_details', true);
    $video_url = isset($ogtv_details['url']) ? esc_url($ogtv_details['url']) : '';

    // Add a nonce for security
    wp_nonce_field('ogtv_save_meta_box', 'ogtv_meta_box_nonce');
    ?>

    <p>
        <label for="ogtv_video_url"><strong>Enter Video URL:</strong></label>
        <input type="url" id="ogtv_video_url" name="ogtv_details[url]" value="<?php echo $video_url; ?>" class="widefat">
    </p>
    <p><small>Example: https://www.youtube.com/watch?v=example</small></p>

    <?php
}

// Save Meta Box Content

function ogtv_save_meta_box($post_id) {
    // Verify nonce
    if (!isset($_POST['ogtv_meta_box_nonce']) || !wp_verify_nonce($_POST['ogtv_meta_box_nonce'], 'ogtv_save_meta_box')) {
        return;
    }

    // Prevent auto-saving from interfering
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Ensure user has permission
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Sanitize and save data
    if (isset($_POST['ogtv_details']['url'])) {
        $ogtv_details = [
            'url' => esc_url_raw($_POST['ogtv_details']['url'])
        ];
        update_post_meta($post_id, 'ogtv_details', $ogtv_details);
    } else {
        delete_post_meta($post_id, 'ogtv_details');
    }
}
add_action('save_post', 'ogtv_save_meta_box');
