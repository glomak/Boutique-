<?php
/**
 * The template for displaying the footer.
 * @package Ecommerce Solution
 */
?>

<?php if( get_theme_mod( 'ecommerce_solution_hide_show_scroll',true) != '') { ?>
    <?php $theme_lay = get_theme_mod( 'ecommerce_solution_footer_options','Right');
        if($theme_lay == 'Left align'){ ?>
            <a href="#" id="scrollbutton" class="left"><i class="<?php echo esc_attr(get_theme_mod('ecommerce_solution_back_to_top_icon','fas fa-long-arrow-alt-up')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Back to Top', 'ecommerce-solution' ); ?></span></a>
        <?php }else if($theme_lay == 'Center align'){ ?>
            <a href="#" id="scrollbutton" class="center"><i class="<?php echo esc_attr(get_theme_mod('ecommerce_solution_back_to_top_icon','fas fa-long-arrow-alt-up')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Back to Top', 'ecommerce-solution' ); ?></span></a>
        <?php }else{ ?>
            <a href="#" id="scrollbutton"><i class="<?php echo esc_attr(get_theme_mod('ecommerce_solution_back_to_top_icon','fas fa-long-arrow-alt-up')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Back to Top', 'ecommerce-solution' ); ?></span></a>
    <?php }?>
<?php }?>

<footer role="contentinfo">
    <?php //Set widget areas classes based on user choice
        $widget_areas = get_theme_mod('footer_widget_areas', '3');
        if ($widget_areas == '3') {
            $cols = 'col-md-4';
        } elseif ($widget_areas == '4') {
            $cols = 'col-md-3';
        } elseif ($widget_areas == '2') {
            $cols = 'col-md-6';
        } else {
            $cols = 'col-md-12';
        }
    ?>
    <aside id="sidebar-footer" class="footer-wp" role="complementary">
        <div class="container">
            <div class="row">
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <div class="sidebar-column <?php echo esc_attr( $cols ) ?>">
                        <?php dynamic_sidebar( 'footer-1'); ?>
                    </div>
                <?php endif; ?> 
                <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                    <div class="sidebar-column <?php echo esc_attr( $cols ) ?>">
                        <?php dynamic_sidebar( 'footer-2'); ?>
                    </div>
                <?php endif; ?> 
                <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                    <div class="sidebar-column <?php echo esc_attr( $cols ) ?>">
                        <?php dynamic_sidebar( 'footer-3'); ?>
                    </div>
                <?php endif; ?> 
                <?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
                    <div class="sidebar-column <?php echo esc_attr( $cols ) ?>">
                        <?php dynamic_sidebar( 'footer-4'); ?>
                    </div>
                <?php endif; ?>
            </div> 
        </div>  
    </aside>
	<div class="copyright-wrapper">
        <div class="container">
            <p><?php echo esc_html(get_theme_mod('ecommerce_solution_footer_copy',__('Copyright 2019','ecommerce-solution'))); ?> <?php ecommerce_solution_credit(); ?></p>
        </div>
        <div class="clear"></div>
    </div>
</footer>
    
<?php wp_footer(); ?>

</body>
</html>