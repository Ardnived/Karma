<?php

class Karma_Events_Admin {
	private static $slug = 'events';
	private static $metakey = '_karma_metadata';

	public static function init() {
		error_log('test0');
		register_post_type( self::$slug, array(
			'label' => "Events",
			'labels' => array(
				'name' => "Events",
				'singular_name' => "Event",
			),
			'public' => true,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'register_meta_box_cb' => array( __CLASS__, 'add_meta_boxes' ),
		) );

		add_action( 'save_post', array( __CLASS__, 'save_metadata' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_scripts_and_styles' ) );
	}

	public static function register_scripts_and_styles() {
		wp_register_script( 'karma-events-admin', get_template_directory_uri() . '/js/events-admin.js', array( 'jquery' ) );
	}

	public static function add_meta_boxes() {
		add_meta_box(
			'meta', // ID
			"Meta Data", // Title
			array( __CLASS__, 'render_metadata_form' ), // Callback
			self::$slug, // Slug
			'normal', // Context
			'core' // Priority
		);
	}

	public static function render_metadata_form( $post ) {
		wp_enqueue_script( 'karma-events-admin' );
		wp_nonce_field( 'karma_save_metadata', 'karma_metadata_nonce' );

		$metadata = get_post_meta( $post->ID, self::$metakey, true );

		$metadata = shortcode_atts( array(
			'startdate' => '',
			'enddate'   => '',
			'contacts'  => array(),
		), $metadata );

		var_dump( $metadata );
		?>
		<table>
			<tr class="karma-startdate">
				<th>
					<label for="karma_metadata_startdate">Start Date</label>
				</td>
				<td>
					<input id="karma_metadata_startdate" type="date" name="karma_metadata[startdate]" value="<?php echo $metadata['startdate']; ?>"></input>
				</td>
			</tr>
			<tr class="karma-enddate">
				<th>
					<label for="karma_metadata_enddate">End Date</label>
				</td>
				<td>
					<input id="karma_metadata_enddate" type="date" name="karma_metadata[enddate]" value="<?php echo $metadata['enddate']; ?>"></input>
				</td>
			</tr>
			<tr class="karma-contacts">
				<th>
					<label>Links</label>
				</td>
				<td>
					<?php
					if ( ! empty( $metadata['contacts'] ) ) {
						foreach ( $metadata['contacts'] as $index => $contact ) {
							self::render_contact_block( $contact );
						}
					}

					self::render_contact_block();
					?>
				</td>
			</tr>
		</table>
		<?php
	}

	public static function render_contact_block( $data = array() ) {
		$data = shortcode_atts( array(
			'name' => "",
			'link' => "",
		), $data );

		?>
		<div class="karma-contact empty">
			<input type="text" class="karma-name" name="karma_contact_name[]" value="<?php echo $data['name']; ?>"></input>
			<input type="text" class="karma-link" name="karma_contact_link[]" value="<?php echo $data['link']; ?>"></input>
		</div>
		<?php
	}

	public static function save_metadata( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['karma_metadata_nonce'] ) ) { return; }

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['karma_metadata_nonce'], 'karma_save_metadata' ) ) { return; }

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

		// Make sure that it is set.
		if ( ! isset( $_POST['karma_metadata'] ) ) { return; }

		$data = $_POST['karma_metadata'];
		$data['contacts'] = array();

		foreach ( $_POST['karma_contact_link'] as $index => $link ) {
			if ( ! empty( $link ) ) {
				$data['contacts'][] = array(
					'name' => $_POST['karma_contact_name'][ $index ],
					'link' => $link,
				);
			}
		}

		// Update the meta field in the database.
		error_log( print_r( $data, true ) );
		update_post_meta( $post_id, self::$metakey, $data );
	}
}

Karma_Events_Admin::init();
