<?php
global $post;

// Note: try to keep this in sync with the css-defined colours for each month.
$colours = array(
	'jan' => "rgb(38,62,85)",
	'feb' => "rgb(54,92,118)",
	'mar' => "rgb(71,119,150)",
	'apr' => "rgb(88,148,184)",
	'may' => "rgb(119,165,146)",
	'jun' => "rgb(167,182,111)",
	'jul' => "rgb(225,199,80)",
	'aug' => "rgb(219,168,67)",
	'sep' => "rgb(216,138,55)",
	'oct' => "rgb(213,108,43)",
	'nov' => "rgb(189,78,38)",
	'dec' => "rgb(165,49,35)",
);

$colour = $colours[ strtolower( date( "M", Karma_Events::get_startdate() ) ) ];
?>
<table style="width: 90%; margin: 20px auto; border-collapse: collapse; border-spacing: 0; border: 0; padding: 0; text-align: left;">
	<tr>
		<td>
			<div style="border: 10px solid <?php echo $colour; ?>; border-bottom-width: 15px; border-top-left-radius: 10px; border-top-right-radius: 10px;"></div>
		</td>
	</tr>
	<tr>
		<td>
			<?php
			if ( has_post_thumbnail() ) {
				$url = wp_get_attachment_url( get_post_thumbnail_id() );
			} else {
				$url = get_template_directory_uri() . "/img/sunrise-crop.jpg";
			}
			?>
			<img style="width: 100%; display: block;" src="<?php echo $url; ?>"></img>
		</td>
	</tr>
	<tr style="background: <?php echo $colour; ?>; padding: 10px;">
		<th style="padding: 0 0.9em;">
			<h3 style="color: white; text-transform: uppercase; margin: 0;"><?php the_title(); ?></h3>
		</th>
	</tr>
	<tr style="background: #EFEFEF;">
		<td style="text-align: right; padding: 2px 1em; text-transform: uppercase; font-size: 80%; font-weight: bold;">
			<?php echo date( "M j", Karma_Events::get_startdate() ); ?> - <?php echo date( "M j, Y", Karma_Events::get_enddate() ); ?>
		</td>
	</tr>
	<tr style="background: #EFEFEF;">
		<td style="padding: 1em; padding-top: 0;">
			<?php the_excerpt(); ?>
			<a href="<?php the_permalink(); ?>" style="font-size: 90%;">Read more online</a>
			<b style="font-size: 80%;"> OR </b>
			<a href="<?php the_permalink(); ?>?subscribe=1" style="font-size: 90%;">Subscribe to news about this event</a>.
		</td>
	</tr>
	<tr>
		<td>
			<div style="background: <?php echo $colour; ?>; border-bottom: 10px solid <?php echo $colour; ?>; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px;"></div>
		</td>
	</tr>
</table>

<span style="font-size: 12px; line-height: 12px;">
	If you have any trouble with this email or the website contact Devindra Payment at <a href="mailto:devindra@shaw.ca">devindra@shaw.ca</a>
	<br>
	You received this email because you are subscribed to receive emails about new events at <?php echo get_bloginfo( 'name' ); ?>
</span>
