<?php
namespace CP_Staff;

use CP_Staff\Admin\Settings;
use RuntimeException;

require_once __DIR__ . '/../includes/ChurchPlugins/Setup/Plugin.php';

/**
 * Provides the global $cp_staff object
 *
 * @author costmo
 */
class Init extends \ChurchPlugins\Setup\Plugin {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * @var Setup\Init
	 */
	public $setup;

	public $enqueue;

	protected $limiter;

	/**
	 * Template class instance
	 *
	 * @var Templates
	 */
	public $templates;

	/**
	 * Only make one instance of Init
	 *
	 * @return Init
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Init ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor: Add Hooks and Actions
	 */
	protected function __construct() {
		$this->enqueue  = new \WPackio\Enqueue( 'cpStaff', 'dist', $this->get_version(), 'plugin', CP_STAFF_PLUGIN_FILE );
		$this->limiter  = new Ratelimit( "send_staff_email" );
		$this->migrator = Setup\Migrator::get_instance();

		parent::__construct();
	}

	public function staff_meta() {
		if ( 'cp_staff' != get_post_type() ) {
			return;
		}

		if ( ! Settings::get( 'use_email_modal', false ) ) {
			return;
		}

		$details = [
			'name'  => get_the_title(),
			'id'    => get_the_ID(),
			'email' => base64_encode( get_post_meta( get_the_ID(), 'email', true ) )
		];

		echo '<meta itemprop="staffDetails" data-details="' . esc_attr( json_encode( $details ) ) . '">';
	}

