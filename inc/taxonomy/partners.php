<?php
/**
 * Partners Taxonomy
 *
 * This file registers a custom taxonomy for partners that can be used across multiple post types
 * and includes meta boxes for partner-specific logo and social links.
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Partners Taxonomy
 */
function register_partners_taxonomy() {
    $labels = array(
        'name'                       => _x('Partners', 'taxonomy general name', 'opengovasia'),
        'singular_name'              => _x('Partner', 'taxonomy singular name', 'opengovasia'),
        'search_items'               => __('Search Partners', 'opengovasia'),
        'popular_items'              => __('Popular Partners', 'opengovasia'),
        'all_items'                  => __('All Partners', 'opengovasia'),
        'parent_item'                => __('Parent Partner', 'opengovasia'),
        'parent_item_colon'          => __('Parent Partner:', 'opengovasia'),
        'edit_item'                  => __('Edit Partner', 'opengovasia'),
        'update_item'                => __('Update Partner', 'opengovasia'),
        'add_new_item'               => __('Add New Partner', 'opengovasia'),
        'new_item_name'              => __('New Partner Name', 'opengovasia'),
        'separate_items_with_commas' => __('Separate partners with commas', 'opengovasia'),
        'add_or_remove_items'        => __('Add or remove partners', 'opengovasia'),
        'choose_from_most_used'      => __('Choose from the most used partners', 'opengovasia'),
        'not_found'                  => __('No partners found.', 'opengovasia'),
        'menu_name'                  => __('Partners', 'opengovasia'),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'partner'),
        'show_in_rest'          => true,
        'rest_base'             => 'partners',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
    );

    register_taxonomy('partner', array('events', 'company', 'awards'), $args);
}
add_action('init', 'register_partners_taxonomy');

/**
 * Add Meta Fields to Partner Taxonomy
 */
function add_partner_taxonomy_fields() {
    add_action('partner_add_form_fields', 'add_partner_term_fields');
    add_action('partner_edit_form_fields', 'edit_partner_term_fields', 10, 2);
    add_action('created_partner', 'save_partner_term_fields', 10, 2);
    add_action('edited_partner', 'save_partner_term_fields', 10, 2);
}
add_action('init', 'add_partner_taxonomy_fields');

/**
 * Add Partner term fields on add screen
 */
function add_partner_term_fields() {
    wp_enqueue_media();
    ?>
    <div class="form-field term-partner-logo-wrap">
        <label for="partner-logo"><?php _e('Partner Logo', 'opengovasia'); ?></label>
        <input type="url" name="partner_logo" id="partner-logo" class="partner-logo-url" value="" style="margin-bottom: 10px;" />
        <p>
            <button type="button" class="choose-partner-logo button button-secondary"><?php _e('Choose Logo', 'opengovasia'); ?></button>
        </p>
        <div class="partner-logo-preview" style="margin-top: 10px; max-width: 200px;"></div>
        <p class="description"><?php _e('Upload or select the partner\'s logo.', 'opengovasia'); ?></p>
    </div>
    
    <div class="form-field term-partner-socials-wrap">
        <label><?php _e('Social Links', 'opengovasia'); ?></label>
        <div id="partner-socials-list">
            <div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">
                <select name="partner_socials[0][platform]" style="width: 120px; margin-right: 10px;">
                    <option value="">Select Platform</option>
                    <option value="facebook">Facebook</option>
                    <option value="twitter">Twitter</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="instagram">Instagram</option>
                    <option value="youtube">YouTube</option>
                    <option value="website">Website</option>
                </select>
                <input type="url" name="partner_socials[0][url]" placeholder="Social URL" style="flex: 1;">
                <button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>
            </div>
        </div>
        <button type="button" class="add-social button button-secondary" data-partner-index="0">+ Add Social</button>
    </div>
    
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Media Library Image Picker for Partner Logo
            $('.choose-partner-logo').on('click', function(e) {
                e.preventDefault();
                var logoField = $('#partner-logo');
                var previewContainer = $('.partner-logo-preview');

                var mediaFrame = wp.media({
                    title: 'Select Partner Logo',
                    button: { text: 'Use this logo' },
                    multiple: false
                });

                mediaFrame.on('select', function() {
                    var attachment = mediaFrame.state().get('selection').first().toJSON();
                    logoField.val(attachment.url);

                    // Update preview
                    previewContainer.html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto; max-height: 150px;">');
                });

                mediaFrame.open();
            });

            // Add Social Link dynamically
            $('.add-social').on('click', function() {
                var list = $('#partner-socials-list');
                var index = list.children().length;
                
                var newSocial = $('<div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">' +
                    '<select name="partner_socials[' + index + '][platform]" style="width: 120px; margin-right: 10px;">' +
                    '<option value="">Select Platform</option>' +
                    '<option value="facebook">Facebook</option>' +
                    '<option value="twitter">Twitter</option>' +
                    '<option value="linkedin">LinkedIn</option>' +
                    '<option value="instagram">Instagram</option>' +
                    '<option value="youtube">YouTube</option>' +
                    '<option value="website">Website</option>' +
                    '</select>' +
                    '<input type="url" name="partner_socials[' + index + '][url]" placeholder="Social URL" style="flex: 1;">' +
                    '<button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>' +
                    '</div>');
                list.append(newSocial);
            });

            // Remove Social Link
            $(document).on('click', '.remove-social', function() {
                $(this).closest('.social-item').remove();
                
                // Reindex the remaining socials
                $('#partner-socials-list .social-item').each(function(index) {
                    $(this).find('select, input').each(function() {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/partner_socials\[\d+\]/, 'partner_socials[' + index + ']'));
                        }
                    });
                });
            });
        });
    </script>
    <?php
}

