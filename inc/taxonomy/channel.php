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
function assign_default_category_to_custom_post_type($post_id, $post, $update)
{
    // Only run on save (not autosave or revisions)
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (wp_is_post_revision($post_id))
        return;

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
function custom_category_base_on_theme_activation()
{
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

        // Enqueue script for company search
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_localize_script('category-image-upload', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('company_search_nonce')
        ));
    }
}
add_action('admin_enqueue_scripts', 'enqueue_category_image_uploader');

// AJAX handler for company search
function handle_company_search()
{
    check_ajax_referer('company_search_nonce', 'nonce');

    $search_term = sanitize_text_field($_POST['term']);
    $current_term_id = isset($_POST['current_term_id']) ? intval($_POST['current_term_id']) : 0;

    $companies = get_posts(array(
        'post_type' => 'company',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        's' => $search_term,
        'meta_query' => array()
    ));

    // Get all terms that have sponsored companies
    $used_companies = array();
    $terms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => 'sponsored_by',
                'compare' => 'EXISTS'
            )
        )
    ));

    foreach ($terms as $term) {
        // Skip current term being edited
        if ($term->term_id == $current_term_id) {
            continue;
        }

        $sponsored_by = get_term_meta($term->term_id, 'sponsored_by', true);
        if ($sponsored_by) {
            $used_companies[$sponsored_by] = $term->name;
        }
    }

    $results = array();
    foreach ($companies as $company) {
        $item = array(
            'id' => $company->ID,
            'label' => $company->post_title,
            'value' => $company->post_title
        );

        // Add warning if company is already used
        if (isset($used_companies[$company->ID])) {
            $item['label'] .= ' (Currently used in: ' . $used_companies[$company->ID] . ')';
            $item['used_in'] = $used_companies[$company->ID];
            $item['is_used'] = true;
        }

        $results[] = $item;
    }

    wp_send_json($results);
}
add_action('wp_ajax_search_companies', 'handle_company_search');

