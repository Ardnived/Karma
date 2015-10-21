
// 'karma_ajax_url' and 'karma_action_nonce' are defined using wp_localize_script.

jQuery(document).ready( function() {
	jQuery('#user_email').on( 'click', function( event ) {
		this.setSelectionRange( 0, this.value.length );
	} );

	jQuery('#user_email').on( 'change', function( event ) {
		var element = jQuery(this);
		element.css( 'outline', '1px solid gold' );

		jQuery.post( karma_ajax_url, {
			action: 'karma_update_email',
			security: karma_action_nonce,
			email: element.val(),
		}, function( response ) {
			console.log('result', response);
			if ( response == 'success' ) {
				element.css( 'outline', '1px solid green' );
			} else {
				element.css( 'outline', '1px solid red' );
			}
		}, 'text' );
	} );

	jQuery('#user_email_subscription').on( 'change', function( event ) {
		var element = jQuery(this);
		element.css( 'outline', '1px solid gold' );

		jQuery.post( karma_ajax_url, {
			action: 'karma_event_subscribe',
			security: karma_action_nonce,
			event_id: -1,
			subscribe: this.checked ? 1 : 0,
		}, function( response ) {
			console.log('result', response);
			if ( response == 'success' ) {
				element.css( 'outline', '1px solid green' );
			} else {
				element.css( 'outline', '1px solid red' );
			}
		}, 'text' );
	} );

	jQuery('#main').on( 'click', '.subscribe-action', function( event ) {
		var element = jQuery(this);
		element.css( 'outline', '1px solid gold' );

		var should_be_subscribed;

		if ( element.text() === 'unsubscribe' || element.text() === 'subscribed' ) {
			should_be_subscribed = false;
			element.text( 'unsubscribing...' );
		} else {
			should_be_subscribed = true;
			element.text( 'subscribing...' );
		}

		jQuery.post( karma_ajax_url, {
			action: 'karma_event_subscribe',
			security: karma_action_nonce,
			event_id: element.data('id'),
			subscribe: should_be_subscribed ? 1 : 0,
		}, function( response ) {
			console.log('result', response);
			if ( response == 'success' ) {
				element.css( 'outline', '1px solid green' );

				if ( should_be_subscribed ) {
					element.text( 'subscribed' );
				} else {
					element.text( 'unsubscribed' );
				}
			} else {
				element.css( 'outline', '1px solid red' );
				element.text( 'failure..' );
			}
		}, 'text' );

		event.preventDefault();
	} );
} );