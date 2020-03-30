<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package congenial
 */

if ( ! is_active_sidebar( 'right_widget' ) ) {
	return;
}
?>
<div class="col-md-4">
<aside id="secondary" class="widget-area">
	<?php dynamic_sidebar( 'right_widget' ); ?>
</aside><!-- #secondary -->
</div>
