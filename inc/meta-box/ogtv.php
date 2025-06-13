<?php
/**
 * OGTV Meta Box
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Register Meta Box
function ogtv_add_meta_box()
{
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
function ogtv_meta_box_callback($post)
{
    // Retrieve stored values
    $video_url = get_custom_meta($post->ID, 'video_url', true);

    // Add a nonce for security
    wp_nonce_field('ogtv_save_meta_box', 'ogtv_meta_box_nonce');

    ?>

    <p>
        <label for="ogtv_video_url"><strong>Enter Video URL:</strong></label>
        <input type="url" id="video_url" name="video_url" value="<?php echo $video_url; ?>" class="widefat">
    </p>
    <p><small>Example: https://www.youtube.com/watch?v=example</small></p>

    <?php
}

// Save Meta Box Content
function ogtv_save_meta_box($post_id)
{
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
    if (isset($_POST['video_url']) && !empty($_POST['video_url'])) {
        $video_url = esc_url_raw($_POST['video_url']);
        update_custom_meta($post_id, 'video_url', $video_url);
    } else {
        delete_custom_meta($post_id, 'video_url');
    }
}
add_action('save_post', 'ogtv_save_meta_box');

// Register in the REST API
register_rest_field('ogtv', 'video_url', [
    'get_callback' => function ($object) {
        return get_custom_meta($object['id'], 'video_url', true);
    },
    'schema' => [
        'type' => 'object',
        'context' => ['view'],
        'description' => 'OGTV video details from the custom meta box.',
    ]
]);