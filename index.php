<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Karma
 */

get_header();
get_sidebar();
?>
<div id="primary" class="content-area">
	<?php
	if ( is_404() || ! have_posts() ) {
		?>
		<div class="alert error">
			The page you clicked could not be found.
		</div>
		<?php
	}

	?>
	<main id="main" class="site-main preload" role="main">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			}
		} else {
			get_template_part( 'template-parts/content', 'none' );
		}
		?>
	</main><!-- #main -->
</div><!-- #primary -->
<?php
get_footer();
?>
