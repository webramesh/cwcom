	jQuery('#filter').submit(function(){	    
		var filter = jQuery(this);
		var load_text = filter.attr('data-load'); 
		var submit_text = filter.find('button[type="submit"]').text();
         var tender_type = jQuery('#archive-container').attr('data-type');

		jQuery.ajax({
			url : loadmore_params.ajaxurl,
			data : filter.serialize()+'&tender_type='+tender_type+'&action=prodfilter', // form data
			dataType : 'json', // this data type allows us to receive objects from the server
			type:filter.attr('method'), 
			beforeSend : function(xhr){
				filter.find('button[type="submit"]').text(load_text);
			},
			success : function( data ){
 
				// when filter applied:
				// set the current page to 1
				loadmore_params.current_page = 1;
 
				// set the new query parameters
				loadmore_params.posts = data.posts;				
 
				// set the new max page parameter
				loadmore_params.max_page = data.max_page;
 
				// change the button label back
				filter.find('button[type="submit"]').text(submit_text);
 
				// insert the posts to the container
				jQuery('#archive-container').html(data.content);
				// insert count of fpund posts to the container
				jQuery('#found-posts span').html(data.found_posts);

				// hide load more button, if there are not enough posts for the second page
				if ( data.max_page < 2 ) { 
					jQuery('#kd_loadmore').hide();
				} else {
					jQuery('#kd_loadmore').show();
				}
			}
		});
 
		// do not submit the form
		return false;
		
		
		
		
	});
	
/*не работает обработка запроса по ресету*/
		jQuery('.filter.button-wrap button[type="reset"]').click(reset_func);
		function reset_func(e){ 
		var filter = jQuery('.filter.button-wrap button[type="reset"]').parent().parent();
		var load_text = filter.attr('data-load'); 
		var reset_text = filter.find('button[type="reset"]').text();
		 var tender_type = jQuery('#archive-container').attr('data-type');
		  jQuery('#filter')[0].reset();

		  jQuery('#filter').find('option').each(function() {
			  jQuery( this ).removeAttr( "selected" );
		  });

		jQuery.ajax({
			url : loadmore_params.ajaxurl,
			data : filter.serialize()+'&tender_type='+tender_type, // form data
			dataType : 'json', // this data type allows us to receive objects from the server
			type:filter.attr('method'), 
			beforeSend:function(xhr){
				filter.find('button[type="reset"]').text(load_text); 
				/*jQuery('#archive-container .clear-filter').text(load_text);*/
				// изменяем текст кнопки
			},
			success:function(data){
				// when filter applied:
				// set the current page to 1
				loadmore_params.current_page = 1;

				// set the new query parameters
				loadmore_params.posts = data.posts;

				// set the new max page parameter
				loadmore_params.max_page = data.max_page;
				filter.find('button[type="reset"]').text(reset_text); 
				jQuery('#archive-container .clear-filter').text(reset_text);
				jQuery('#archive-container').html(data.content);
                jQuery('#found-posts span').html(data.found_posts);
					// hide load more button, if there are not enough posts for the second page
				if ( data.max_page < 2 ) { 
					jQuery('#kd_loadmore').hide();
				} else {
					jQuery('#kd_loadmore').show();
				}
			},
			error:function(data){ 
				console.log('error');

			},
		});
		return false;
	}

//Ashish date filter call
jQuery( ".filter-head" ).click(function() {
	jQuery( ".form-wrap" ).toggle();});
$(document).ready(function() {
    $('#filter').submit(function(e) {
        e.preventDefault();
        var formData = {
            action: 'prodfilter',
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            // Include other form fields as necessary
        };

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            success: function(data) {
                // Handle the response
            }
        });
    });

    // Optionally ensure the "to_date" is always later than "from_date"
    $('#from_date').on('change', function() {
        $('#to_date').attr('min', $(this).val());
    });

    $('#to_date').on('change', function() {
        $('#from_date').attr('max', $(this).val());
    });
});