/**
 * Edit Partner term fields on edit screen
 */
function edit_partner_term_fields($term, $taxonomy) {
    wp_enqueue_media();
    
    $term_id = $term->term_id;
    $partner_logo = get_term_meta($term_id, 'partner_logo', true);
    $partner_socials = get_term_meta($term_id, 'partner_socials', true);
    
    if (!is_array($partner_socials)) {
        $partner_socials = array();
    }
    ?>
    <tr class="form-field term-partner-logo-wrap">
        <th scope="row"><label for="partner-logo"><?php _e('Partner Logo', 'opengovasia'); ?></label></th>
        <td>
            <input type="url" name="partner_logo" id="partner-logo" class="partner-logo-url" value="<?php echo esc_url($partner_logo); ?>" style="margin-bottom: 10px;" />
            <p>
                <button type="button" class="choose-partner-logo button button-secondary"><?php _e('Choose Logo', 'opengovasia'); ?></button>
            </p>
            <?php if (!empty($partner_logo)) : ?>
                <div class="partner-logo-preview" style="margin-top: 10px; max-width: 200px;">
                    <img src="<?php echo esc_url($partner_logo); ?>" style="max-width: 100%; height: auto; max-height: 150px;">
                </div>
            <?php else : ?>
                <div class="partner-logo-preview" style="margin-top: 10px; max-width: 200px;"></div>
            <?php endif; ?>
            <p class="description"><?php _e('Upload or select the partner\'s logo.', 'opengovasia'); ?></p>
        </td>
    </tr>
    
    <tr class="form-field term-partner-socials-wrap">
        <th scope="row"><label><?php _e('Social Links', 'opengovasia'); ?></label></th>
        <td>
            <div id="partner-socials-list">
                <?php
                if (!empty($partner_socials)) {
                    foreach ($partner_socials as $index => $social) {
                        ?>
                        <div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">
                            <select name="partner_socials[<?php echo $index; ?>][platform]" style="width: 120px; margin-right: 10px;">
                                <option value="">Select Platform</option>
                                <option value="facebook" <?php selected($social['platform'] ?? '', 'facebook'); ?>>Facebook</option>
                                <option value="twitter" <?php selected($social['platform'] ?? '', 'twitter'); ?>>Twitter</option>
                                <option value="linkedin" <?php selected($social['platform'] ?? '', 'linkedin'); ?>>LinkedIn</option>
                                <option value="instagram" <?php selected($social['platform'] ?? '', 'instagram'); ?>>Instagram</option>
                                <option value="youtube" <?php selected($social['platform'] ?? '', 'youtube'); ?>>YouTube</option>
                                <option value="website" <?php selected($social['platform'] ?? '', 'website'); ?>>Website</option>
                            </select>
                            <input type="url" name="partner_socials[<?php echo $index; ?>][url]" value="<?php echo esc_url($social['url'] ?? ''); ?>" placeholder="Social URL" style="flex: 1;">
                            <button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">
                        <select name="partner_socials[0][platform]" style="width: 120px; margin-right: 10px;">
                            <option value="">Select Platform</option>
                            <option value="facebook">Facebook</option>
                            <option value="twitter">Twitter</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="instagram">Instagram</option>
                            <option value="youtube">YouTube</option>
                            <option value="website">Website</option>
                        </select>
                        <input type="url" name="partner_socials[0][url]" placeholder="Social URL" style="flex: 1;">
                        <button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>
                    </div>
                    <?php
                }
                ?>
            </div>
            <button type="button" class="add-social button button-secondary">+ Add Social</button>
            <p class="description"><?php _e('Add social media links for this partner.', 'opengovasia'); ?></p>
            
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Media Library Image Picker for Partner Logo
                    $('.choose-partner-logo').on('click', function(e) {
                        e.preventDefault();
                        var logoField = $('#partner-logo');
                        var previewContainer = $('.partner-logo-preview');

                        var mediaFrame = wp.media({
                            title: 'Select Partner Logo',
                            button: { text: 'Use this logo' },
                            multiple: false
                        });

                        mediaFrame.on('select', function() {
                            var attachment = mediaFrame.state().get('selection').first().toJSON();
                            logoField.val(attachment.url);

                            // Update preview
                            previewContainer.html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto; max-height: 150px;">');
                        });

                        mediaFrame.open();
                    });

                    // Add Social Link dynamically
                    $('.add-social').on('click', function() {
                        var list = $('#partner-socials-list');
                        var index = list.children().length;
                        
                        var newSocial = $('<div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">' +
                            '<select name="partner_socials[' + index + '][platform]" style="width: 120px; margin-right: 10px;">' +
                            '<option value="">Select Platform</option>' +
                            '<option value="facebook">Facebook</option>' +
                            '<option value="twitter">Twitter</option>' +
                            '<option value="linkedin">LinkedIn</option>' +
                            '<option value="instagram">Instagram</option>' +
                            '<option value="youtube">YouTube</option>' +
                            '<option value="website">Website</option>' +
                            '</select>' +
                            '<input type="url" name="partner_socials[' + index + '][url]" placeholder="Social URL" style="flex: 1;">' +
                            '<button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>' +
                            '</div>');
                        list.append(newSocial);
                    });

                    // Remove Social Link
                    $(document).on('click', '.remove-social', function() {
                        $(this).closest('.social-item').remove();
                        
                        // Reindex the remaining socials
                        $('#partner-socials-list .social-item').each(function(index) {
                            $(this).find('select, input').each(function() {
                                var name = $(this).attr('name');
                                if (name) {
                                    $(this).attr('name', name.replace(/partner_socials\[\d+\]/, 'partner_socials[' + index + ']'));
                                }
                            });
                        });
                    });
                });
            </script>
        </td>
    </tr>
    <?php
}

