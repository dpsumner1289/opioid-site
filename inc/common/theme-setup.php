<?php
// add base theme features support
if ( ! function_exists( 'buildcreate_setup' ) ) {
	add_action( 'after_setup_theme', 'buildcreate_setup' );
	function buildcreate_setup() {
		load_theme_textdomain( 'build-create', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
}

// hide toolbar for non-admins
if(!function_exists('bc_hide_admin_bar')) {
	function bc_hide_admin_bar() {
		if (!current_user_can('edit_posts')) {
			show_admin_bar(false);
		}
	}
	add_action('set_current_user', 'bc_hide_admin_bar');
}

// register menus
if(!function_exists('menu_registration')) :
    function menu_registration() {
        register_nav_menus( array(
            'menu-1' => esc_html__( 'Primary', 'build-create' ),
        ) );
    }
    add_action( 'after_setup_theme', 'menu_registration' );
endif;

// allow mime types
if(!function_exists('bc_mime_types')) :
    function bc_mime_types($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }
    add_filter('upload_mimes', 'bc_mime_types');
endif;

// add new image sizes
if( function_exists('add_image_size') ){
	add_image_size('resource-thumb', 328, 219, true);
	add_image_size('sponsor-size', 415, 150, false);
}
add_filter( 'image_size_names_choose', 'my_custom_sizes' );
function my_custom_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'resource-thumb' => __( 'Resource Thumbnail' ),
		'sponsor-size' => __( 'Sponsor' ),
	));
}

// enqueue theme scripts and styles
if(!function_exists('buildcreate_enqueue')) {
	function buildcreate_enqueue() {
		wp_enqueue_style('bc-styles', get_stylesheet_directory_uri().'/style.css', array(), '5.1.3');
		wp_enqueue_style('fontawesome', get_stylesheet_directory_uri().'/fonts/css/all.min.css', array());
		wp_enqueue_style('fonts', get_stylesheet_directory_uri().'/fonts/fonts.css', array());
		wp_enqueue_style('mmenu-css', get_stylesheet_directory_uri().'/node_modules/mmenu-light/dist/mmenu-light.css', array());
		wp_enqueue_script("jquery");
		wp_enqueue_script('mmenu-js', get_stylesheet_directory_uri().'/node_modules/mmenu-light/dist/mmenu-light.js', array());
	}
	add_action('wp_enqueue_scripts', 'buildcreate_enqueue');
}