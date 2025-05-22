<?php
/**
 * Partners Custom Post Type
 *
 * This file registers a custom post type for partners and maintains relationships
 * with other post types (events, company, awards).
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}



/**
 * Add Partner Meta Boxes
 */
function add_partner_meta_boxes()
{
    add_meta_box(
        'partner_social_links',
        __('Social Links', 'opengovasia'),
        'render_partner_social_links',
        'partner',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_partner_meta_boxes');

/**
 * Partner Social Links Meta Box
 */
function render_partner_social_links($post)
{
    wp_nonce_field('partner_social_links_save', 'partner_social_links_nonce');

    $partner_socials = get_post_meta($post->ID, '_partner_socials', true);

    if (!is_array($partner_socials)) {
        $partner_socials = array();
    }
    ?>
    <div class="partner-socials-wrap">
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
                        <input type="url" name="partner_socials[<?php echo $index; ?>][url]"
                            value="<?php echo esc_url($social['url'] ?? ''); ?>" placeholder="Social URL" style="flex: 1;">
                        <button type="button" class="remove-social button button-secondary"
                            style="margin-left:10px;">Remove</button>
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
                    <button type="button" class="remove-social button button-secondary"
                        style="margin-left:10px;">Remove</button>
                </div>
                <?php
            }
            ?>
        </div>
        <button type="button" class="add-social button button-secondary">+ Add Social</button>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                // Add Social Link dynamically
                $('.add-social').on('click', function () {
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
                $(document).on('click', '.remove-social', function () {
                    $(this).closest('.social-item').remove();

                    // Reindex the remaining socials
                    $('#partner-socials-list .social-item').each(function (index) {
                        $(this).find('select, input').each(function () {
                            var name = $(this).attr('name');
                            if (name) {
                                $(this).attr('name', name.replace(/partner_socials\[\d+\]/, 'partner_socials[' + index + ']'));
                            }
                        });
                    });
                });
            });
        </script>
    </div>
    <?php
}

/**
 * Save Partner Meta
 */
function save_partner_meta($post_id)
{
    // Security check
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['partner_social_links_nonce']) || !wp_verify_nonce($_POST['partner_social_links_nonce'], 'partner_social_links_save'))
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    // Save social links
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
        update_post_meta($post_id, '_partner_socials', $socials);
    }
}
add_action('save_post_partner', 'save_partner_meta');

/**
 * Add Partner to REST API
 */
function register_partner_meta_rest_fields()
{
    register_rest_field('partner', 'partner_socials', array(
        'get_callback' => function ($post_arr) {
            return get_post_meta($post_arr['id'], '_partner_socials', true);
        },
        'schema' => array(
            'description' => __('Partner Social Links'),
            'type' => 'array'
        ),
    ));
}
add_action('rest_api_init', 'register_partner_meta_rest_fields');

/**
 * Add Partner Selector Field to Post Types
 */
