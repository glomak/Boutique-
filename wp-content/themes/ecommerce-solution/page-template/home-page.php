<?php
/**
 * Template Name: Custom Home Page
 */

get_header(); ?>

<main role="main" id="skip_content">
  <?php do_action( 'ecommerce_solution_above_slider' ); ?>
  <?php if( get_theme_mod('ecommerce_solution_slider_hide',true) != ''){ ?>
    <section id="slider">
      <?php $slider_speed = get_theme_mod('ecommerce_solution_slider_speed', 3000); ?>
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="<?php echo esc_attr($slider_speed); ?>"> 
        <?php $slider_page = array();
          for ( $count = 1; $count <= 4; $count++ ) {
            $mod = intval( get_theme_mod( 'ecommerce_solution_slider_setting' . $count ));
            if ( 'page-none-selected' != $mod ) {
              $slider_page[] = $mod;
            }
          }
          if( !empty($slider_page) ) :
            $args = array(
              'post_type' => 'page',
              'post__in' => $slider_page,
              'orderby' => 'post__in'
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
              $i = 1;
        ?>     
        <div class="carousel-inner" role="listbox">
          <?php  while ( $query->have_posts() ) : $query->the_post(); ?>
            <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
              <?php the_post_thumbnail(); ?>
              <div class="carousel-caption">
                <div class="inner_carousel">
                  <div class="carousel-content">
                    <h1><?php the_title(); ?></h1>
                    <p><?php $excerpt = get_the_excerpt(); echo esc_html( ecommerce_solution_string_limit_words( $excerpt, esc_attr(get_theme_mod('ecommerce_solution_slider_excerpt_number','15')))); ?></p>
                    <div class="more-btn">
                      <a href="<?php the_permalink(); ?>"><?php esc_html_e('SHOP NOW','ecommerce-solution'); ?><span class="screen-reader-text"><?php esc_html_e( 'SHOP NOW','ecommerce-solution' );?></span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php $i++; endwhile; 
          wp_reset_postdata();?>
        </div>
        <?php else : ?>
            <div class="no-postfound"></div>
        <?php endif;
        endif;?>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
          <span class="screen-reader-text"><?php esc_attr_e( 'Previous','ecommerce-solution' );?></span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
          <span class="screen-reader-text"><?php esc_attr_e( 'Next','ecommerce-solution' );?></span>
        </a>
      </div>  
      <div class="clearfix"></div>
    </section>
  <?php }?>

  <?php do_action( 'ecommerce_solution_below_slider' ); ?>

  <section id="new-collection"> 
    <div class="container">    
      <?php $collection_page = array();
        $mod = absint( get_theme_mod( 'ecommerce_solution_product_settings','ecommerce-solution'));
        if ( 'page-none-selected' != $mod ) {
          $collection_page[] = $mod;
        }
        if( !empty($collection_page) ) :
          $args = array(
            'post_type' => 'page',
            'post__in' => $collection_page,
            'orderby' => 'post__in'
          );
          $query = new WP_Query( $args );
          if ( $query->have_posts() ) :
            $count = 0;
            while ( $query->have_posts() ) : $query->the_post(); ?>
              <strong><?php the_title(); ?></strong>
              <?php the_content(); ?>
            <?php $count++; endwhile; ?>
          <?php else : ?>
            <div class="no-postfound"></div>
          <?php endif;
        endif;
        wp_reset_postdata()
      ?>
    </div>
  </section>

  <?php do_action( 'ecommerce_solution_below_new_collection' ); ?>

  <div class="container front-page-content">
    <?php while ( have_posts() ) : the_post(); ?>
      <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
  </div>
</main>

<?php get_footer(); ?>