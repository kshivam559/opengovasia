<?php

/**
 * Country Taxonomy Functions
 *
 * This file contains functions related to the 'country' taxonomy.
 *
 * @package OpenGovAsia
 */

// Register the Country Taxonomy
add_action('init', 'register_country_taxonomy');
function register_country_taxonomy()
{
    register_taxonomy('country', ['post', 'awards', 'events', 'ogtv'], [
        'labels' => [
            'name' => __('Countries', 'country-taxonomy'),
            'singular_name' => __('Country', 'country-taxonomy'),
            'menu_name' => __('Countries', 'country-taxonomy'),
            'add_new_item' => __('Add New Country', 'country-taxonomy'),
            'edit_item' => __('Edit Country', 'country-taxonomy'),
            'update_item' => __('Update Country', 'country-taxonomy'),
            'search_items' => __('Search Countries', 'country-taxonomy'),
            'not_found' => __('No countries found', 'country-taxonomy'),
        ],
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'query_var' => true,
        'show_in_rest' => true,
        // 'rewrite' => ['slug' => 'country'],
        'rewrite' => false,
    ]);
}

// 2. Create Default Country Term
add_action('init', 'create_default_country');
function create_default_country()
{
    if (!term_exists('global', 'country')) {
        wp_insert_term('Global', 'country', ['slug' => 'global']);
    }
}

// 3. Assign Default Country on Save
add_action('save_post', 'assign_default_country', 10, 3);
function assign_default_country($post_id, $post, $update)
{
    // Skip auto-drafts, revisions, and autosaves
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || $post->post_status === 'auto-draft') {
        return;
    }

    // Check if the post type is one we want to modify
    if (!in_array($post->post_type, ['events', 'awards', 'ogtv', 'post'])) {
        return;
    }

    $default_country = get_term_by('slug', 'global', 'country');
    if (!$default_country) {
        return;
    }

    // Only set default country if no country is assigned
    if (empty(wp_get_post_terms($post_id, 'country', ['fields' => 'ids']))) {
        wp_set_post_terms($post_id, [$default_country->term_id], 'country', false);
    }
}

// 4. Prevent Deletion of Default Term
add_action('pre_delete_term', 'prevent_default_country_deletion', 10, 2);
function prevent_default_country_deletion($term_id, $taxonomy)
{
    if ($taxonomy !== 'country') {
        return;
    }

    $default_country = get_term_by('slug', 'global', 'country');
    if ($default_country && $default_country->term_id == $term_id) {
        wp_die(__('You cannot delete the default country (Global).', 'country-taxonomy'));
    }
}

// 5. Add Custom Fields to Add Term Form
add_action('country_add_form_fields', 'add_country_custom_fields');
function add_country_custom_fields()
{
    ?>
    <div class="form-field">
        <label for="country_flag"><?php _e('Country Flag Image', 'country-taxonomy'); ?></label>
        <input type="text" id="country_flag" name="country_flag" class="regular-text" />
        <button class="button upload_flag_button" style="margin-top: 10px;"><?php _e('Select Image', 'country-taxonomy'); ?></button>
        <p class="description"><?php _e('Select or paste the URL of the country flag image.', 'country-taxonomy'); ?></p>
        <div id="flag_preview" style="margin-top: 10px;"></div>
    </div>

    <div class="form-field">
        <label for="country_color"><?php _e('Country Color', 'country-taxonomy'); ?></label>
        <input type="text" id="country_color" name="country_color" class="color-picker" data-default-color="#0c50a8" />
        <p class="description"><?php _e('Select a HEX color for the country.', 'country-taxonomy'); ?></p>
    </div>
    <?php wp_nonce_field('save_country_meta', 'country_meta_nonce'); ?>
<?php
}

