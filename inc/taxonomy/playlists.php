<?php

/**
 * The taxonomy for OGTV Playlists
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the custom taxonomy for OGTV Playlists
 */

function opengovasia_register_ogtv_playlists_taxonomy()
{
    $labels = array(
        'name' => _x('Playlists', 'taxonomy general name', 'opengovasia'),
        'singular_name' => _x('Playlist', 'taxonomy singular name', 'opengovasia'),
        'search_items' => __('Search Playlists', 'opengovasia'),
        'all_items' => __('All Playlists', 'opengovasia'),
        'parent_item' => __('Parent Playlist', 'opengovasia'),
        'parent_item_colon' => __('Parent Playlist:', 'opengovasia'),
        'edit_item' => __('Edit Playlist', 'opengovasia'),
        'update_item' => __('Update Playlist', 'opengovasia'),
        'add_new_item' => __('Add New Playlist', 'opengovasia'),
        'new_item_name' => __('New Playlist Name', 'opengovasia'),
        'menu_name' => __('Playlists', 'opengovasia'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'show_in_rest' => true,

        // Add the rewrite rules for the taxonomy
        // This will create pretty permalinks for the taxonomy
        // e.g., /playlists/playlist-name/
        // You can customize the slug as needed
        // For example, to use "channels" instead of "playlists", change it to:
        // 'rewrite'          => array('slug' => _x('channels', 'URL slug', 'opengovasia')),
    );

    register_taxonomy('playlists', ['ogtv'], $args);
}

add_action('init', 'opengovasia_register_ogtv_playlists_taxonomy');

function add_playlist_field() { ?>
    <div class="form-field term-group">
        <label for="playlist_channel_image"><?php _e('Banner Image'); ?></label>
        <input type="text" id="playlist_channel_image" name="channel_image" value="" class="regular-text">
        <button type="button" class="upload_playlist_image_button button" style="margin-top: 10px;"><?php _e('Upload Image'); ?></button>
        <input type="hidden" name="playlist_channel_image_nonce" value="<?php echo wp_create_nonce('playlist_channel_image_nonce'); ?>">
        <br>
        <img id="playlist_channel_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Image upload
        $('.upload_playlist_image_button').click(function(e) {
            e.preventDefault();
            var inputField = $(this).prev('input');
            var previewImage = $('#playlist_channel_image_preview');

            var uploader = wp.media({
                title: 'Upload Image',
                button: { text: 'Use this image' },
                multiple: false
            }).on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
                previewImage.attr('src', attachment.url).show();
            }).open();
        });
    });
    </script>
<?php }
add_action('playlists_add_form_fields', 'add_playlist_field');

// Edit Custom Fields in Edit Playlist Form
function edit_playlist_field($term) {
    $image = get_term_meta($term->term_id, 'channel_image', true); ?>
    
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="playlist_channel_image"><?php _e('Banner Image'); ?></label></th>
        <td>
            <input type="text" id="playlist_channel_image" name="channel_image" value="<?php echo esc_attr($image); ?>" class="regular-text">
            <button type="button" class="upload_playlist_image_button button" style="margin-top:10px;"><?php _e('Upload Image'); ?></button>
            <input type="hidden" name="playlist_channel_image_nonce" value="<?php echo wp_create_nonce('playlist_channel_image_nonce'); ?>">
            <br>
            <img id="playlist_channel_image_preview" src="<?php echo esc_attr($image); ?>" style="max-width: 100px; margin-top: 10px; <?php echo $image ? 'display:block;' : 'display:none;'; ?>">
        </td>
    </tr>

    <script>
    jQuery(document).ready(function($) {
        // Image upload
        $('.upload_playlist_image_button').click(function(e) {
            e.preventDefault();
            var inputField = $(this).prev('input');
            var previewImage = $('#playlist_channel_image_preview');

            var uploader = wp.media({
                title: 'Upload Image',
                button: { text: 'Use this image' },
                multiple: false
            }).on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
                previewImage.attr('src', attachment.url).show();
            }).open();
        });
    });
    </script>
<?php }
add_action('playlists_edit_form_fields', 'edit_playlist_field');

// Save Custom Fields for Playlists
function save_playlist_field($term_id) {
    if (!isset($_POST['playlist_channel_image_nonce']) || !wp_verify_nonce($_POST['playlist_channel_image_nonce'], 'playlist_channel_image_nonce')) {
        return;
    }

    if (isset($_POST['channel_image'])) {
        update_term_meta($term_id, 'channel_image', esc_url($_POST['channel_image']));
    }
}
add_action('created_playlists', 'save_playlist_field');
add_action('edited_playlists', 'save_playlist_field');

// Enqueue scripts for playlists admin
function enqueue_playlist_admin_scripts($hook) {
    if ('edit-tags.php' === $hook && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'playlists') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'enqueue_playlist_admin_scripts');