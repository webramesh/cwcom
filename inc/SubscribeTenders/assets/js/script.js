jQuery( function( $ ) {
jQuery('.treaking-tender-form input[name="treaking_tender_status"]:checked').closest('.favorite_tender').addClass('treaking-active');

jQuery("body").on("change", '.treaking-tender-form', function(e){

	if (e.target.checked) { 
  	jQuery(this).closest('.favorite_tender').addClass('treaking-active');
  } else { 
  	jQuery(this).closest('.favorite_tender').removeClass('treaking-active');
  } 
}) 


});