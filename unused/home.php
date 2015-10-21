<?php
/**
 * @package Karma
 */

get_header(); ?>

<?php
$parser = new GSheet_Parser();
$data = $parser->parse( "https://spreadsheets.google.com/feeds/list/1aDtgp1G1dSEsL95YnYp55mjigTJhlPVMsFFzhnQzvoY/od6/public/full" );

for ( $i = 0; $i < count( $data ); $i++ ) {
	$data[$i]['startdate'] = strtotime( $data[$i]['startdate'] );
	$data[$i]['enddate'] = strtotime( $data[$i]['enddate'] );
	$event = $data[$i];

	Karma_Events_Display::register( $data[$i] );
}

while ( have_posts() ) {
	the_post();

	$links = array();
	$contacts = Karma_Events::get_contacts();

	if ( ! empty( $contacts ) ) {
		foreach ( $contacts as $index => $contact ) {
			$links[ $contact['name'] ] = $contact['link'];
		}
	}

	Karma_Events_Display::register( array(
		'startdate' => Karma_Events::get_startdate(),
		'enddate' => Karma_Events::get_enddate(),
		'title' => get_the_title(),
		'content' => get_the_content(),
		'links' => $links,
	) );
}

/*
Karma_Events::register( array(
	'startdate' => strtotime( "2015/08/10" ),
	'enddate' => strtotime( "2015/08/12" ),
	'title' => "London Yuva Seminar",
	'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tincidunt lorem quam, id bibendum eros feugiat id. Curabitur tortor orci, iaculis id massa vel, tempus eleifend elit. Donec euismod elit at est gravida, in congue metus facilisis.",
	'links' => array(
		"Website" => "http://www.realizecanada.ca"
	),
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/08/17" ),
	'enddate' => strtotime( "2015/08/24" ),
	'title' => "Shri Krishna Puja 2015",
	'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tincidunt lorem quam, id bibendum eros feugiat id. Curabitur tortor orci, iaculis id massa vel, tempus eleifend elit. Donec euismod elit at est gravida, in congue metus facilisis.",
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/09/03" ),
	'enddate' => strtotime( "2015/09/04" ),
	'img' => "http://placehold.it/230x100",
	'title' => "North China Tour",
	'content' => "In non pellentesque erat. Curabitur ullamcorper turpis velit, maximus ornare nisl auctor eu. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam et sem vel sapien consequat euismod nec et justo.",
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/02/01" ),
	'enddate' => strtotime( "2015/03/24" ),
	'title' => "Australia Tour",
	'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tincidunt lorem quam, id bibendum eros feugiat id. Curabitur tortor orci, iaculis id massa vel, tempus eleifend elit. Donec euismod elit at est gravida, in congue metus facilisis.",
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/09/13" ),
	'enddate' => strtotime( "2015/09/24" ),
	'title' => "Amsterdam Seminar",
	'content' => "Curabitur interdum mauris in risus tempor, ac aliquam est molestie. Suspendisse pellentesque tortor quis posuere maximus. Nunc in dignissim sapien. Suspendisse et sollicitudin neque.",
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/08/30" ),
	'enddate' => strtotime( "2015/09/28" ),
	'title' => "Malaysia Tour",
	'content' => "Curabitur interdum mauris in risus tempor, ac aliquam est molestie. Suspendisse pellentesque tortor quis posuere maximus. Nunc in dignissim sapien. Suspendisse et sollicitudin neque.",
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/03/21" ),
	'enddate' => strtotime( "2015/03/21" ),
	'title' => "Inner Peace Day",
	'content' => "Curabitur interdum mauris in risus tempor, ac aliquam est molestie. Suspendisse pellentesque tortor quis posuere maximus. Nunc in dignissim sapien. Suspendisse et sollicitudin neque.",
) );

Karma_Events::register( array(
	'startdate' => strtotime( "2015/06/03" ),
	'enddate' => strtotime( "2015/07/04" ),
	'img' => "http://placehold.it/230x100",
	'title' => "Uganda Tour",
	'content' => "In non pellentesque erat. Curabitur ullamcorper turpis velit, maximus ornare nisl auctor eu. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam et sem vel sapien consequat euismod nec et justo.",
) );
*/
?>

<div id="primary" class="content-area">
	<section id="timeline">
		<div class="header">
			<?php Karma_Events_Display::render_months(); ?>
		</div>
		<div class="body">
			<?php Karma_Events_Display::render_strips(); ?>
		</div>
		<div class="clear"></div>
	</section>
	<div class="clear"></div>

	<main id="main" class="site-main preload" role="main">
		<?php Karma_Events_Display::render_articles(); ?>
	</main><!-- #main -->
	<div class="clear"></div>
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
