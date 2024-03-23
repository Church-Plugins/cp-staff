<?php
/**
 * CP_Staff\Templates class
 *
 * @package CP_Staff
 * @since 1.2.0
 */

namespace CP_Staff;

/**
 * Implements the template class
 *
 * @since 1.2.0
 */
class Templates extends \ChurchPlugins\Templates {
	/**
	 * Returns array of registered post types
	 *
	 * @return array
	 */
	public function get_post_types() {
		return cp_staff()->setup->post_types->get_post_types();
	}

	/**
	 * Returns array of registered taxonomies
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		return cp_staff()->setup->taxonomies->get_taxonomies();
	}

	/**
	 * Returns array of registered meta boxes
	 *
	 * @return string
	 */
	public function get_plugin_id() {
		return 'cp-staff';
	}

	/**
	 * Returns the plugin path
	 *
	 * @return string
	 */
	public function get_plugin_path() {
		return cp_staff()->get_plugin_path();
	}
}
