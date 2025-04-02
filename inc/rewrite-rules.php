<?php

/**
 * Rewrite Rules for Custom Taxonomies
 *
 * @package OpenGovAsia
 */

// Add custom rewrite rules for upcoming events page.

/**
 * Register Events Page functionality that works automatically when theme is activated
 */

// Register rewrite rules for events page
function theme_register_events_rewrite_rules()
{
    add_rewrite_rule('^upcoming-events/?$', 'index.php?pagename=upcoming-events', 'top');
    // add_rewrite_rule('^upcoming-events/page/([0-9]+)/?$', 'index.php?pagename=upcoming-events&paged=$matches[1]', 'top');
}
add_action('init', 'theme_register_events_rewrite_rules');

// Create the events page on theme activation
function theme_create_events_page()
{
    // Check if the page already exists
    $page = get_page_by_path('upcoming-events');

    // If the page doesn't exist, create it
    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Upcoming Events',
            'post_name' => 'upcoming-events',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>This page displays upcoming events.</p><!-- /wp:paragraph -->',
            'comment_status' => 'closed'
        ));

        // Set page template if needed
        // update_post_meta($page_id, '_wp_page_template', 'template-events.php');
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}

// Hook the function to theme activation
add_action('after_switch_theme', 'theme_create_events_page');

// Make sure we load the correct template for this page
function theme_events_template($template)
{
    if (is_page('upcoming-events')) {
        $new_template = locate_template(['upcoming-events.php']);
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'theme_events_template');