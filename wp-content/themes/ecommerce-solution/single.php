<?php
/**
 * The Template for displaying all single posts.
 * @package Ecommerce Solution
 */
get_header(); ?>

<main id="skip_content" role="main">
	<div class="container">
	    <div class="main-wrapper">
	    	<?php
            $layout_option = get_theme_mod( 'ecommerce_solution_layout_options','Right Sidebar');
            if($layout_option == 'One Column'){ ?>
				<div class="content_box">
					<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
		            endwhile; // end of the loop. ?>
		       	</div>
		    <?php }else if($layout_option == 'Three Columns'){ ?>
		    	<div class="row">
					<div id="sidebar" class="col-lg-3 col-md-3"><?php dynamic_sidebar('sidebar-2'); ?></div>
					<div class="content_box col-lg-6 col-md-6">
						<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
			            endwhile; // end of the loop. ?>
			       	</div>
					<div id="sidebar" class="col-lg-3 col-md-3"><?php dynamic_sidebar('sidebar-2'); ?></div>
				</div>
			<?php }else if($layout_option == 'Four Columns'){ ?>
				<div class="row">
					<div id="sidebar" class="col-lg-3 col-md-3"><?php dynamic_sidebar('sidebar-2'); ?></div>
					<div class="content_box col-lg-3 col-md-3">
						<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
			            endwhile; // end of the loop. ?>
			       	</div>
					<div id="sidebar" class="col-lg-3 col-md-3"><?php dynamic_sidebar('sidebar-2'); ?></div>
					<div id="sidebar" class="col-lg-3 col-md-3"><?php dynamic_sidebar('sidebar-2'); ?></div>
				</div>
			<?php }else if($layout_option == 'Grid Layout'){ ?>
				<div class="row">
					<div class="content_box col-lg-8 col-md-8">
						<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
			            endwhile; // end of the loop. ?>
			       	</div>
					<div id="sidebar" class="col-lg-4 col-md-4"><?php dynamic_sidebar('sidebar-2'); ?></div>
					</div>
		    <?php }else if($layout_option == 'Left Sidebar'){ ?>
		    	<div class="row">
		    		<div id="sidebar" class="col-lg-4 col-md-4"><?php dynamic_sidebar('sidebar-1'); ?></div>
					<div class="content_box col-lg-8 col-md-8">
						<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
			            endwhile; // end of the loop. ?>
			       	</div>
		       	</div>
		    <?php }else if($layout_option == 'Right Sidebar'){ ?>
		    	<div class="row">
			       	<div class="content_box col-lg-8 col-md-8">
						<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
			            endwhile; // end of the loop. ?>
			       	</div>
					<div id="sidebar" class="col-lg-4 col-md-4"><?php dynamic_sidebar('sidebar-1'); ?></div>
				</div>
			<?php } else {?>
				<div class="row">
			       	<div class="content_box col-lg-8 col-md-8">
						<?php while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/single-post' );
			            endwhile; // end of the loop. ?>
			       	</div>
					<div id="sidebar" class="col-lg-4 col-md-4"><?php dynamic_sidebar('sidebar-1'); ?></div>
				</div>
			<?php }?>
		    <div class="clearfix"></div>
	    </div>
	</div>
</main>

<?php get_footer(); ?>