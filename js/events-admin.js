
jQuery(document).ready( function() {
	var container = jQuery( '.karma-contacts td' );
	var template = container.children('.karma-contact').last().clone();

	container.on( 'change', '.karma-name', function() {
		var element = jQuery(this);
		var parent = element.closest( '.karma-contact' );

		console.log('change', element.val(), parent );

		if ( element.val() === '' ) {
			parent.remove();
		} else if ( parent.hasClass('empty') ) {
			container.append( template.clone() );
			parent.removeClass('empty');
		}
	} );
} );
