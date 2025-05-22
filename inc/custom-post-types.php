<?php
// Register Custom Post Types: Posts, Awards, Events, OGTV

function register_custom_post_types()
{
    $post_types = [
        'awards' => ['name' => 'Awards', 'icon' => 'dashicons-awards'], // Trophy icon
        'events' => ['name' => 'Events', 'icon' => 'dashicons-calendar'], // Calendar icon
        'ogtv' => ['name' => 'OGTV', 'icon' => 'dashicons-video-alt3'], // Video icon
        'partner' => ['name' => 'Partners', 'icon' => 'dashicons-groups'], // Network icon
        'testimonials' => ['name' => 'Testimonials', 'icon' => 'dashicons-format-quote'], // Quote icon
    ];

    foreach ($post_types as $slug => $data) {

        $rewrite_slug = ($slug === 'partner') ? 'company' : $slug; // Use 'company' for partners

        register_post_type($slug, [
            'labels' => [
                'name' => __($data['name'], 'opengovasia'),
                'singular_name' => __($data['name'], 'opengovasia'),
                'add_new' => 'Add New ' . $data['name'],
                'add_new_item' => 'New ' . $data['name'],
                'edit_item' => 'Edit ' . $data['name'],
                'view_item' => 'View ' . $data['name'],
                'view_items' => 'View ' . $data['name'],
                'search_items' => 'Search ' . $data['name'],
                'not_found' => 'No ' . $data['name'] . ' Found',
                'all_items' => 'All ' . $data['name'],
                'archives' => $data['name'] . ' Archives',
                'attributes' => $data['name'] . ' Attributes',
                'insert_into_item' => 'Insert into ' . $data['name'],
                'uploaded_to_this_item' => 'Uploaded to this ' . $data['name'],
                'featured_image' => $data['name'] . ' Featured image',
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'taxonomies' => ['country'], // Attach global country taxonomy
            'rewrite' => ['slug' => $rewrite_slug], // Custom rewrite slug
            'menu_icon' => $data['icon'], // Assign specific Dashicon
            'menu_position' => 5,
            'show_in_rest' => true, // ($slug === 'events') ? false : true, // Enable Gutenberg editor
            'exclude_from_search' => ($slug === 'ogtv') ? true : false, // Exclude OGTV from search results
        ]);
    }
}
add_action('init', 'register_custom_post_types');
