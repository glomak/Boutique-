<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package congenial
 */
 
get_template_part('inc/footer','widget');

?>

	</div><!-- #content -->

	<footer id="colophon" class="copyright site-footer text-center">
		<div class="site-info">
	      <p><?php echo 
		   /* translators: %s: CMS name, i.e. WordPress. */
		  esc_html( get_theme_mod( 'copyright_section_text' )); ?></p>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>


		
</body>
</html>
