<?php
/**
 * congenial functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package congenial
 */

if ( ! function_exists( 'congenial_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function congenial_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on congenial, use a find and replace
	 * to change 'congenial' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'congenial', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
		add_theme_support( 'title-tag' );
    /*
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support( 'post-thumbnails' );

    // Add menus.
    register_nav_menus( array(
        'primary' => __( 'Menu', 'congenial' ),

    ) );

	/*
     * Woocommerce Support
     */
	$GLOBALS['content_width'] = apply_filters( 'house_shop_content_width', 640 );
    add_theme_support( 'woocommerce' );
    add_image_size('house-shop-homepage-thumb',240,145,true);


    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );

   	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'congenial_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );

}
endif; // congenial_setup

add_action( 'after_setup_theme', 'congenial_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function congenial_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'congenial_content_width', 640 );
}
add_action( 'after_setup_theme', 'congenial_content_width', 0 );


if ( ! function_exists( 'congenial_widgets_init' ) ) :

function congenial_widgets_init() {

    /*
     * Register widget areas.
     */
  
  
     register_sidebar( array(
        'name' => __( 'Right Sidebar', 'congenial' ),
        'id' => 'right_widget',
		'description'   => esc_html__( 'Add widgets here.', 'congenial' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer Widget 1', 'congenial' ),
        'id' => 'footer_widget_1',
		'description'   => esc_html__( 'Add widgets here.', 'congenial' ),
        'before_widget' => '<li id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer Widget 2', 'congenial' ),
        'id' => 'footer_widget_2',
		'description'   => esc_html__( 'Add widgets here.', 'congenial' ),
        'before_widget' => '<li id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer Widget 3', 'congenial' ),
        'id' => 'footer_widget_3',
		'description'   => esc_html__( 'Add widgets here.', 'congenial' ),
        'before_widget' => '<li id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );
	
	register_sidebar( array(
        'name' => __( 'Footer Widget 4', 'congenial' ),
        'id' => 'footer_widget_4',
		'description'   => esc_html__( 'Add widgets here.', 'congenial' ),
        'before_widget' => '<li id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    
}
add_action( 'widgets_init', 'congenial_widgets_init' );
endif;// congenial_widgets_init


if ( ! function_exists( 'congenial_customize_register' ) ) :

function congenial_customize_register( $wp_customize ) {

 /* Message Layout */

    $wp_customize->add_section( 'copyright_section', array(
        'priority'       => 250,
        'title' => __( 'Copyright', 'congenial' )
    ));

    $wp_customize->add_setting( 'copyright_section_text', array(
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'esc_textarea'
    ));

    $wp_customize->add_control( 'copyright_section_text', array(
        'label' => __( 'Copyright', 'congenial' ),
        'type' => 'textarea',
        'section' => 'copyright_section'
    ));

}

add_action( 'customize_register', 'congenial_customize_register' );

endif;// congenial_customize_register



if ( ! function_exists( 'congenial_enqueue_scripts' ) ) :
    function congenial_enqueue_scripts() {

     /* Js */

    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.js', array('jquery'), '20151215', true );
	wp_enqueue_script( 'congenial-navigation', get_template_directory_uri() . '/js/navigation.js', array('jquery'), '20151215', true );
	wp_enqueue_script( 'congenial-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array('jquery'), '20151215', true );

     /* Css */

    wp_enqueue_style( 'congenial-style', get_stylesheet_uri() );
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
    wp_enqueue_style( 'theme', get_template_directory_uri() . '/css/theme.css');
    wp_enqueue_style( 'themify-icons', get_template_directory_uri() . '/css/themify-icons/themify-icons.css');

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
    }
    add_action( 'wp_enqueue_scripts', 'congenial_enqueue_scripts' );
endif;




/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/*
 * Resource files included by Bootstrap.
 */
require get_template_directory() . '/inc/bootstrap/wp_bootstrap_navwalker.php';

/**
 * Tgmpa Plugin Active
 */
require_once ( get_template_directory() . '/inc/plugin-activation/congenial-plugin-activation.php');