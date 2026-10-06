<?php

use CP_Staff\Admin\Settings;
use CP_Staff\Init;
use CP_Staff\Setup\Migrator;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['cp_staff_test_options'] = array();
	}

	private function set_main_options( array $options ) {
		$GLOBALS['cp_staff_test_options']['cp_staff_main_options'] = $options;
	}

	private function plugin() {
		$reflection = new ReflectionClass( Init::class );
		return $reflection->newInstanceWithoutConstructor();
	}

	public function test_feature_reads_missing_legacy_value_as_on() {
		$this->assertTrue( Settings::is_on( 'enable_captcha', 'on' ) );
		$this->assertTrue( Settings::is_on( 'block_staff_emails', 'on' ) );
		$this->assertTrue( $this->plugin()->is_address_blocked( 'pastor@example.org' ) );
	}

	public function test_feature_reads_explicit_on() {
		$this->set_main_options( array(
			'enable_captcha'    => 'on',
			'block_staff_emails' => 'on',
		) );

		$this->assertTrue( Settings::is_on( 'enable_captcha', 'on' ) );
		$this->assertTrue( Settings::is_on( 'block_staff_emails', 'on' ) );
		$this->assertTrue( $this->plugin()->is_address_blocked( 'pastor@example.org' ) );
	}

	public function test_feature_reads_explicit_off() {
		$this->set_main_options( array(
			'enable_captcha'    => 'off',
			'block_staff_emails' => 'off',
		) );

		$this->assertFalse( Settings::is_on( 'enable_captcha', 'on' ) );
		$this->assertFalse( Settings::is_on( 'block_staff_emails', 'on' ) );
		$this->assertFalse( $this->plugin()->is_address_blocked( 'pastor@example.org' ) );
	}

	public function test_unchecked_checkbox_is_stored_as_off_and_checked_as_on() {
		$this->assertSame( 'off', Settings::sanitize_on_off_checkbox( null ) );
		$this->assertSame( 'off', Settings::sanitize_on_off_checkbox( '' ) );
		$this->assertSame( 'off', Settings::sanitize_on_off_checkbox( false ) );
		$this->assertSame( 'on', Settings::sanitize_on_off_checkbox( 'on' ) );
	}

	public function test_checkbox_display_matches_effective_state() {
		$this->assertSame( 'on', Settings::escape_on_off_checkbox( null ) );
		$this->assertSame( 'on', Settings::escape_on_off_checkbox( false ) );
		$this->assertSame( 'on', Settings::escape_on_off_checkbox( '' ) );
		$this->assertSame( 'on', Settings::escape_on_off_checkbox( 'on' ) );
		$this->assertSame( '', Settings::escape_on_off_checkbox( 'off' ) );
	}

	public function test_captcha_is_active_for_legacy_on_when_both_keys_are_set() {
		$this->set_main_options( array(
			'captcha_site_key'   => 'site-key',
			'captcha_secret_key' => 'secret-key',
		) );

		$this->assertTrue( Settings::is_captcha_active() );
	}

	public function test_captcha_is_active_only_when_enabled_and_both_keys_are_set() {
		$this->set_main_options( array(
			'enable_captcha'     => 'on',
			'captcha_site_key'   => 'site-key',
			'captcha_secret_key' => 'secret-key',
		) );
		$this->assertTrue( Settings::is_captcha_active() );

		$this->set_main_options( array(
			'enable_captcha'     => 'off',
			'captcha_site_key'   => 'site-key',
			'captcha_secret_key' => 'secret-key',
		) );
		$this->assertFalse( Settings::is_captcha_active() );
	}

	public function test_captcha_is_inactive_when_either_key_is_missing() {
		$cases = array(
			array(
				'enable_captcha'     => 'on',
				'captcha_site_key'   => '',
				'captcha_secret_key' => 'secret-key',
			),
			array(
				'enable_captcha'     => 'on',
				'captcha_site_key'   => 'site-key',
				'captcha_secret_key' => '',
			),
			array(
				'enable_captcha'     => 'on',
			),
			array(
				'captcha_secret_key' => 'secret-key',
			),
			array(
				'captcha_site_key' => 'site-key',
			),
		);

		foreach ( $cases as $options ) {
			$this->set_main_options( $options );
			$this->assertFalse( Settings::is_captcha_active(), json_encode( $options ) );
		}
	}

	public function test_captcha_verification_is_skipped_unless_captcha_is_active() {
		$plugin = $this->plugin();

		$this->set_main_options( array(
			'enable_captcha'     => 'off',
			'captcha_site_key'   => 'site-key',
			'captcha_secret_key' => 'secret-key',
		) );
		$this->assertTrue( $plugin->is_verified_captcha() );

		$this->set_main_options( array(
			'enable_captcha'     => 'on',
			'captcha_secret_key' => 'secret-key',
		) );
		$this->assertTrue( $plugin->is_verified_captcha() );

		$this->set_main_options( array(
			'captcha_secret_key' => 'secret-key',
		) );
		$this->assertTrue( $plugin->is_verified_captcha() );
	}

	public function test_migration_writes_on_for_missing_features_and_keeps_saved_values() {
		$this->assertSame(
			array(
				'enable_captcha'     => 'on',
				'block_staff_emails' => 'on',
			),
			Settings::with_legacy_feature_defaults( array() )
		);

		$this->assertSame(
			array(
				'enable_captcha'     => 'off',
				'from_email'         => 'office@example.org',
				'block_staff_emails' => 'on',
			),
			Settings::with_legacy_feature_defaults( array(
				'enable_captcha' => 'off',
				'from_email'     => 'office@example.org',
			) )
		);

		$this->assertSame(
			array(
				'enable_captcha'     => 'on',
				'block_staff_emails' => 'off',
			),
			Settings::with_legacy_feature_defaults( array(
				'enable_captcha'     => 'on',
				'block_staff_emails' => 'off',
			) )
		);
	}

	public function test_upgrade_migration_persists_legacy_on_values() {
		$GLOBALS['cp_staff_test_options']['cp_staff_main_options'] = array(
			'from_name' => 'Example Church',
		);

		$migrator   = Migrator::get_instance();
		$reflection = new ReflectionMethod( Migrator::class, 'migrate_to_1_2_2' );
		$reflection->invoke( $migrator );

		$this->assertSame(
			array(
				'from_name'          => 'Example Church',
				'enable_captcha'     => 'on',
				'block_staff_emails' => 'on',
			),
			get_option( 'cp_staff_main_options' )
		);

		$this->set_main_options( array(
			'enable_captcha'     => 'off',
			'block_staff_emails' => 'off',
		) );
		$reflection->invoke( $migrator );

		$this->assertSame(
			array(
				'enable_captcha'     => 'off',
				'block_staff_emails' => 'off',
			),
			get_option( 'cp_staff_main_options' )
		);
	}
}
