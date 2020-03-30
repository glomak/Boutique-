<?php $related_posts = ecommerce_solution_related_posts();
if(get_theme_mod('ecommerce_solution_related_enable_disable',true)==1){ ?>
<?php if ( $related_posts->have_posts() ): ?>
    <div class="related-posts">
        <h3><?php echo esc_html(get_theme_mod('ecommerce_solution_related_title',__('Related Post','ecommerce-solution')));?></h3>
        <div class="row">
            <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                <div class="col-lg-4 col-md-6">
                    <div class="related-inner-box">
                        <?php if(has_post_thumbnail()) { ?>
                            <div class="box-image">
                                <?php the_post_thumbnail(); ?>
                            </div>
                        <?php }?>
                        <h4><?php the_title();?></h4>
                        <?php $excerpt = get_the_excerpt(); echo esc_html( ecommerce_solution_string_limit_words( $excerpt, esc_attr(get_theme_mod('ecommerce_solution_related_post_excerpt_number','15')))); ?>
                        <div class="postbtn">
                            <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_theme_mod('ecommerce_solution_button_text','View More'));?><i class="<?php echo esc_attr(get_theme_mod('ecommerce_solution_button_icon','fas fa-long-arrow-alt-right')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'View More','ecommerce-solution' );?></span></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>
<?php wp_reset_postdata(); }?>