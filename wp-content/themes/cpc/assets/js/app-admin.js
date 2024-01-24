jQuery(document).ready(function($) {

	if ( $('#page_template').length > 0 ) {

		showPageMetaBoxes( $('#page_template').val() );

		$('#page_template').change( function() {

			showPageMetaBoxes( $(this).val() );

		});

	}

});

function showPageMetaBoxes( template ) {

	jQuery('#postdivrich').css({
		'position':'absolute',
		'top' : '-9999px'
	});

}