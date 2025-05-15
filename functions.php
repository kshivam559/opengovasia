<?php
/**
 * OpenGov Asia functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package OpenGov_Asia
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function opengovasia_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on OpenGov Asia, use a find and replace
	 * to change 'opengovasia' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('opengovasia', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary-menu' => esc_html__('Primary Menu', 'opengovasia'),
			'mobile_menu' => __('Mobile Menu', 'opengovasia'),
			'latest-topic-menu' => __('Latest Topic Menu', 'opengovasia'),
			'explore-menu' => __('Explore Menu', 'opengovasia'),
			'about-menu' => __('About Menu', 'opengovasia')
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height' => 80,
			'width' => 444,
			'flex-width' => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'opengovasia_setup');


/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */

function opengovasia_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar', 'opengovasia'),
			'id' => 'opengovasia_sidebar',
			'description' => esc_html__('Add widgets here.', 'opengovasia'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}
add_action('widgets_init', 'opengovasia_widgets_init');

/**
 * Remove post tags from the post type
 */

function remove_post_tags()
{
	unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'remove_post_tags');


/**
 * Enqueue scripts and styles.
 */
function opengovasia_scripts()
{

	/* Enqueue styles */

	wp_enqueue_style('opengovasia-fonts', get_template_directory_uri() . '/assets/css/fonts.css', array(), _S_VERSION);

	wp_enqueue_style('opengovasia-uni-core-css', get_template_directory_uri() . '/assets/js/uni-core/css/uni-core.min.css', array(), _S_VERSION);

	wp_enqueue_style('opengovasia-unicons-css', get_template_directory_uri() . '/assets/css/unicons.min.css', array(), _S_VERSION);

	wp_enqueue_style('opengovasia-swiper-bundle-css', get_template_directory_uri() . '/assets/css/swiper-bundle.min.css', array(), _S_VERSION);

	wp_enqueue_style('opengovasia-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('opengovasia-style', 'rtl', 'replace');

	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('wc-blocks-style'); // Remove WooCommerce block CSS
	wp_dequeue_style('global-styles');
	wp_dequeue_style('classic-theme-styles');

	/* Enqueue scripts */

	wp_enqueue_script('opengovasia-uni-core', get_template_directory_uri() . '/assets/js/uni-core/js/uni-core-bundle.min.js', array(), _S_VERSION, true);

	wp_enqueue_script('jquery-core');

	wp_enqueue_script('opengovasia-swiper-bundle-js', get_template_directory_uri() . '/assets/js/libs/swiper-bundle.min.js', array(), _S_VERSION, true);

	wp_enqueue_script('opengovasia-data-attr-js', get_template_directory_uri() . '/assets/js/helpers/data-attr-helper.js', array(), _S_VERSION, true);

	wp_enqueue_script('opengovasia-swiper-helper-js', get_template_directory_uri() . '/assets/js/helpers/swiper-helper.js', array(), _S_VERSION, true);

	wp_enqueue_script('opengovasia-app-head-js', get_template_directory_uri() . '/assets/js/app-head-bs.js', array(), _S_VERSION, true);

	wp_enqueue_script('opengovasia-app-js', get_template_directory_uri() . '/assets/js/app.js', array(), _S_VERSION, true);


	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'opengovasia_scripts');

/**
 * Remove the emoji URL from the head
 */

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


/**
 * Template Functions
 */

require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/custom-post-types.php';

/**
 * Custom Post type Taxonomies for this theme.
 */

require get_template_directory() . '/inc/taxonomy/country.php';

require get_template_directory() . '/inc/taxonomy/channel.php';

require get_template_directory() . '/inc/taxonomy/years.php';

require get_template_directory() . '/inc/taxonomy/playlists.php';

/*
 * Custom Query Filter for this theme.
 */

require get_template_directory() . '/inc/query-filters.php';


// Flush rewrite rules on theme activation
function flush_rewrite_rules_once()
{
	register_country_taxonomy(); // Ensure taxonomy exists before flushing rules
	flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_rewrite_rules_once');


/**
 * Custom Meta Box for this theme.
 */

require get_template_directory() . '/inc/meta-box/events.php';
require get_template_directory() . '/inc/meta-box/awards.php';
require get_template_directory() . '/inc/meta-box/ogtv.php';

/**
 * Custom Navigation Walker for this theme.
 */

require get_template_directory() . '/inc/class-walker-nav.php';

/** 
 * Register Social Media Meta for Author Profile
 */

require get_template_directory() . '/inc/author-box.php';


/**
 * Custom Infinite Scroll for this theme.
 */

require get_template_directory() . '/inc/infinite-scroll.php';

/**
 * Country Parameters Persistance for this theme.
 */

require get_template_directory() . '/inc/country-params-persist.php';

/**
 * Add rewrite rules for custom post types
 */

require get_template_directory() . '/inc/rewrite-rules.php';

/**
 * Add custom query loop
 */

require get_template_directory() . '/inc/query-loop.php';

/**
 * Add PWA Support
 */

require get_template_directory() . '/inc/pwa-settings.php';