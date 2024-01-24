<?php

// ADD LOGGING FUNCTION
if ( !function_exists('_log') ) {
	function _log( $message ) {
		if ( WP_DEBUG === true ){
			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( print_r( $message, true ) );
			} else {
				error_log( $message );
			}
		}
	}
}

// REQUIRE CLASSES
require_once( 'classes/lib/detect/Mobile_Detect.php' );
require_once( 'classes/class-cpc-sql.php' );
require_once( 'classes/class-cpc-ajax.php' );
require_once( 'classes/class-cpc.php' );

if ( is_admin() ) {
	require_once( 'classes/class-cpc-admin.php' );
}