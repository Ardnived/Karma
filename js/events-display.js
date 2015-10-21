// 'karma_events_meta' is defined using wp_localize_script

jQuery('body').removeClass('no-js').addClass('js');

jQuery(window).load( function() {
	var hero = null;
	var main = jQuery('#main');
	var page = jQuery('#page');
	var timeline = jQuery('#timeline');

	var hero = jQuery('.hero');
	hero.addClass('no-transitions')
	hero.removeClass('hero');

	var grid = main.masonry({
		itemSelector: "article",
		columWidth: 200,
	});

	hero.addClass('hero');
	hero.removeClass('no-transitions')

	function close_event( push_history ) {
		if ( hero !== null ) {
			hero.removeClass('hero');
			hero = null;

			if ( push_history ) {
				history.pushState( {
					selector: 'body',
				}, "", karma_events_meta.index_url ); // TODO: make this url more reliable.
			}
		}
	}

	function open_event( article, push_history ) {
		if ( article == null ) return;
		if ( article == hero ) return;
		close_event();

		window.scrollTo( 0, 0 );
		
		hero = article;
		hero.addClass('hero');

		if ( push_history ) {
			history.pushState( {
				selector: '#'+this.id,
			}, article.find('.title').text(), article.data('permalink') );
		}
	}

	page.on( 'click', '.event', function( event ) {
		var element = jQuery(this);

		if ( ! element.hasClass('hero') ) {
			open_event( element, true );
		}

		event.stopPropagation();
	} );

	jQuery('html').click( function( event ) {
		console.log(event);
		if ( event.toElement.nodeName !== 'INPUT' && event.toElement.nodeName !== 'A' ) {
			close_event( true );
		}
	} );

	page.on( 'click', '.close-action', function( event ) {
		close_event( true );
		event.preventDefault();
		event.stopPropagation();
	} );

	page.on( 'click', '.open-action', function( event ) {
		var article = jQuery( '.event-'+this.dataset.id ).first();
		open_event( article, true );
		event.preventDefault();
		event.stopPropagation();
	} );

	window.onpopstate = function( event ) {
		var element = jQuery(event.state.selector);

		if ( hero !== null ) {
			hero.removeClass('hero');
		}
		
		hero = element;
		hero.addClass('hero');
	};

	//open_event( jQuery('#'+karma_events_meta.hero) );

	main.removeClass('preload');
} );
