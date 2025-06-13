<?php

/**
 * Awards Category Taxonomy
 *
 * This file contains the cateogry taxonomy for Awards.
 *
 * @package OpenGovAsia
 */

function register_awards_category_taxonomy()
{
    $args = [
        'labels' => [
            'name' => __('Category', 'opengovasia'),
            'singular_name' => __('Category', 'opengovasia'),
            'search_items' => __('Search Categories', 'opengovasia'),
            'all_items' => __('All Categories', 'opengovasia'),
            'parent_item' => __('Parent Category', 'opengovasia'),
            'parent_item_colon' => __('Parent Category:', 'opengovasia'),
            'edit_item' => __('Edit Category', 'opengovasia'),
            'update_item' => __('Update Category', 'opengovasia'),
            'add_new_item' => __('Add New Category', 'opengovasia'),
            'new_item_name' => __('New Category Name', 'opengovasia'),
            'menu_name' => __('Categories', 'opengovasia'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'query_var' => true,
        'show_in_rest' => true,
    ];
    register_taxonomy('awards-category', ['awards'], $args);
}
add_action('init', 'register_awards_category_taxonomy');

// Add Banner Image Field to New Awards Category Form
function add_awards_category_field()
{ ?>
    <div class="form-field term-group">
        <label for="awards_category_banner_image"><?php _e('Banner Image'); ?></label>
        <input type="text" id="awards_category_banner_image" name="channel_image" value="" class="regular-text">
        <button type="button" class="upload_awards_category_image_button button"
            style="margin-top: 10px;"><?php _e('Upload Image'); ?></button>
        <input type="hidden" name="awards_category_banner_image_nonce"
            value="<?php echo wp_create_nonce('awards_category_banner_image_nonce'); ?>">
        <br>
        <img id="awards_category_banner_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Image upload
            $('.upload_awards_category_image_button').click(function (e) {
                e.preventDefault();
                var inputField = $(this).prev('input');
                var previewImage = $('#awards_category_banner_image_preview');

                var uploader = wp.media({
                    title: 'Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = uploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    previewImage.attr('src', attachment.url).show();
                }).open();
            });
        });
    </script>
<?php }
add_action('awards-category_add_form_fields', 'add_awards_category_field');

// Edit Banner Image Field in Edit Awards Category Form
function edit_awards_category_field($term)
{
    $image = get_term_meta($term->term_id, 'channel_image', true); ?>

    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="awards_category_banner_image"><?php _e('Banner Image'); ?></label></th>
        <td>
            <input type="text" id="awards_category_banner_image" name="channel_image"
                value="<?php echo esc_attr($image); ?>" class="regular-text">
            <button type="button" class="upload_awards_category_image_button button"
                style="margin-top:10px;"><?php _e('Upload Image'); ?></button>
            <input type="hidden" name="awards_category_banner_image_nonce"
                value="<?php echo wp_create_nonce('awards_category_banner_image_nonce'); ?>">
            <br>
            <img id="awards_category_banner_image_preview" src="<?php echo esc_attr($image); ?>"
                style="max-width: 100px; margin-top: 10px; <?php echo $image ? 'display:block;' : 'display:none;'; ?>">
        </td>
    </tr>

    <script>
        jQuery(document).ready(function ($) {
            // Image upload
            $('.upload_awards_category_image_button').click(function (e) {
                e.preventDefault();
                var inputField = $(this).prev('input');
                var previewImage = $('#awards_category_banner_image_preview');

                var uploader = wp.media({
                    title: 'Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = uploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    previewImage.attr('src', attachment.url).show();
                }).open();
            });
        });
    </script>
<?php }
add_action('awards-category_edit_form_fields', 'edit_awards_category_field');

// Save Banner Image Field for Awards Category
function save_awards_category_field($term_id)
{
    if (!isset($_POST['awards_category_banner_image_nonce']) || !wp_verify_nonce($_POST['awards_category_banner_image_nonce'], 'awards_category_banner_image_nonce')) {
        return;
    }

    if (isset($_POST['channel_image'])) {
        update_term_meta($term_id, 'channel_image', esc_url($_POST['channel_image']));
    }
}
add_action('created_awards-category', 'save_awards_category_field');
add_action('edited_awards-category', 'save_awards_category_field');

// Enqueue scripts for awards-category admin
function enqueue_awards_category_admin_scripts($hook)
{
    if ('edit-tags.php' === $hook && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'awards-category') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'enqueue_awards_category_admin_scripts');