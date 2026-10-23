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

if ( ! function_exists( 'absint' ) ) {
	function absint( $maybeint ) {
		return abs( (int) $maybeint );
	}
}

if ( ! function_exists( 'is_email' ) ) {
	function is_email( $email ) {
		return is_string( $email ) && false !== filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
}

if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post = null, ...$args ) {
		$id = is_object( $post ) ? (int) $post->ID : (int) $post;
		if ( ! isset( $GLOBALS['cp_staff_posts'][ $id ] ) ) {
			return null;
		}

		return $GLOBALS['cp_staff_posts'][ $id ];
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		$id = (int) $post_id;
		if ( ! isset( $GLOBALS['cp_staff_posts'][ $id ] ) ) {
			return $single ? '' : array();
		}

		$meta = $GLOBALS['cp_staff_posts'][ $id ]->meta;
		if ( '' === $key ) {
			return $meta;
		}

		if ( ! array_key_exists( $key, $meta ) ) {
			return $single ? '' : array();
		}

		return $meta[ $key ];
	}
}

if ( ! function_exists( '_sanitize_text_fields' ) ) {
	function _sanitize_text_fields( $value, $trim = false ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = (string) $value;
		return $trim ? trim( $value ) : $value;
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'wpautop' ) ) {
	function wpautop( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $show = '' ) {
		return 'admin_email' === $show ? 'admin@example.org' : 'Example Church';
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action = -1 ) {
		return 'valid-nonce' === $nonce && 'cp_staff_send_email' === $action;
	}
}

if ( ! function_exists( 'wp_mail' ) ) {
	function wp_mail( $to, $subject, $message, $headers = '' ) {
		$GLOBALS['cp_staff_mail'][] = array(
			'to'      => $to,
			'subject' => $subject,
			'message' => $message,
			'headers' => $headers,
		);
		return true;
	}
}

if ( ! function_exists( 'wp_send_json_error' ) ) {
	function wp_send_json_error( $data = null ) {
		$GLOBALS['cp_staff_json'] = array(
			'success' => false,
			'data'    => $data,
		);
		throw new RuntimeException( 'json_error' );
	}
}

if ( ! function_exists( 'wp_send_json_success' ) ) {
	function wp_send_json_success( $data = null ) {
		$GLOBALS['cp_staff_json'] = array(
			'success' => true,
			'data'    => $data,
		);
		throw new RuntimeException( 'json_success' );
	}
}

$GLOBALS['cp_staff_posts'] = array();
$GLOBALS['cp_staff_mail']  = array();

require_once dirname( __DIR__ ) . '/includes/ChurchPlugins/Helpers.php';
require_once dirname( __DIR__ ) . '/includes/Admin/Settings.php';
require_once dirname( __DIR__ ) . '/includes/Init.php';
require_once dirname( __DIR__ ) . '/includes/Setup/Migrator.php';