// Add Custom Fields to Add Category Form
function add_channel_field()
{
    $nonce = wp_create_nonce('channel_image_nonce');
    ?>
    <div class="form-field term-group">
        <label for="channel_image"><?php _e('Banner Image', 'text_domain'); ?></label>
        <input type="text" id="channel_image" name="channel_image" value="" class="regular-text">
        <button class="upload_image_button button"
            style="margin-top: 10px;"><?php _e('Upload Image', 'text_domain'); ?></button>
        <input type="hidden" name="channel_image_nonce" value="<?php echo esc_attr($nonce); ?>">
        <br>
        <img id="channel_image_preview" src="" style="max-width: 100px; margin-top: 10px; display: none;">
    </div>

    <div class="form-field term-group">
        <label for="sponsored_by"><?php _e('Sponsored by', 'text_domain'); ?></label>
        <input type="text" id="sponsored_by_search" name="sponsored_by_search" value="" class="regular-text"
            placeholder="<?php _e('Search for a company...', 'text_domain'); ?>">
        <input type="hidden" id="sponsored_by" name="sponsored_by" value="">
        <div id="company_preview" style="margin-top: 10px; display: none;">
            <div class="company-selection-card"
                style="padding: 15px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 6px; display: flex; align-items: center; gap: 15px;">
                <div class="company-avatar"
                    style="width: 40px; height: 40px; border-radius: 50%; background: #e1e1e1; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666;">
                    <span id="company_initial"></span>
                </div>
                <div class="company-info" style="flex: 1;">
                    <div style="font-weight: 600; color: #23282d; margin-bottom: 2px;">
                        <span id="selected_company_name"></span>
                    </div>
                    <div style="font-size: 12px; color: #666;">
                        <?php _e('Selected as channel sponsor', 'text_domain'); ?>
                    </div>
                </div>
                <button type="button" id="remove_company" class="button button-small"
                    style="background: #dc3232; color: white; border-color: #dc3232; font-size: 11px;">
                    <?php _e('Remove', 'text_domain'); ?>
                </button>
            </div>
        </div>
        <div id="conflict_warning" style="margin-top: 10px; display: none;">
            <div style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                <strong><?php _e('Warning:', 'text_domain'); ?></strong>
                <span id="conflict_message"></span>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Custom CSS for autocomplete dropdown
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .ui-autocomplete {
                        max-height: 300px;
                        overflow-y: auto;
                        overflow-x: hidden;
                        border: 1px solid #ddd !important;
                        border-radius: 4px !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
                        background: white !important;
                        padding: 4px !important;
                        margin-top: 2px !important;
                        z-index: 999999 !important;
                    }
                    .ui-autocomplete .ui-menu-item {
                        border: none !important;
                        margin: 2px 0 !important;
                        border-radius: 6px !important;
                        overflow: hidden !important;
                    }
                    .ui-autocomplete .ui-menu-item:hover .company-item-wrapper,
                    .ui-autocomplete .ui-menu-item.ui-state-active .company-item-wrapper,
                    .ui-autocomplete .ui-menu-item.ui-state-focus .company-item-wrapper {
                        background: #f8f9fa !important;
                        transform: translateX(2px) !important;
                    }
                    .ui-autocomplete .ui-menu-item a {
                        border: none !important;
                        background: none !important;
                        padding: 0 !important;
                        display: block !important;
                        color: inherit !important;
                    }
                    .ui-helper-hidden-accessible {
                        display: none !important;
                    }
                `)
                .appendTo('head');

            // Image upload functionality
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

            // Enhanced company search autocomplete
            $('#sponsored_by_search').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: ajax_object.ajax_url,
                        dataType: 'json',
                        data: {
                            action: 'search_companies',
                            term: request.term,
                            current_term_id: 0,
                            nonce: ajax_object.nonce
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    if (ui.item.is_used) {
                        $('#conflict_message').text('This company is currently sponsoring "' + ui.item.used_in + '". Selecting it will remove it from that channel.');
                        $('#conflict_warning').show();
                    } else {
                        $('#conflict_warning').hide();
                    }

                    $('#sponsored_by').val(ui.item.id);
                    $('#selected_company_name').text(ui.item.value);
                    $('#company_initial').text(ui.item.value.charAt(0).toUpperCase());
                    $('#company_preview').show();
                    $(this).val('');
                    return false;
                }
            }).autocomplete("instance")._renderItem = function (ul, item) {
                var listItem = $("<li>").addClass("company-search-item");
                var initial = item.value.charAt(0).toUpperCase();
                var content = "<div class='company-item-wrapper' style='display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 6px; transition: all 0.2s ease;'>";

                // Company avatar
                content += "<div class='company-avatar-small' style='width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 12px; flex-shrink: 0;'>";
                content += initial;
                content += "</div>";

                // Company info
                content += "<div class='company-details' style='flex: 1; min-width: 0;'>";
                if (item.is_used) {
                    content += "<div style='color: #d63384; font-weight: 600; font-size: 14px; margin-bottom: 2px;'>" + item.value + "</div>";
                    content += "<div style='font-size: 11px; color: #6c757d; display: flex; align-items: center; gap: 4px;'>";
                    content += "<span style='display: inline-block; width: 6px; height: 6px; background: #ffc107; border-radius: 50%; flex-shrink: 0;'></span>";
                    content += "Used in: " + item.used_in;
                    content += "</div>";
                } else {
                    content += "<div style='color: #2c3e50; font-weight: 500; font-size: 14px; margin-bottom: 2px;'>" + item.value + "</div>";
                    content += "<div style='font-size: 11px; color: #27ae60; display: flex; align-items: center; gap: 4px;'>";
                    content += "<span style='display: inline-block; width: 6px; height: 6px; background: #27ae60; border-radius: 50%; flex-shrink: 0;'></span>";
                    content += "Available";
                    content += "</div>";
                }
                content += "</div>";

                // Status indicator
                if (item.is_used) {
                    content += "<div style='background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;'>In Use</div>";
                } else {
                    content += "<div style='background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;'>Available</div>";
                }

                content += "</div>";
                return listItem.append(content).appendTo(ul);
            };

            // Remove company selection
            $('#remove_company').click(function (e) {
                e.preventDefault();
                $('#sponsored_by').val('');
                $('#sponsored_by_search').val('');
                $('#company_preview').hide();
                $('#conflict_warning').hide();
            });
        });
    </script>
    <?php
}
add_action('category_add_form_fields', 'add_channel_field', 10, 2);

// Edit Custom Fields in Edit Category Form
function edit_channel_field($term)
{
    $image = get_term_meta($term->term_id, 'channel_image', true);
    $sponsored_by = get_term_meta($term->term_id, 'sponsored_by', true);
    $nonce = wp_create_nonce('channel_image_nonce');

    // Get company details if selected
    $company_name = '';
    if ($sponsored_by) {
        $company = get_post($sponsored_by);
        if ($company) {
            $company_name = $company->post_title;
        }
    }
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="channel_image"><?php _e('Banner Image', 'text_domain'); ?></label></th>
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

    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="sponsored_by"><?php _e('Sponsored by', 'text_domain'); ?></label></th>
        <td>
            <input type="text" id="sponsored_by_search" name="sponsored_by_search" value="" class="regular-text"
                placeholder="<?php _e('Search for a company...', 'text_domain'); ?>">
            <input type="hidden" id="sponsored_by" name="sponsored_by" value="<?php echo esc_attr($sponsored_by); ?>">
            <div id="company_preview"
                style="margin-top: 10px; <?php echo $sponsored_by ? 'display:block;' : 'display:none;'; ?>">
                <div class="company-selection-card"
                    style="padding: 15px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 6px; display: flex; align-items: center; gap: 15px;">
                    <div class="company-avatar"
                        style="width: 40px; height: 40px; border-radius: 50%; background: #e1e1e1; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666;">
                        <span
                            id="company_initial"><?php echo $company_name ? esc_html(strtoupper(substr($company_name, 0, 1))) : ''; ?></span>
                    </div>
                    <div class="company-info" style="flex: 1;">
                        <div style="font-weight: 600; color: #23282d; margin-bottom: 2px;">
                            <span id="selected_company_name"><?php echo esc_html($company_name); ?></span>
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            <?php _e('Selected as channel sponsor', 'text_domain'); ?>
                        </div>
                    </div>
                    <button type="button" id="remove_company" class="button button-small"
                        style="background: #dc3232; color: white; border-color: #dc3232; font-size: 11px;">
                        <?php _e('Remove', 'text_domain'); ?>
                    </button>
                </div>
            </div>
            <div id="conflict_warning" style="margin-top: 10px; display: none;">
                <div
                    style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                    <strong><?php _e('Warning:', 'text_domain'); ?></strong>
                    <span id="conflict_message"></span>
                </div>
            </div>
        </td>
    </tr>

    <script>
        jQuery(document).ready(function ($) {
            // Custom CSS for autocomplete dropdown
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .ui-autocomplete {
                        max-height: 300px;
                        overflow-y: auto;
                        overflow-x: hidden;
                        border: 1px solid #ddd !important;
                        border-radius: 4px !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
                        background: white !important;
                        padding: 4px !important;
                        margin-top: 2px !important;
                        z-index: 999999 !important;
                    }
                    .ui-autocomplete .ui-menu-item {
                        border: none !important;
                        margin: 2px 0 !important;
                        border-radius: 6px !important;
                        overflow: hidden !important;
                    }
                    .ui-autocomplete .ui-menu-item:hover .company-item-wrapper,
                    .ui-autocomplete .ui-menu-item.ui-state-active .company-item-wrapper,
                    .ui-autocomplete .ui-menu-item.ui-state-focus .company-item-wrapper {
                        background: #f8f9fa !important;
                        transform: translateX(2px) !important;
                    }
                    .ui-autocomplete .ui-menu-item a {
                        border: none !important;
                        background: none !important;
                        padding: 0 !important;
                        display: block !important;
                        color: inherit !important;
                    }
                    .ui-helper-hidden-accessible {
                        display: none !important;
                    }
                `)
                .appendTo('head');

            // Image upload functionality
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

            // Enhanced company search autocomplete
            $('#sponsored_by_search').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: ajax_object.ajax_url,
                        dataType: 'json',
                        data: {
                            action: 'search_companies',
                            term: request.term,
                            current_term_id: <?php echo intval($term->term_id); ?>,
                            nonce: ajax_object.nonce
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    if (ui.item.is_used) {
                        $('#conflict_message').text('This company is currently sponsoring "' + ui.item.used_in + '". Selecting it will remove it from that channel.');
                        $('#conflict_warning').show();
                    } else {
                        $('#conflict_warning').hide();
                    }

                    $('#sponsored_by').val(ui.item.id);
                    $('#selected_company_name').text(ui.item.value);
                    $('#company_initial').text(ui.item.value.charAt(0).toUpperCase());
                    $('#company_preview').show();
                    $(this).val('');
                    return false;
                }
            }).autocomplete("instance")._renderItem = function (ul, item) {
                var listItem = $("<li>").addClass("company-search-item");
                var initial = item.value.charAt(0).toUpperCase();
                var content = "<div class='company-item-wrapper' style='display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 6px; transition: all 0.2s ease;'>";

                // Company avatar
                content += "<div class='company-avatar-small' style='width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 12px; flex-shrink: 0;'>";
                content += initial;
                content += "</div>";

                // Company info
                content += "<div class='company-details' style='flex: 1; min-width: 0;'>";
                if (item.is_used) {
                    content += "<div style='color: #d63384; font-weight: 600; font-size: 14px; margin-bottom: 2px;'>" + item.value + "</div>";
                    content += "<div style='font-size: 11px; color: #6c757d; display: flex; align-items: center; gap: 4px;'>";
                    content += "<span style='display: inline-block; width: 6px; height: 6px; background: #ffc107; border-radius: 50%; flex-shrink: 0;'></span>";
                    content += "Used in: " + item.used_in;
                    content += "</div>";
                } else {
                    content += "<div style='color: #2c3e50; font-weight: 500; font-size: 14px; margin-bottom: 2px;'>" + item.value + "</div>";
                    content += "<div style='font-size: 11px; color: #27ae60; display: flex; align-items: center; gap: 4px;'>";
                    content += "<span style='display: inline-block; width: 6px; height: 6px; background: #27ae60; border-radius: 50%; flex-shrink: 0;'></span>";
                    content += "Available";
                    content += "</div>";
                }
                content += "</div>";

                // Status indicator
                if (item.is_used) {
                    content += "<div style='background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;'>In Use</div>";
                } else {
                    content += "<div style='background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;'>Available</div>";
                }

                content += "</div>";
                return listItem.append(content).appendTo(ul);
            };

            // Remove company selection
            $('#remove_company').click(function (e) {
                e.preventDefault();
                $('#sponsored_by').val('');
                $('#sponsored_by_search').val('');
                $('#company_preview').hide();
                $('#conflict_warning').hide();
            });
        });
    </script>
    <?php
}
add_action('category_edit_form_fields', 'edit_channel_field', 10, 2);

