<?php
/**
 * Plugin constants
 */

/**
 * Setup/config constants
 */
if( !defined( 'CP_STAFF_PLUGIN_FILE' ) ) {
	 define ( 'CP_STAFF_PLUGIN_FILE',
	 	dirname( dirname( __FILE__ ) ) . "/cp-staff.php"
	);
}
if( !defined( 'CP_STAFF_PLUGIN_DIR' ) ) {
	 define ( 'CP_STAFF_PLUGIN_DIR',
	 	plugin_dir_path( CP_STAFF_PLUGIN_FILE )
	);
}
if( !defined( 'CP_STAFF_PLUGIN_URL' ) ) {
	 define ( 'CP_STAFF_PLUGIN_URL',
	 	plugin_dir_url( CP_STAFF_PLUGIN_FILE )
	);
}
if( !defined( 'CP_STAFF_PLUGIN_VERSION' ) ) {
	 define ( 'CP_STAFF_PLUGIN_VERSION',
	 	'1.0.0'
	);
}
if( !defined( 'CP_STAFF_INCLUDES' ) ) {
	 define ( 'CP_STAFF_INCLUDES',
	 	plugin_dir_path( dirname( __FILE__ ) ) . 'includes'
	);
}
if( !defined( 'CP_STAFF_PREFIX' ) ) {
	define ( 'CP_STAFF_PREFIX',
		'cps'
   );
}
if( !defined( 'CP_STAFF_TEXT_DOMAIN' ) ) {
	 define ( 'CP_STAFF_TEXT_DOMAIN',
		'cp_staff'
   );
}
if( !defined( 'CP_STAFF_DIST' ) ) {
	 define ( 'CP_STAFF_DIST',
		CP_STAFF_PLUGIN_URL . "/dist/"
   );
}

/**
 * Licensing constants
 */
if( !defined( 'CP_STAFF_STORE_URL' ) ) {
	 define ( 'CP_STAFF_STORE_URL',
	 	'https://churchplugins.com'
	);
}
if( !defined( 'CP_STAFF_ITEM_NAME' ) ) {
	 define ( 'CP_STAFF_ITEM_NAME',
	 	'Church Plugins - Staff'
	);
}

/**
 * App constants
 */
if( !defined( 'CP_STAFF_APP_PATH' ) ) {
	 define ( 'CP_STAFF_APP_PATH',
	 	plugin_dir_path( dirname( __FILE__ ) ) . 'app'
	);
}
if( !defined( 'CP_STAFF_ASSET_MANIFEST' ) ) {
	 define ( 'CP_STAFF_ASSET_MANIFEST',
	 	plugin_dir_path( dirname( __FILE__ ) ) . 'app/build/asset-manifest.json'
	);
}
