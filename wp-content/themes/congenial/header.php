<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package congenial
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="profile" href="https://gmpg.org/xfn/11">

        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'congenial' ); ?></a>
          

        <div class="container">
            <!-- The overlay -->
			<div id="mySidenav" class="sidenav">
              <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                 <div class="overlay-content">
                <?php
		          wp_nav_menu( array(
			      'theme_location'    => 'primary',
			      'depth'             => 2,
			      'container'         => '',
			      'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
			      'walker'            => new WP_Bootstrap_Navwalker(),
		          ) );
		        ?>
                </div>
              </div>

<!-- Use any element to open the sidenav -->


<!-- Add all page content inside this div if you want the side nav to push page content to the right (not used if you only want the sidenav to sit on top of the page -->

		<!-- site-branding -->
        <div class="site-brand col-md-11">
	  		<div class="site-branding">
			    <?php
			     the_custom_logo();
			     if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			     else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			    endif;
			    $congenial_description = get_bloginfo( 'description', 'display' );
			     if ( $congenial_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo esc_html($congenial_description); /* WPCS: xss ok. */ ?></p>
			    <?php endif; ?>
		    </div><!-- .site-branding -->
        
		
		
		</div>
		<div class="col-md-1">
				<div class="canvas">
                <a href="javascript:void(0)" onclick="openNav()"><i class="ti-align-left"></i></a>
		         </div>
		  </div>

		
		
			<?php if ( get_header_image() ) : ?>
				<?php
					/**
					 * Filter the default congenial custom header sizes attribute.
					 *
					 * @since congenial
					 *
					 * @param string $custom_header_sizes sizes attribute
					 * for Custom Header. Default '(max-width: 709px) 85vw,
					 * (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px'.
					 */
					$custom_header_sizes = apply_filters( 'congenial_custom_header_sizes', '(max-width: 709px) 85vw, (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px' );
				?>
				<div class="header-image">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img src="<?php header_image(); ?>" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( get_custom_header()->attachment_id ) ); ?>" sizes="<?php echo esc_attr( $custom_header_sizes ); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
					</a>
				</div><!-- .header-image -->
			<?php endif; // End header image check. ?>