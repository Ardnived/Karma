
jQuery(window).load( function() {
	var hero = null;
	var main = jQuery('#main');
	var timeline = jQuery('#timeline');
	var grid = main.masonry({
		itemSelector: "article",
		columWidth: 200,
	});

	main.removeClass('preload');

	timeline.on( 'click', '.strip', function( event ) {
		var anchor = jQuery(this).data('anchor');
		var target = jQuery(anchor);

		if ( ! target.hasClass('hero') ) {
			//target.click();
			target.click();
			event.stopPropagation();
		}
	} );

	main.on( 'click', 'article', function( event ) {
		var element = jQuery(this);

		if ( hero == null ) {
			hero = element;
			hero.addClass('hero');

			// TODO: Sort out some kind of scrolling into view.

			event.stopPropagation();
		} else if ( element.hasClass('hero') ) {
			event.stopPropagation();
		}
	} );

	jQuery('body').click( function() {
		if ( hero != null ) {
			hero.removeClass('hero');
			hero = null;
		}
	} );
} );
