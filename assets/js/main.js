(function($){
	$('.carlist-filter').on( 'change', function(){
		var parent = $(this).parents( '.carlist-container' ),
			data = { action : 'carlist_filter', nonce: carlist.nonce };

		parent.find( '.carlist-overlay' ).addClass('show');

		parent.find( '.carlist-filter' ).each(function(){
			data[ jQuery(this).data('key') ] = jQuery(this).val();
		});

		jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : carlist.ajaxurl,
			data : data,
			success: function(response) {
				if( response.success ) {
					console.log( response );
					parent.find( '.carlist-content' ).html( response.data );
				}

				parent.find( '.carlist-overlay' ).removeClass('show');

			}
		});

	})
})(jQuery)