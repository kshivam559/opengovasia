<?php
/**
 * Company Meta Box
 *
 * This file registers meta box for companies that can:
 * - Sponsor channels (one company per channel)
 * - Partner with events (multiple companies per event)
 * - Receive awards (can be tagged on multiple awards)
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Company Meta Boxes
 */
function add_company_meta_boxes()
{
    add_meta_box(
        'company_social_links',
        __('Social Links', 'opengovasia'),
        'render_company_social_links',
        'company',
        'normal',
        'default'
    );

    add_meta_box(
        'company_attached_content',
        __('Attached Content', 'opengovasia'),
        'render_attached_content',
        'company',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_company_meta_boxes');

/**
 * Company Social Links Meta Box
 */
function render_company_social_links($post)
{
    wp_nonce_field('company_social_links_save', 'company_social_links_nonce');

    $company_socials = get_custom_meta($post->ID, 'socials', true);
    if (!is_array($company_socials)) {
        $company_socials = array();
    }
    ?>
    <div class="company-socials-wrap">
        <div id="company-socials-list">
            <?php
            if (!empty($company_socials)) {
                foreach ($company_socials as $index => $social) {
                    ?>
                    <div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">
                        <select name="company_socials[<?php echo $index; ?>][platform]" style="width: 120px; margin-right: 10px;">
                            <option value="">Select Platform</option>
                            <option value="facebook" <?php selected($social['platform'] ?? '', 'facebook'); ?>>Facebook</option>
                            <option value="twitter" <?php selected($social['platform'] ?? '', 'twitter'); ?>>Twitter</option>
                            <option value="linkedin" <?php selected($social['platform'] ?? '', 'linkedin'); ?>>LinkedIn</option>
                            <option value="instagram" <?php selected($social['platform'] ?? '', 'instagram'); ?>>Instagram</option>
                            <option value="youtube" <?php selected($social['platform'] ?? '', 'youtube'); ?>>YouTube</option>
                            <option value="website" <?php selected($social['platform'] ?? '', 'website'); ?>>Website</option>
                        </select>
                        <input type="url" name="company_socials[<?php echo $index; ?>][url]"
                            value="<?php echo esc_url($social['url'] ?? ''); ?>" placeholder="Social URL" style="flex: 1;">
                        <button type="button" class="remove-social button button-secondary"
                            style="margin-left:10px;">Remove</button>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">
                    <select name="company_socials[0][platform]" style="width: 120px; margin-right: 10px;">
                        <option value="">Select Platform</option>
                        <option value="facebook">Facebook</option>
                        <option value="twitter">Twitter</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="instagram">Instagram</option>
                        <option value="youtube">YouTube</option>
                        <option value="website">Website</option>
                    </select>
                    <input type="url" name="company_socials[0][url]" placeholder="Social URL" style="flex: 1;">
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
                $('.add-social').on('click', function () {
                    var list = $('#company-socials-list');
                    var index = list.children().length;

                    var newSocial = $('<div class="social-item" style="display: flex; margin-bottom: 10px; align-items: center;">' +
                        '<select name="company_socials[' + index + '][platform]" style="width: 120px; margin-right: 10px;">' +
                        '<option value="">Select Platform</option>' +
                        '<option value="facebook">Facebook</option>' +
                        '<option value="twitter">Twitter</option>' +
                        '<option value="linkedin">LinkedIn</option>' +
                        '<option value="instagram">Instagram</option>' +
                        '<option value="youtube">YouTube</option>' +
                        '<option value="website">Website</option>' +
                        '</select>' +
                        '<input type="url" name="company_socials[' + index + '][url]" placeholder="Social URL" style="flex: 1;">' +
                        '<button type="button" class="remove-social button button-secondary" style="margin-left:10px;">Remove</button>' +
                        '</div>');
                    list.append(newSocial);
                });

                $(document).on('click', '.remove-social', function () {
                    $(this).closest('.social-item').remove();

                    $('#company-socials-list .social-item').each(function (index) {
                        $(this).find('select, input').each(function () {
                            var name = $(this).attr('name');
                            if (name) {
                                $(this).attr('name', name.replace(/company_socials\[\d+\]/, 'company_socials[' + index + ']'));
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
 * Save Company Meta
 */
function save_company_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['company_social_links_nonce']) || !wp_verify_nonce($_POST['company_social_links_nonce'], 'company_social_links_save'))
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    if (isset($_POST['company_socials']) && is_array($_POST['company_socials'])) {
        $socials = array();
        foreach ($_POST['company_socials'] as $social) {
            if (!empty($social['platform']) && !empty($social['url'])) {
                $socials[] = array(
                    'platform' => sanitize_text_field($social['platform']),
                    'url' => esc_url_raw($social['url'])
                );
            }
        }
        update_custom_meta($post_id, 'socials', $socials);

    }
}
add_action('save_post_company', 'save_company_meta');

/**
 * Add Company Selector Field to Events and Awards
 */
function add_company_selector_field()
{
    add_meta_box(
        'company_selector',
        __('Partner Companies', 'opengovasia'),
        'render_company_selector',
        array('events', 'awards'),
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_company_selector_field');

/**
 * Render Company Selector Field
 */
function render_company_selector($post)
{
    wp_nonce_field('company_selector_save', 'company_selector_nonce');

    $selected_companies = get_custom_meta($post->ID, 'companies', true);
    if (!is_array($selected_companies)) {
        $selected_companies = array();
    }

    $label = ($post->post_type === 'events') ? 'Partner Companies' : 'Award Recipients';
    ?>
    <div class="company-selector-container">
        <div class="company-search">
            <input type="text" id="company-search-input"
                placeholder="<?php printf(__('Search for %s...', 'opengovasia'), strtolower($label)); ?>"
                style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 4px;">
            <div id="company-search-results"
                style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; margin-bottom: 15px; display: none; border-radius: 4px;">
            </div>
        </div>

        <div class="selected-companies">
            <h4><?php echo esc_html($label); ?></h4>
            <ul id="selected-companies-list" style="margin-top: 0; padding: 0; list-style: none; width: 100%;">
                <?php
                foreach ($selected_companies as $company_id) {
                    $company = get_post($company_id);
                    if ($company && $company->post_type == 'company') {
                        echo '<li data-id="' . esc_attr($company->ID) . '" style="padding: 10px; margin-bottom: 5px; background: #f8f8f8; border: 1px solid #ddd; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">' .
                            '<span>' . esc_html($company->post_title) . '</span>' .
                            '<button type="button" class="remove-company button button-small">Remove</button>' .
                            '<input type="hidden" name="selected_companies[]" value="' . esc_attr($company->ID) . '">' .
                            '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var searchTimeout;
            var searchInput = $('#company-search-input');
            var searchResults = $('#company-search-results');
            var selectedList = $('#selected-companies-list');

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
                            action: 'search_companies_cpt',
                            query: query,
                            nonce: '<?php echo wp_create_nonce('search_companies_nonce'); ?>'
                        },
                        success: function (response) {
                            if (response.success && response.data.length > 0) {
                                searchResults.empty();

                                $.each(response.data, function (index, company) {
                                    if ($('#selected-companies-list li[data-id="' + company.id + '"]').length > 0) {
                                        return;
                                    }

                                    var item = $('<div class="company-result" data-id="' + company.id + '" style="padding: 10px; cursor: pointer;">' +
                                        company.title + '</div>');

                                    item.hover(
                                        function () { $(this).css('background-color', '#f0f0f0'); },
                                        function () { $(this).css('background-color', 'transparent'); }
                                    );

                                    searchResults.append(item);
                                });

                                searchResults.show();
                            } else {
                                searchResults.html('<div style="padding: 10px;">No companies found</div>');
                                searchResults.show();
                            }
                        }
                    });
                }, 300);
            });

            $(document).on('click', '.company-result', function () {
                var companyId = $(this).data('id');
                var companyName = $(this).text();

                var selectedItem = $('<li data-id="' + companyId + '" style="padding: 10px; margin-bottom: 5px; background: #f8f8f8; border: 1px solid #ddd; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">' +
                    '<span>' + companyName + '</span>' +
                    '<button type="button" class="remove-company button button-small">Remove</button>' +
                    '<input type="hidden" name="selected_companies[]" value="' + companyId + '">' +
                    '</li>');

                selectedList.append(selectedItem);
                searchInput.val('');
                searchResults.hide();
            });

            $(document).on('click', '.remove-company', function (e) {
                e.preventDefault();
                $(this).closest('li').remove();
            });
        });
    </script>
    <?php
}

/**
 * AJAX handler for company search
 */
function search_companies_cpt_callback()
{
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'search_companies_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $query = sanitize_text_field($_GET['query']);
    if (empty($query)) {
        wp_send_json_error('Empty query');
    }

    $args = array(
        'post_type' => 'company',
        'post_status' => 'publish',
        's' => $query,
        'posts_per_page' => 10
    );

    $companies_query = new WP_Query($args);
    $results = array();

    if ($companies_query->have_posts()) {
        while ($companies_query->have_posts()) {
            $companies_query->the_post();
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title()
            );
        }
    }

    wp_reset_postdata();
    wp_send_json_success($results);
}
add_action('wp_ajax_search_companies_cpt', 'search_companies_cpt_callback');

/**
 * Save Company Relationships
 */
function save_company_relationships($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['company_selector_nonce']) || !wp_verify_nonce($_POST['company_selector_nonce'], 'company_selector_save'))
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    if (isset($_POST['selected_companies'])) {
        $companies = array_map('intval', $_POST['selected_companies']);
        update_custom_meta($post_id, 'companies', $companies);
    } else {
        delete_custom_meta($post_id, 'companies');
    }
}
add_action('save_post', 'save_company_relationships');


/**
 * Helper function to get company data organized by relationship type
 */
function get_company_relationships($company_id)
{
    // Validate company ID
    if (empty($company_id) || !is_numeric($company_id)) {
        return array(
            'sponsored_channels' => array(),
            'tagged_events' => array(),
            'tagged_awards' => array()
        );
    }

    $relationships = array(
        'sponsored_channels' => array(),
        'tagged_events' => array(),
        'tagged_awards' => array()
    );

    // Get sponsored channels with error handling
    $sponsored_categories = get_categories(array(
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => 'sponsored_by',
                'value' => $company_id,
                'compare' => '='
            )
        )
    ));

    if (is_array($sponsored_categories)) {
        foreach ($sponsored_categories as $category) {
            if (is_object($category) && isset($category->term_id)) {
                $relationships['sponsored_channels'][] = $category;
            }
        }
    }

    // Get events where this company is tagged - using HybridMeta query
    $events_query = HybridMeta::query(array(
        'post_type' => 'events',
        'posts_per_page' => -1,
        'post_status' => array('publish', 'draft', 'pending', 'private')
    ));

    if ($events_query->have_posts()) {
        while ($events_query->have_posts()) {
            $events_query->the_post();
            $event_id = get_the_ID();
            $event_companies = get_custom_meta($event_id, 'companies', true);

            if (is_array($event_companies) && in_array($company_id, array_map('intval', $event_companies))) {
                $relationships['tagged_events'][] = get_post($event_id);
            }
        }
        wp_reset_postdata();
    }

    // Get awards where this company is tagged - using HybridMeta query
    $awards_query = HybridMeta::query(array(
        'post_type' => 'awards',
        'posts_per_page' => -1,
        'post_status' => array('publish', 'draft', 'pending', 'private')
    ));

    if ($awards_query->have_posts()) {
        while ($awards_query->have_posts()) {
            $awards_query->the_post();
            $award_id = get_the_ID();
            $award_companies = get_custom_meta($award_id, 'companies', true);

            if (is_array($award_companies) && in_array($company_id, array_map('intval', $award_companies))) {
                $relationships['tagged_awards'][] = get_post($award_id);
            }
        }
        wp_reset_postdata();
    }

    return $relationships;
}

