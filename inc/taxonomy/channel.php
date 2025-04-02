<?php

// Rename "Category" to "Channel" and attach it to all custom post types
function rename_category_to_channel()
{
    global $wp_taxonomies;
    if (isset($wp_taxonomies['category'])) {
        $wp_taxonomies['category']->labels = (object) array_merge((array) $wp_taxonomies['category']->labels, [
            'name' => __('Channels'),
            'singular_name' => __('Channel'),
            'all_items' => __('All Channels'),
            'edit_item' => __('Edit Channel'),
            'view_item' => __('View Channel'),
            'update_item' => __('Update Channel'),
            'add_new_item' => __('Add New Channel'),
            'new_item_name' => __('New Channel Name'),
            'search_items' => __('Search Channels'),
            'parent_item' => __('Parent Channel'),
            'parent_item_colon' => __('Parent Channel:'),
            'not_found' => __('No Channels found.'),
            'items_list_navigation' => __('Channels list navigation'),
            'items_list' => __('Channels list'),
            'menu_name' => __('Channel'),
            'name_admin_bar' => __('Channel'),
        ]);

        // Attach the taxonomy to all post types
        $wp_taxonomies['category']->object_type = ['post', 'awards', 'events', 'ogtv'];
    }
}
add_action('init', 'rename_category_to_channel');

function enqueue_category_image_uploader($hook)
{
    if ('edit-tags.php' === $hook || 'term.php' === $hook || 'edit-category' === $hook) {
        wp_enqueue_media();
        wp_enqueue_script('category-image-upload', get_template_directory_uri() . '/category-image.js', ['jquery'], null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_category_image_uploader');


// Add Image Upload Field to Channel Taxonomy
function add_channel_image_field()
{
    $nonce = wp_create_nonce('channel_image_nonce');
    ?>
    <div class="form-field term-group">
        <label for="channel_image"><?php _e('Upload Image', 'text_domain'); ?></label>
        <input type="text" id="channel_image" name="channel_image" value="" class="regular-text">
        <button class="upload_image_button button"
            style="margin-top: 10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
        <input type="hidden" name="channel_image_nonce" value="<?php echo esc_attr($nonce); ?>">
        <br>
        <img id="channel_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>
    <script>
        jQuery(document).ready(function ($) {
            $('.upload_image_button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var custom_uploader = wp.media({
                    title: 'Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#channel_image').val(attachment.url);
                    $('#channel_image_preview').attr('src', attachment.url).show();
                }).open();
            });
        });
    </script>
    <?php
}
add_action('category_add_form_fields', 'add_channel_image_field', 10, 2);

// Edit Image Upload Field in Existing Categories
function edit_channel_image_field($term)
{
    $image = get_term_meta($term->term_id, 'channel_image', true);
    $nonce = wp_create_nonce('channel_image_nonce');
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="channel_image"><?php _e('Upload Image', 'text_domain'); ?></label></th>
        <td>
            <input type="text" id="channel_image" name="channel_image" value="<?php echo esc_attr($image); ?>"
                class="regular-text">
            <button class="upload_image_button button"
                style="margin-top:10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
            <input type="hidden" name="channel_image_nonce" value="<?php echo esc_attr($nonce); ?>">
            <br>
            <img id="channel_image_preview" src="<?php echo esc_attr($image); ?>"
                style="max-width: 100px; margin-top: 10px; <?php echo $image ? 'display:block;' : 'display:none;'; ?>">
        </td>
    </tr>
    <script>
        jQuery(document).ready(function ($) {
            $('.upload_image_button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var custom_uploader = wp.media({
                    title: 'Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#channel_image').val(attachment.url);
                    $('#channel_image_preview').attr('src', attachment.url).show();
                }).open();
            });
        });
    </script>
    <?php
}
add_action('category_edit_form_fields', 'edit_channel_image_field', 10, 2);

// Save Image with Nonce Verification
function save_channel_image($term_id)
{
    if (!isset($_POST['channel_image_nonce']) || !wp_verify_nonce($_POST['channel_image_nonce'], 'channel_image_nonce')) {
        return; // Nonce verification failed
    }

    if (isset($_POST['channel_image'])) {
        update_term_meta($term_id, 'channel_image', esc_url($_POST['channel_image']));
    }
}
add_action('created_category', 'save_channel_image', 10, 2);
add_action('edited_category', 'save_channel_image', 10, 2);


