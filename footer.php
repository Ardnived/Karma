<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Karma
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<?php /*
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'karma' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'karma' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'karma' ), 'karma', '<a href="http://underscores.me/" rel="designer">Underscores.me</a>' ); ?>
			*/ ?>
		</div><!-- .site-info -->

		<?php /*
		<noscript>
			<div class="alert warning">
				This website requires JavaScript to function properly.
			</div>
		</noscript>
		*/ ?>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
