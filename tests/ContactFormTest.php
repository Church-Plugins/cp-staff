<?php

use CP_Staff\Init;
use PHPUnit\Framework\TestCase;

class ContactFormTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['cp_staff_test_options'] = array(
			'cp_staff_main_options' => array(
				'enable_captcha'     => 'off',
				'block_staff_emails' => 'off',
				'use_email_modal'    => 'on',
			),
		);
		$GLOBALS['cp_staff_posts'] = array();
		$GLOBALS['cp_staff_mail']  = array();
		$GLOBALS['cp_staff_json']  = null;
		$_POST                     = array();
		$_REQUEST                  = array();
	}

	private function plugin() {
		$reflection = new ReflectionClass( Init::class );
		return $reflection->newInstanceWithoutConstructor();
	}

	private function add_staff( $id, $status, $type, $email ) {
		$post              = new stdClass();
		$post->ID          = (int) $id;
		$post->post_type   = $type;
		$post->post_status = $status;
		$post->meta        = array( 'email' => $email );
		$GLOBALS['cp_staff_posts'][ (int) $id ] = $post;
	}

	private function send( array $post ) {
		$_POST    = $post;
		$_REQUEST = array(
			'cp_staff_send_email_nonce' => 'valid-nonce',
		);

		try {
			$this->plugin()->maybe_send_email();
		} catch ( RuntimeException $e ) {
			return $e->getMessage();
		}

		$this->fail( 'The contact form did not return a response.' );
	}

	public function test_arbitrary_email_to_is_ignored_for_a_staff_record() {
		$this->add_staff( 7, 'publish', 'cp_staff', 'pastor@church.test' );

		$result = $this->send( array(
			'staff-id'    => '7',
			'email-to'    => 'other@example.com',
			'email-from'  => 'visitor@gmail.com',
			'from-name'   => 'Visitor',
			'subject'     => 'Hello',
			'message'     => 'Hi there',
			'email-verify' => '',
		) );

		$this->assertSame( 'json_success', $result );
		$this->assertCount( 1, $GLOBALS['cp_staff_mail'] );
		$this->assertSame( 'pastor@church.test', $GLOBALS['cp_staff_mail'][0]['to'] );
	}

	public function test_arbitrary_email_to_without_a_staff_record_is_rejected() {
		$result = $this->send( array(
			'email-to'   => 'other@example.com',
			'email-from' => 'visitor@gmail.com',
			'from-name'  => 'Visitor',
			'subject'    => 'Hello',
			'message'    => 'Hi there',
		) );

		$this->assertSame( 'json_error', $result );
		$this->assertSame( array(), $GLOBALS['cp_staff_mail'] );
		$this->assertSame( '', $this->plugin()->get_staff_recipient_email( '' ) );
		$this->assertSame(
			'Please refresh the page and try again.',
			$GLOBALS['cp_staff_json']['data']['error']
		);
	}

	public function test_valid_staff_id_sends_to_the_stored_email() {
		$this->add_staff( 12, 'publish', 'cp_staff', 'office@church.test' );

		$this->assertSame( 'office@church.test', $this->plugin()->get_staff_recipient_email( '12' ) );

		$result = $this->send( array(
			'staff-id'   => '12',
			'email-from' => 'visitor@gmail.com',
			'from-name'  => 'Visitor',
			'subject'    => 'Question',
			'message'    => 'Can we meet?',
		) );

		$this->assertSame( 'json_success', $result );
		$this->assertSame( 'office@church.test', $GLOBALS['cp_staff_mail'][0]['to'] );
	}

	public function test_unpublished_or_non_staff_id_is_rejected() {
		$this->add_staff( 3, 'draft', 'cp_staff', 'draft@church.test' );
		$this->add_staff( 4, 'publish', 'post', 'page@church.test' );
		$this->add_staff( 5, 'private', 'cp_staff', 'private@church.test' );

		$this->assertSame( '', $this->plugin()->get_staff_recipient_email( 3 ) );
		$this->assertSame( '', $this->plugin()->get_staff_recipient_email( 4 ) );
		$this->assertSame( '', $this->plugin()->get_staff_recipient_email( 5 ) );
		$this->assertSame( '', $this->plugin()->get_staff_recipient_email( 99 ) );

		foreach ( array( '3', '4', '5', '99' ) as $staff_id ) {
			$GLOBALS['cp_staff_mail'] = array();
			$result                   = $this->send( array(
				'staff-id'   => $staff_id,
				'email-to'   => 'other@example.com',
				'email-from' => 'visitor@gmail.com',
				'from-name'  => 'Visitor',
				'subject'    => 'Hello',
				'message'    => 'Hi there',
			) );

			$this->assertSame( 'json_error', $result, $staff_id );
			$this->assertSame( array(), $GLOBALS['cp_staff_mail'], $staff_id );
		}
	}

	public function test_normal_submission_passes_when_email_verify_is_filled() {
		$this->add_staff( 7, 'publish', 'cp_staff', 'pastor@church.test' );

		$result = $this->send( array(
			'staff-id'     => '7',
			'email-from'   => 'visitor@gmail.com',
			'from-name'    => 'Visitor',
			'subject'      => 'Hello',
			'message'      => 'Hi there',
			'email-verify' => 'filled-in',
		) );

		$this->assertSame( 'json_success', $result );
		$this->assertSame( 'pastor@church.test', $GLOBALS['cp_staff_mail'][0]['to'] );
	}

	public function test_staff_record_without_a_valid_email_is_rejected() {
		$this->add_staff( 8, 'publish', 'cp_staff', 'not-an-email' );

		$this->assertSame( '', $this->plugin()->get_staff_recipient_email( 8 ) );

		$result = $this->send( array(
			'staff-id'   => '8',
			'email-to'   => 'other@example.com',
			'email-from' => 'visitor@gmail.com',
			'from-name'  => 'Visitor',
			'subject'    => 'Hello',
			'message'    => 'Hi there',
		) );

		$this->assertSame( 'json_error', $result );
		$this->assertSame( array(), $GLOBALS['cp_staff_mail'] );
	}

	public function test_handler_returns_an_error_when_the_contact_modal_is_off() {
		unset( $GLOBALS['cp_staff_test_options']['cp_staff_main_options']['use_email_modal'] );
		$this->add_staff( 7, 'publish', 'cp_staff', 'pastor@church.test' );

		$result = $this->send( array(
			'staff-id'   => '7',
			'email-to'   => 'other@example.com',
			'email-from' => 'visitor@gmail.com',
			'from-name'  => 'Visitor',
			'subject'    => 'Hello',
			'message'    => 'Hi there',
		) );

		$this->assertSame( 'json_error', $result );
		$this->assertSame( array(), $GLOBALS['cp_staff_mail'] );
		$this->assertSame( 'Messaging is not available.', $GLOBALS['cp_staff_json']['data']['error'] );
	}

	public function test_script_adds_staff_id_when_a_modal_copy_omits_it() {
		$js = file_get_contents( dirname( __DIR__ ) . '/assets/js/main.js' );

		$this->assertNotFalse( $js );
		$this->assertStringContainsString(
			'<input type="hidden" name="staff-id" class="staff-id">',
			$js
		);
		$this->assertMatchesRegularExpression(
			'/if\s*\(\s*!\s*\$form\.find\(\s*[\'"]\.staff-id[\'"]\s*\)\.length\s*\)/',
			$js
		);
	}
}
