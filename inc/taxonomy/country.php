<?php

/**
 * Country Taxonomy Functions (Production Ready)
 *
 * @package OpenGovAsia
 */

// Register Country Taxonomy
add_action('init', 'register_country_taxonomy');
function register_country_taxonomy() {
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
        'rewrite' => false,
    ]);
}

// Create Default Country Term
add_action('init', 'create_default_country');
function create_default_country() {
    if (!term_exists('global', 'country')) {
        $term = wp_insert_term('Global', 'country', ['slug' => 'global']);
        if (!is_wp_error($term)) {
            update_term_meta($term['term_id'], 'country_color', '#0c50a8');
        }
    }
}

// Assign Default Country on Save
add_action('save_post', 'assign_default_country', 10, 3);
function assign_default_country($post_id, $post, $update) {
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || $post->post_status === 'auto-draft') {
        return;
    }

    if (!in_array($post->post_type, ['events', 'awards', 'ogtv', 'post'])) {
        return;
    }

    if (empty(wp_get_post_terms($post_id, 'country', ['fields' => 'ids']))) {
        $default_country = get_term_by('slug', 'global', 'country');
        if ($default_country) {
            wp_set_post_terms($post_id, [$default_country->term_id], 'country', false);
        }
    }
}

// Prevent Deletion of Default Term
add_action('pre_delete_term', 'prevent_default_country_deletion', 10, 2);
function prevent_default_country_deletion($term_id, $taxonomy) {
    if ($taxonomy === 'country') {
        $default_country = get_term_by('slug', 'global', 'country');
        if ($default_country && $default_country->term_id == $term_id) {
            wp_die(__('You cannot delete the default country (Global).', 'country-taxonomy'));
        }
    }
}

// Add Custom Fields to Add Term Form
add_action('country_add_form_fields', 'add_country_custom_fields');
function add_country_custom_fields() {
    wp_nonce_field('save_country_meta', 'country_meta_nonce');
    ?>
    <div class="form-field">
        <label for="country_flag"><?php _e('Country Flag Image', 'country-taxonomy'); ?></label>
        <input type="url" id="country_flag" name="country_flag" class="regular-text" maxlength="500" />
        <button type="button" class="button upload_flag_button" style="margin-top: 10px;"><?php _e('Select Image', 'country-taxonomy'); ?></button>
        <p class="description"><?php _e('Select or paste the URL of the country flag image.', 'country-taxonomy'); ?></p>
        <div id="flag_preview"></div>
    </div>
    <div class="form-field">
        <label for="country_color"><?php _e('Country Color', 'country-taxonomy'); ?></label>
        <input type="text" id="country_color" name="country_color" class="color-picker" value="#0c50a8" data-default-color="#0c50a8" />
        <p class="description"><?php _e('Select a HEX color for the country.', 'country-taxonomy'); ?></p>
    </div>
    <?php
}

// Add Custom Fields to Edit Term Form
add_action('country_edit_form_fields', 'edit_country_custom_fields', 10, 2);
function edit_country_custom_fields($term, $taxonomy) {
    $flag_url = get_term_meta($term->term_id, 'country_flag', true);
    $color = get_term_meta($term->term_id, 'country_color', true) ?: '#0c50a8';
    
    wp_nonce_field('save_country_meta', 'country_meta_nonce');
    ?>
    <tr class="form-field">
        <th scope="row"><label for="country_flag"><?php _e('Country Flag Image', 'country-taxonomy'); ?></label></th>
        <td>
            <input type="url" id="country_flag" name="country_flag" value="<?php echo esc_attr($flag_url); ?>" class="regular-text" maxlength="500" />
            <button type="button" class="button upload_flag_button" style="margin-top: 10px;"><?php _e('Select Image', 'country-taxonomy'); ?></button>
            <p class="description"><?php _e('Select or paste the URL of the country flag image.', 'country-taxonomy'); ?></p>
            <?php if ($flag_url): ?>
                <img src="<?php echo esc_url($flag_url); ?>" style="max-width: 100px; margin-top: 10px;" alt="Flag preview" />
            <?php endif; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="country_color"><?php _e('Country Color', 'country-taxonomy'); ?></label></th>
        <td>
            <input type="text" id="country_color" name="country_color" value="<?php echo esc_attr($color); ?>" class="color-picker" data-default-color="#0c50a8" />
            <p class="description"><?php _e('Select a HEX color for the country.', 'country-taxonomy'); ?></p>
        </td>
    </tr>
    <?php
}