	/**
	 * `wp_enqueue_scripts` actions for the app's compiled sources
	 *
	 * @return void
	 * @author costmo
	 */
	public function scripts() {
		$this->enqueue->enqueue( 'styles', 'main', [] );

		$this->enqueue->enqueue( 'scripts', 'main', [ 'js_dep' => [ 'jquery', 'jquery-ui-dialog', 'jquery-form' ] ] );

		if( Settings::get( 'enable_captcha', 'on' ) == 'on' ) {
			$site_key = Settings::get( 'captcha_site_key', '' );
			if( ! empty( $site_key ) ) {
				wp_enqueue_script( 'cp-staff-grecaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $site_key );
				wp_localize_script( 'cp-staff-grecaptcha', 'recaptchaSiteKey', $site_key );
			}
		}

		wp_enqueue_script( 'feather-icons' );
		wp_enqueue_style( 'material-icons' );
	}

	public function admin_scripts() {
		$this->enqueue->enqueue( 'styles', 'admin', [] );
	}

	/**
	 * Includes
	 *
	 * @return void
	 */
	protected function includes() {
		require_once( 'Templates.php' );
		Admin\Init::get_instance();
		$this->setup     = Setup\Init::get_instance();
		$this->templates = Templates::get_instance();

		parent::includes();
	}

	protected function actions() {
		add_action( 'cp_staff_send_email', [ $this, 'maybe_send_email' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts'] );
		add_action( 'wp_footer', [ $this, 'modal_template' ] );
		add_action( 'fl_after_schema_meta', [ $this, 'staff_meta' ] );
	}

	/**
	 * Required Plugins notice
	 *
	 * @return void
	 */
	public function required_plugins() {
		printf( '<div class="error"><p>%s</p></div>', __( 'Your system does not meet the requirements for Church Plugins - Staff', 'cp-staff' ) );
	}

	public function maybe_send_email() {

		$email_to = \ChurchPlugins\Helpers::get_post( 'email-to' );
		$reply_to = \ChurchPlugins\Helpers::get_post( 'email-from' );
		$honeypot = \ChurchPlugins\Helpers::get_post( 'email-verify' );
		$name     = \ChurchPlugins\Helpers::get_post( 'from-name' );
		$subject  = \ChurchPlugins\Helpers::get_post( 'subject' );
		$message  = \ChurchPlugins\Helpers::get_post( 'message' );
		$limit    = intval( Settings::get( 'throttle_amount', 3 ) );


		if( ! wp_verify_nonce( $_REQUEST['cp_staff_send_email_nonce'], 'cp_staff_send_email' ) || ! is_email( $email_to ) ) {
			wp_send_json_error( array( 'error' => __( 'Something went wrong. Please reload the page and try again.', 'church-plugins' ) ) );
		}

		if ( empty( $name ) ) {
			wp_send_json_error( array( 'error' => __( 'Please enter a your full name.', 'church-plugins' ), 'request' => $_REQUEST ) );
		}

		if ( ! is_email ( $reply_to ) ) {
			wp_send_json_error( array( 'error' => __( 'Please enter a valid email address.', 'church-plugins' ), 'request' => $_REQUEST ) );
		}

		if( $this->check_if_ratelimited( $reply_to, $limit ) ) {
			wp_send_json_error( array( 'error' => __( "Daily send limit of {$limit} submissions exceeded - Message blocked. Please try again later.", 'church-plugins' ) ) );
		}

		if( ! empty( $honeypot ) ) {
			wp_send_json_error( array( 'error' => __( 'Blocked for suspicious activity', 'church-plugins' ), 'request' => $_REQUEST ) );
		}

		if( empty( $subject ) ) {
			wp_send_json_error( array( 'error' => __( 'Please add an Email Subject.', 'church-plugins' ), 'request' => $_REQUEST ) );
		}

		if( empty( $message ) ) {
			wp_send_json_error( array( 'error' => __( 'Please add an Email Message.', 'church-plugins' ), 'request' => $_REQUEST ) );
		}

		if( $this->is_address_blocked( $reply_to ) ) {
			wp_send_json_error( array( 'error' => __( 'You are not allowed to send a message as a staff member', 'cp-staff' ), 'request' => $_REQUEST ) );
		}

		if( ! $this->is_verified_captcha() ) {
			wp_send_json_error( array( 'error' => __( 'Your captcha score is too low', 'cp-staff' ), 'request' => $_REQUEST ) );
		}

		$subject = apply_filters( 'cp_staff_email_subject', __( '[Web Inquiry]', 'cp-staff' ) . ' ' . $subject, $subject );

		$message_suffix = apply_filters( 'cp_staff_email_message_suffix', '<br /><br />-<br />' . sprintf( __( 'Submitted by %s via Staff Web Inquiry form. Simply click Reply to respond to them directly.', 'cp-staff' ), $name ) );
		$message        = apply_filters( 'cp_staff_email_message', $message . $message_suffix );

		$from_email = Settings::get( 'from_email', get_bloginfo( 'admin_email' ) );
		$from_name  = Settings::get( 'from_name', get_bloginfo( 'name' ) );

		wp_mail( $email_to, stripslashes( $subject ), stripslashes( wpautop( $message ) ), [
			'Content-Type: text/html; cahrset=UTF-8',
			"From: $from_name <$from_email>",
			sprintf( 'Reply-To: %s <%s>', $name, $reply_to )
		] );

		wp_send_json_success( array( 'success' => __( 'Email sent!', 'church-plugins' ), 'request' => $_REQUEST ) );
	}

	public function modal_template() {
		cp_staff()->templates->get_template_part( 'parts/email-modal' );
	}

	/** Helper Methods **************************************/

	public function get_default_thumb() {
		return CP_STAFF_PLUGIN_URL . '/app/public/logo512.png';
	}

	/**
	 * Determine if the current user has exceeded the number of responses allowed per day
	 *
	 * @since  1.1.0
	 *
	 * @param $email
	 * @param $limit
	 *
	 * @return bool
	 * @author Jonathan Roley, 6/6/23
	 */
	public function check_if_ratelimited( $email, $limit ) {
		if( Settings::get( 'throttle_staff_emails', 'off' ) == 'off' ) {
			return false;
		}

		try {
			$remote_addr = '0.0.0.0';
			if( !empty( $_SERVER ) && is_array( $_SERVER ) && !empty( $_SERVER['REMOTE_ADDR'] ) ) {
				$remote_addr = $_SERVER['REMOTE_ADDR'];
			}

			$this->limiter->add_entries(
				array(
					$remote_addr, // user IP address
					$email // sender email address
				),
				$limit
			);
			return false;
		}
		catch(RuntimeException $err) {
			return true;
		}
	}

	/**
	 * Determine if the provided address is restricted
	 *
	 * @since  1.1.0
	 *
	 * @param $email
	 *
	 * @return bool
	 * @author Jonathan Roley, 6/6/23
	 */
	public function is_address_blocked( $email ) {
		if( Settings::get( 'block_staff_emails', 'on' ) == 'off' ) {
			return false;
		}

		$site_domain = explode( '//', site_url() )[1];
		$site_domain = str_replace( 'www.', '', $site_domain );

		return str_contains( $email, $site_domain );
	}

	/**
	 * Determine if the captcha is verified
	 *
	 * @since  1.1.0
	 *
	 * @return bool
	 * @author Jonathan Roley, 6/6/23
	 */
	public function is_verified_captcha() {
		$token      = \ChurchPlugins\Helpers::get_post( 'token' );
		$action     = \ChurchPlugins\Helpers::get_post( 'action' );
		$secret_key = Settings::get( 'captcha_secret_key', '' );

		if( empty( $secret_key ) ) {
			return true;
		}

		$post_body = array(
			'secret'   => $secret_key,
			'response' => $token
		);

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify' );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post_body ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		$response = json_decode( curl_exec( $ch ), true );
		curl_close( $ch );

		return $response['success'] == '1' && $response['action'] == $action && $response['score'] > 0.5;
	}

	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_support_url() {
		return 'https://churchplugins.com/support';
	}

	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'Church Plugins - Staff', 'cp-staff' );
	}

	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @return string the plugin name
	 */
	public function get_plugin_path() {
		return CP_STAFF_PLUGIN_DIR;
	}

	/**
	 * Provide a unique ID tag for the plugin
	 *
	 * @return string
	 */
	public function get_id() {
		return 'cp-staff';
	}

	/**
	 * Provide a unique ID tag for the plugin
	 *
	 * @return string
	 */
	public function get_version() {
		return CP_STAFF_PLUGIN_VERSION;
	}

	/**
	 * Get the API namespace to use
	 *
	 * @return string
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_api_namespace() {
		return $this->get_id() . '/v1';
	}

	public function enabled() {
		return true;
	}

	public function get_plugin_dir() {
		return CP_STAFF_PLUGIN_DIR;
	}

	public function get_plugin_url() {
		return CP_STAFF_PLUGIN_URL;
	}

	/*** @TODO Remove at a later point. The following functions are copied from ChurchPlugins\Setup\Plugin.php and inserted here as overrides for old versions of core in other plugins ***/

	/**
	 * Handles the table upgrades for the plugin
	 *
	 * @return void
	 */
	public function maybe_migrate() {
		if ( ! $this->migrator ) {
			return;
		}

		// get old version (if exists)
		$old_version = get_option( $this->get_id() . '-version', '0.0.1' );
		$new_version = $this->get_version();

		// don't migrate if we don't need to
		if ( $old_version === $new_version ) {
			return;
		}

		$this->migrator->run_migrations( $old_version, $new_version );

		// update version
		update_option( $this->get_id() . '-version', $new_version );
	}

	public function activate() {
		update_option( $this->get_id() . '-version', $this->get_version() );

		do_action( 'cp_activated_' . $this->get_id(), $this );
	}

	public function deactivate() {
		do_action( 'cp_deactivated_' . $this->get_id(), $this );
	}

}
