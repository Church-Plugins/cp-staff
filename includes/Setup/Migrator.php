<?php

namespace CP_Staff\Setup;

use CP_Staff\Admin\Settings;

if ( ! class_exists( 'ChurchPlugins\Setup\Migrator', false ) ) {
	require_once CP_STAFF_INCLUDES . '/ChurchPlugins/Setup/Migrator.php';
}

/**
 * Handle migrations for plugin updates
 *
 * @package CP_Staff\Setup
 */
class Migrator extends \ChurchPlugins\Setup\Migrator {

	public function get_migrations(): array {
		return [
			'1.2.1' => [
				'up' => [ $this, 'migrate_to_1_2_1' ],
			],
			'1.2.2' => [
				'up' => [ $this, 'migrate_to_1_2_2' ],
			],
		];
	}

    /**
     * Migration for version 1.2.1
     * Set disable_archive setting to true
     */
    protected function migrate_to_1_2_1() {
        $options = get_option( 'cp_staff_staff_options', [] );
        $options['disable_archive'] = 'on';
        update_option( 'cp_staff_staff_options', $options );
    }

	/**
	 * Keep captcha and staff-email protection on for sites that never saved them.
	 *
	 * Those checkboxes were not stored when unchecked, and a missing value was
	 * treated as on. Write that effective value once so later saves can turn
	 * them off without changing sites that have not touched the setting.
	 */
	protected function migrate_to_1_2_2() {
		$options = get_option( 'cp_staff_main_options', array() );
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$updated = Settings::with_legacy_feature_defaults( $options );
		if ( $updated !== $options ) {
			update_option( 'cp_staff_main_options', $updated );
		}
	}

}