// Save Custom Fields
add_action('created_country', 'save_country_custom_fields', 10, 2);
add_action('edited_country', 'save_country_custom_fields', 10, 2);
function save_country_custom_fields($term_id, $tt_id) {
    if (!isset($_POST['country_meta_nonce']) || !wp_verify_nonce($_POST['country_meta_nonce'], 'save_country_meta')) {
        return;
    }

    if (!current_user_can('manage_categories')) {
        return;
    }

    // Save flag URL with validation
    if (isset($_POST['country_flag'])) {
        $flag_url = sanitize_url($_POST['country_flag']);
        if (empty($flag_url) || filter_var($flag_url, FILTER_VALIDATE_URL)) {
            update_term_meta($term_id, 'country_flag', $flag_url);
        }
    }

    // Save color with default fallback
    $color = '#0c50a8'; // Default color
    if (isset($_POST['country_color']) && !empty($_POST['country_color'])) {
        $submitted_color = sanitize_hex_color($_POST['country_color']);
        if ($submitted_color) {
            $color = $submitted_color;
        }
    }
    update_term_meta($term_id, 'country_color', $color);
}

// Enqueue Admin Assets
add_action('admin_enqueue_scripts', 'enqueue_country_admin_assets');
function enqueue_country_admin_assets($hook) {
    if (($hook === 'edit-tags.php' || $hook === 'term.php') && 
        isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'country') {
        
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        $script = "
        jQuery(document).ready(function($) {
            $('.color-picker').wpColorPicker({
                defaultColor: '#0c50a8',
                change: function(event, ui) {
                    // Optional: handle color change
                },
                clear: function() {
                    // Reset to default when cleared
                    $(this).val('#0c50a8').trigger('change');
                }
            });
            
            $('.upload_flag_button').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var inputField = button.prev('input');
                
                var mediaUploader = wp.media({
                    title: 'Select Flag Image',
                    button: { text: 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url).trigger('change');
                });
                
                mediaUploader.open();
            });
            
            $('#country_flag').on('change keyup', function() {
                var url = $(this).val();
                var preview = $('#flag_preview');
                if (url && preview.length) {
                    preview.html('<img src=\"' + url + '\" style=\"max-width: 100px; margin-top: 10px;\" alt=\"Flag preview\" />');
                } else {
                    preview.empty();
                }
            });
        });";
        
        wp_add_inline_script('wp-color-picker', $script);
    }
}

// Add Custom Column to Country List
add_filter('manage_edit-country_columns', 'add_country_columns');
function add_country_columns($columns) {
    $new_columns = [];
    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
        unset($columns['cb']);
    }
    $new_columns['flag'] = __('Flag', 'country-taxonomy');
    return array_merge($new_columns, $columns);
}

// Display Custom Column Content
add_filter('manage_country_custom_column', 'manage_country_columns', 10, 3);
function manage_country_columns($out, $column_name, $term_id) {
    if ($column_name === 'flag') {
        $flag_url = get_term_meta($term_id, 'country_flag', true);
        if ($flag_url) {
            $out = '<img src="' . esc_url($flag_url) . '" style="width: 50px; height: 33px; object-fit: contain;" alt="Flag" />';
        } else {
            $out = '—';
        }
    }
    return $out;
}

// Helper function to get country data
function get_country_data($term_identifier) {
    $term = is_numeric($term_identifier) 
        ? get_term_by('id', $term_identifier, 'country')
        : get_term_by('slug', $term_identifier, 'country');

    if (!$term || is_wp_error($term)) {
        return false;
    }

    return [
        'id' => $term->term_id,
        'name' => $term->name,
        'slug' => $term->slug,
        'flag' => get_term_meta($term->term_id, 'country_flag', true),
        'color' => get_term_meta($term->term_id, 'country_color', true) ?: '#0c50a8',
    ];
}