// 6. Add Custom Fields to Edit Term Form
add_action('country_edit_form_fields', 'edit_country_custom_fields', 10, 2);
function edit_country_custom_fields($term, $taxonomy)
{
    $flag_url = get_term_meta($term->term_id, 'country_flag', true);
    $color = get_term_meta($term->term_id, 'country_color', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="country_flag"><?php _e('Country Flag Image', 'country-taxonomy'); ?></label></th>
        <td>
            <input type="text" id="country_flag" name="country_flag" value="<?php echo esc_attr($flag_url); ?>"
                class="regular-text" />
            <button class="button upload_flag_button" style="margin-top: 10px;"><?php _e('Select Image', 'country-taxonomy'); ?></button>
            <p class="description"><?php _e('Select or paste the URL of the country flag image.', 'country-taxonomy'); ?>
            </p>
            <?php if ($flag_url): ?>
                <img src="<?php echo esc_url($flag_url); ?>" style="max-width: 100px; margin-top: 10px;" />
            <?php endif; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="country_color"><?php _e('Country Color', 'country-taxonomy'); ?></label></th>
        <td>
            <input type="text" id="country_color" name="country_color" value="<?php echo esc_attr($color); ?>"
                class="color-picker" data-default-color="#0c50a8" />
            <p class="description"><?php _e('Select a HEX color for the country.', 'country-taxonomy'); ?></p>
        </td>
    </tr>
    <?php wp_nonce_field('save_country_meta', 'country_meta_nonce'); ?>
<?php
}

// 7. Save Custom Fields with Nonce Validation on Add Term
add_action('created_country', 'save_country_custom_fields', 10, 2);

// 8. Save Custom Fields with Nonce Validation on Edit Term
add_action('edited_country', 'save_country_custom_fields', 10, 2);

function save_country_custom_fields($term_id, $tt_id)
{
    // Check if the nonce is set and valid
    if (!isset($_POST['country_meta_nonce']) || !wp_verify_nonce($_POST['country_meta_nonce'], 'save_country_meta')) {
        return;
    }

    // Check if the current user has the capability to edit terms
    if (!current_user_can('manage_categories')) {
        return;
    }

    // Update the flag URL metadata
    if (isset($_POST['country_flag'])) {
        $flag_url = esc_url_raw($_POST['country_flag']);
        update_term_meta($term_id, 'country_flag', $flag_url);
    }

    // Update the color metadata
    if (isset($_POST['country_color'])) {
        $color = sanitize_hex_color($_POST['country_color']);
        update_term_meta($term_id, 'country_color', $color);
    }
}

// 9. Enqueue Media, Color Picker, and Inline Script
add_action('admin_enqueue_scripts', 'enqueue_country_admin_assets');
function enqueue_country_admin_assets($hook)
{
    // Check if we're on the right admin page for our taxonomy
    if (
        ($hook === 'edit-tags.php' || $hook === 'term.php') &&
        isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'country'
    ) {

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Register and enqueue our custom script
        wp_register_script(
            'country-taxonomy-js',
            '', // We'll use inline script instead of a file
            ['jquery', 'wp-color-picker', 'media-upload'],
            '1.0.0',
            true
        );
        wp_enqueue_script('country-taxonomy-js');

        // Add our inline script
        wp_add_inline_script('country-taxonomy-js', get_country_admin_js());
    }
}

// 10. Get the JavaScript code as a string
function get_country_admin_js()
{
    ob_start();
    ?>
    jQuery(document).ready(function($) {
    
    $('.color-picker').wpColorPicker();

    $('.upload_flag_button').on('click', function(e) {
    e.preventDefault();

    var inputField = $('#country_flag');

    var mediaUploader = wp.media({
    title: '<?php echo esc_js(__('Select Flag Image', 'country-taxonomy')); ?>',
    button: { text: '<?php echo esc_js(__('Use this image', 'country-taxonomy')); ?>' },
    multiple: false
    });

    mediaUploader.on('select', function() {
    var attachment = mediaUploader.state().get('selection').first().toJSON();
    inputField.val(attachment.url);

    if ($('#flag_preview').length > 0) {
    $('#flag_preview').html('<img src="' + attachment.url + '" style="max-width: 100px;" />');
    }
    });

    mediaUploader.open();
    });


    $('#country_flag').on('change keyup paste', function() {
    var url = $(this).val();
    if (url && $('#flag_preview').length > 0) {
    $('#flag_preview').html('<img src="' + url + '" style="max-width: 100px;" />');
    }
    });
    });
    <?php
    return ob_get_clean();
}

// 11. Add Custom Column to Country Taxonomy List
add_filter('manage_edit-country_columns', 'add_country_columns');
function add_country_columns($columns)
{
    $new_columns = [];

    // Insert flag column after checkbox but before name
    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
        unset($columns['cb']);
    }

    $new_columns['flag'] = __('Flag', 'country-taxonomy');

    // Add the rest of the columns
    return array_merge($new_columns, $columns);
}

// 12. Display Custom Column Content
add_filter('manage_country_custom_column', 'manage_country_columns', 10, 3);
function manage_country_columns($out, $column_name, $term_id)
{
    if ($column_name !== 'flag') {
        return $out;
    }

    $flag_url = get_term_meta($term_id, 'country_flag', true);
    if ($flag_url) {
        $out = '<img src="' . esc_url($flag_url) . '" style="aspect-ratio: 3 / 2; width: 50px; object-fit: contain; display: block;" alt="' . esc_attr__('Country flag', 'country-taxonomy') . '" />';
    } else {
        $out = '—';
    }

    return $out;
}

// 13. Add a helper function to get country data by term ID or slug
function get_country_data($term_identifier)
{
    $term = null;

    if (is_numeric($term_identifier)) {
        $term = get_term_by('id', $term_identifier, 'country');
    } else {
        $term = get_term_by('slug', $term_identifier, 'country');
    }

    if (!$term || is_wp_error($term)) {
        return false;
    }

    return [
        'id' => $term->term_id,
        'name' => $term->name,
        'slug' => $term->slug,
        'flag' => get_term_meta($term->term_id, 'country_flag', true),
        'color' => get_term_meta($term->term_id, 'country_color', true),
    ];
}