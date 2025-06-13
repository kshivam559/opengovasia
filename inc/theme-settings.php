<?php
/**
 * Theme Settings Page for OpenGovAsia
 * 
 * Provides a settings page in the WordPress admin to manage banner images.
 * 
 * @package OpenGovAsia
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Register the settings page
add_action('admin_menu', function () {
    add_theme_page(
        'Theme Settings',
        'Theme Settings',
        'edit_theme_options',
        'banner-images-settings',
        'render_opengovasia_admin_page'
    );
}, 99);

// Handle form submission
add_action('admin_init', function () {
    if (!isset($_POST['opengovasia_banner_nonce']) || !wp_verify_nonce($_POST['opengovasia_banner_nonce'], 'opengovasia_banner_save')) {
        return;
    }

    // Reset settings
    if (isset($_POST['reset_all_settings'])) {
        remove_theme_mod('banner_images');
        remove_theme_mod('homepage_banner');
        remove_theme_mod('text_content');
        add_settings_error('settings', 'settings_reset', 'All settings reset successfully.', 'updated');
        return;
    }

    // Export settings
    if (isset($_POST['export_settings'])) {
        $settings = [
            'banner_images' => get_theme_mod('banner_images', []),
            'homepage_banner' => get_theme_mod('homepage_banner', []),
            'text_content' => get_theme_mod('text_content', [])
        ];
        header('Content-disposition: attachment; filename=opengovasia_settings_export.json');
        header('Content-type: application/json');
        echo json_encode($settings);
        exit;
    }

    // Import settings
    if (!empty($_FILES['import_settings_file']['tmp_name'])) {
        $imported_data = json_decode(file_get_contents($_FILES['import_settings_file']['tmp_name']), true);
        if (is_array($imported_data)) {
            foreach (['banner_images', 'homepage_banner', 'text_content'] as $key) {
                if (isset($imported_data[$key])) {
                    set_theme_mod($key, $imported_data[$key]);
                }
            }
            add_settings_error('settings', 'settings_imported', 'Settings imported successfully.', 'updated');
        }
        return;
    }

    // Save regular settings
    $banner_settings = [];
    $text_settings = [];
    $fields = ['past_events', 'upcoming_events', 'channels', 'ogtv', 'awards', 'awards_category', 'company'];

    foreach ($fields as $field) {
        $banner_settings[$field] = sanitize_url($_POST[$field . '_banner'] ?? '');
        $text_settings[$field . '_title'] = sanitize_text_field($_POST[$field . '_title'] ?? '');
        $text_settings[$field . '_description'] = wp_kses_post(trim($_POST[$field . '_description'] ?? ''));
    }

    set_theme_mod('banner_images', $banner_settings);
    set_theme_mod('text_content', $text_settings);

    // Handle homepage banner settings
    $homepage_settings = [];
    foreach (['desktop_banner', 'mobile_banner', 'desktop_banner_link', 'mobile_banner_link'] as $field) {
        $homepage_settings[$field] = sanitize_url($_POST[$field] ?? '');
    }
    set_theme_mod('homepage_banner', $homepage_settings);

    add_settings_error('settings', 'settings_updated', 'Settings saved successfully.', 'updated');
});

// Enqueue media scripts
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'appearance_page_banner-images-settings') {
        wp_enqueue_media();
        wp_enqueue_editor();
    }
});

function render_opengovasia_admin_page()
{
    $banner_settings = get_theme_mod('banner_images', []);
    $homepage_settings = get_theme_mod('homepage_banner', []);
    $text_settings = get_theme_mod('text_content', []);
    $current_tab = $_GET['tab'] ?? 'homepage';

    $pages = [
        'channels' => 'Channels',
        'events' => 'Events',
        'awards' => 'Awards',
        'ogtv' => 'OGTV',
        'company' => 'Company',
    ];
    ?>
    <div class="wrap">
        <h1>Theme Settings</h1>
        <?php settings_errors('settings'); ?>

        <div class="nav-tab-wrapper wp-clearfix">
            <a href="?page=banner-images-settings&tab=homepage"
                class="nav-tab <?php echo $current_tab === 'homepage' ? 'nav-tab-active' : ''; ?>">Homepage</a>
            <?php foreach ($pages as $key => $label): ?>
                <a href="?page=banner-images-settings&tab=<?php echo $key; ?>"
                    class="nav-tab <?php echo $current_tab === $key ? 'nav-tab-active' : ''; ?>"><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
            <a href="?page=banner-images-settings&tab=import_export"
                class="nav-tab <?php echo $current_tab === 'import_export' ? 'nav-tab-active' : ''; ?>">Import/Export</a>
        </div>

        <form method="post" enctype="multipart/form-data"
            action="?page=banner-images-settings&tab=<?php echo esc_attr($current_tab); ?>">
            <?php wp_nonce_field('opengovasia_banner_save', 'opengovasia_banner_nonce'); ?>

            <?php if ($current_tab === 'homepage'): ?>
                <div class="postbox">
                    <h2 class="hndle">Homepage Banners</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="desktop_banner">Desktop Banner</label></th>
                                <td>
                                    <?php render_image_field('desktop_banner', $homepage_settings['desktop_banner'] ?? '', 'Recommended: 2496 × 300 px'); ?>
                                    <input type="url" class="regular-text" id="desktop_banner_link" name="desktop_banner_link"
                                        value="<?php echo esc_attr($homepage_settings['desktop_banner_link'] ?? ''); ?>"
                                        placeholder="Banner link URL (optional)" style="margin-top: 10px;" />
                                    <p class="description">Link URL for the desktop banner (optional)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="mobile_banner">Mobile Banner</label></th>
                                <td>
                                    <?php render_image_field('mobile_banner', $homepage_settings['mobile_banner'] ?? '', 'Recommended: 1380 × 300 px'); ?>
                                    <input type="url" class="regular-text" id="mobile_banner_link" name="mobile_banner_link"
                                        value="<?php echo esc_attr($homepage_settings['mobile_banner_link'] ?? ''); ?>"
                                        placeholder="Banner link URL (optional)" style="margin-top: 10px;" />
                                    <p class="description">Link URL for the mobile banner (optional)</p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('Save Homepage Settings'); ?>
                    </div>
                </div>

            <?php elseif ($current_tab === 'events'): ?>
                <div class="postbox">
                    <h2 class="hndle">Past Events Settings</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="past_events_banner">Banner Image</label></th>
                                <td><?php render_image_field('past_events_banner', $banner_settings['past_events'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th><label for="past_events_title">Page Title</label></th>
                                <td>
                                    <input type="text" class="regular-text" id="past_events_title" name="past_events_title"
                                        value="<?php echo esc_attr($text_settings['past_events_title'] ?? ''); ?>"
                                        placeholder="Enter page title" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="past_events_description">Description</label></th>
                                <td>
                                    <?php
                                    wp_editor(
                                        $text_settings['past_events_description'] ?? '',
                                        'past_events_description',
                                        [
                                            'textarea_name' => 'past_events_description',
                                            'media_buttons' => true,
                                            'textarea_rows' => 8,
                                            'teeny' => false,
                                            'tinymce' => true,
                                            'quicktags' => true
                                        ]
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="postbox">
                    <h2 class="hndle">Upcoming Events Settings</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="upcoming_events_banner">Banner Image</label></th>
                                <td><?php render_image_field('upcoming_events_banner', $banner_settings['upcoming_events'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th><label for="upcoming_events_title">Page Title</label></th>
                                <td>
                                    <input type="text" class="regular-text" id="upcoming_events_title" name="upcoming_events_title"
                                        value="<?php echo esc_attr($text_settings['upcoming_events_title'] ?? ''); ?>"
                                        placeholder="Enter page title" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="upcoming_events_description">Description</label></th>
                                <td>
                                    <?php
                                    wp_editor(
                                        $text_settings['upcoming_events_description'] ?? '',
                                        'upcoming_events_description',
                                        [
                                            'textarea_name' => 'upcoming_events_description',
                                            'media_buttons' => true,
                                            'textarea_rows' => 8,
                                            'teeny' => false,
                                            'tinymce' => true,
                                            'quicktags' => true
                                        ]
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('Save Events Settings'); ?>
                    </div>
                </div>

            <?php elseif ($current_tab === 'import_export'): ?>
                <div class="postbox">
                    <h2 class="hndle">Export Settings</h2>
                    <div class="inside">
                        <p>Download all current settings as JSON file.</p>
                        <input type="submit" name="export_settings" class="button" value="Export All Settings">
                    </div>
                </div>
                <div class="postbox">
                    <h2 class="hndle">Import Settings</h2>
                    <div class="inside">
                        <p>Upload a JSON settings file to restore all settings.</p>
                        <input type="file" name="import_settings_file" accept=".json" class="regular-text">
                        <?php submit_button('Import Settings', 'secondary'); ?>
                    </div>
                </div>
                <div class="postbox">
                    <h2 class="hndle">Reset Settings</h2>
                    <div class="inside">
                        <p><strong>Warning:</strong> This will reset all settings.</p>
                        <input type="submit" name="reset_all_settings" class="button button-secondary"
                            value="Reset All Settings" onclick="return confirm('Are you sure? This cannot be undone.');" />
                    </div>
                </div>

            <?php elseif ($current_tab === 'awards'): ?>
                <div class="postbox">
                    <h2 class="hndle">Awards Page Settings</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="awards_banner">Banner Image</label></th>
                                <td><?php render_image_field('awards_banner', $banner_settings['awards'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th><label for="awards_title">Page Title</label></th>
                                <td>
                                    <input type="text" class="regular-text" id="awards_title" name="awards_title"
                                        value="<?php echo esc_attr($text_settings['awards_title'] ?? ''); ?>"
                                        placeholder="Enter page title" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="awards_description">Description</label></th>
                                <td>
                                    <?php
                                    wp_editor(
                                        $text_settings['awards_description'] ?? '',
                                        'awards_description',
                                        [
                                            'textarea_name' => 'awards_description',
                                            'media_buttons' => true,
                                            'textarea_rows' => 8,
                                            'teeny' => false,
                                            'tinymce' => true,
                                            'quicktags' => true
                                        ]
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="postbox">
                    <h2 class="hndle">Awards Category Settings</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="awards_category_banner">Category Banner Image</label></th>
                                <td><?php render_image_field('awards_category_banner', $banner_settings['awards_category'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th><label for="awards_category_title">Category Page Title</label></th>
                                <td>
                                    <input type="text" class="regular-text" id="awards_category_title" name="awards_category_title"
                                        value="<?php echo esc_attr($text_settings['awards_category_title'] ?? ''); ?>"
                                        placeholder="Enter category page title" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="awards_category_description">Category Description</label></th>
                                <td>
                                    <?php
                                    wp_editor(
                                        $text_settings['awards_category_description'] ?? '',
                                        'awards_category_description',
                                        [
                                            'textarea_name' => 'awards_category_description',
                                            'media_buttons' => true,
                                            'textarea_rows' => 8,
                                            'teeny' => false,
                                            'tinymce' => true,
                                            'quicktags' => true
                                        ]
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('Save Awards Settings'); ?>
                    </div>
                </div>

            <?php elseif (isset($pages[$current_tab])): ?>
                <div class="postbox">
                    <h2 class="hndle"><?php echo esc_html($pages[$current_tab]); ?> Page Settings</h2>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="<?php echo $current_tab; ?>_banner">Banner Image</label></th>
                                <td><?php render_image_field($current_tab . '_banner', $banner_settings[$current_tab] ?? ''); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="<?php echo $current_tab; ?>_title">Page Title</label></th>
                                <td>
                                    <input type="text" class="regular-text" id="<?php echo $current_tab; ?>_title"
                                        name="<?php echo $current_tab; ?>_title"
                                        value="<?php echo esc_attr($text_settings[$current_tab . '_title'] ?? ''); ?>"
                                        placeholder="Enter page title" />
                                </td>
                            </tr>
                            <tr>
                                <th><label for="<?php echo $current_tab; ?>_description">Description</label></th>
                                <td>
                                    <?php
                                    wp_editor(
                                        $text_settings[$current_tab . '_description'] ?? '',
                                        $current_tab . '_description',
                                        [
                                            'textarea_name' => $current_tab . '_description',
                                            'media_buttons' => true,
                                            'textarea_rows' => 8,
                                            'teeny' => false,
                                            'tinymce' => true,
                                            'quicktags' => true
                                        ]
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('Save ' . $pages[$current_tab] . ' Settings'); ?>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Image upload handling
            $(document).on('click', '.upload-button', function (e) {
                e.preventDefault();
                var button = $(this);
                var target = button.data('target');
                var frame = wp.media({
                    title: 'Select Banner Image',
                    button: { text: 'Use Image' },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#' + target).val(attachment.url);
                    updateImagePreview(target, attachment.url);
                    button.text('Change Image');
                    button.siblings('.remove-button').show();
                });

                frame.open();
            });

            // Manual URL input
            $(document).on('input blur', '.image-url-input', function () {
                var input = $(this);
                var target = input.attr('id');
                var url = input.val().trim();
                var wrapper = input.closest('.image-field-wrapper');

                if (url) {
                    updateImagePreview(target, url);
                    wrapper.find('.upload-button').text('Change Image');
                    wrapper.find('.remove-button').show();
                } else {
                    $('#preview_' + target).hide().html('');
                    wrapper.find('.upload-button').text('Select Image');
                    wrapper.find('.remove-button').hide();
                }
            });

            // Remove image
            $(document).on('click', '.remove-button', function (e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('#' + target).val('');
                $('#preview_' + target).hide().html('');
                $(this).hide().siblings('.upload-button').text('Select Image');
            });

            function updateImagePreview(target, url) {
                var preview = $('#preview_' + target);
                var img = new Image();
                img.onload = function () {
                    preview.html('<img src="' + url + '" alt="Preview" style="max-width:200px;height:auto;border:1px solid #ddd;border-radius:4px;padding:4px;">').show();
                };
                img.onerror = function () {
                    preview.html('<p style="color:#d63638;">Invalid image URL</p>').show();
                };
                img.src = url;
            }
        });
    </script>

    <style>
        .image-field-wrapper {
            max-width: 600px;
        }

        .image-field-wrapper .image-preview {
            margin-top: 10px;
        }

        .postbox h2.hndle {
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .form-table th {
            width: 200px;
            padding: 15px 0;
        }

        .form-table td {
            padding: 15px 0;
        }
    </style>
    <?php
}

function render_image_field($field_id, $value = '', $description = '')
{
    ?>
    <div class="image-field-wrapper">
        <input type="url" class="regular-text image-url-input" id="<?php echo esc_attr($field_id); ?>"
            name="<?php echo esc_attr($field_id); ?>" value="<?php echo esc_attr($value); ?>"
            placeholder="Enter image URL or use upload button" />
        <?php if ($description): ?>
            <p class="description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <p>
            <button type="button" class="button upload-button" data-target="<?php echo esc_attr($field_id); ?>"
                style="margin-top:5px;">
                <?php echo !empty($value) ? 'Change Image' : 'Select Image'; ?>
            </button>
            <button type="button" class="button button-link-delete remove-button"
                data-target="<?php echo esc_attr($field_id); ?>" <?php echo empty($value) ? 'style="display:none;"' : ''; ?>>Remove</button>
        </p>
        <div class="image-preview" id="preview_<?php echo esc_attr($field_id); ?>" <?php echo empty($value) ? 'style="display:none;"' : ''; ?>>
            <?php if (!empty($value)): ?>
                <img src="<?php echo esc_url($value); ?>" alt="Preview"
                    style="max-width:200px;height:auto;border:1px solid #ddd;border-radius:4px;padding:4px;">
            <?php endif; ?>
        </div>
    </div>
    <?php
}