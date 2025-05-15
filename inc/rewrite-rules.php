<?php

/**
 * Rewrite Rules for Custom Pages
 *
 * @package OpenGovAsia
 */

/**
 * Register Events Page functionality that works automatically when theme is activated
 */

// Register rewrite rules for events page
function theme_register_events_rewrite_rules()
{
    add_rewrite_rule('^upcoming-events/?$', 'index.php?pagename=upcoming-events', 'top');
    add_rewrite_rule('^channels/?$', 'index.php?pagename=channels', 'top');

}
add_action('init', 'theme_register_events_rewrite_rules');

// Create the events page on theme activation
function theme_register_pages()
{
    // Check if the page already exists
    $upcoming_events_page = get_page_by_path('upcoming-events');
    $channels_page = get_page_by_path('channels');

    // If the page doesn't exist, create it
    if (!$upcoming_events_page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Upcoming Events',
            'post_name' => 'upcoming-events',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>This page displays upcoming events.</p><!-- /wp:paragraph -->',
            'comment_status' => 'closed'
        ));

    }

    // If the page doesn't exist, create it
    if (!$channels_page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Channels',
            'post_name' => 'channels',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>This page displays channels.</p><!-- /wp:paragraph -->',
            'comment_status' => 'closed'
        ));

    }

    // Flush rewrite rules
    flush_rewrite_rules();
}

// Hook the function to theme activation
add_action('after_switch_theme', 'theme_register_pages');

// Make sure we load the correct template for this page
function theme_upcoming_events_template($events_template)
{
    if (is_page('upcoming-events')) {
        $new_template = locate_template(['upcoming-events.php']);
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $events_template;
}

add_filter('template_include', 'theme_upcoming_events_template');

function theme_channels_template($channels_template)
{
    if (is_page('channels')) {
        $new_template = locate_template(['channels.php']);
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $channels_template;
}

add_filter('template_include', 'theme_channels_template');

/**
 * Exclude custom pages from search results
 *
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */

function theme_exclude_custom_pages_from_search($query)
{
    if ($query->is_search() && $query->is_main_query()) {
        // Get the IDs of the pages to exclude
        $channels_page = get_page_by_path('channels');
        $upcoming_events_page = get_page_by_path('upcoming-events');

        $exclude_ids = array();

        if ($channels_page) {
            $exclude_ids[] = $channels_page->ID;
        }
        if ($upcoming_events_page) {
            $exclude_ids[] = $upcoming_events_page->ID;
        }

        if (!empty($exclude_ids)) {
            $query->set('post__not_in', $exclude_ids);
        }
    }
}
add_action('pre_get_posts', 'theme_exclude_custom_pages_from_search');


function theme_add_custom_page_labels($post_states, $post)
{
    if ($post->post_type === 'page') {
        if ($post->post_name === 'channels') {
            $post_states['custom_channels'] = 'Channels Page';
        }
        if ($post->post_name === 'upcoming-events') {
            $post_states['custom_upcoming'] = 'Upcoming Events Page';
        }
    }
    return $post_states;
}
add_filter('display_post_states', 'theme_add_custom_page_labels', 10, 2);