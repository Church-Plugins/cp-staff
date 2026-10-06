<?php
/**
 * Minimal WordPress stand-ins for the settings unit tests.
 */

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'CP_STAFF_INCLUDES', dirname( __DIR__ ) . '/includes' );

$GLOBALS['cp_staff_test_options'] = array();

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		if ( array_key_exists( $option, $GLOBALS['cp_staff_test_options'] ) ) {
			return $GLOBALS['cp_staff_test_options'][ $option ];
		}

		return $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $option, $value ) {
		$GLOBALS['cp_staff_test_options'][ $option ] = $value;
		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value, ...$args ) {
		return $value;
	}
}

if ( ! function_exists( 'site_url' ) ) {
	function site_url() {
		return 'https://www.example.org';
	}
}

require_once dirname( __DIR__ ) . '/includes/Admin/Settings.php';
require_once dirname( __DIR__ ) . '/includes/Init.php';
require_once dirname( __DIR__ ) . '/includes/Setup/Migrator.php';
