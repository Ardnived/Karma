<?php

class Karma_Subscriber extends WP_Widget {
	public static $table = 'karma_subscriptions';
	public static $cookie = 'karma_email';
	public static $nonce = 'karma_action_nonce';
	
	public static function init() {
		global $wpdb;

		self::$table = $wpdb->prefix . self::$table;
		self::$cookie = $_SERVER['SERVER_NAME'] . '_' . self::$cookie;
		self::refresh_cookie(); // Reset the cookie's expiry date.

		add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );

		add_action( 'wp_ajax_karma_update_email', array( __CLASS__, 'change_email' ) );
		add_action( 'wp_ajax_nopriv_karma_update_email', array( __CLASS__, 'change_email' ) );

		add_action( 'wp_ajax_karma_event_subscribe', array( __CLASS__, 'set_event_subscription' ) );
		add_action( 'wp_ajax_nopriv_karma_event_subscribe', array( __CLASS__, 'set_event_subscription' ) );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_public_scripts_and_styles' ) );
		add_action( "after_switch_theme", array( __CLASS__, 'create_table' ) );
	}

	public static function register_widget() {
		register_widget( __CLASS__ );
	}

	public static function register_public_scripts_and_styles() {
		wp_register_script( 'subscriber', get_template_directory_uri() . "/js/subscriber.js", array( 'jquery' ) );
		
		wp_localize_script( 'subscriber', 'karma_action_nonce', wp_create_nonce( self::$nonce ) );
		wp_localize_script( 'subscriber', 'karma_ajax_url', admin_url( 'admin-ajax.php' ) );

		wp_enqueue_script( 'subscriber' );
	}

	public static function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE " . self::$table . " (
			email varchar(50) NOT NULL,
			event_id mediumint(9) NOT NULL,
			CONSTRAINT uc_Entry UNIQUE (email,event_id)
		) $charset_collate;";

		dbDelta( $sql );
	}

	public static function change_email() {
		check_ajax_referer( self::$nonce, 'security' );
		$email = sanitize_email( $_POST['email'] );

		if ( is_user_logged_in() ) {
			wp_update_user( array(
				'ID' => get_current_user_id(),
				'user_email' => $email,
			) );
		} else {
			self::refresh_cookie( $email );
		}

		echo 'success';
		wp_die();
	}

	public static function get_email() {
		if ( is_user_logged_in() ) {
			return wp_get_current_user()->user_email;
		} else if ( isset( $_COOKIE[ self::$cookie ] ) ) {
			return $_COOKIE[ self::$cookie ];
		} else {
			return null;
		}
	}

	public static function set_event_subscription( $event_id = null ) {
		if ( empty( $event_id ) ) $event_id = sanitize_text_field( $_POST['event_id'] );
		check_ajax_referer( self::$nonce, 'security' );

		global $wpdb;

		$email = self::get_email();

		if ( empty( $email ) ) {
			echo 'User does not have a valid email: ';
			var_dump( $email );
		}

		$data = array(
			'email' => $email,
			'event_id' => $event_id,
		);

		$format = array( '%s', '%d' );

		if ( $_POST['subscribe'] == true ) {
			$result = $wpdb->insert( self::$table, $data, $format );
		} else if ( $_POST['subscribe'] == false ) {
			$result = $wpdb->delete( self::$table, $data, $format );
		}

		if ( $result === false ) {
			$wpdb->print_error();
		} else {
			echo 'success';
		}

		wp_die();
	}

	public static function is_subscribed( $event_id = -1, $email = null ) {
		if ( empty( $email ) ) $email = self::get_email();
		global $wpdb;

		$query = "SELECT COUNT(*) FROM ".self::$table." WHERE email = %s AND event_id = %d";
		$query = $wpdb->prepare( $query, $email, $event_id );
		$results = $wpdb->get_var( $query );
		return $results;

		return $wpdb->get_var( $query ) > 0;
	}

	public static function get_subscriber_count( $event_id = -1 ) {
		global $wpdb;

		$query = "SELECT COUNT(*) FROM ".self::$table." WHERE event_id = %d";
		$query = $wpdb->prepare( $query, $event_id );
		return $wpdb->get_var( $query );
	}

	public static function get_subscribers( $event_id = -1 ) {
		global $wpdb;

		$query = "SELECT email FROM ".self::$table." WHERE event_id = %d";
		$query = $wpdb->prepare( $query, $event_id );
		return $wpdb->get_col( $query );
	}

	public static function get_subscriptions( $email = null ) {
		if ( empty( $email ) ) $email = self::get_email();
		global $wpdb;

		$query = "SELECT event_id FROM ".self::$table." WHERE email = %s ORDER BY event_id";
		$query = $wpdb->prepare( $query, $email );
		$results = $wpdb->get_col( $query );

		for ( $i = 0; $i < 2; $i++ ) { 
			if ( isset( $results[ $i ] ) && $results[ $i ] < 1 ) {
				unset( $results[ $i ] );
			}
		}

		return $results;
	}

	public static function refresh_cookie( $email = null ) {
		$email = empty( $email ) && isset( $_COOKIE[ self::$cookie ] ) ? $_COOKIE[ self::$cookie ] : $email;
		$domain = $_SERVER['SERVER_NAME'];

		if ( ! empty( $email ) ) {
			setcookie( self::$cookie, $email, time()+60*60*24*90, '/', $domain, false, false );
		}
	}


	// WIDGET FUNCTIONS

	function __construct() {
		parent::__construct(
			// base ID of the widget
			'karma_subscriber_widget',
			// name of the widget
			__('Karma Subscriber', 'karma' ),
			// widget options
			array (
				'description' => __( 'Allows users to subscribe to an event.', 'karma' )
			)
		);
	}

	function resolve_form() {
		if ( isset( $_REQUEST['email'] ) ) {
			
		}
	}

	function widget( $args, $instance ) {
		$user = wp_get_current_user();
		$email = empty( $user ) ? $_COOKIE[ self::$cookie ] : $user->user_email;
		$subscriptions = self::get_subscriptions();

		?>
		<form type="POST">
			<input id="user_email" name="email" type="text" value="<?php echo $email; ?>" placeholder="Enter your email"></input>
			<div>
				<small>
					<label>
						<input id="user_email_subscription" name="subscribe" type="checkbox" <?php checked( self::is_subscribed() ); ?> value="1"></input>
						<span>Send me emails about new events.</span>
					</label>
				</small>
			</div>
			<input type="submit" class="button" value="Save"></input>
		</form>
		<?php
		if ( ! empty( $subscriptions ) ) {
			?>
			<h4>Event Subscriptions</h4>
			<ul>
				<?php
				foreach ( $subscriptions as $index => $event_id ) {
					if ( $event_id > 0 ) {
						?>
						<li>
							<a class="open-action" href="<?php echo get_the_permalink( $event_id ); ?>" data-id="<?php echo $event_id; ?>">
								<?php echo get_the_title( $event_id ); ?>
							</a>
						</li>
						<?php
					}
				}
				?>
			</ul>
			<small>You will receive an email if news is<br>posted for the above events.</small>
			<?php
		}

		?>
		<h4>Actions</h4>
		<ul>
			<?php
			if ( is_user_logged_in() ) {
				if ( current_user_can( 'edit_posts' ) && is_single() ) {
					?>
					<li><?php echo edit_post_link( __('Edit this event') ); ?></li>
					<li><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/post-new.php?post_type=<?php echo Karma_Events::$slug; ?>" title="Contribute">Create a new event</a></li>
					<?php
				}
				?>
				<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a></li>
				<?php
			} else {
				?>
				<li><a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Login</a></li>
				<li><a href="<?php echo wp_registration_url(); ?>">Register</a></li>
				<li><small>You don't need to register or login to use this site.</small></li>
				<?php
			}
		?>
		</ul>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		// No options yet.
	}

	function form( $instance ) {
		// No options yet.
	}
}

Karma_Subscriber::init();