/**
 * Save Partner term meta
 */
function save_partner_term_fields($term_id, $tt_id) {
    if (isset($_POST['partner_logo'])) {
        update_term_meta($term_id, 'partner_logo', esc_url_raw($_POST['partner_logo']));
    }
    
    if (isset($_POST['partner_socials']) && is_array($_POST['partner_socials'])) {
        $socials = array();
        foreach ($_POST['partner_socials'] as $social) {
            if (!empty($social['platform']) && !empty($social['url'])) {
                $socials[] = array(
                    'platform' => sanitize_text_field($social['platform']),
                    'url' => esc_url_raw($social['url'])
                );
            }
        }
        update_term_meta($term_id, 'partner_socials', $socials);
    }
}

/**
 * Add Partner Meta to REST API
 */
function register_partner_meta_rest_fields() {
    register_rest_field('partner', 'partner_logo', array(
        'get_callback' => function($term_arr) {
            return get_term_meta($term_arr['id'], 'partner_logo', true);
        },
        'schema' => array(
            'description' => __('Partner Logo URL'),
            'type' => 'string'
        ),
    ));
    
    register_rest_field('partner', 'partner_socials', array(
        'get_callback' => function($term_arr) {
            return get_term_meta($term_arr['id'], 'partner_socials', true);
        },
        'schema' => array(
            'description' => __('Partner Social Links'),
            'type' => 'array'
        ),
    ));
}
add_action('rest_api_init', 'register_partner_meta_rest_fields');

/**
 * Add Partner Selector Field to Posts
 */
