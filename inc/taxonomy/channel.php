<?php

// Rename "Category" to "Channel" and attach it to all custom post types
function rename_category_to_channel() {
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

        $wp_taxonomies['category']->object_type = ['post', 'awards', 'events', 'ogtv'];
    }
}
add_action('init', 'rename_category_to_channel');

// Assign default category to custom post types if none is assigned
function assign_default_category_to_custom_post_type($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    $custom_post_types = ['events', 'awards', 'ogtv'];

    if (in_array($post->post_type, $custom_post_types)) {
        $categories = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);

        if (empty($categories)) {
            $default_cat_id = get_option('default_category');
            wp_set_post_terms($post_id, [$default_cat_id], 'category');
        }
    }
}
add_action('save_post', 'assign_default_category_to_custom_post_type', 10, 3);

// Change category base to 'channel' on theme activation
function custom_category_base_on_theme_activation() {
    update_option('category_base', 'channel');
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'custom_category_base_on_theme_activation');

// Enqueue scripts for admin
function enqueue_category_admin_scripts($hook) {
    if ('edit-tags.php' === $hook || 'term.php' === $hook) {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_localize_script('jquery-ui-autocomplete', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('company_search_nonce')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_category_admin_scripts');

// AJAX handler for company search
function handle_company_search() {
    if (!check_ajax_referer('company_search_nonce', 'nonce', false)) {
        wp_die('Security check failed');
    }

    $search_term = sanitize_text_field($_POST['term']);
    
    $companies = get_posts([
        'post_type' => 'company',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        's' => $search_term,
    ]);

    $results = [];
    foreach ($companies as $company) {
        $results[] = [
            'id' => $company->ID,
            'label' => $company->post_title,
            'value' => $company->post_title,
        ];
    }

    wp_send_json($results);
}
add_action('wp_ajax_search_companies', 'handle_company_search');

// Add Custom Fields to Add Category Form
function add_channel_field() { ?>
    <div class="form-field term-group">
        <label for="channel_image"><?php _e('Banner Image'); ?></label>
        <input type="text" id="channel_image" name="channel_image" value="" class="regular-text">
        <button type="button" class="upload_image_button button" style="margin-top: 10px;"><?php _e('Upload Image'); ?></button>
        <input type="hidden" name="channel_image_nonce" value="<?php echo wp_create_nonce('channel_image_nonce'); ?>">
        <br>
        <img id="channel_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>

    <div class="form-field term-group">
        <label for="sponsored_by"><?php _e('Sponsored by'); ?></label>
        <input type="text" id="sponsored_by_search" name="sponsored_by_search" value="" class="regular-text" placeholder="<?php _e('Search for a company...'); ?>">
        <input type="hidden" id="sponsored_by" name="sponsored_by" value="">
        <div id="company_preview" style="margin-top: 10px; display: none;">
            <div class="company-selection-card" style="padding: 15px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 6px; display: flex; align-items: center; gap: 15px;">
                <div class="company-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: #e1e1e1; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666;">
                    <span id="company_initial"></span>
                </div>
                <div class="company-info" style="flex: 1;">
                    <div style="font-weight: 600; color: #23282d; margin-bottom: 2px;">
                        <span id="selected_company_name"></span>
                    </div>
                    <div style="font-size: 12px; color: #666;"><?php _e('Selected as channel sponsor'); ?></div>
                </div>
                <button type="button" id="remove_company" class="button button-small" style="background: #dc3232; color: white; border-color: #dc3232; font-size: 11px;"><?php _e('Remove'); ?></button>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Image upload
        $('.upload_image_button').click(function(e) {
            e.preventDefault();
            var inputField = $(this).prev('input');
            var previewImage = $('#channel_image_preview');

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

        // Company search autocomplete
        $('#sponsored_by_search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: ajax_object.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'search_companies',
                        term: request.term,
                        nonce: ajax_object.nonce
                    },
                    success: function(data) { response(data); }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $('#sponsored_by').val(ui.item.id);
                $('#selected_company_name').text(ui.item.value);
                $('#company_initial').text(ui.item.value.charAt(0).toUpperCase());
                $('#company_preview').show();
                $(this).val('');
                return false;
            }
        });

        // Remove company selection
        $('#remove_company').click(function(e) {
            e.preventDefault();
            $('#sponsored_by').val('');
            $('#sponsored_by_search').val('');
            $('#company_preview').hide();
        });
    });
    </script>
<?php }
add_action('category_add_form_fields', 'add_channel_field');

