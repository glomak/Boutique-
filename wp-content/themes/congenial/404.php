<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package congenial
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<section class="message error-404 not-found text-center">
				<div class="page-content">
					<h1><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'congenial' ); ?></h1>
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'congenial' ); ?></p>
				<div class="home-btn">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="home-turn"><?php esc_html_e('Home Back','congenial'); ?></a>
				</div>
				
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