/**
 * Render Attached Content Meta Box with enhanced UI
 */
function render_attached_content($post)
{
    if (!is_object($post) || !isset($post->ID)) {
        echo '<div class="notice notice-error"><p>Invalid post data.</p></div>';
        return;
    }

    $relationships = get_company_relationships($post->ID);

    // Check if there's any content
    $has_content = !empty($relationships['sponsored_channels']) ||
        !empty($relationships['tagged_events']) ||
        !empty($relationships['tagged_awards']);

    // Add enhanced styles
    echo '<style>
        .attached-content-container {
            background: #fff;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .relationship-section {
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
        }
        .relationship-section:last-child {
            border-bottom: none;
        }
        .section-header {
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 600;
            color: #1d2327;
        }
        .section-count {
            color: #646970;
            font-weight: normal;
            font-size: 14px;
        }
        .content-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            margin-bottom: 8px;
            background: #f9f9f9;
            border: 1px solid #e8e8e8;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .content-item:hover {
            background: #f0f6ff;
            border-color: #0073aa;
        }
        .content-item:last-child {
            margin-bottom: 0;
        }
        .item-title {
            font-weight: 500;
            color: #1d2327;
            margin: 0;
        }
        .item-meta {
            font-size: 12px;
            color: #646970;
            margin-top: 4px;
        }
        .item-actions {
            display: flex;
            gap: 8px;
        }
        .btn-small {
            padding: 4px 8px;
            font-size: 12px;
            line-height: 1.4;
            text-decoration: none;
            border-radius: 3px;
            border: 1px solid;
            display: inline-block;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-edit {
            background: #0073aa;
            border-color: #0073aa;
            color: #fff;
        }
        .btn-edit:hover {
            background: #005a87;
            border-color: #005a87;
            color: #fff;
        }
        .btn-preview {
            background: #50575e;
            border-color: #50575e;
            color: #fff;
        }
        .btn-preview:hover {
            background: #3c434a;
            border-color: #3c434a;
            color: #fff;
        }
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: #646970;
            background: #f9f9f9;
            border-radius: 6px;
            border: 2px dashed #c3c4c7;
        }
        .empty-icon {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.3;
        }
        .empty-text {
            margin: 0;
            font-style: italic;
            font-size: 16px;
        }
    </style>';

    if (!$has_content) {
        echo '<div class="empty-state">';
        echo '<div class="empty-icon">🏢</div>';
        echo '<p class="empty-text">' . __('No content is currently attached to this company.', 'opengovasia') . '</p>';
        echo '</div>';
        return;
    }

    echo '<div class="attached-content-container">';

    // Sponsored Channels Section
    if (!empty($relationships['sponsored_channels'])) {
        echo '<div class="relationship-section">';
        echo '<h4 class="section-header">';
        echo '<span>📺</span>';
        echo __('Channel Sponsorships', 'opengovasia');
        echo ' <span class="section-count">(' . count($relationships['sponsored_channels']) . ')</span>';
        echo '</h4>';

        foreach ($relationships['sponsored_channels'] as $category) {
            if (!is_object($category) || !isset($category->term_id)) {
                continue;
            }

            echo '<div class="content-item">';
            echo '<div>';
            echo '<div class="item-title">' . esc_html($category->name) . '</div>';
            if (isset($category->description) && !empty($category->description)) {
                echo '<div class="item-meta">' . esc_html(wp_trim_words($category->description, 10)) . '</div>';
            }
            echo '</div>';

            echo '<div class="item-actions">';
            $edit_link = admin_url('term.php?taxonomy=category&tag_ID=' . $category->term_id . '&post_type=post');
            $preview_link = get_category_link($category->term_id);

            echo '<a href="' . esc_url($edit_link) . '" class="btn-small btn-edit">' . __('Edit', 'opengovasia') . '</a>';
            echo '<a href="' . esc_url($preview_link) . '" class="btn-small btn-preview" target="_blank">' . __('Preview', 'opengovasia') . '</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Events Section
    if (!empty($relationships['tagged_events'])) {
        echo '<div class="relationship-section">';
        echo '<h4 class="section-header">';
        echo '<span>📅</span>';
        echo __('Events', 'opengovasia');
        echo ' <span class="section-count">(' . count($relationships['tagged_events']) . ')</span>';
        echo '</h4>';

        foreach ($relationships['tagged_events'] as $event) {
            if (!is_object($event) || !isset($event->ID) || !isset($event->post_title)) {
                continue;
            }

            echo '<div class="content-item">';
            echo '<div>';
            echo '<div class="item-title">' . esc_html($event->post_title) . '</div>';

            // Add event date if available
            $event_date = get_custom_meta($event->ID, 'event_date', true);
            if (!empty($event_date)) {
                echo '<div class="item-meta">' . __('Date:', 'opengovasia') . ' ' . esc_html(date('M j, Y', strtotime($event_date))) . '</div>';
            }
            echo '</div>';

            echo '<div class="item-actions">';
            $edit_link = get_edit_post_link($event->ID);
            $preview_link = get_permalink($event->ID);

            if ($edit_link) {
                echo '<a href="' . esc_url($edit_link) . '" class="btn-small btn-edit">' . __('Edit', 'opengovasia') . '</a>';
            }
            if ($preview_link) {
                echo '<a href="' . esc_url($preview_link) . '" class="btn-small btn-preview" target="_blank">' . __('Preview', 'opengovasia') . '</a>';
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Awards Section
    if (!empty($relationships['tagged_awards'])) {
        echo '<div class="relationship-section">';
        echo '<h4 class="section-header">';
        echo '<span>🏆</span>';
        echo __('Awards', 'opengovasia');
        echo ' <span class="section-count">(' . count($relationships['tagged_awards']) . ')</span>';
        echo '</h4>';

        foreach ($relationships['tagged_awards'] as $award) {
            if (!is_object($award) || !isset($award->ID) || !isset($award->post_title)) {
                continue;
            }

            echo '<div class="content-item">';
            echo '<div>';
            echo '<div class="item-title">' . esc_html($award->post_title) . '</div>';

            // Add award year if available
            $award_year = get_custom_meta($award->ID, 'award_year', true);
            if (!empty($award_year)) {
                echo '<div class="item-meta">' . __('Year:', 'opengovasia') . ' ' . esc_html($award_year) . '</div>';
            }
            echo '</div>';

            echo '<div class="item-actions">';
            $edit_link = get_edit_post_link($award->ID);
            $preview_link = get_permalink($award->ID);

            if ($edit_link) {
                echo '<a href="' . esc_url($edit_link) . '" class="btn-small btn-edit">' . __('Edit', 'opengovasia') . '</a>';
            }
            if ($preview_link) {
                echo '<a href="' . esc_url($preview_link) . '" class="btn-small btn-preview" target="_blank">' . __('Preview', 'opengovasia') . '</a>';
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    echo '</div>';
}

/**
 * Register Custom Admin Columns for Company CPT
 */
function set_company_admin_columns($columns)
{
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Company', 'opengovasia'),
        'thumbnail' => __('Company Logo', 'opengovasia'),
        'tagged_content' => __('Tagged Content', 'opengovasia'),
        'sponsorships' => __('Sponsored Channel', 'opengovasia'),
        'date' => $columns['date']
    );
    return $columns;
}
add_filter('manage_company_posts_columns', 'set_company_admin_columns');

/**
 * Handle Company Custom Admin Columns Content
 */
function company_custom_column($column, $post_id)
{
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo '<img src="' . get_the_post_thumbnail_url($post_id, 'full') . '" style="max-width: 80px; height: auto; border-radius: 4px;">';
            } else {
                echo '<div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 20px;">🏢</div>';
            }
            break;

        case 'tagged_content':
            $relationships = get_company_relationships($post_id);
            $events_count = count($relationships['tagged_events']);
            $awards_count = count($relationships['tagged_awards']);
            $total = $events_count + $awards_count;

            if ($total > 0) {
                echo '<div>';
                echo '<strong style="font-size: 18px; color: #0073aa;">' . $total . '</strong>';
                echo '<div style="font-size: 11px; color: #666; margin-top: 2px;">';
                $parts = array();
                if ($events_count > 0)
                    $parts[] = $events_count . ' events';
                if ($awards_count > 0)
                    $parts[] = $awards_count . ' awards';
                echo implode(' + ', $parts);
                echo '</div>';
                echo '</div>';
            } else {
                echo '<span style="color: #999;">0</span>';
            }
            break;

        case 'sponsorships':
            $relationships = get_company_relationships($post_id);
            $sponsored_channels = $relationships['sponsored_channels'];

            if (!empty($sponsored_channels)) {
                echo '<div>';
                foreach ($sponsored_channels as $index => $category) {
                    if ($index > 0)
                        echo '<br>';
                    echo '<span style="background: #0c50a8; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; display: inline-block; margin-bottom: 2px;">';
                    echo esc_html($category->name);
                    echo '</span>';
                }
                echo '</div>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
    }
}
add_action('manage_company_posts_custom_column', 'company_custom_column', 10, 2);


/**
 * Add Companies Data to REST API Response for Events and Awards
 */
function add_companies_data_to_rest_api()
{
    $post_types = ['events', 'awards'];

    foreach ($post_types as $post_type) {
        register_rest_field($post_type, 'partners_data', [
            'get_callback' => function ($post_arr) {
                $companies = get_custom_meta($post_arr['id'], 'companies', true);

                if (!is_array($companies) || empty($companies)) {
                    return [];
                }

                $companies_data = [];
                foreach ($companies as $company_id) {
                    $company = get_post($company_id);
                    if ($company && $company->post_status === 'publish') {
                        $companies_data[] = [
                            'id' => $company_id,
                            'title' => $company->post_title,
                            'link' => get_permalink($company_id),
                            'logo' => get_the_post_thumbnail_url($company_id, 'full'),
                            'socials' => get_custom_meta($company_id, 'socials', true)
                        ];
                    }
                }

                return $companies_data;
            },
        ]);
    }
}
add_action('rest_api_init', 'add_companies_data_to_rest_api');

/**
 * Add Company Data to REST API Response
 */
function add_company_data_to_rest_api()
{
    // Register REST API field for Company meta boxes
    register_rest_field('company', 'company_data', [
        'get_callback' => function ($object) {
            $company_id = $object['id'];

            // Get social links
            $social_links = get_custom_meta($company_id, 'socials', true);
            if (!is_array($social_links)) {
                $social_links = [];
            }

            // Get relationships
            $relationships = get_company_relationships($company_id);

            // Prepare company data
            $company_data = [
                'social_links' => $social_links,
                'relationships' => [
                    'sponsored_channels' => [],
                    'tagged_events' => [],
                    'tagged_awards' => []
                ]
            ];

            // Format sponsored channels
            if (!empty($relationships['sponsored_channels'])) {
                foreach ($relationships['sponsored_channels'] as $category) {
                    if (is_object($category) && isset($category->term_id)) {
                        $company_data['relationships']['sponsored_channels'][] = [
                            'id' => $category->term_id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'description' => $category->description,
                            'link' => get_category_link($category->term_id)
                        ];
                    }
                }
            }

            // Format tagged events
            if (!empty($relationships['tagged_events'])) {
                foreach ($relationships['tagged_events'] as $event) {
                    if (is_object($event) && isset($event->ID)) {
                        $event_date = get_custom_meta($event->ID, 'event_date', true);
                        $company_data['relationships']['tagged_events'][] = [
                            'id' => $event->ID,
                            'title' => $event->post_title,
                            'slug' => $event->post_name,
                            'status' => $event->post_status,
                            'date' => $event_date ? $event_date : null,
                            'link' => get_permalink($event->ID),

                        ];
                    }
                }
            }

            // Format tagged awards
            if (!empty($relationships['tagged_awards'])) {
                foreach ($relationships['tagged_awards'] as $award) {
                    if (is_object($award) && isset($award->ID)) {
                        $award_year = get_custom_meta($award->ID, 'award_year', true);
                        $company_data['relationships']['tagged_awards'][] = [
                            'id' => $award->ID,
                            'title' => $award->post_title,
                            'slug' => $award->post_name,
                            'status' => $award->post_status,
                            'description' => $award->post_content,
                            'year' => $award_year ? $award_year : null,
                            'link' => get_permalink($award->ID),
                        ];
                    }
                }
            }

            return $company_data;
        },
        'schema' => [
            'type' => 'object',
            'context' => ['view', 'edit'],
            'description' => 'Company details including social links and relationships.',
            'properties' => [
                'social_links' => [
                    'type' => 'array',
                    'description' => 'Social media links for the company',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'platform' => ['type' => 'string'],
                            'url' => ['type' => 'string']
                        ]
                    ]
                ],
                'relationships' => [
                    'type' => 'object',
                    'description' => 'Company relationships with other content',
                    'properties' => [
                        'sponsored_channels' => [
                            'type' => 'array',
                            'description' => 'Channels sponsored by this company'
                        ],
                        'tagged_events' => [
                            'type' => 'array',
                            'description' => 'Events where this company is tagged'
                        ],
                        'tagged_awards' => [
                            'type' => 'array',
                            'description' => 'Awards where this company is tagged'
                        ]
                    ]
                ]
            ]
        ]
    ]);
}
add_action('rest_api_init', 'add_company_data_to_rest_api');