// Remove company from other channels if it is already assigned to another channel
function remove_company_from_other_channels($term_id, $company_id)
{
    if (!$company_id) {
        return;
    }

    $terms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => 'sponsored_by',
                'value' => $company_id,
                'compare' => '='
            )
        )
    ));

    foreach ($terms as $term) {
        if ($term->term_id != $term_id) {
            delete_term_meta($term->term_id, 'sponsored_by');
        }
    }
}

// Save Custom Fields on Create/Edit
function save_channel_field($term_id)
{
    if (!isset($_POST['channel_image_nonce']) || !wp_verify_nonce($_POST['channel_image_nonce'], 'channel_image_nonce')) {
        return;
    }

    if (isset($_POST['channel_image'])) {
        update_term_meta($term_id, 'channel_image', esc_url($_POST['channel_image']));
    }

    if (isset($_POST['sponsored_by'])) {
        $sponsored_by = intval($_POST['sponsored_by']);
        if ($sponsored_by > 0) {
            // Remove company from other channels first
            remove_company_from_other_channels($term_id, $sponsored_by);
            update_term_meta($term_id, 'sponsored_by', $sponsored_by);
        } else {
            delete_term_meta($term_id, 'sponsored_by');
        }
    }
}

add_action('created_category', 'save_channel_field', 10, 2);
add_action('edited_category', 'save_channel_field', 10, 2);