function add_partner_selector_field() {
    // Add to post types
    add_meta_box(
        'partner_selector',
        __('Partners', 'opengovasia'),
        'render_partner_selector',
        array('events', 'company', 'awards'),
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_partner_selector_field');

/**
 * Render Partner Selector Field
 */
function render_partner_selector($post) {
    wp_nonce_field('partner_selector_save', 'partner_selector_nonce');
    
    // Get currently selected partners
    $selected_partners = get_the_terms($post->ID, 'partner');
    $selected_partner_ids = array();
    
    if (!empty($selected_partners) && !is_wp_error($selected_partners)) {
        foreach($selected_partners as $partner) {
            $selected_partner_ids[] = $partner->term_id;
        }
    }
    
    // Output the selector
    ?>
    <div class="partner-selector-container">
        <div class="partner-search">
            <input type="text" id="partner-search-input" placeholder="<?php _e('Search for partners...', 'opengovasia'); ?>" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 4px;">
            <div id="partner-search-results" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; margin-bottom: 15px; display: none; border-radius: 4px;"></div>
        </div>
        
        <div class="selected-partners">
            <h4><?php _e('Selected Partners', 'opengovasia'); ?></h4>
            <ul id="selected-partners-list" style="margin-top: 0; padding: 0; list-style: none; width: 100%;">
                <?php
                if (!empty($selected_partners) && !is_wp_error($selected_partners)) {
                    foreach($selected_partners as $partner) {
                        echo '<li data-id="' . esc_attr($partner->term_id) . '" style="padding: 10px; margin-bottom: 5px; background: #f8f8f8; border: 1px solid #ddd; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">' . 
                             '<span>' . esc_html($partner->name) . '</span>' .
                             '<button type="button" class="remove-partner button button-small" style="background: #f1f1f1; border-color: #c3c4c7; color: #a00;">Remove</button>' .
                             '<input type="hidden" name="selected_partners[]" value="' . esc_attr($partner->term_id) . '">' .
                             '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var searchTimeout;
        var searchInput = $('#partner-search-input');
        var searchResults = $('#partner-search-results');
        var selectedList = $('#selected-partners-list');
        
        // Search for partners
        searchInput.on('keyup', function() {
            clearTimeout(searchTimeout);
            var query = $(this).val();
            
            if (query.length < 2) {
                searchResults.hide();
                return;
            }
            
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'search_partners',
                        query: query,
                        nonce: '<?php echo wp_create_nonce('search_partners_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            searchResults.empty();
                            
                            $.each(response.data, function(index, partner) {
                                // Skip if already selected
                                if ($('#selected-partners-list li[data-id="' + partner.id + '"]').length > 0) {
                                    return;
                                }
                                
                                var item = $('<div class="partner-result" data-id="' + partner.id + '" style="padding: 10px; cursor: pointer;">' + 
                                            partner.name + '</div>');
                                
                                item.hover(
                                    function() { $(this).css('background-color', '#f0f0f0'); },
                                    function() { $(this).css('background-color', 'transparent'); }
                                );
                                
                                searchResults.append(item);
                            });
                            
                            searchResults.show();
                        } else {
                            searchResults.html('<div style="padding: 10px;">No partners found</div>');
                            searchResults.show();
                        }
                    }
                });
            }, 300);
        });
        
        // Select a partner from results
        $(document).on('click', '.partner-result', function() {
            var partnerId = $(this).data('id');
            var partnerName = $(this).text();
            
            // Add to selected list
            var selectedItem = $('<li data-id="' + partnerId + '" style="padding: 10px; margin-bottom: 5px; background: #f8f8f8; border: 1px solid #ddd; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">' + 
                                '<span>' + partnerName + '</span>' +
                                '<button type="button" class="remove-partner button button-small" style="background: #f1f1f1; border-color: #c3c4c7; color: #a00;">Remove</button>' +
                                '<input type="hidden" name="selected_partners[]" value="' + partnerId + '">' +
                                '</li>');
            
            selectedList.append(selectedItem);
            
            // Clear search
            searchInput.val('');
            searchResults.hide();
        });
        
        // Remove a selected partner
        $(document).on('click', '.remove-partner', function(e) {
            e.preventDefault();
            $(this).closest('li').remove();
        });
    });
    </script>
    <?php
}

/**
 * AJAX handler for partner search
 */
function search_partners_callback() {
    // Security check
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'search_partners_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $query = sanitize_text_field($_GET['query']);
    
    if (empty($query)) {
        wp_send_json_error('Empty query');
    }
    
    $partners = get_terms(array(
        'taxonomy' => 'partner',
        'hide_empty' => false,
        'search' => $query,
        'number' => 10
    ));
    
    $results = array();
    
    if (!empty($partners) && !is_wp_error($partners)) {
        foreach ($partners as $partner) {
            $results[] = array(
                'id' => $partner->term_id,
                'name' => $partner->name
            );
        }
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_search_partners', 'search_partners_callback');

/**
 * Save Partner Relationships
 */
function save_partner_relationships($post_id) {
    // Security checks
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['partner_selector_nonce']) || !wp_verify_nonce($_POST['partner_selector_nonce'], 'partner_selector_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Save partners
    if (isset($_POST['selected_partners'])) {
        $partners = array_map('intval', $_POST['selected_partners']);
        wp_set_object_terms($post_id, $partners, 'partner');
    } else {
        // Remove all partners if none selected
        wp_set_object_terms($post_id, array(), 'partner');
    }
}
add_action('save_post', 'save_partner_relationships');