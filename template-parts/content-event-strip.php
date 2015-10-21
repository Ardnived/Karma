<?php
global $post;

$startdate = Karma_Events::get_startdate();
$enddate = Karma_Events::get_enddate();

$startday = floor( abs( Karma_Events::$left_clamp - $startdate ) / 86400 );
$endday = floor( abs( $enddate - Karma_Events::$left_clamp ) / 86400 );

if ( ! isset( $strip_row_tracker ) ) {
	$strip_row_tracker = array();
}

$row = 0;
while ( isset( $strip[ $row ] ) && $strip_row_tracker[ $row ] > $startdate ) {
	$row++;
}

$strip_row_tracker[ $row ] = $enddate;

$width = ( $endday - $startday ) / 365 * 100;
$position = $startday / 365 * 100;

$month = strtolower( date( "M", $startdate ) );

?>
<a class="strip <?php echo $month; ?> open-action"
	style="top: <?php echo 10 * $row; ?>px; left: <?php echo $position; ?>%; min-width: <?php echo $width; ?>%; width: <?php echo $width; ?>%;"
	href="#<?php the_permalink(); ?>"
	data-id="<?php the_ID(); ?>">
	<?php the_title(); ?>
</a>