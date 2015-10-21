<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Karma
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'no-js' ); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'karma' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<!--div class="site-branding">
			<h1 class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			</h1>
			<div class="site-description"><?php bloginfo( 'description' ); ?></div>
		</div><!-- .site-branding -->

		<!--nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'karma' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
		</nav><!-- #site-navigation -->

		<nav id="timeline">
			<div class="header">
				<?php Karma_Events::render_months(); ?>
			</div>
			<div class="body">
				<?php
				global $wp_query;

				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content-event-strip' );
				}

				rewind_posts();
				?>
			</div>
			<div class="clear"></div>
		</nav>
		<div class="clear"></div>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