// Edit Custom Fields in Edit Category Form
function edit_channel_field($term) {
    $image = get_term_meta($term->term_id, 'channel_image', true);
    $sponsored_by = get_term_meta($term->term_id, 'sponsored_by', true);
    
    $company_name = '';
    if ($sponsored_by) {
        $company = get_post($sponsored_by);
        if ($company) {
            $company_name = $company->post_title;
        }
    } ?>
    
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="channel_image"><?php _e('Banner Image'); ?></label></th>
        <td>
            <input type="text" id="channel_image" name="channel_image" value="<?php echo esc_attr($image); ?>" class="regular-text">
            <button type="button" class="upload_image_button button" style="margin-top:10px;"><?php _e('Upload Image'); ?></button>
            <input type="hidden" name="channel_image_nonce" value="<?php echo wp_create_nonce('channel_image_nonce'); ?>">
            <br>
            <img id="channel_image_preview" src="<?php echo esc_attr($image); ?>" style="max-width: 100px; margin-top: 10px; <?php echo $image ? 'display:block;' : 'display:none;'; ?>">
        </td>
    </tr>

    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="sponsored_by"><?php _e('Sponsored by'); ?></label></th>
        <td>
            <input type="text" id="sponsored_by_search" name="sponsored_by_search" value="" class="regular-text" placeholder="<?php _e('Search for a company...'); ?>">
            <input type="hidden" id="sponsored_by" name="sponsored_by" value="<?php echo esc_attr($sponsored_by); ?>">
            <div id="company_preview" style="margin-top: 10px; <?php echo $sponsored_by ? 'display:block;' : 'display:none;'; ?>">
                <div class="company-selection-card" style="padding: 15px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 6px; display: flex; align-items: center; gap: 15px;">
                    <div class="company-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: #e1e1e1; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666;">
                        <span id="company_initial"><?php echo $company_name ? esc_html(strtoupper(substr($company_name, 0, 1))) : ''; ?></span>
                    </div>
                    <div class="company-info" style="flex: 1;">
                        <div style="font-weight: 600; color: #23282d; margin-bottom: 2px;">
                            <span id="selected_company_name"><?php echo esc_html($company_name); ?></span>
                        </div>
                        <div style="font-size: 12px; color: #666;"><?php _e('Selected as channel sponsor'); ?></div>
                    </div>
                    <button type="button" id="remove_company" class="button button-small" style="background: #dc3232; color: white; border-color: #dc3232; font-size: 11px;"><?php _e('Remove'); ?></button>
                </div>
            </div>
        </td>
    </tr>

    <script>
    jQuery(document).ready(function($) {
        // Image upload
        $('.upload_image_button').click(function(e) {
            e.preventDefault();
            var inputField = $(this).prev('input');
            var previewImage = $('#channel_image_preview');

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

        // Company search autocomplete
        $('#sponsored_by_search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: ajax_object.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'search_companies',
                        term: request.term,
                        nonce: ajax_object.nonce
                    },
                    success: function(data) { response(data); }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $('#sponsored_by').val(ui.item.id);
                $('#selected_company_name').text(ui.item.value);
                $('#company_initial').text(ui.item.value.charAt(0).toUpperCase());
                $('#company_preview').show();
                $(this).val('');
                return false;
            }
        });

        // Remove company selection
        $('#remove_company').click(function(e) {
            e.preventDefault();
            $('#sponsored_by').val('');
            $('#sponsored_by_search').val('');
            $('#company_preview').hide();
        });
    });
    </script>
<?php }
add_action('category_edit_form_fields', 'edit_channel_field');

// Save Custom Fields
function save_channel_field($term_id) {
    if (!isset($_POST['channel_image_nonce']) || !wp_verify_nonce($_POST['channel_image_nonce'], 'channel_image_nonce')) {
        return;
    }

    if (isset($_POST['channel_image'])) {
        update_term_meta($term_id, 'channel_image', esc_url($_POST['channel_image']));
    }

    if (isset($_POST['sponsored_by'])) {
        $sponsored_by = intval($_POST['sponsored_by']);
        if ($sponsored_by > 0) {
            update_term_meta($term_id, 'sponsored_by', $sponsored_by);
        } else {
            delete_term_meta($term_id, 'sponsored_by');
        }
    }
}
add_action('created_category', 'save_channel_field');
add_action('edited_category', 'save_channel_field');

// Helper function to get sponsored company data
function get_channel_sponsor($term_id) {
    $sponsored_by = get_term_meta($term_id, 'sponsored_by', true);
    if ($sponsored_by) {
        $company = get_post($sponsored_by);
        if ($company && $company->post_status === 'publish') {
            return [
                'id' => $company->ID,
                'title' => $company->post_title,
                'url' => get_permalink($company->ID),
                'featured_image' => get_the_post_thumbnail_url($company->ID, 'full')
            ];
        }
    }
    return false;
}

// Add "Sponsored By" column to the channels/categories admin table
function add_sponsored_by_column($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'name') {
            $new_columns['sponsored_by'] = __('Sponsored By');
        }
    }
    return $new_columns;
}
add_filter('manage_edit-category_columns', 'add_sponsored_by_column');

// Populate the "Sponsored By" column
function populate_sponsored_by_column($content, $column_name, $term_id) {
    if ($column_name === 'sponsored_by') {
        $sponsored_by = get_term_meta($term_id, 'sponsored_by', true);

        if ($sponsored_by) {
            $company = get_post($sponsored_by);
            if ($company && $company->post_status === 'publish') {
                $company_image = get_the_post_thumbnail_url($company->ID, 'full');
                
                $content = '<a href="' . esc_url(get_edit_post_link($company->ID)) . '" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">';
                
                if ($company_image) {
                    $content .= '<img src="' . esc_url($company_image) . '" style="max-width: 80px; border-radius: 4px; object-fit: cover;">';
                } else {
                    $initial = strtoupper(substr($company->post_title, 0, 1));
                    $content .= '<div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666;">' . $initial . '</div>';
                }
                
                // $content .= '<span>' . esc_html($company->post_title) . '</span>';
                $content .= '</a>';
            } else {
                $content = '<span style="color: #dc3232; font-style: italic;">' . __('Invalid Company') . '</span>';
            }
        }
    }
    return $content;
}
add_action('manage_category_custom_column', 'populate_sponsored_by_column', 10, 3);