jQuery(function($) {

	// Add .dfrapi_hide_label class to .form-table > tr > th labels when specified in add_settings_field();
	dfrapi_hide_label = $( "label[for='DFRAPI_HIDE_LABEL']" ).closest( "th" );
	$(dfrapi_hide_label).addClass( "dfrapi_hide_label" );

});




       