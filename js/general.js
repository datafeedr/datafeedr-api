jQuery(function($) {

	// Add .dfrapi_hide_label class to .form-table > tr > th labels when specified in add_settings_field();
	dfrapi_hide_label = $( "label[for='DFRAPI_HIDE_LABEL']" ).closest( "th" );
	$(dfrapi_hide_label).addClass( "dfrapi_hide_label" );

	// Toggle Amazon API settings
	function dfrapi_toggle_amazon_api_settings() {
		var amazon_api = $( 'input[name="dfrapi_configuration[amazon_api]"]:checked' ).val();
		if ( amazon_api == 'paapi' ) {
			$( '.paapi_settings' ).show();
			$( '.capi_settings' ).hide();
		} else {
			$( '.paapi_settings' ).hide();
			$( '.capi_settings' ).show();
		}
	}

	$( 'input[name="dfrapi_configuration[amazon_api]"]' ).on( 'change', function() {
		dfrapi_toggle_amazon_api_settings();
	} );

	dfrapi_toggle_amazon_api_settings();

});




       