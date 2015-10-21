<?php

class Karma_Events {
	public static $slug = 'event';
	public static $contacts_key = '_karma_contacts';
	public static $startdate_key = '_karma_startdate';
	public static $enddate_key = '_karma_enddate';
	public static $nonce_key = 'karma_events_metadata_nonce';
	
	public static $left_clamp = null;
	public static $right_clamp = null;

	public static $hero_slug = null;

	public static function init() {
		register_post_type( self::$slug, array(
			'label' => "Events",
			'labels' => array(
				'name' => "Events",
				'singular_name' => "Event",
			),
			'public' => true,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'register_meta_box_cb' => array( __CLASS__, 'add_meta_boxes' ),
			'rewrite' => array(
				'slug' => "",
				'with_front' => false,
			),
		) );

		add_action( 'save_post', array( __CLASS__, 'save_metadata' ) );
		add_action( 'admin_menu', array( __CLASS__, 'remove_menu_items' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_admin_scripts_and_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_public_scripts_and_styles' ) );
		//add_filter( Karma_Events::$slug . '_rewrite_rules', array( __CLASS__, 'rewrite_event_url' ) );
		//add_filter( 'post_rewrite_rules', array( __CLASS__, 'rewrite_post_url' ) );

		self::$left_clamp = strtotime( date('Y/m/01') );
		self::$right_clamp = strtotime( date( 'Y/m/t', strtotime( '11 months', self::$left_clamp ) ) );
	}

	public static function remove_menu_items() {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=page' );
	}

	public static function rewrite_event_url( $rewrite ) {
		$new_rewrite = array();

		foreach ( $rewrite as $rule => $target ) {
			$new_rule = substr( $rule, 1 + strlen( Karma_Events::$slug ) );
			$new_rewrite[ $new_rule ] = $target;
		}

		//error_log( 'events ' . print_r( $new_rewrite, true ) );
		return $new_rewrite;
	}

	public static function rewrite_post_url( $rewrite ) {
		$new_rewrite = array();

		foreach ( $rewrite as $rule => $target ) {
			$new_rule = 'post/' . $rule;
			$new_rewrite[ $new_rule ] = $target;
		}

		//error_log( 'posts ' . print_r( $new_rewrite, true ) );
		return $new_rewrite;
	}

	public static function alter_query( $query, $left_clamp = null, $right_clamp = null, $limit = -1, $offset = 0 ) {
		if ( empty( $left_clamp  ) ) $left_clamp  = self::$left_clamp;
		if ( empty( $right_clamp ) ) $right_clamp = self::$right_clamp;

		self::$hero_slug = $query->get( 'event', null );
		if ( empty( self::$hero_slug ) ) self::$hero_slug = $query->get( 'name', null );
		if ( empty( self::$hero_slug ) ) self::$hero_slug = $query->get( 'attachment', null );

		/*?><pre style="margin-top: 100px;"><?php var_dump( $query->query_vars ); ?></pre><?php*/

		$query->set( 'post_type', self::$slug );
		$query->set( 'name', '' );
		$query->set( 'event', '' );
		$query->set( 'attachment', '' );

		$query->set( 'order', 'ASC' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', self::$startdate_key );
		$query->set( 'meta_type', 'DATE' );

		//$query->set( 'posts_per_page', $limit );
		//$query->set( 'offset', $offset );

		$dates = array( date( 'Y-m-d', self::$left_clamp ), date( 'Y-m-d', self::$right_clamp ) );

		$query->set( 'meta_query', array(
			'relation' => 'OR',
			array(
				'key' => self::$enddate_key,
				'value' => $dates,
				'compare' => 'BETWEEN',
				'type' => 'DATE',
			),
			array(
				'key' => self::$startdate_key,
				'value' => $dates,
				'compare' => 'BETWEEN',
				'type' => 'DATE',
			),
		) );
	}

	public static function register_admin_scripts_and_styles( $hook_suffix ) {
		if ( in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) ) {
			wp_register_script( 'events', get_template_directory_uri() . '/js/events-admin.js', array( 'jquery' ) );
			wp_enqueue_script( 'events' );
		}
	}

	public static function register_public_scripts_and_styles() {
		wp_register_script( 'masonry', "https://cdnjs.cloudflare.com/ajax/libs/masonry/3.3.2/masonry.pkgd.min.js" );
		wp_register_script( 'events', get_template_directory_uri() . "/js/events-display.js", array( 'jquery' ) );

		wp_localize_script( 'events', 'karma_events_meta', array(
			'hero' => self::$hero_slug,
			'index_url' => get_home_url(),
		) );

		wp_enqueue_script( 'masonry' );
		wp_enqueue_script( 'events' );
	}

	public static function add_meta_boxes() {
		add_meta_box(
			'meta', // ID
			"Meta Data", // Title
			array( __CLASS__, 'render_metadata_form' ), // Callback
			self::$slug, // Slug
			'side', // Context
			'core' // Priority
		);
	}

	public static function render_metadata_form( $post ) {
		wp_enqueue_script( 'karma-events-admin' );
		wp_nonce_field( self::$nonce_key.'_str', self::$nonce_key );

		if ( $post->post_status !== 'publish' ) {
			$subscription_count = Karma_Subscriber::get_subscriber_count();
			?>
			<small>
				<strong>Note</strong>: when this event is published, <?php echo $subscription_count == 1 ? "1 subscriber" : $subscription_count . " subscribers"; ?> will be notified via email.
			</small>
			<?php
		}
		?>
		<table class="karma-metadata">
			<tr class="karma-startdate">
				<th>
					<label for="karma_metadata_startdate">Start Date</label>
				</th>
				<td>
					<input id="karma_metadata_startdate" type="date" name="<?php echo self::$startdate_key; ?>" value="<?php echo date( 'Y-m-d', self::get_startdate() ); ?>"></input>
				</td>
			</tr>
			<tr class="karma-enddate">
				<th>
					<label for="karma_metadata_enddate">End Date</label>
				</th>
				<td>
					<input id="karma_metadata_enddate" type="date" name="<?php echo self::$enddate_key; ?>" value="<?php echo date( 'Y-m-d', self::get_enddate() ); ?>"></input>
				</td>
			</tr>
			<tr class="karma-contacts">
				<th>
					<label>Links</label>
				</th>
				<td class="karma-contacts-list">
					<?php
					$contacts = self::get_contacts( $post->ID );
					if ( ! empty( $contacts ) ) {
						foreach ( $contacts as $index => $contact ) {
							self::render_contact_block( $contact );
						}
					}

					self::render_contact_block();
					?>
				</td>
			</tr>
		</table>
		<style>
		.karma-metadata th,
		.karma-metadata td {
			display: block;
			text-align: left;
		}

		.karma-metadata input {
			width: 100%;
		}

		.karma-contact input {
			width: calc(50% - 4px);
		}
		</style>
		<?php
	}

	public static function render_contact_block( $data = array() ) {
		$data = shortcode_atts( array(
			'name' => "",
			'link' => "",
		), $data );

		?>
		<div class="karma-contact empty">
			<input type="text" class="karma-name" name="<?php echo self::$contacts_key; ?>_name[]" value="<?php echo $data['name']; ?>"></input>
			<input type="text" class="karma-link" name="<?php echo self::$contacts_key; ?>_link[]" value="<?php echo $data['link']; ?>"></input>
		</div>
		<?php
	}

	public static function get_startdate( $event_id = null, $clamp = false ) {
		if ( empty( $event_id ) ) {
			$event_id = get_the_ID();
		}

		$date = get_post_meta( $event_id, self::$startdate_key, true );

		if ( empty ( $date ) && isset( $_POST[ self::$startdate_key ] ) ) {
			$date = sanitize_text_field( $_POST[ self::$startdate_key ] );
		}

		$date = strtotime( $date );

		if ( $clamp && $date < self::$left_clamp ) {
			return self::$left_clamp;
		} else {
			return $date;
		}
	}

	public static function get_enddate( $event_id = null, $clamp = false ) {
		if ( empty( $event_id ) ) {
			$event_id = get_the_ID();
		}

		$date = get_post_meta( $event_id, self::$enddate_key, true );

		if ( empty ( $date ) && isset( $_POST[ self::$enddate_key ] ) ) {
			$date = sanitize_text_field( $_POST[ self::$enddate_key ] );
		}

		$date = strtotime( $date );

		if ( $clamp && $date > self::$right_clamp ) {
			return self::$right_clamp;
		} else {
			return $date;
		}
	}

	public static function get_contacts( $event_id = null ) {
		if ( empty( $event_id ) ) {
			$event_id = get_the_ID();
		}
		
		return get_post_meta( $event_id, self::$contacts_key, true );
	}

	public static function save_metadata( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST[ self::$nonce_key ] ) ) { return; }

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[ self::$nonce_key ], self::$nonce_key.'_str' ) ) { return; }

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

		$contacts = array();

		foreach ( $_POST[ self::$contacts_key . '_link' ] as $index => $link ) {
			if ( ! empty( $link ) ) {
				$contacts[] = array(
					'name' => sanitize_text_field( $_POST[ self::$contacts_key . '_name' ][ $index ] ),
					'link' => sanitize_text_field( $link ),
				);
			}
		}

		// Update the meta fields in the database.
		update_post_meta( $post_id, self::$contacts_key, $contacts );
		update_post_meta( $post_id, self::$startdate_key, sanitize_text_field( $_POST[ self::$startdate_key ] ) );
		update_post_meta( $post_id, self::$enddate_key, sanitize_text_field( $_POST[ self::$enddate_key ] ) );
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
}

Karma_Events::init();
