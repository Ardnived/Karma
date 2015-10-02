<?php

class Karma_Events_Display {
	private static $events = array();
	private static $row_tracker = array();
	private static $left_clamp = null;
	private static $right_clamp = null;
	private static $sorted = true;

	public static function init() {
		self::$left_clamp = strtotime( date('Y/m/01') );
		self::$right_clamp = strtotime( date( 'Y/m/t', strtotime( '11 months', self::$left_clamp ) ) );
	}

	public static function register( $args ) {
		if ( $args['startdate'] > self::$right_clamp ) return;
		if ( $args['enddate'] < self::$left_clamp ) return;

		$args['slug'] = sanitize_title( $args['title'] );
		self::$events[] = $args;
		self::$sorted = false;
	}

	public static function sort() {
		if ( ! $sorted ) {
			usort( self::$events, function( $a, $b ) {
				return $a['startdate'] > $b['startdate'];
			} );

			$sorted = true;
		}
	}

	public static function render_months() {
		$offset = 0;

		for ( $i = 0; $i < 12; $i++ ) {
			$date = strtotime( $i . ' month', self::$left_clamp );
			$label = date( 'M', $date );

			if ( $label == 'Jan' || $i == 0 ) {
				$label .= " '" . date( 'y', $date );
			}

			?>
			<div style="left: <?php echo $offset; ?>%;"><?php echo $label; ?></div>
			<?php
			$offset += 8.333;
		}
	}

	public static function render_strips() {
		self::sort();

		foreach ( self::$events as $index => $args ) {
			self::render_strip( $args );
		}
	}

	public static function render_strip( $args ) {
		if ( $args['startdate'] < self::$left_clamp ) {
			$startdate = self::$left_clamp;
		} else {
			$startdate = $args['startdate'];
		}

		if ( $args['enddate'] > self::$right_clamp ) {
			$enddate = self::$right_clamp;
		} else {
			$enddate = $args['enddate'];
		}

		$startday = floor( abs( self::$left_clamp - $startdate ) / 86400 ); //date( "z", $startdate );
		$endday = floor( abs( $enddate - self::$left_clamp ) / 86400 );
		$row = 0;

		while ( self::$row_tracker[ $row ] > $args['startdate'] ) {
			$row++;
		}

		self::$row_tracker[ $row ] = $args['enddate'];
		$width = ( $endday - $startday ) / 365 * 100;
		$position = $startday / 365 * 100;

		?>
		<div class="strip <?php echo strtolower( date( "M", $args['startdate'] ) ); ?>" style="top: <?php echo 10 * $row; ?>px; left: <?php echo $position; ?>%; min-width: <?php echo $width; ?>%; width: <?php echo $width; ?>%;" data-anchor="#<?php echo $args['slug']; ?>">
			<?php echo $args['title']; ?>
		</div>
		<?php
	}

	public static function render_articles() {
		self::sort();

		foreach ( self::$events as $index => $args ) {
			self::render_article( $args );
		}
	}

	public static function render_article( $args ) {
		$class = strtolower( date( "M", $args['startdate'] ) );
		$class .= ( empty( $args['img'] ) ? "" : " img" );

		?>
		<article id="<?php echo $args['slug']; ?>" class="<?php echo $class; ?>">
			<?php if ( ! empty( $args['img'] ) ) { ?>
				<img src="<?php echo $args['img']; ?>"></img>
			<?php } ?>
			<div class="date">
				<div class="startdate">
					<div class="month"><?php echo date( "M", $args['startdate'] ); ?></div>
					<div class="day"><?php echo date( "d", $args['startdate'] ); ?></div>
				</div>
				<div class="enddate">
					<div class="until">until</div>
					<div class="day"><?php echo date( "M d", $args['enddate'] ); ?></div>
				</div>
			</div>
			<h4 class="title"><?php echo $args['title']; ?></h4>
			<div class="content"><?php echo $args['content']; ?></div>
			<ul class="links">
				<?php
				if ( ! empty( $args['links'] ) ) {
					foreach ( $args['links'] as $name => $url ) {
						?>
						<li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
						<?php
					}
				}
				?>
			</ul>
		</article>
		<?php
	}
}

Karma_Events_Display::init();
