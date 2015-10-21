<?php

class Karma_News {
	public static $slug = 'news';
	public static $parent_key = '_karma_event';
	public static $nonce_key = 'karma_news_metadata_nonce';

	public static function init() {
		register_post_type( self::$slug, array(
			'label' => "News",
			'labels' => array(
				'name' => "News Posts",
				'singular_name' => "News",
			),
			'public' => true,
			'menu_icon' => 'dashicons-admin-post',
			'supports' => array( 'title', 'editor' ),
			'register_meta_box_cb' => array( __CLASS__, 'add_meta_boxes' ),
			'rewrite' => array(
				'slug' => "news",
				'with_front' => false,
			),
		) );

		add_action( 'save_post', array( __CLASS__, 'save_metadata' ) );
	}

	public static function add_meta_boxes() {
		add_meta_box(
			'meta', // ID
			"Parent Event", // Title
			array( __CLASS__, 'render_metadata_form' ), // Callback
			self::$slug, // Slug
			'side', // Context
			'core' // Priority
		);
	}

	public static function render_metadata_form( $post ) {
		wp_enqueue_script( 'karma-events-admin' );
		wp_nonce_field( self::$nonce_key.'_str', self::$nonce_key );

		?>
		<select id="karma_metadata_parent" name="<?php echo self::$parent_key; ?>" required="required">
			<option value=""> - Choose an event - </option>
			<?php
			$events = get_posts( array(
				'posts_per_page' => -1,
				'post_type' => Karma_Events::$slug,
			) );

			$parent_id = get_post_meta( $post->ID, self::$parent_key, true );

			foreach ( $events as $index => $event ) {
				?>
				<option <?php selected( $parent_id, $event->ID ); ?> value="<?php echo $event->ID; ?>">
					<?php echo $event->post_title; ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
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

		// Update the meta fields in the database.
		update_post_meta( $post_id, self::$parent_key, sanitize_text_field( $_POST[ self::$parent_key ] ) );
	}
}

Karma_News::init();
