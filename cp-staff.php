<?php
/**
 * Plugin Name: CP Staff
 * Plugin URL: https://churchplugins.com
 * Description: Staff management for churches
 * Version: 1.2.1
 * Author: Church Plugins
 * Author URI: https://churchplugins.com
 * Text Domain: cp-staff
 * Domain Path: languages
 */

if( !defined( 'CP_STAFF_PLUGIN_VERSION' ) ) {
	 define ( 'CP_STAFF_PLUGIN_VERSION',
	 	'1.2.1'
	);
}

require_once( dirname( __FILE__ ) . "/includes/Constants.php" );

require_once( CP_STAFF_PLUGIN_DIR . "/includes/ChurchPlugins/init.php" );
require_once( CP_STAFF_PLUGIN_DIR . 'vendor/autoload.php' );

global $cp_staff;
$cp_staff = cp_staff();

/**
 * @return CP_Staff\Init
 */
function cp_staff() {
	return CP_Staff\Init::get_instance();
}

register_activation_hook( __FILE__, array( $cp_staff, 'activate' ) );
register_deactivation_hook( __FILE__, array( $cp_staff, 'deactivate' ) );

/**
 * Load plugin text domain for translations.
 *
 * @return void
 */
function cp_staff_load_textdomain() {

	// Traditional WordPress plugin locale filter
	$get_locale = get_user_locale();

	/**
	 * Defines the plugin language locale used in RCP.
	 *
	 * @var string $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
	 *                  otherwise uses `get_locale()`.
	 */
	$locale        = apply_filters( 'plugin_locale',  $get_locale, 'cp-staff' );
	$mofile        = sprintf( '%1$s-%2$s.mo', 'cp-staff', $locale );

	// Setup paths to current locale file
	$mofile_global = WP_LANG_DIR . '/cp-staff/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/cp-staff folder
		load_textdomain( 'cp-staff', $mofile_global );
	}

}
add_action( 'init', 'cp_staff_load_textdomain' );