function add_partner_selector_field()
{
    add_meta_box(
        'partner_selector',
        __('Partners', 'opengovasia'),
        'render_partner_selector',
        array('events', 'awards'),
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_partner_selector_field');

/**
 * Render Partner Selector Field
 */
function render_partner_selector($post)
{
    wp_nonce_field('partner_selector_save', 'partner_selector_nonce');

    // Get currently selected partners - UPDATED to use "partners" meta key
    $selected_partners = get_post_meta($post->ID, 'partners', true);

    if (!is_array($selected_partners)) {
        $selected_partners = array();
    }

    // Output the selector
    ?>
    <div class="partner-selector-container">
        <div class="partner-search">
            <input type="text" id="partner-search-input" placeholder="<?php _e('Search for partners...', 'opengovasia'); ?>"
                style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 4px;">
            <div id="partner-search-results"
                style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; margin-bottom: 15px; display: none; border-radius: 4px;">
            </div>
        </div>

        <div class="selected-partners">
            <h4><?php _e('Selected Partners', 'opengovasia'); ?></h4>
            <ul id="selected-partners-list" style="margin-top: 0; padding: 0; list-style: none; width: 100%;">
                <?php
                if (!empty($selected_partners)) {
                    foreach ($selected_partners as $partner_id) {
                        $partner = get_post($partner_id);
                        if ($partner && $partner->post_type == 'partner') {
                            echo '<li data-id="' . esc_attr($partner->ID) . '" style="padding: 10px; margin-bottom: 5px; background: #f8f8f8; border: 1px solid #ddd; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">' .
                                '<span>' . esc_html($partner->post_title) . '</span>' .
                                '<button type="button" class="remove-partner button button-small" style="background: #f1f1f1; border-color: #c3c4c7; color: #a00;">Remove</button>' .
                                '<input type="hidden" name="selected_partners[]" value="' . esc_attr($partner->ID) . '">' .
                                '</li>';
                        }
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var searchTimeout;
            var searchInput = $('#partner-search-input');
            var searchResults = $('#partner-search-results');
            var selectedList = $('#selected-partners-list');

            // Search for partners
            searchInput.on('keyup', function () {
                clearTimeout(searchTimeout);
                var query = $(this).val();

                if (query.length < 2) {
                    searchResults.hide();
                    return;
                }

                searchTimeout = setTimeout(function () {
                    $.ajax({
                        url: ajaxurl,
                        type: 'GET',
                        data: {
                            action: 'search_partners_cpt',
                            query: query,
                            nonce: '<?php echo wp_create_nonce('search_partners_nonce'); ?>'
                        },
                        success: function (response) {
                            if (response.success && response.data.length > 0) {
                                searchResults.empty();

                                $.each(response.data, function (index, partner) {
                                    // Skip if already selected
                                    if ($('#selected-partners-list li[data-id="' + partner.id + '"]').length > 0) {
                                        return;
                                    }

                                    var item = $('<div class="partner-result" data-id="' + partner.id + '" style="padding: 10px; cursor: pointer;">' +
                                        partner.title + '</div>');

                                    item.hover(
                                        function () { $(this).css('background-color', '#f0f0f0'); },
                                        function () { $(this).css('background-color', 'transparent'); }
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
            $(document).on('click', '.partner-result', function () {
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
            $(document).on('click', '.remove-partner', function (e) {
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
function search_partners_cpt_callback()
{
    // Security check
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'search_partners_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $query = sanitize_text_field($_GET['query']);

    if (empty($query)) {
        wp_send_json_error('Empty query');
    }

    $args = array(
        'post_type' => 'partner',
        'post_status' => 'publish',
        's' => $query,
        'posts_per_page' => 10
    );

    $partners_query = new WP_Query($args);
    $results = array();

    if ($partners_query->have_posts()) {
        while ($partners_query->have_posts()) {
            $partners_query->the_post();
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title()
            );
        }
    }

    wp_reset_postdata();
    wp_send_json_success($results);
}
add_action('wp_ajax_search_partners_cpt', 'search_partners_cpt_callback');

/**
 * Save Partner Relationships
 */
function save_partner_relationships($post_id)
{
    // Security checks
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['partner_selector_nonce']) || !wp_verify_nonce($_POST['partner_selector_nonce'], 'partner_selector_save'))
        return;
    if (!current_user_can('edit_post', $post_id))
        return;


    if (isset($_POST['selected_partners'])) {
        $partners = array_map('intval', $_POST['selected_partners']);
        update_post_meta($post_id, 'partners', $partners);

        // Also update partner posts to track the relationship
        foreach ($partners as $partner_id) {
            $related_posts = get_post_meta($partner_id, '_related_posts', true);
            if (!is_array($related_posts)) {
                $related_posts = array();
            }

            if (!in_array($post_id, $related_posts)) {
                $related_posts[] = $post_id;
                update_post_meta($partner_id, '_related_posts', $related_posts);
            }
        }
    } else {
        // Remove all partners if none selected
        $previous_partners = get_post_meta($post_id, 'partners', true);

        if (is_array($previous_partners)) {
            foreach ($previous_partners as $partner_id) {
                $related_posts = get_post_meta($partner_id, '_related_posts', true);

                if (is_array($related_posts)) {
                    $key = array_search($post_id, $related_posts);
                    if ($key !== false) {
                        unset($related_posts[$key]);
                        update_post_meta($partner_id, '_related_posts', array_values($related_posts));
                    }
                }
            }
        }

        delete_post_meta($post_id, 'partners');
    }
}
add_action('save_post', 'save_partner_relationships');

/**
 * Add Related Content Meta Box to Partner CPT
 */
function add_related_content_meta_box()
{
    add_meta_box(
        'partner_related_content',
        __('Related Content', 'opengovasia'),
        'render_related_content',
        'partner',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_related_content_meta_box');

/**
 * Render Related Content Meta Box
 */
function render_related_content($post)
{
    $related_posts = get_post_meta($post->ID, '_related_posts', true);

    if (!is_array($related_posts) || empty($related_posts)) {
        echo '<p>' . __('No related content found for this partner.', 'opengovasia') . '</p>';
        return;
    }

    echo '<ul style="margin-left: 1em;">';

    foreach ($related_posts as $related_id) {
        $related_post = get_post($related_id);

        if ($related_post) {
            echo '<li><a href="' . get_edit_post_link($related_id) . '">' .
                $related_post->post_title . '</a> (' .
                get_post_type_object($related_post->post_type)->labels->singular_name . ')</li>';
        }
    }

    echo '</ul>';
}


/**
 * Register Custom Admin Columns for Partner CPT
 */
function set_partner_admin_columns($columns)
{
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Partner', 'opengovasia'),
        'thumbnail' => __('Partner Logo', 'opengovasia'),
        'related' => __('Related Content', 'opengovasia'),
        'date' => $columns['date']
    );
    return $columns;
}
add_filter('manage_partner_posts_columns', 'set_partner_admin_columns');

/**
 * Handle Partner Custom Admin Columns Content
 */
function partner_custom_column($column, $post_id)
{
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo '<img src="' . get_the_post_thumbnail_url($post_id, 'full') . '" style="max-width: 80px; height: auto;">';
            } else {
                echo '—';
            }
            break;

        case 'related':
            $related_posts = get_post_meta($post_id, '_related_posts', true);
            if (is_array($related_posts) && !empty($related_posts)) {
                echo count($related_posts);
            } else {
                echo '0';
            }
            break;
    }
}
add_action('manage_partner_posts_custom_column', 'partner_custom_column', 10, 2);

/**
 * Register Partners Meta for Multiple Custom Post Types
 */
function register_partners_meta_for_multiple_cpts()
{
    // Make sure we're using the correct post type names
    // Changed from 'event' to 'events' to match your API endpoint
    $cpts = ['events', 'awards'];

    foreach ($cpts as $cpt) {
        register_post_meta($cpt, 'partners', [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => [
                'schema' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer'
                    ]
                ]
            ],
            
        ]);
    }
}
add_action('init', 'register_partners_meta_for_multiple_cpts');

/**
 * Add Partners Data to REST API Response
 */
function add_partners_data_to_rest_api()
{
    $post_types = ['events', 'awards'];
    
    foreach ($post_types as $post_type) {
        register_rest_field($post_type, 'partners_data', [
            'get_callback' => function($post_arr) {
                $partners = get_post_meta($post_arr['id'], 'partners', true);
                
                if (!is_array($partners) || empty($partners)) {
                    return [];
                }
                
                $partners_data = [];
                foreach ($partners as $partner_id) {
                    $partner = get_post($partner_id);
                    if ($partner && $partner->post_status === 'publish') {
                        $partners_data[] = [
                            
                            'title' => $partner->post_title,
                            'link' => get_permalink($partner_id),
                            'logo' => get_the_post_thumbnail_url($partner_id, 'medium'),
                            'socials' => get_post_meta($partner_id, '_partner_socials', true)
                        ];
                    }
                }
                
                return $partners_data;
            },
            'schema' => [
                'description' => __('Partner data including title, link, and logo'),
                'type' => 'array'
            ],
        ]);
    }
}
add_action('rest_api_init', 'add_partners_data_to_rest_api');
