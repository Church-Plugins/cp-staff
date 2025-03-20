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

}