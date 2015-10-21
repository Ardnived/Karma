<?php

class Karma_Emails {

	public static $subscription_id = null;

	public static function init() {
		add_action( 'publish_' . Karma_Events::$slug, array( __CLASS__, 'events_published_notification' ), 10, 2 );
		add_action( 'publish_' . Karma_News::$slug, array( __CLASS__, 'news_published_notification' ), 10, 2 );

		add_filter( 'wp_mail_content_type', array( __CLASS__, 'set_email_content_type' ) );
		add_filter( 'wp_mail_from_name', array( __CLASS__, 'get_mail_from_name' ) );
		add_filter( 'wp_mail_from', array( __CLASS__, 'get_mail_from' ) );
		add_filter( 'wp_mail', array( __CLASS__, 'adjust_mail_headers' ) );

		add_filter( 'wp_trim_excerpt', array( __CLASS__, 'get_excerpt' ), 10, 2 );
	}

	public static function get_mail_from_name( $name ) {
		return "Sahaja Yoga Events";
	}

	public static function get_mail_from( $email ) {
		return 'noreply@' . $_SERVER['SERVER_NAME'];
	}

	public static function adjust_mail_headers( $data ) {
		if ( ! empty( self::$subscription_id ) ) {
			$recipients = Karma_Subscriber::get_subscribers( self::$subscription_id );
			$data['headers'] .= "BCC: " . implode( ",", $recipients ) . '\r\n';
		}

		return $data;
	}

	public static function get_excerpt( $text, $raw_excerpt ) {
		if ( ! $raw_excerpt ) {
			$content = apply_filters( 'the_content', get_the_content() );
			$text = substr( $content, 0, strpos( $content, '</p>' ) + 4 );
		}

		return $text;
	}

	public static function set_email_content_type() {
		return "text/html";
	}

	public static function get_event_splash( $event ) {
		global $post;
		$post = $event;
		setup_postdata( $post );

		ob_start();
		get_template_part( 'template-parts/content-event-email' );

		wp_reset_postdata();
		return ob_get_clean();
	}

	public static function get_news_splash( $news ) {
		global $post;
		$post = $news;
		setup_postdata( $post );

		ob_start();
		//get_template_part( 'template-parts/content-event-email' );

		wp_reset_postdata();
		return ob_get_clean();
	}

	public static function events_published_notification( $ID, $post ) {
		error_log( "ATTEMPT EMAIL, " . $_POST['post_status'] . ", from " . $_POST['original_post_status'] . ", " . $post->post_type );
		if ( $_POST['post_status'] !== 'publish' || $_POST['original_post_status'] === 'publish' ) return;
		self::$subscription_id = -1;

		$subject = "[SYE] " . $post->post_title;
		$message = self::get_event_splash( $post );
		$success = wp_mail( "", $subject, $message );

		if ( $success ) {
			error_log("Sent email.");
		} else {
			error_log("Failed to send emails.");
		}
	}

	public static function news_published_notification( $ID, $post, $old_post ) {
		error_log( "ATTEMPT EMAIL, " . $_POST['post_status'] . ", from " . $_POST['original_post_status'] . ", " . $post->post_type );
		if ( $_POST['post_status'] !== 'publish' || $_POST['original_post_status'] === 'publish' ) return;
		self::$subscription_id = get_post_meta( $ID, Karma_News::$parent_key, true );

		$subject = "[SYE] " . $post->post_title;
		$message = self::get_news_splash( $post );
		$success = wp_mail( "", $subject, $message );

		if ( $success ) {
			error_log("Sent email.");
		} else {
			error_log("Failed to send emails.");
		}
	}

}

Karma_Emails::init();
