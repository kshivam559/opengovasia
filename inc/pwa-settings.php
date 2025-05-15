<?php

/**
 * PWA Settings
 * 
 * This file contains the settings for the Progressive Web App (PWA) functionality.
 * It includes the service worker registration, manifest file generation,
 * and other PWA-related configurations.
 * 
 * @package OpenGovAsia
 * 
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add PWA settings page
add_action('admin_menu', function () {
    add_theme_page('PWA Settings', 'PWA Settings', 'manage_options', 'opengovasia-pwa', 'opengovasia_pwa_settings_page');
});

// Register settings
add_action('admin_init', function () {
    $fields = [
        'enable' => 0,
        'name' => get_bloginfo('name'),
        'short_name' => get_bloginfo('name'),
        'theme_color' => '#0c50a8',
        'bg_color' => '#0c50a8',
        'start_url' => '/',
        'display' => 'standalone',
        'orientation' => 'any',
        'icon_512' => '',
        'icon_192' => '',
        'icon_144' => '',
        'icon_96' => '',
        'splash_screen' => '',
        'scope' => '',
        'lang' => 'en',
        'description' => '',
        'dir' => 'ltr',
        'categories' => '',
        'screenshots' => '',
        'shortcuts' => '[]'
    ];
    foreach ($fields as $field => $default) {
        register_setting('opengovasia_pwa_settings', "opengovasia_pwa_{$field}");
    }
});

// Render settings page
function opengovasia_pwa_settings_page()
{
    ?>
    <div class="wrap">
        <h1>PWA Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('opengovasia_pwa_settings'); ?>
            <table class="form-table">
                <tr>
                    <th>Enable PWA</th>
                    <td><input type="checkbox" name="opengovasia_pwa_enable" value="1" <?php checked(1, get_option('opengovasia_pwa_enable')); ?> /></td>
                </tr>
                <tr>
                    <th>App Name <span style="color:red;">*</span></th>
                    <td><input type="text" name="opengovasia_pwa_name"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_name')); ?>" class="regular-text"
                            required /></td>
                </tr>
                <tr>
                    <th>Short Name <span style="color:red;">*</span></th>
                    <td><input type="text" name="opengovasia_pwa_short_name"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_short_name')); ?>" class="regular-text"
                            required /></td>
                </tr>
                <tr>
                    <th>Theme Color <span style="color:red;">*</span></th>
                    <td><input type="text" class="pwa-color-field" name="opengovasia_pwa_theme_color"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_theme_color', '#0c50a8')); ?>"
                            required /></td>
                </tr>
                <tr>
                    <th>Background Color <span style="color:red;">*</span></th>
                    <td><input type="text" class="pwa-color-field" name="opengovasia_pwa_bg_color"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_bg_color', '#0c50a8')); ?>" required />
                    </td>
                </tr>
                <tr>
                    <th>Start URL <span style="color:red;">*</span></th>
                    <td><input type="text" name="opengovasia_pwa_start_url"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_start_url', '/')); ?>"
                            class="regular-text" /></td>
                </tr>
                <tr>
                    <th>Display Mode <span style="color:red;">*</span></th>
                    <td>
                        <select name="opengovasia_pwa_display" required>
                            <?php foreach (['fullscreen', 'standalone', 'minimal-ui', 'browser'] as $mode) {
                                echo "<option value='$mode'" . selected(get_option('opengovasia_pwa_display'), $mode, false) . ">$mode</option>";
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Orientation</th>
                    <td>
                        <select name="opengovasia_pwa_orientation">
                            <?php foreach (['any', 'portrait', 'landscape'] as $orientation) {
                                echo "<option value='$orientation'" . selected(get_option('opengovasia_pwa_orientation'), $orientation, false) . ">$orientation</option>";
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>App Icon (512x512) <span style="color:red;">*</span></th>
                    <td>
                        <input type="text" id="opengovasia_pwa_icon_512" name="opengovasia_pwa_icon_512"
                            value="<?php echo esc_url(get_option('opengovasia_pwa_icon_512')); ?>" class="regular-text"
                            required />
                        <button type="button" class="button opengovasia-pwa-media-upload"
                            data-target="opengovasia_pwa_icon_512">Choose Image</button>
                        <div class="image-preview">
                            <?php if (get_option('opengovasia_pwa_icon_512')): ?>
                                <img id="opengovasia_pwa_icon_512_preview"
                                    src="<?php echo esc_url(get_option('opengovasia_pwa_icon_512')); ?>"
                                    style="max-width:100px;margin-top:10px;">
                            <?php else: ?>
                                <img id="opengovasia_pwa_icon_512_preview" src=""
                                    style="max-width:100px;margin-top:10px;display:none;">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>App Icon (192x192)</th>
                    <td>
                        <input type="text" id="opengovasia_pwa_icon_192" name="opengovasia_pwa_icon_192"
                            value="<?php echo esc_url(get_option('opengovasia_pwa_icon_192')); ?>" class="regular-text" />
                        <button type="button" class="button opengovasia-pwa-media-upload"
                            data-target="opengovasia_pwa_icon_192">Choose Image</button>
                        <div class="image-preview">
                            <?php if (get_option('opengovasia_pwa_icon_192')): ?>
                                <img id="opengovasia_pwa_icon_192_preview"
                                    src="<?php echo esc_url(get_option('opengovasia_pwa_icon_192')); ?>"
                                    style="max-width:100px;margin-top:10px;">
                            <?php else: ?>
                                <img id="opengovasia_pwa_icon_192_preview" src=""
                                    style="max-width:100px;margin-top:10px;display:none;">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>App Icon (144x144)</th>
                    <td>
                        <input type="text" id="opengovasia_pwa_icon_144" name="opengovasia_pwa_icon_144"
                            value="<?php echo esc_url(get_option('opengovasia_pwa_icon_144')); ?>" class="regular-text" />
                        <button type="button" class="button opengovasia-pwa-media-upload"
                            data-target="opengovasia_pwa_icon_144">Choose Image</button>
                        <div class="image-preview">
                            <?php if (get_option('opengovasia_pwa_icon_144')): ?>
                                <img id="opengovasia_pwa_icon_144_preview"
                                    src="<?php echo esc_url(get_option('opengovasia_pwa_icon_144')); ?>"
                                    style="max-width:100px;margin-top:10px;">
                            <?php else: ?>
                                <img id="opengovasia_pwa_icon_144_preview" src=""
                                    style="max-width:100px;margin-top:10px;display:none;">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>App Icon (96x96)</th>
                    <td>
                        <input type="text" id="opengovasia_pwa_icon_96" name="opengovasia_pwa_icon_96"
                            value="<?php echo esc_url(get_option('opengovasia_pwa_icon_96')); ?>" class="regular-text" />
                        <button type="button" class="button opengovasia-pwa-media-upload"
                            data-target="opengovasia_pwa_icon_96">Choose Image</button>
                        <div class="image-preview">
                            <?php if (get_option('opengovasia_pwa_icon_96')): ?>
                                <img id="opengovasia_pwa_icon_96_preview"
                                    src="<?php echo esc_url(get_option('opengovasia_pwa_icon_96')); ?>"
                                    style="max-width:100px;margin-top:10px;">
                            <?php else: ?>
                                <img id="opengovasia_pwa_icon_96_preview" src=""
                                    style="max-width:100px;margin-top:10px;display:none;">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Splash Screen Image</th>
                    <td>
                        <input type="text" id="opengovasia_pwa_splash_screen" name="opengovasia_pwa_splash_screen"
                            value="<?php echo esc_url(get_option('opengovasia_pwa_splash_screen')); ?>"
                            class="regular-text" />
                        <button type="button" class="button opengovasia-pwa-media-upload"
                            data-target="opengovasia_pwa_splash_screen">Choose Image</button>
                        <div class="image-preview">
                            <?php if (get_option('opengovasia_pwa_splash_screen')): ?>
                                <img id="opengovasia_pwa_splash_screen_preview"
                                    src="<?php echo esc_url(get_option('opengovasia_pwa_splash_screen')); ?>"
                                    style="max-width:200px;margin-top:10px;">
                            <?php else: ?>
                                <img id="opengovasia_pwa_splash_screen_preview" src=""
                                    style="max-width:200px;margin-top:10px;display:none;">
                            <?php endif; ?>
                        </div>
                        <p class="description">Recommended size: 1242x2688 (iPhone XS Max)</p>
                    </td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><textarea name="opengovasia_pwa_description"
                            class="large-text"><?php echo esc_textarea(get_option('opengovasia_pwa_description')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>Scope</th>
                    <td><input type="text" name="opengovasia_pwa_scope"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_scope', '/')); ?>"
                            class="regular-text" /></td>
                </tr>
                <tr>
                    <th>Language</th>
                    <td><input type="text" name="opengovasia_pwa_lang"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_lang', 'en')); ?>"
                            class="regular-text" /></td>
                </tr>
                <tr>
                    <th>Text Direction</th>
                    <td>
                        <select name="opengovasia_pwa_dir">
                            <option value="ltr" <?php selected('ltr', get_option('opengovasia_pwa_dir', 'ltr')); ?>>Left to
                                Right</option>
                            <option value="rtl" <?php selected('rtl', get_option('opengovasia_pwa_dir')); ?>>Right to Left
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Categories (comma separated)</th>
                    <td><input type="text" name="opengovasia_pwa_categories"
                            value="<?php echo esc_attr(get_option('opengovasia_pwa_categories')); ?>"
                            class="regular-text" /></td>
                </tr>
                <tr>
                    <th>Screenshots (comma separated URLs)</th>
                    <td><textarea name="opengovasia_pwa_screenshots"
                            class="large-text"><?php echo esc_textarea(get_option('opengovasia_pwa_screenshots')); ?></textarea>
                    </td>
                </tr>
            </table>
            <p><span style="color:red;">*</span> Required fields</p>

            <h2>PWA Shortcuts</h2>
            <p class="description">Define shortcuts for your PWA. These appear in the app's context menu and provide quick
                access to key features.</p>

            <div id="pwa-shortcuts-container">
                <!-- Shortcut items will be dynamically added here -->
            </div>

            <button type="button" class="button button-secondary" id="add-pwa-shortcut">Add Shortcut</button>

            <!-- Hidden field to store the shortcuts data as JSON -->
            <input type="hidden" name="opengovasia_pwa_shortcuts" id="opengovasia_pwa_shortcuts"
                value="<?php echo esc_attr(get_option('opengovasia_pwa_shortcuts', '[]')); ?>">

            <template id="shortcut-template">
                <div class="pwa-shortcut-item"
                    style="border: 1px solid #ccc; padding: 15px; margin: 15px 0; background: #f9f9f9; position: relative;">
                    <button type="button" class="button button-link remove-shortcut"
                        style="position: absolute; right: 10px; top: 10px; color: #cc0000;">Remove</button>

                    <table class="form-table">
                        <tr>
                            <th scope="row">Name <span style="color:red;">*</span></th>
                            <td><input type="text" class="regular-text shortcut-name" required></td>
                        </tr>
                        <tr>
                            <th scope="row">Short Name <span style="color:red;">*</span></th>
                            <td><input type="text" class="regular-text shortcut-short-name" required></td>
                        </tr>
                        <tr>
                            <th scope="row">URL <span style="color:red;">*</span></th>
                            <td><input type="text" class="regular-text shortcut-url" required></td>
                        </tr>
                        <tr>
                            <th scope="row">Description</th>
                            <td><input type="text" class="regular-text shortcut-description"></td>
                        </tr>
                        <tr>
                            <th scope="row">Icon</th>
                            <td>
                                <input type="text" class="regular-text shortcut-icon">
                                <button type="button"
                                    class="button opengovasia-pwa-media-upload shortcut-icon-button">Choose Icon</button>
                                <div class="image-preview shortcut-icon-preview">
                                    <img src="" style="max-width:100px;margin-top:10px;display:none;">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </template>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

// Admin JS & CSS
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'appearance_page_opengovasia-pwa')
        return;

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_media();

    // Register the script with a proper handle but use inline script directly
    $script_handle = 'opengovasia_pwa_admin_script';

    // Register script with minimal dependencies
    wp_register_script($script_handle, '', ['jquery', 'wp-color-picker'], false, true);

    // Enqueue the script
    wp_enqueue_script($script_handle);

    // Add the inline script
    wp_add_inline_script($script_handle, "
        jQuery(document).ready(function($) {
            // Initialize color picker
            $('.pwa-color-field').wpColorPicker();
            
            // Media uploader for all image fields
            $('.opengovasia-pwa-media-upload').on('click', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var targetId = button.data('target');
                var inputField = $('#' + targetId);
                var previewImg = $('#' + targetId + '_preview');
                
                var frame = wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    
                    // Update preview
                    previewImg.attr('src', attachment.url);
                    previewImg.show();
                });
                
                frame.open();
            });
            
            // Form validation before submit
            $('form').on('submit', function(e) {
                if (!$('input[name=\"opengovasia_pwa_enable\"]').is(':checked')) {
                    return true; // Skip validation if PWA is disabled
                }
                
                var required = [
                    'input[name=\"opengovasia_pwa_name\"]',
                    'input[name=\"opengovasia_pwa_short_name\"]',
                    'input[name=\"opengovasia_pwa_theme_color\"]',
                    'input[name=\"opengovasia_pwa_bg_color\"]',
                    'select[name=\"opengovasia_pwa_display\"]',
                    'input[name=\"opengovasia_pwa_icon_512\"]'
                ];
                
                var valid = true;
                
                $.each(required, function(index, selector) {
                    var field = $(selector);
                    if (!field.val()) {
                        alert('Please fill in all required fields marked with *');
                        field.focus();
                        valid = false;
                        return false;
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                }
            });

            // Initialize shortcuts from the hidden input
            const shortcutsContainer = $('#pwa-shortcuts-container');
            const shortcutsInput = $('#opengovasia_pwa_shortcuts');
            const shortcutTemplate = $('#shortcut-template').html();
            let shortcuts = [];
            
            try {
                shortcuts = JSON.parse(shortcutsInput.val() || '[]');
            } catch (e) {
                shortcuts = [];
            }
            
            // Render existing shortcuts
            function renderShortcuts() {
                shortcutsContainer.empty();
                shortcuts.forEach((shortcut, index) => {
                    addShortcutItem(shortcut, index);
                });
            }
            
            // Add a new shortcut item to the UI
            function addShortcutItem(shortcut = {}, index) {
                const shortcutElement = $(shortcutTemplate);
                
                // Set values if they exist
                shortcutElement.find('.shortcut-name').val(shortcut.name || '');
                shortcutElement.find('.shortcut-short-name').val(shortcut.short_name || '');
                shortcutElement.find('.shortcut-url').val(shortcut.url || '');
                shortcutElement.find('.shortcut-description').val(shortcut.description || '');
                shortcutElement.find('.shortcut-icon').val(shortcut.icon || '');
                
                // Show icon preview if it exists
                if (shortcut.icon) {
                    const previewImg = shortcutElement.find('.shortcut-icon-preview img');
                    previewImg.attr('src', shortcut.icon);
                    previewImg.show();
                }
                
                // Add data index for tracking
                shortcutElement.attr('data-index', index);
                
                // Add to container
                shortcutsContainer.append(shortcutElement);
                
                // Setup media uploader for icon
                shortcutElement.find('.shortcut-icon-button').on('click', function() {
                    const button = $(this);
                    const inputField = button.prev('.shortcut-icon');
                    const previewImg = button.next('.shortcut-icon-preview').find('img');
                    
                    const frame = wp.media({
                        title: 'Select or Upload Icon Image',
                        button: { text: 'Use this image' },
                        multiple: false
                    });
                    
                    frame.on('select', function() {
                        const attachment = frame.state().get('selection').first().toJSON();
                        inputField.val(attachment.url);
                        
                        // Update preview
                        previewImg.attr('src', attachment.url);
                        previewImg.show();
                        
                        // Update the shortcuts data
                        updateShortcut(inputField.closest('.pwa-shortcut-item'));
                    });
                    
                    frame.open();
                });
                
                // Handle remove button
                shortcutElement.find('.remove-shortcut').on('click', function() {
                    const itemIndex = $(this).closest('.pwa-shortcut-item').attr('data-index');
                    shortcuts.splice(itemIndex, 1);
                    updateShortcutsInput();
                    renderShortcuts();
                });
                
                // Update data when fields change
                shortcutElement.find('input[type=\"text\"]').on('change', function() {
                    updateShortcut($(this).closest('.pwa-shortcut-item'));
                });
            }
            
            // Update a shortcut item in the data array
            function updateShortcut(shortcutElement) {
                const index = shortcutElement.attr('data-index');
                
                shortcuts[index] = {
                    name: shortcutElement.find('.shortcut-name').val(),
                    short_name: shortcutElement.find('.shortcut-short-name').val(),
                    url: shortcutElement.find('.shortcut-url').val(),
                    description: shortcutElement.find('.shortcut-description').val(),
                    icon: shortcutElement.find('.shortcut-icon').val()
                };
                
                updateShortcutsInput();
            }
            
            // Update the hidden input with the current shortcuts data
            function updateShortcutsInput() {
                shortcutsInput.val(JSON.stringify(shortcuts));
            }
            
            // Initialize
            renderShortcuts();
            
            // Add a new shortcut when the button is clicked
            $('#add-pwa-shortcut').on('click', function() {
                shortcuts.push({
                    name: '',
                    short_name: '',
                    url: '',
                    description: '',
                    icon: ''
                });
                
                updateShortcutsInput();
                addShortcutItem({}, shortcuts.length - 1);
            });
            
            // Validate shortcuts before form submission
            $('form').on('submit', function(e) {
                if (!$('input[name=\"opengovasia_pwa_enable\"]').is(':checked')) {
                    return true; // Skip validation if PWA is disabled
                }
                
                // Validate each shortcut
                let valid = true;
                
                $('.pwa-shortcut-item').each(function() {
                    const name = $(this).find('.shortcut-name').val();
                    const shortName = $(this).find('.shortcut-short-name').val();
                    const url = $(this).find('.shortcut-url').val();
                    
                    if (!name || !shortName || !url) {
                        alert('Please fill in all required fields for shortcuts (Name, Short Name and URL)');
                        valid = false;
                        return false;
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    ");

    // Add enhanced styling for the previews
    wp_add_inline_style('wp-admin', "
        .image-preview {
            margin-top: 10px;
        }
        .image-preview img {
            border: 1px solid #ddd;
            padding: 5px;
            background: #f7f7f7;
            display: block;
            max-width: 150px;
            height: auto;
        }
        .opengovasia-pwa-media-upload {
            margin-left: 5px !important;
        }
        .wp-picker-container {
            display: inline-block;
        }
    ");
});

// Validate and save manifest
function opengovasia_pwa_generate_manifest()
{
    if (!get_option('opengovasia_pwa_enable'))
        return;

    // Check required fields
    $required_fields = [
        'opengovasia_pwa_name',
        'opengovasia_pwa_short_name',
        'opengovasia_pwa_theme_color',
        'opengovasia_pwa_bg_color',
        'opengovasia_pwa_start_url',
        'opengovasia_pwa_display',
        'opengovasia_pwa_icon_512'
    ];

    foreach ($required_fields as $field) {
        if (empty(get_option($field))) {
            // Don't generate manifest if required fields are missing
            return;
        }
    }

    $manifest = [
        'name' => get_option('opengovasia_pwa_name'),
        'short_name' => get_option('opengovasia_pwa_short_name'),
        'start_url' => get_option('opengovasia_pwa_start_url', '/'),
        'scope' => get_option('opengovasia_pwa_scope', '/'),
        'display' => get_option('opengovasia_pwa_display'),
        'orientation' => get_option('opengovasia_pwa_orientation'),
        'background_color' => get_option('opengovasia_pwa_bg_color'),
        'theme_color' => get_option('opengovasia_pwa_theme_color'),
        'description' => get_option('opengovasia_pwa_description'),
        'lang' => get_option('opengovasia_pwa_lang'),
        'dir' => get_option('opengovasia_pwa_dir', 'ltr'),
        'icons' => []
    ];

    // Add icons with different sizes
    $icon_sizes = [
        'opengovasia_pwa_icon_512' => '512x512',
        'opengovasia_pwa_icon_192' => '192x192',
        'opengovasia_pwa_icon_144' => '144x144',
        'opengovasia_pwa_icon_96' => '96x96'
    ];

    foreach ($icon_sizes as $option_name => $size) {
        $icon_url = get_option($option_name);
        if (!empty($icon_url)) {
            $manifest['icons'][] = [
                'src' => $icon_url,
                'sizes' => $size,
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ];
        }
    }

    // Add optional categories and screenshots
    $categories = array_filter(array_map('trim', explode(',', get_option('opengovasia_pwa_categories'))));
    $screenshots = array_filter(array_map('trim', explode(',', get_option('opengovasia_pwa_screenshots'))));

    if (!empty($categories)) {
        $manifest['categories'] = $categories;
    }

    if (!empty($screenshots)) {
        $manifest['screenshots'] = array_map(function ($url) {
            return ['src' => $url];
        }, $screenshots);
    }

    // Add splash screen if provided
    $splash_screen = get_option('opengovasia_pwa_splash_screen');
    if (!empty($splash_screen)) {
        if (!isset($manifest['screenshots'])) {
            $manifest['screenshots'] = [];
        }

        // Add splash screen as first screenshot if not already included
        $splash_exists = false;
        foreach ($manifest['screenshots'] as $screenshot) {
            if ($screenshot['src'] === $splash_screen) {
                $splash_exists = true;
                break;
            }
        }

        if (!$splash_exists) {
            array_unshift($manifest['screenshots'], ['src' => $splash_screen]);
        }
    }

    // Add shortcuts to manifest
    $shortcuts_json = get_option('opengovasia_pwa_shortcuts', '[]');
    $shortcuts = json_decode($shortcuts_json, true);

    if (is_array($shortcuts) && !empty($shortcuts)) {
        $manifest_shortcuts = [];

        foreach ($shortcuts as $shortcut) {
            // Validate required fields
            if (empty($shortcut['name']) || empty($shortcut['url'])) {
                continue;
            }

            $manifest_shortcut = [
                'name' => $shortcut['name'],
                'short_name' => !empty($shortcut['short_name']) ? $shortcut['short_name'] : $shortcut['name'],
                'url' => $shortcut['url']
            ];

            // Add optional fields if provided
            if (!empty($shortcut['description'])) {
                $manifest_shortcut['description'] = $shortcut['description'];
            }

            if (!empty($shortcut['icon'])) {
                $manifest_shortcut['icons'] = [
                    [
                        'src' => $shortcut['icon'],
                        'sizes' => '96x96',
                        'type' => 'image/png'
                    ]
                ];
            }

            $manifest_shortcuts[] = $manifest_shortcut;
        }

        if (!empty($manifest_shortcuts)) {
            $manifest['shortcuts'] = $manifest_shortcuts;
        }
    }


    // Save manifest file
    file_put_contents(
        get_template_directory() . '/manifest.json',
        json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

}
function opengovasia_pwa_admin_notices()
{
    // Check if we're on the right page
    $screen = get_current_screen();
    if ($screen->id !== 'appearance_page_opengovasia-pwa') {
        return;
    }

    // Check if settings were just updated
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
        // Generate the manifest file
        opengovasia_pwa_generate_manifest();

        // Display the success message
        echo '<div class="notice notice-success is-dismissible"><p>Manifest file has been updated!</p></div>';
    }
}
add_action('admin_notices', 'opengovasia_pwa_admin_notices');


// Link manifest in <head>
add_action('wp_head', function () {
    if (!get_option('opengovasia_pwa_enable'))
        return;

    // Add manifest link
    echo '<link rel="manifest" href="' . esc_url(get_template_directory_uri() . '/manifest.json') . '">' . "\n";

    // Add theme-color meta tag
    $theme_color = get_option('opengovasia_pwa_theme_color');
    if (!empty($theme_color)) {
        echo '<meta name="theme-color" content="' . esc_attr($theme_color) . '">' . "\n";
    }

    // Add Apple touch icon if available
    $icon_192 = get_option('opengovasia_pwa_icon_192');
    if (!empty($icon_192)) {
        echo '<link rel="apple-touch-icon" href="' . esc_url($icon_192) . '">' . "\n";
    } else {
        $icon_512 = get_option('opengovasia_pwa_icon_512');
        if (!empty($icon_512)) {
            echo '<link rel="apple-touch-icon" href="' . esc_url($icon_512) . '">' . "\n";
        }
    }

    // Add Apple splash screen if available
    $splash_screen = get_option('opengovasia_pwa_splash_screen');
    if (!empty($splash_screen)) {
        echo '<link rel="apple-touch-startup-image" href="' . esc_url($splash_screen) . '">' . "\n";
    }

    // Add Apple mobile web app capable
    echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
});



// Register service worker

// Register the service worker script in footer
function opengovasia_add_service_worker_registration()
{
    ?>
    <script>
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            window.addEventListener('load', async function () {
                try {
                    // Register the service worker
                    const registration = await navigator.serviceWorker.register('/wp-content/themes/opengovasia/sw.js');
                    console.log('Service Worker registered with scope:', registration.scope);

                    // We'll use this registration later for push notifications
                    window.swRegistration = registration;

                    // Check if we already have permission for notifications
                    const permission = Notification.permission;
                    if (permission === 'granted') {
                        console.log('Notification permission already granted');
                    } else if (permission !== 'denied') {
                        // We'll request permission when user interacts with the site
                        console.log('Notification permission not yet requested');
                    }
                } catch (error) {
                    console.error('Service Worker registration failed:', error);
                }
            });
        }
        async function subscribeToPushNotifications() {
            try {
                // Request permission when user interacts with subscription UI
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    throw new Error('Notification permission denied');
                }

                // Get the registration we stored earlier
                const registration = window.swRegistration;

                // Check for existing subscription
                let subscription = await registration.pushManager.getSubscription();

                if (!subscription) {
                    // You'll need to generate VAPID keys on your server
                    const vapidPublicKey = 'YOUR_PUBLIC_VAPID_KEY';
                    const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

                    // Create new subscription
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: convertedVapidKey
                    });

                    // Send to your server
                    await sendSubscriptionToServer(subscription);
                }

                return subscription;
            } catch (error) {
                console.error('Error subscribing to push notifications:', error);
            }
        }

    </script>
    <?php
}

