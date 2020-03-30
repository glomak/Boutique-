<?php
/**
 * Ecommerce Solution Theme Customizer
 * @package Ecommerce Solution
 */

load_template( trailingslashit( get_template_directory() ) . '/inc/logo-sizer.php' );
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function ecommerce_solution_customize_register( $wp_customize ) {

	load_template( trailingslashit( get_template_directory() ) . 'inc/custom-control.php' );
	load_template( trailingslashit( get_template_directory() ) . '/inc/icon-changer.php' );

	$wp_customize->add_setting( 'ecommerce_solution_logo_sizer',array(
		'default' => 50,
		'transport' => 'refresh',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_logo_sizer',array(
		'label' => esc_html__( 'Logo Sizer','ecommerce-solution' ),
		'section' => 'title_tagline',
		'priority'    => 9,
		'input_attrs' => array(
			'min' => 0,
			'max' => 100,
			'step' => 1,
		),
	)));

	$wp_customize->add_setting('ecommerce_solution_site_title_enable',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_site_title_enable',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Site Title','ecommerce-solution'),
       'section' => 'title_tagline'
    ));

    $wp_customize->add_setting('ecommerce_solution_site_tagline_enable',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_site_tagline_enable',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Site Tagline','ecommerce-solution'),
       'section' => 'title_tagline'
    ));

    $wp_customize->add_setting('ecommerce_solution_background_skin',array(
        'default' => __('Without Background','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_background_skin',array(
        'type' => 'radio',
        'label' => __('Background Skin','ecommerce-solution'),
        'description' => __('Here you can add the background skin along with the background image.','ecommerce-solution'),
        'section' => 'background_image',
        'choices' => array(
            'With Background' => __('With Background Skin','ecommerce-solution'),
            'Without Background' => __('Without Background Skin','ecommerce-solution'),
        ),
	) );

	//add home page setting pannel
	$wp_customize->add_panel( 'ecommerce_solution_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Home Page Settings', 'ecommerce-solution' ),
	    'description' => __( 'Description of what this panel does.', 'ecommerce-solution' ),
	) );

	//layout setting
	$wp_customize->add_section( 'ecommerce_solution_option', array(
    	'title'      => __( 'Layout Settings', 'ecommerce-solution' ),
    	'panel'    => 'ecommerce_solution_panel_id',
	) );

	$wp_customize->add_setting('ecommerce_solution_preloader',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_preloader',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide Preloader','ecommerce-solution'),
       'section' => 'ecommerce_solution_option'
    ));

	$wp_customize->add_setting('ecommerce_solution_preloader_bg_color_option', array(
		'default'           => '#000',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_preloader_bg_color_option', array(
		'label'    => __('Preloader Background Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_option',
	)));

	$wp_customize->add_setting('ecommerce_solution_preloader_icon_color_option', array(
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_preloader_icon_color_option', array(
		'label'    => __('Preloader Icon Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_option',
	)));

	$wp_customize->add_setting('ecommerce_solution_width_layout_options',array(
        'default' => __('Default','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_width_layout_options',array(
        'type' => 'radio',
        'label' => __('Container Box','ecommerce-solution'),
        'description' => __('Here you can change the Width layout. ','ecommerce-solution'),
        'section' => 'ecommerce_solution_option',
        'choices' => array(
            'Default' => __('Default','ecommerce-solution'),
            'Container Layout' => __('Container Layout','ecommerce-solution'),
            'Box Layout' => __('Box Layout','ecommerce-solution'),
        ),
	) );

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('ecommerce_solution_layout_options',array(
        'default' => __('Right Sidebar','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'	        
	) );

	$wp_customize->add_control('ecommerce_solution_layout_options', array(
        'type' => 'select',
        'label' => __('Select different post sidebar layout','ecommerce-solution'),
        'section' => 'ecommerce_solution_option',
        'choices' => array(
            'One Column' => __('One Column','ecommerce-solution'),
            'Three Columns' => __('Three Columns','ecommerce-solution'),
            'Four Columns' => __('Four Columns','ecommerce-solution'),
            'Grid Layout' => __('Grid Layout','ecommerce-solution'),
            'Left Sidebar' => __('Left Sidebar','ecommerce-solution'),
            'Right Sidebar' => __('Right Sidebar','ecommerce-solution')
        ),
	)   );

	$font_array = array(
		''                       => 'No Fonts',
		'Abril Fatface'          => 'Abril Fatface',
		'Acme'                   => 'Acme',
		'Anton'                  => 'Anton',
		'Architects Daughter'    => 'Architects Daughter',
		'Arimo'                  => 'Arimo',
		'Arsenal'                => 'Arsenal',
		'Arvo'                   => 'Arvo',
		'Alegreya'               => 'Alegreya',
		'Alfa Slab One'          => 'Alfa Slab One',
		'Averia Serif Libre'     => 'Averia Serif Libre',
		'Bangers'                => 'Bangers',
		'Boogaloo'               => 'Boogaloo',
		'Bad Script'             => 'Bad Script',
		'Bitter'                 => 'Bitter',
		'Bree Serif'             => 'Bree Serif',
		'BenchNine'              => 'BenchNine',
		'Cabin'                  => 'Cabin',
		'Cardo'                  => 'Cardo',
		'Courgette'              => 'Courgette',
		'Cherry Swash'           => 'Cherry Swash',
		'Cormorant Garamond'     => 'Cormorant Garamond',
		'Crimson Text'           => 'Crimson Text',
		'Cuprum'                 => 'Cuprum',
		'Cookie'                 => 'Cookie',
		'Chewy'                  => 'Chewy',
		'Days One'               => 'Days One',
		'Dosis'                  => 'Dosis',
		'Droid Sans'             => 'Droid Sans',
		'Economica'              => 'Economica',
		'Fredoka One'            => 'Fredoka One',
		'Fjalla One'             => 'Fjalla One', 
		'Francois One'           => 'Francois One',
		'Frank Ruhl Libre'       => 'Frank Ruhl Libre',
		'Gloria Hallelujah'      => 'Gloria Hallelujah',
		'Great Vibes'            => 'Great Vibes',
		'Handlee'                => 'Handlee',
		'Hammersmith One'        => 'Hammersmith One',
		'Inconsolata'            => 'Inconsolata',
		'Indie Flower'           => 'Indie Flower', 
		'IM Fell English SC'     => 'IM Fell English SC',
		'Julius Sans One'        => 'Julius Sans One',
		'Josefin Slab'           => 'Josefin Slab',
		'Josefin Sans'           => 'Josefin Sans',
		'Kanit'                  => 'Kanit', 
		'Lobster'                => 'Lobster',
		'Lato'                   => 'Lato',
		'Lora'                   => 'Lora',
		'Libre Baskerville'      => 'Libre Baskerville',
		'Lobster Two'            => 'Lobster Two', 
		'Merriweather'           => 'Merriweather',
		'Monda'                  => 'Monda', 
		'Montserrat'             => 'Montserrat',
		'Muli'                   => 'Muli', 
		'Marck Script'           => 'Marck Script', 
		'Noto Serif'             => 'Noto Serif', 
		'Open Sans'              => 'Open Sans', 
		'Overpass'               => 'Overpass',
		'Overpass Mono'          => 'Overpass Mono',
		'Oxygen'                 => 'Oxygen', 
		'Orbitron'               => 'Orbitron',
		'Patua One'              => 'Patua One',
		'Pacifico'               => 'Pacifico',
		'Padauk'                 => 'Padauk',
		'Playball'               => 'Playball',
		'Playfair Display'       => 'Playfair Display', 
		'PT Sans'                => 'PT Sans',
		'Philosopher'            => 'Philosopher',
		'Permanent Marker'       => 'Permanent Marker',
		'Poiret One'             => 'Poiret One',
		'Quicksand'              => 'Quicksand',
		'Quattrocento Sans'      => 'Quattrocento Sans',
		'Raleway'                => 'Raleway',
		'Rubik'                  => 'Rubik', 
		'Rokkitt'                => 'Rokkitt',
		'Russo One'              => 'Russo One',
		'Righteous'              => 'Righteous',
		'Slabo'                  => 'Slabo', 
		'Source Sans Pro'        => 'Source Sans Pro',
		'Shadows Into Light Two' => 'Shadows Into Light Two', 
		'Shadows Into Light'     => 'Shadows Into Light',
		'Sacramento'             => 'Sacramento',
		'Shrikhand'              => 'Shrikhand',
		'Tangerine'              => 'Tangerine',
		'Ubuntu'                 => 'Ubuntu',
		'VT323'                  => 'VT323',
		'Varela Round'           => 'Varela Round',
		'Vampiro One'            => 'Vampiro One',
		'Vollkorn'               => 'Vollkorn', 
		'Volkhov'                => 'Volkhov',
		'Yanone Kaffeesatz'      => 'Yanone Kaffeesatz'
	);

	//Typography
	$wp_customize->add_section('ecommerce_solution_typography', array(
		'title'    => __('Typography', 'ecommerce-solution'),
		'panel'    => 'ecommerce_solution_panel_id',
	));

	// This is Paragraph Color picker setting
	$wp_customize->add_setting('ecommerce_solution_paragraph_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_paragraph_color', array(
		'label'    => __('Paragraph Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_paragraph_color',
	)));

	//This is Paragraph FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_paragraph_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control(	'ecommerce_solution_paragraph_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('Paragraph Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	$wp_customize->add_setting('ecommerce_solution_paragraph_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('ecommerce_solution_paragraph_font_size', array(
		'label'   => __('Paragraph Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_paragraph_font_size',
		'type'    => 'text',
	));

	// This is "a" Tag Color picker setting
	$wp_customize->add_setting('ecommerce_solution_atag_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_atag_color', array(
		'label'    => __('"a" Tag Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_atag_color',
	)));

	//This is "a" Tag FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_atag_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control(	'ecommerce_solution_atag_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('"a" Tag Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	// This is "a" Tag Color picker setting
	$wp_customize->add_setting('ecommerce_solution_li_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_li_color', array(
		'label'    => __('"li" Tag Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_li_color',
	)));

	//This is "li" Tag FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_li_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control(	'ecommerce_solution_li_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('"li" Tag Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	// This is H1 Color picker setting
	$wp_customize->add_setting('ecommerce_solution_h1_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_h1_color', array(
		'label'    => __('H1 Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_h1_color',
	)));

	//This is H1 FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_h1_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control('ecommerce_solution_h1_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('H1 Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	//This is H1 FontSize setting
	$wp_customize->add_setting('ecommerce_solution_h1_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('ecommerce_solution_h1_font_size', array(
		'label'   => __('H1 Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_h1_font_size',
		'type'    => 'text',
	));
	// This is H2 Color picker setting
	$wp_customize->add_setting('ecommerce_solution_h2_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_h2_color', array(
		'label'    => __('h2 Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_h2_color',
	)));

	//This is H2 FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_h2_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control(
	'ecommerce_solution_h2_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('h2 Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	//This is H2 FontSize setting
	$wp_customize->add_setting('ecommerce_solution_h2_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('ecommerce_solution_h2_font_size', array(
		'label'   => __('H2 Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_h2_font_size',
		'type'    => 'text',
	));

	// This is H3 Color picker setting
	$wp_customize->add_setting('ecommerce_solution_h3_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_h3_color', array(
		'label'    => __('H3 Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_h3_color',
	)));

	//This is H3 FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_h3_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control(
	'ecommerce_solution_h3_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('H3 Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	//This is H3 FontSize setting
	$wp_customize->add_setting('ecommerce_solution_h3_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('ecommerce_solution_h3_font_size', array(
		'label'   => __('H3 Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_h3_font_size',
		'type'    => 'text',
	));

	// This is H4 Color picker setting
	$wp_customize->add_setting('ecommerce_solution_h4_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_h4_color', array(
		'label'    => __('H4 Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_h4_color',
	)));

	//This is H4 FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_h4_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control('ecommerce_solution_h4_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('H4 Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	//This is H4 FontSize setting
	$wp_customize->add_setting('ecommerce_solution_h4_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('ecommerce_solution_h4_font_size', array(
		'label'   => __('H4 Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_h4_font_size',
		'type'    => 'text',
	));

	// This is H5 Color picker setting
	$wp_customize->add_setting('ecommerce_solution_h5_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_h5_color', array(
		'label'    => __('H5 Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_h5_color',
	)));

	//This is H5 FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_h5_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control('ecommerce_solution_h5_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('H5 Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	//This is H5 FontSize setting
	$wp_customize->add_setting('ecommerce_solution_h5_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('ecommerce_solution_h5_font_size', array(
		'label'   => __('H5 Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_h5_font_size',
		'type'    => 'text',
	));

	// This is H6 Color picker setting
	$wp_customize->add_setting('ecommerce_solution_h6_color', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
		));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_h6_color', array(
		'label'    => __('H6 Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_typography',
		'settings' => 'ecommerce_solution_h6_color',
	)));

	//This is H6 FontFamily picker setting
	$wp_customize->add_setting('ecommerce_solution_h6_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
	));
	$wp_customize->add_control('ecommerce_solution_h6_font_family', array(
		'section' => 'ecommerce_solution_typography',
		'label'   => __('H6 Fonts', 'ecommerce-solution'),
		'type'    => 'select',
		'choices' => $font_array,
	));

	//This is H6 FontSize setting
	$wp_customize->add_setting('ecommerce_solution_h6_font_size', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('ecommerce_solution_h6_font_size', array(
		'label'   => __('H6 Font Size', 'ecommerce-solution'),
		'section' => 'ecommerce_solution_typography',
		'setting' => 'ecommerce_solution_h6_font_size',
		'type'    => 'text',
	));

	//Global Color
	$wp_customize->add_section('ecommerce_solution_global_color', array(
		'title'    => __('Theme Color Option', 'ecommerce-solution'),
		'panel'    => 'ecommerce_solution_panel_id',
	));

	$wp_customize->add_setting('ecommerce_solution_first_color', array(
		'default'           => '#ffca04',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ecommerce_solution_first_color', array(
		'label'    => __('Highlight Color', 'ecommerce-solution'),
		'section'  => 'ecommerce_solution_global_color',
		'settings' => 'ecommerce_solution_first_color',
	)));

	//Blog Post Settings
	$wp_customize->add_section('ecommerce_solution_post_settings', array(
		'title'    => __('Post General Settings', 'ecommerce-solution'),
		'panel'    => 'ecommerce_solution_panel_id',
	));

	$wp_customize->add_setting('ecommerce_solution_post_layouts',array(
        'default' => __('Layout 1','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control(new Ecommerce_Solution_Image_Radio_Control($wp_customize, 'ecommerce_solution_post_layouts', array(
        'type' => 'select',
        'label' => __('Post Layouts','ecommerce-solution'),
        'description' => __('Here you can change the 3 different layouts of post','ecommerce-solution'),
        'section' => 'ecommerce_solution_post_settings',
        'choices' => array(
            'Layout 1' => get_template_directory_uri().'/images/layout1.png',
            'Layout 2' => get_template_directory_uri().'/images/layout2.png',
            'Layout 3' => get_template_directory_uri().'/images/layout3.png',
    ))));

	$wp_customize->add_setting('ecommerce_solution_metafields_date',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_metafields_date',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide Date ','ecommerce-solution'),
       'section' => 'ecommerce_solution_post_settings'
    ));

	$wp_customize->add_setting('ecommerce_solution_post_date_icon',array(
		'default'	=> 'far fa-calendar-alt',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_post_date_icon',array(
		'label'	=> __('Post Date Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_post_settings',
		'type'		=> 'icon'
	)));

    $wp_customize->add_setting('ecommerce_solution_metafields_author',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_metafields_author',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide Author','ecommerce-solution'),
       'section' => 'ecommerce_solution_post_settings'
    ));

    $wp_customize->add_setting('ecommerce_solution_post_author_icon',array(
		'default'	=> 'fas fa-user',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_post_author_icon',array(
		'label'	=> __('Post Author Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_post_settings',
		'type'		=> 'icon'
	)));

    $wp_customize->add_setting('ecommerce_solution_metafields_comment',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_metafields_comment',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide Comments','ecommerce-solution'),
       'section' => 'ecommerce_solution_post_settings'
    ));

    $wp_customize->add_setting('ecommerce_solution_post_comment_icon',array(
		'default'	=> 'fas fa-comments',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_post_comment_icon',array(
		'label'	=> __('Post Comment Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_post_settings',
		'type'		=> 'icon'
	)));

    $wp_customize->add_setting('ecommerce_solution_metafields_tags',array(
       'default' => 'true',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_metafields_tags',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide Tags','ecommerce-solution'),
       'section' => 'ecommerce_solution_post_settings'
    ));

    //Post excerpt
	$wp_customize->add_setting( 'ecommerce_solution_post_excerpt_number', array(
		'default'              => 30,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'absint',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'ecommerce_solution_post_excerpt_number', array(
		'label'       => esc_html__( 'Blog Post Content Limit','ecommerce-solution' ),
		'section'     => 'ecommerce_solution_post_settings',
		'type'        => 'number',
		'settings'    => 'ecommerce_solution_post_excerpt_number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	$wp_customize->add_setting('ecommerce_solution_content_settings',array(
        'default' => __('Excerpt','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_content_settings',array(
        'type' => 'radio',
        'label' => __('Content Settings','ecommerce-solution'),
        'section' => 'ecommerce_solution_post_settings',
        'choices' => array(
            'Excerpt' => __('Excerpt','ecommerce-solution'),
            'Content' => __('Content','ecommerce-solution'),
        ),
	) );

	$wp_customize->add_setting('ecommerce_solution_button_text',array(
		'default'=> 'View More',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('ecommerce_solution_button_text',array(
		'label'	=> __('Add Button Text','ecommerce-solution'),
		'section'=> 'ecommerce_solution_post_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('ecommerce_solution_button_icon',array(
		'default'	=> 'fas fa-long-arrow-alt-right',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_button_icon',array(
		'label'	=> __('Button Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_post_settings',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting( 'ecommerce_solution_post_button_padding_top',array(
		'default' => 15,
		'transport' => 'refresh',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_post_button_padding_top',	array(
		'label' => esc_html__( 'Button Top Bottom Padding (px)','ecommerce-solution' ),
		'section' => 'ecommerce_solution_post_settings',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	)));

	$wp_customize->add_setting( 'ecommerce_solution_post_button_padding_right',array(
		'default' => 15,
		'transport' => 'refresh',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_post_button_padding_right',	array(
		'label' => esc_html__( 'Button Right Left Padding (px)','ecommerce-solution' ),
		'section' => 'ecommerce_solution_post_settings',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	)));

	$wp_customize->add_setting( 'ecommerce_solution_post_button_border_radius',array(
		'default' => 30,
		'transport' => 'refresh',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_post_button_border_radius',	array(
		'label' => esc_html__( 'Button Border Radius (px)','ecommerce-solution' ),
		'section' => 'ecommerce_solution_post_settings',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	)));

	//Related Post Settings
	$wp_customize->add_section('ecommerce_solution_related_settings', array(
		'title'    => __('Related Post Settings', 'ecommerce-solution'),
		'panel'    => 'ecommerce_solution_panel_id',
	));

	$wp_customize->add_setting( 'ecommerce_solution_related_enable_disable',array(
		'default' => true,
      	'sanitize_callback'	=> 'sanitize_text_field'
    ) );
    $wp_customize->add_control('ecommerce_solution_related_enable_disable',array(
    	'type' => 'checkbox',
        'label' => __( 'Enable / Disable Related Post','ecommerce-solution' ),
        'section' => 'ecommerce_solution_related_settings'
    ));

    $wp_customize->add_setting('ecommerce_solution_related_title',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('ecommerce_solution_related_title',array(
		'label'	=> __('Add Section Title','ecommerce-solution'),
		'section'	=> 'ecommerce_solution_related_settings',
		'type'		=> 'text'
	));

	$wp_customize->add_setting( 'ecommerce_solution_related_posts_count_number', array(
		'default'              => 3,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	) );
	$wp_customize->add_control( 'ecommerce_solution_related_posts_count_number', array(
		'label'       => esc_html__( 'Related Post Count','ecommerce-solution' ),
		'section'     => 'ecommerce_solution_related_settings',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 10,
		),
	) );

	$wp_customize->add_setting('ecommerce_solution_related_posts_taxanomies',array(
        'default' => __('categories','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_related_posts_taxanomies',array(
        'type' => 'radio',
        'label' => __('Post Taxonomies','ecommerce-solution'),
        'section' => 'ecommerce_solution_related_settings',
        'choices' => array(
            'categories' => __('Categories','ecommerce-solution'),
            'tags' => __('Tags','ecommerce-solution'),
        ),
	) );

	$wp_customize->add_setting( 'ecommerce_solution_related_post_excerpt_number',array(
		'default' => 15,
		'transport' => 'refresh',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_related_post_excerpt_number',	array(
		'label' => esc_html__( 'Content Limit','ecommerce-solution' ),
		'section' => 'ecommerce_solution_related_settings',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	)));

	//Top Bar Section
	$wp_customize->add_section('ecommerce_solution_topbar',array(
		'title'	=> __('Topbar','ecommerce-solution'),
		'description'	=> __('Add contact us here','ecommerce-solution'),
		'priority'	=> null,
		'panel' => 'ecommerce_solution_panel_id',
	));

	//Sticky Header
	$wp_customize->add_setting( 'ecommerce_solution_sticky_header',array(
      	'sanitize_callback'	=> 'sanitize_text_field'
    ) );
    $wp_customize->add_control('ecommerce_solution_sticky_header',array(
    	'type' => 'checkbox',
        'label' => __( 'Enable / Disable Sticky Header','ecommerce-solution' ),
        'section' => 'ecommerce_solution_topbar'
    ));

    $wp_customize->add_setting('ecommerce_solution_phone_icon',array(
		'default'	=> 'fas fa-phone',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_phone_icon',array(
		'label'	=> __('Phone Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_phone_number',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('ecommerce_solution_phone_number',array(
		'label'	=> __('Add phone number','ecommerce-solution'),
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'text'
	));

	$wp_customize->add_setting( 'ecommerce_solution_search_enable_disable',array(
		'default'	=> 'true',
      	'sanitize_callback'	=> 'sanitize_text_field'
    ) );
    $wp_customize->add_control('ecommerce_solution_search_enable_disable',array(
    	'type' => 'checkbox',
        'label' => __( 'Enable / Disable Search & Category','ecommerce-solution' ),
        'section' => 'ecommerce_solution_topbar'
    ));

    $wp_customize->add_setting( 'ecommerce_solution_myaccount_enable_disable',array(
    	'default'	=> 'true',
      	'sanitize_callback'	=> 'sanitize_text_field'
    ) );
    $wp_customize->add_control('ecommerce_solution_myaccount_enable_disable',array(
    	'type' => 'checkbox',
        'label' => __( 'Enable / Disable My Account','ecommerce-solution' ),
        'section' => 'ecommerce_solution_topbar'
    ));

    $wp_customize->add_setting( 'ecommerce_solution_cart_enable_disable',array(
    	'default'	=> 'true',
      	'sanitize_callback'	=> 'sanitize_text_field'
    ) );
    $wp_customize->add_control('ecommerce_solution_cart_enable_disable',array(
    	'type' => 'checkbox',
        'label' => __( 'Enable / Disable Cart','ecommerce-solution' ),
        'section' => 'ecommerce_solution_topbar'
    ));

	$wp_customize->add_setting('ecommerce_solution_category_icon',array(
		'default'	=> 'fas fa-bars',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_category_icon',array(
		'label'	=> __('Category Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_category_show_down_icon',array(
		'default'	=> 'fas fa-sort-down',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_category_show_down_icon',array(
		'label'	=> __('Category Show Down Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_category_list_icon',array(
		'default'	=> 'fas fa-chevron-right',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_category_list_icon',array(
		'label'	=> __('Category List Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_myaccount_icon',array(
		'default'	=> 'fas fa-sign-in-alt',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_myaccount_icon',array(
		'label'	=> __('My Account Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_login_user_icon',array(
		'default'	=> 'fas fa-user',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_login_user_icon',array(
		'label'	=> __('Login / Register Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_shopping_cart_icon',array(
		'default'	=> 'fas fa-shopping-cart',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_shopping_cart_icon',array(
		'label'	=> __('Cart Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_shopping_cart_icon',array(
		'default'	=> 'fas fa-shopping-cart',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_shopping_cart_icon',array(
		'label'	=> __('Cart Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_responsive_menu_open_icon',array(
		'default'	=> 'fas fa-bars',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_responsive_menu_open_icon',array(
		'label'	=> __('Responsive Open Menu Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_responsive_menu_close_icon',array(
		'default'	=> 'fas fa-times',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_responsive_menu_close_icon',array(
		'label'	=> __('Responsive Close Menu Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_topbar',
		'type'		=> 'icon'
	)));

	//Slider Section
	$wp_customize->add_section( 'ecommerce_solution_slider_section' , array(
    	'title'      => __( 'Slider Section', 'ecommerce-solution' ),
		'priority'   => null,
		'panel' => 'ecommerce_solution_panel_id'
	) );

	$wp_customize->add_setting('ecommerce_solution_slider_hide',array(
       'default' => 'false',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('ecommerce_solution_slider_hide',array(
       'type' => 'checkbox',
       'label' => __('Show / Hide slider','ecommerce-solution'),
       'section' => 'ecommerce_solution_slider_section',
    ));

	for ( $count = 1; $count <= 4; $count++ ) {

		$wp_customize->add_setting( 'ecommerce_solution_slider_setting' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'ecommerce_solution_sanitize_dropdown_pages'
		) );
		$wp_customize->add_control( 'ecommerce_solution_slider_setting' . $count, array(
			'label'    => __( 'Select Slide Image Page', 'ecommerce-solution' ),
			'description' => __('Slider image size (1500 x 600)','ecommerce-solution'),
			'section'  => 'ecommerce_solution_slider_section',
			'allow_addition' => true,
			'type'     => 'dropdown-pages'
		) );
	}

	//content layout
    $wp_customize->add_setting('ecommerce_solution_slider_content_layout',array(
    'default' => __('Left','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_slider_content_layout',array(
        'type' => 'radio',
        'label' => __('Slider Content Layout','ecommerce-solution'),
        'section' => 'ecommerce_solution_slider_section',
        'choices' => array(
            'Center' => __('Center','ecommerce-solution'),
            'Left' => __('Left','ecommerce-solution'),
            'Right' => __('Right','ecommerce-solution'),
        ),
	) );

	//Slider excerpt
	$wp_customize->add_setting( 'ecommerce_solution_slider_excerpt_number', array(
		'default'              => 30,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	) );
	$wp_customize->add_control( 'ecommerce_solution_slider_excerpt_number', array(
		'label'       => esc_html__( 'Slider Content Limit','ecommerce-solution' ),
		'section'     => 'ecommerce_solution_slider_section',
		'type'        => 'number',
		'settings'    => 'ecommerce_solution_slider_excerpt_number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	//Opacity
	$wp_customize->add_setting('ecommerce_solution_slider_opacity',array(
		'default'              => 0.7,
		'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control( 'ecommerce_solution_slider_opacity', array(
		'label'       => esc_html__( 'Slider Image Opacity','ecommerce-solution' ),
		'section'     => 'ecommerce_solution_slider_section',
		'type'        => 'select',
		'settings'    => 'ecommerce_solution_slider_opacity',
		'choices' => array(
			'0' =>  esc_attr('0','ecommerce-solution'),
			'0.1' =>  esc_attr('0.1','ecommerce-solution'),
			'0.2' =>  esc_attr('0.2','ecommerce-solution'),
			'0.3' =>  esc_attr('0.3','ecommerce-solution'),
			'0.4' =>  esc_attr('0.4','ecommerce-solution'),
			'0.5' =>  esc_attr('0.5','ecommerce-solution'),
			'0.6' =>  esc_attr('0.6','ecommerce-solution'),
			'0.7' =>  esc_attr('0.7','ecommerce-solution'),
			'0.8' =>  esc_attr('0.8','ecommerce-solution'),
			'0.9' =>  esc_attr('0.9','ecommerce-solution')
		),
	));

	$wp_customize->add_setting( 'ecommerce_solution_slider_speed',array(
		'default' => 3000,
		'transport' => 'refresh',
		'type' => 'theme_mod',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_slider_speed',array(
		'label' => esc_html__( 'Slider Slide Speed','ecommerce-solution' ),
		'section' => 'ecommerce_solution_slider_section',
		'input_attrs' => array(
			'min' => 1000,
			'max' => 5000,
			'step' => 500,
		),
	)));

	//New Collection Section
	$wp_customize->add_section( 'ecommerce_solution_new_collection_section' , array(
    	'title'      => __( 'New Collection', 'ecommerce-solution' ),
		'priority'   => null,
		'panel' => 'ecommerce_solution_panel_id'
	) );

	$wp_customize->add_setting( 'ecommerce_solution_product_settings', array(
		'default'           => '',
		'sanitize_callback' => 'ecommerce_solution_sanitize_dropdown_pages'
	) );
	$wp_customize->add_control( 'ecommerce_solution_product_settings', array(
		'label'    => __( 'Select product Page', 'ecommerce-solution' ),
		'section'  => 'ecommerce_solution_new_collection_section',
		'allow_addition' => true,
		'type'     => 'dropdown-pages'
	) );

	//footer text
	$wp_customize->add_section('ecommerce_solution_footer_section',array(
		'title'	=> __('Footer Text','ecommerce-solution'),
		'panel' => 'ecommerce_solution_panel_id'
	));

	$wp_customize->add_setting('footer_widget_areas',array(
        'default'           => '3',
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices',
    ));
    $wp_customize->add_control('footer_widget_areas',array(
        'type'        => 'radio',
        'label'       => __('Footer widget area', 'ecommerce-solution'),
        'section'     => 'ecommerce_solution_footer_section',
        'description' => __('Select the number of widget areas you want in the footer. After that, go to Appearance > Widgets and add your widgets.', 'ecommerce-solution'),
        'choices' => array(
            '1'     => __('One', 'ecommerce-solution'),
            '2'     => __('Two', 'ecommerce-solution'),
            '3'     => __('Three', 'ecommerce-solution'),
            '4'     => __('Four', 'ecommerce-solution')
        ),
    ));

    $wp_customize->add_setting('ecommerce_solution_hide_show_scroll',array(
        'default' => 'true',
        'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('ecommerce_solution_hide_show_scroll',array(
     	'type' => 'checkbox',
      	'label' => __('Enable / Disable Back To Top','ecommerce-solution'),
      	'section' => 'ecommerce_solution_footer_section',
	));

	$wp_customize->add_setting('ecommerce_solution_back_to_top_icon',array(
		'default'	=> 'fas fa-long-arrow-alt-up',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Ecommerce_Solution_Icon_Changer(
        $wp_customize,'ecommerce_solution_back_to_top_icon',array(
		'label'	=> __('Back to Top Icon','ecommerce-solution'),
		'transport' => 'refresh',
		'section'	=> 'ecommerce_solution_footer_section',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('ecommerce_solution_footer_options',array(
        'default' => __('Right align','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_footer_options',array(
        'type' => 'radio',
        'label' => __('Back To Top Alignment','ecommerce-solution'),
        'section' => 'ecommerce_solution_footer_section',
        'choices' => array(
            'Left align' => __('Left Align','ecommerce-solution'),
            'Right align' => __('Right Align','ecommerce-solution'),
            'Center align' => __('Center Align','ecommerce-solution'),
        ),
	) );

	$wp_customize->add_setting( 'ecommerce_solution_back_to_top_border_radius',array(
		'default' => 50,
		'transport' => 'postMessage',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_back_to_top_border_radius', array(
		'label' => esc_html__( 'Back to Top Border Radius (px)','ecommerce-solution' ),
		'section' => 'ecommerce_solution_footer_section',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	)));
	
	$wp_customize->add_setting('ecommerce_solution_footer_copy',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control('ecommerce_solution_footer_copy',array(
		'label'	=> __('Copyright Text','ecommerce-solution'),
		'section'	=> 'ecommerce_solution_footer_section',
		'description'	=> __('Add some text for footer like copyright etc.','ecommerce-solution'),
		'type'		=> 'text'
	));

	$wp_customize->add_setting('ecommerce_solution_footer_text_align',array(
        'default' => __('center','ecommerce-solution'),
        'sanitize_callback' => 'ecommerce_solution_sanitize_choices'
	));
	$wp_customize->add_control('ecommerce_solution_footer_text_align',array(
        'type' => 'radio',
        'label' => __('Copyright Text Alignment ','ecommerce-solution'),
        'section' => 'ecommerce_solution_footer_section',
        'choices' => array(
            'left' => __('Left Align','ecommerce-solution'),
            'right' => __('Right Align','ecommerce-solution'),
            'center' => __('Center Align','ecommerce-solution'),
        ),
	) );

	$wp_customize->add_setting( 'ecommerce_solution_footer_text_padding',array(
		'default' => 20,
		'transport' => 'refresh',
		'sanitize_callback' => 'ecommerce_solution_sanitize_integer'
	));
	$wp_customize->add_control( new Ecommerce_Solution_Custom_Control( $wp_customize, 'ecommerce_solution_footer_text_padding',	array(
		'label' => esc_html__( 'Copyright Text Padding (px)','ecommerce-solution' ),
		'section' => 'ecommerce_solution_footer_section',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	)));
	
}
add_action( 'customize_register', 'ecommerce_solution_customize_register' );	

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Ecommerce_Solution_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'Ecommerce_Solution_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Ecommerce_Solution_Customize_Section_Pro(
				$manager,
				'example_1',
				array(
					'title'   =>  esc_html__( 'Ecommerce Pro', 'ecommerce-solution' ),
					'pro_text' => esc_html__( 'Go Pro', 'ecommerce-solution' ),
					'pro_url'  => esc_url( 'https://www.buywptemplates.com/themes/ecommerce-wordpress-template/' ),
					'priority'   => 9
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'ecommerce-solution-customize-controls', trailingslashit( get_template_directory_uri() ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'ecommerce-solution-customize-controls', trailingslashit( get_template_directory_uri() ) . '/css/customize-controls.css' );
	}

	//Footer widget areas
		function ecommerce_solution_sanitize_choices( $input ) {
		    $valid = array(
		        '1'     => __('One', 'ecommerce-solution'),
		        '2'     => __('Two', 'ecommerce-solution'),
		        '3'     => __('Three', 'ecommerce-solution'),
		        '4'     => __('Four', 'ecommerce-solution')
		    );
		    if ( array_key_exists( $input, $valid ) ) {
		        return $input;
		    } else {
		        return '';
		    }
		}
}

// Doing this customizer thang!
Ecommerce_Solution_Customize::get_instance();