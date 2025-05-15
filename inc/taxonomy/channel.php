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
            'menu_name' => __('Channels'),
            'name_admin_bar' => __('Channels'),
        ]);

        // Attach the taxonomy to all post types
        $wp_taxonomies['category']->object_type = ['post', 'awards', 'events', 'ogtv'];
    }
}
add_action('init', 'rename_category_to_channel');

// Assign default category to custom post types if none is assigned

function assign_default_category_to_custom_post_type($post_id, $post, $update) {
    // Only run on save (not autosave or revisions)
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    // Define your custom post types here
    $custom_post_types = ['events', 'awards', 'ogtv']; // Add more as needed

    // Check if the current post type is among them
    if (in_array($post->post_type, $custom_post_types)) {
        // Get current categories
        $categories = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);

        // If no category assigned, assign the default category
        if (empty($categories)) {
            $default_cat_id = get_option('default_category');
            wp_set_post_terms($post_id, [$default_cat_id], 'category');
        }
    }
}
add_action('save_post', 'assign_default_category_to_custom_post_type', 10, 3);


// Change category base to 'channel' on theme activation
function custom_category_base_on_theme_activation() {
    // Change category base to 'channel'
    update_option('category_base', 'channel');

    // Flush rewrite rules after category base change
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'custom_category_base_on_theme_activation');

function enqueue_category_image_uploader($hook)
{
    if ('edit-tags.php' === $hook || 'term.php' === $hook || 'edit-category' === $hook) {
        wp_enqueue_media();
        wp_enqueue_script('category-image-upload', get_template_directory_uri() . '/category-image.js', ['jquery'], null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_category_image_uploader');


// Add Custom Fields to Add Category Form
function add_channel_field() {
    $nonce = wp_create_nonce('channel_image_nonce');
    ?>
    <div class="form-field term-group">
        <label for="channel_image"><?php _e('Banner Image', 'text_domain'); ?></label>
        <input type="text" id="channel_image" name="channel_image" value="" class="regular-text">
        <button class="upload_image_button button" style="margin-top: 10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
        <input type="hidden" name="channel_image_nonce" value="<?php echo esc_attr($nonce); ?>">
        <br>
        <img id="channel_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>

    <div class="form-field term-group">
        <label for="sponsor_image"><?php _e('Sponsor Image', 'text_domain'); ?></label>
        <input type="text" id="sponsor_image" name="sponsor_image" value="" class="regular-text">
        <button class="upload_image_button button" style="margin-top: 10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
        <br>
        <img id="sponsor_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>

    <div class="form-field term-group">
        <label for="sponsor_link_text"><?php _e('Sponsor Link Text', 'text_domain'); ?></label>
        <input type="text" id="sponsor_link_text" name="sponsor_link_text" value="" class="regular-text">
    </div>

    <div class="form-field term-group">
        <label for="sponsor_link"><?php _e('Sponsor Link URL', 'text_domain'); ?></label>
        <input type="url" id="sponsor_link" name="sponsor_link" value="" class="regular-text">
    </div>

    <script>
        jQuery(document).ready(function ($) {
            $('.upload_image_button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var inputField = button.prev('input');
                var previewImage = button.nextAll('img');

                var custom_uploader = wp.media({
                    title: 'Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    previewImage.attr('src', attachment.url).show();
                }).open();
            });
        });
    </script>
    <?php
}
add_action('category_add_form_fields', 'add_channel_field', 10, 2);

// Edit Custom Fields in Edit Category Form
function edit_channel_field($term) {
    $image = get_term_meta($term->term_id, 'channel_image', true);
    $sponsor_image = get_term_meta($term->term_id, 'sponsor_image', true);
    $sponsor_link_text = get_term_meta($term->term_id, 'sponsor_link_text', true);
    $sponsor_link = get_term_meta($term->term_id, 'sponsor_link', true);
    $nonce = wp_create_nonce('channel_image_nonce');
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="channel_image"><?php _e('Banner Image', 'text_domain'); ?></label></th>
        <td>
            <input type="text" id="channel_image" name="channel_image" value="<?php echo esc_attr($image); ?>" class="regular-text">
            <button class="upload_image_button button" style="margin-top:10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
            <input type="hidden" name="channel_image_nonce" value="<?php echo esc_attr($nonce); ?>">
            <br>
            <img id="channel_image_preview" src="<?php echo esc_attr($image); ?>" style="max-width: 100px; margin-top: 10px; <?php echo $image ? 'display:block;' : 'display:none;'; ?>">
        </td>
    </tr>

    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="sponsor_image"><?php _e('Sponsor Image', 'text_domain'); ?></label></th>
        <td>
            <input type="text" id="sponsor_image" name="sponsor_image" value="<?php echo esc_attr($sponsor_image); ?>" class="regular-text">
            <button class="upload_image_button button" style="margin-top:10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
            <br>
            <img id="sponsor_image_preview" src="<?php echo esc_attr($sponsor_image); ?>" style="max-width: 100px; margin-top: 10px; <?php echo $sponsor_image ? 'display:block;' : 'display:none;'; ?>">
        </td>
    </tr>

    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="sponsor_link_text"><?php _e('Sponsor Link Text', 'text_domain'); ?></label></th>
        <td>
            <input type="text" id="sponsor_link_text" name="sponsor_link_text" value="<?php echo esc_attr($sponsor_link_text); ?>" class="regular-text">
        </td>
    </tr>

    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="sponsor_link"><?php _e('Sponsor Link URL', 'text_domain'); ?></label></th>
        <td>
            <input type="url" id="sponsor_link" name="sponsor_link" value="<?php echo esc_url($sponsor_link); ?>" class="regular-text">
        </td>
    </tr>

    <script>
        jQuery(document).ready(function ($) {
            $('.upload_image_button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var inputField = button.prev('input');
                var previewImage = button.nextAll('img');

                var custom_uploader = wp.media({
                    title: 'Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    previewImage.attr('src', attachment.url).show();
                }).open();
            });
        });
    </script>
    <?php
}
add_action('category_edit_form_fields', 'edit_channel_field', 10, 2);

// Save Custom Fields on Create/Edit
function save_channel_field($term_id) {
    if (!isset($_POST['channel_image_nonce']) || !wp_verify_nonce($_POST['channel_image_nonce'], 'channel_image_nonce')) {
        return;
    }

    if (isset($_POST['channel_image'])) {
        update_term_meta($term_id, 'channel_image', esc_url($_POST['channel_image']));
    }

    if (isset($_POST['sponsor_image'])) {
        update_term_meta($term_id, 'sponsor_image', esc_url($_POST['sponsor_image']));
    }

    if (isset($_POST['sponsor_link_text'])) {
        update_term_meta($term_id, 'sponsor_link_text', sanitize_text_field($_POST['sponsor_link_text']));
    }

    if (isset($_POST['sponsor_link'])) {
        update_term_meta($term_id, 'sponsor_link', esc_url($_POST['sponsor_link']));
    }
}
add_action('created_category', 'save_channel_field', 10, 2);
add_action('edited_category', 'save_channel_field', 10, 2);