// Helper function to get sponsored company data
function get_channel_sponsor($term_id)
{
    $sponsored_by = get_term_meta($term_id, 'sponsored_by', true);
    if ($sponsored_by) {
        $company = get_post($sponsored_by);
        if ($company && $company->post_status === 'publish') {
            return array(
                'id' => $company->ID,
                'title' => $company->post_title,
                'url' => get_permalink($company->ID),
                'featured_image' => get_the_post_thumbnail_url($company->ID, 'full')
            );
        }
    }
    return false;
}

// Add "Sponsored By" column to the channels/categories admin table
function add_sponsored_by_column($columns)
{
    // Insert the new column after the "Name" column
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'name') {
            $new_columns['sponsored_by'] = __('Sponsored By', 'text_domain');
        }
    }
    return $new_columns;
}
add_filter('manage_edit-category_columns', 'add_sponsored_by_column');

// Populate the "Sponsored By" column with company data
function populate_sponsored_by_column($content, $column_name, $term_id)
{
    if ($column_name === 'sponsored_by') {
        $sponsored_by = get_term_meta($term_id, 'sponsored_by', true);

        if ($sponsored_by) {
            $company = get_post($sponsored_by);
            if ($company && $company->post_status === 'publish') {
                // Get company logo/featured image
                $company_image = get_the_post_thumbnail_url($company->ID, 'full');
                $company_initial = strtoupper(substr($company->post_title, 0, 1));

                if ($company_image) {

                    $content .= '<a href="' . esc_url(get_edit_post_link($company->ID)) . '" style="text-decoration: none; color: inherit;">';
                    $content .= '<img src="' . $company_image . '" style="max-width: 80px; height: auto; border-radius: 4px;">';
                    $content .= '</a>';
                } else {
                    $content .= '<a href="' . esc_url(get_edit_post_link($company->ID)) . '" style="text-decoration: none; color: inherit;">';
                    $content = '<div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 20px;">🏢</div>';
                    $content .= '</a>';
                }
            } else {
                // Company exists but is not published or was deleted
                $content = '<div style="color: #dc3232; font-style: italic; display: flex; align-items: center; gap: 6px;">';
                $content .= '<span style="display: inline-block; width: 6px; height: 6px; background: #dc3232; border-radius: 50%;"></span>';
                $content .= __('Invalid Company', 'text_domain');
                $content .= '</div>';
            }
        } /*else {
            // No sponsor assigned
            $content = '<div style="color: #8a8a8a; display: flex; align-items: center; gap: 6px;">';
            $content .= '<span style="display: inline-block; width: 6px; height: 6px; background: #8a8a8a; border-radius: 50%;"></span>';
            $content .= __('No Sponsor', 'text_domain');
            $content .= '</div>';
        }*/
    }
    return $content;
}
add_action('manage_category_custom_column', 'populate_sponsored_by_column', 10, 3);
