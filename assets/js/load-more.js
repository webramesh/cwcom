
	/* ---------------------------------------------------------------------------
		 * Ajax | Load More
		 * --------------------------------------------------------------------------- */


	jQuery('#kd_loadmore').click(load_more);
	function load_more(){
		var text_loadmore = jQuery('#kd_loadmore').text();
		jQuery.ajax({
			url : loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'loadmorebutton', // the parameter for admin-ajax.php
				'query': loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : loadmore_params.current_page // current page
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				jQuery('#kd_loadmore').text('Loading...'); // some type of preloader
			},
			success : function( posts ){
				if( posts ) {

					jQuery('#kd_loadmore').text( text_loadmore );
					jQuery('#archive-container').append( posts ); // insert new posts
					loadmore_params.current_page++;

					if ( loadmore_params.current_page == loadmore_params.max_page )
						jQuery('#kd_loadmore').hide(); // if last page, HIDE the button

				} else {
					jQuery('#kd_loadmore').hide(); // if no data, HIDE the button as well
				}
			}
		});
		return false;
	}



	function getOffset(el) {
		const rect = el.getBoundingClientRect();
		return  rect.top+ window.scrollY;
	}
	document.addEventListener('DOMContentLoaded', function(){
		var el =  document.getElementById('kd_loadmore');
		var h ; var k=1;
		window.onscroll = function (event) {
			h = getOffset(el); p = loadmore_params.current_page;
			
			if ((h - window.scrollY) < screen.height && (h - window.scrollY) > (screen.height-100) && k == p) {
				load_more();
				console.log('load.....');
				k++;
			}
		};
	});
