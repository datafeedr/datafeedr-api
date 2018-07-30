<?php

/**
 * Load AJAX related functions.
 */
require_once( DFRAPI_PATH . 'functions/ajax.php' );

/**
 * These are the Admin pages associated with the Datafeedr API plugin.
 * 
 * These are called from their respecitive classes.
 */
function dfrapi_setting_pages( $key=false ) {
	
	$pages = array ( 
		'dfrapi-configuration' => __( 'Configuration', DFRAPI_DOMAIN ),
		'dfrapi-networks' => __( 'Networks', DFRAPI_DOMAIN ),
		'dfrapi-merchants' => __( 'Merchants', DFRAPI_DOMAIN ),
		'dfrapi-tools' => __( 'Tools', DFRAPI_DOMAIN ),
		'dfrapi-export' => __( 'Export', DFRAPI_DOMAIN ),
		'dfrapi-import' => __( 'Import', DFRAPI_DOMAIN ),
		'dfrapi-account' => __( 'Account', DFRAPI_DOMAIN ),
	);
	
	if ( isset( $pages[$key] ) ) {
		return $pages[$key];
	}
	
}

/**
 * This stores messages to be displayed using the 'admin_notices' action.
 */
function dfrapi_admin_messages( $key = false, $msg = '' ) {
	
	$messages = array(
		
		// User is missing 1 or more of their API Keys.
		'missing_api_keys' => array(
			'class' 		=> 'notice notice-warning',
			'message' 		=> __( 'Your Datafeedr API Keys are missing. ', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi',
			'button_text'	=> __( 'Add API Keys', DFRAPI_DOMAIN )
		),
		
		// User is missing a network
		'missing_network_ids' => array(
			'class' 		=> 'notice notice-warning',
			'message' 		=> __( 'You haven\'t selected any affiliate networks yet.', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_networks',
			'button_text'	=> __( 'Select Networks', DFRAPI_DOMAIN )
		),
		
		// User is missing a merchant
		'missing_merchant_ids' => array(
			'class' 		=> 'notice notice-warning',
			'message' 		=> __( 'You haven\'t selected any merchants yet.', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_merchants',
			'button_text'	=> __( 'Select Merchants', DFRAPI_DOMAIN )
		),
		
		// Display message that user has used 90%+ of API requests.
		'usage_over_90_percent' => array(
			'class' 		=> 'notice notice-warning',
			'message' 		=> __( 'You have used ', DFRAPI_DOMAIN ) . dfrapi_get_api_usage_percentage() . __( '% of your total Datafeedr API requests this month. ', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_account',
			'button_text'	=> __( 'View Account', DFRAPI_DOMAIN )
		),
		
		// Missing affiliate IDs message.
		'missing_affiliate_ids' => array(
			'class' 		=> 'notice notice-warning',
			'message' 		=> __( 'You are missing affiliate IDs. ', DFRAPI_DOMAIN ),
			'url'			=> 'admin.php?page=dfrapi_networks',
			'button_text'	=> __( 'Enter your Affiliate IDs', DFRAPI_DOMAIN )
		),
				
		// Unapproved Zanox merchant(s) message.
		'unapproved_zanox_merchants' => array(
			'class' 		=> 'notice notice-error',
			'message' 		=> '<strong>' . __( 'Unapproved Zanox Merchant(s):', DFRAPI_DOMAIN ) . '</strong> ' .
								__( 'You have selected one or more Zanox merchants who have not approved your Adspace ID(s):<br />', DFRAPI_DOMAIN ) .
			                    $msg . '<br /><br />' .
								__( 'Please remove unapproved Zanox merchants from your ', DFRAPI_DOMAIN ) .
								'<a href="' . admin_url( 'admin.php?page=dfrapi_merchants' ) . '" target="_blank">' .
								__( 'Zanox merchant selection', DFRAPI_DOMAIN ) .
								'</a>' .
								__( ' then delete your cached API data ', DFRAPI_DOMAIN ) .
								'<a href="' . admin_url( 'admin.php?page=dfrapi_tools' ) . '" target="_blank">' .
								__( 'here', DFRAPI_DOMAIN ) .
								'</a>.',
			'url'			=> '',
			'button_text'	=> __( '', DFRAPI_DOMAIN )
		),
		
		// Unapproved Partnerize merchant(s) message.
		'unapproved_ph_merchants' => array(
			'class'       => 'notice notice-error',
			'message'     => '<strong>' . __( 'Unapproved Partnerize Merchant(s):', DFRAPI_DOMAIN ) . '</strong> ' .
			                 __( 'You have selected one or more Partnerize merchants who have not approved your publisher account:<br />',
				                 DFRAPI_DOMAIN ) .
			                 $msg . '<br /><br />' .
			                 __( 'Please remove unapproved Partnerize merchants from your ', DFRAPI_DOMAIN ) .
			                 '<a href="' . admin_url( 'admin.php?page=dfrapi_merchants' ) . '" target="_blank">' .
			                 __( 'Partnerize merchant selection', DFRAPI_DOMAIN ) .
			                 '</a>' .
			                 __( ' then delete your cached API data ', DFRAPI_DOMAIN ) .
			                 '<a href="' . admin_url( 'admin.php?page=dfrapi_tools' ) . '" target="_blank">' .
			                 __( 'here', DFRAPI_DOMAIN ) .
			                 '</a>.',
			'url'         => '',
			'button_text' => __( '', DFRAPI_DOMAIN )
		),

		// Unapproved Effiliation merchant(s) message.
		'unapproved_effiliation_merchants' => array(
			'class'       => 'notice notice-error',
			'message'     => '<strong>' . __( 'Unapproved Effiliation Merchant(s):', DFRAPI_DOMAIN ) . '</strong> ' .
			                 __( 'You have selected one or more Effiliation merchants who have not approved your publisher account:<br />',
				                 DFRAPI_DOMAIN ) .
			                 $msg . '<br /><br />' .
			                 __( 'Please remove unapproved Effiliation merchants from your ', DFRAPI_DOMAIN ) .
			                 '<a href="' . admin_url( 'admin.php?page=dfrapi_merchants' ) . '" target="_blank">' .
			                 __( 'Effiliation merchant selection', DFRAPI_DOMAIN ) .
			                 '</a>' .
			                 __( ' then delete your cached API data ', DFRAPI_DOMAIN ) .
			                 '<a href="' . admin_url( 'admin.php?page=dfrapi_tools' ) . '" target="_blank">' .
			                 __( 'here', DFRAPI_DOMAIN ) .
			                 '</a>.',
			'url'         => '',
			'button_text' => __( '', DFRAPI_DOMAIN )
		),
		
	);
	
	if ( isset( $messages[$key] ) ) {
		dfrapi_admin_notices( $key, $messages );
	}	

}

/**
 * This gets any notices set by the plugin.
 */
function dfrapi_admin_notices( $key, $messages ) {
	$notices = get_option( 'dfrapi_admin_notices', array() );
	$notices[$key] = $messages[$key];
	update_option( 'dfrapi_admin_notices', $notices );
}

/**
 * Button text for admin notices.
 */
function dfrapi_fix_button( $url, $button_text=false ) {
	if ( !$button_text ) {
		$button_text = __( 'Fix This Now', DFRAPI_DOMAIN );
	}
	if ( substr( $url, 0, 4 ) === "http" ) {
		return ' <a target="blank" href="' . $url . '">' .$button_text . '</a>';
	} else {
		return ' <a href="' . admin_url( $url ) . '">' .$button_text . '</a>';
	}
}

/**
 * Return plan IDs & names.
 */
function dfrapi_get_membership_plans() {
	return array(
		10300    => 'Private',
		10200    => 'Free',
		101000   => 'Starter',
		1025000  => 'Basic',
		1125000  => 'Beta Tester',
		10100000 => 'Professional',
		10250000 => 'Enterprise',
		30001    => 'Combo 1',
		30002    => 'Combo 2',
		30003    => 'Combo 3',
	);
}

/**
 * Convert network group names to a css class name.
 */
function dfrapi_group_name_to_css( $group ) {
	if ( is_string( $group ) ) {
		return strtolower( 
			str_replace( 
				array( " ", "-", "." ), 
				"", 
				$group 
			) 
		);
	} elseif ( is_array( $group ) ) {
		$name = str_replace( array( " ", "-", "." ), "", $group['group'] );
		$type = ( $group['type'] == 'coupons' ) ? '_coupons' : '';
		return strtolower( $name . $type );	
	}
}

/**
 * Adds global "support" content to "Help" tabs for all Datafeedr plugins.
 */
function dfrapi_help_tab( $screen ) {

	$screen->add_help_tab( array(
		'id'	=> 'dfrapi_support_tab',
		'title'	=> __( 'Support', DFRAPI_DOMAIN ),
		'content'	=>
			'<h2>' . __( "Datafeedr Support", DFRAPI_DOMAIN ) . '</h2>' . 
			'<p>' . sprintf(__( 'Find answers to common questions and problems in the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">documentation</a> and in the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">support forum</a>', DFRAPI_DOMAIN ), DFRAPI_DOCS_URL, DFRAPI_QNA_URL ) . '. ' . __( 'For additional help, feel free to contact us using the links below.', DFRAPI_DOMAIN ) . '</p>' .
			'<p><a href="' . DFRAPI_ASK_QUESTION_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button button-primary" target="_blank">' . __( 'Post a Question', DFRAPI_DOMAIN ) . '</a> (' . __( 'recommended', DFRAPI_DOMAIN ) . ')</p>' . 
			'<p><a href="' . DFRAPI_EMAIL_US_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button" target="_blank">' . __( 'Email Us', DFRAPI_DOMAIN ) . '</a></p>'

	) );

	$screen->add_help_tab( array(
		'id'	=> 'dfrapi_bug_tab',
		'title'	=> __( 'Found a bug?', DFRAPI_DOMAIN ),
		'content'	=>
			'<h2>' . __( "Found a bug?", DFRAPI_DOMAIN ) . '</h2>' . 
			'<p>' . sprintf( __( 'If you find a bug within Datafeedr, check the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">Bug Reports</a> to see if it’s already been reported. Report a new bug with as much description as possible (context, screenshots, error log, etc.) Thank you!', DFRAPI_DOMAIN ), DFRAPI_BUG_REPORTS_URL ) . '</p>' .
			'<p><a href="' . DFRAPI_REPORT_BUG_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button button-primary" target="_blank">' . __( 'Report a bug', DFRAPI_DOMAIN ) . '</a></p>'

	) );

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', DFRAPI_DOMAIN ) . '</strong></p>' .
		'<p><a href="' . DFRAPI_HOME_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">' . __( 'About Datafeedr', DFRAPI_DOMAIN ) . '</a></p>' .
		'<p><a href="' . DFRAPI_KEYS_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">' . __( 'Datafeedr API Keys', DFRAPI_DOMAIN ) . '</a></p>'
	);

	return $screen;
}