const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { getWebpackEntryPoints } = require( '@wordpress/scripts/utils/config' );

module.exports = {
	...defaultConfig,
	entry: {
		...getWebpackEntryPoints(),
		'admin': './src/admin/index.js',
		'main': './src/main/index.js',
		'styles': './assets/scss/main.scss',
		'admin-styles': './assets/scss/admin.scss',
	}
}