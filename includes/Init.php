<?php
namespace CP_Staff;

use CP_Staff\Admin\Settings;
use RuntimeException;

/**
 * Provides the global $cp_staff object
 *
 * @author costmo
 */
class Init {

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
		$this->enqueue = new \WPackio\Enqueue( 'cpStaff', 'dist', $this->get_version(), 'plugin', CP_STAFF_PLUGIN_FILE );
		$this->limiter = new Ratelimit( "send_staff_email" );
		add_action( 'cp_core_loaded', [ $this, 'maybe_setup' ], - 9999 );
		add_action( 'init', [ $this, 'maybe_init' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts'] );
		add_action( 'wp_footer', [ $this, 'modal_template' ] );
		add_action( 'fl_after_schema_meta', [ $this, 'staff_meta' ] );
	}

	public function staff_meta() {
		if ( 'cp_staff' != get_post_type() ) {
			return;
		}

		$details = [
			'name'       => get_the_title(),
			'id'         => get_the_ID(),
			'email' 		 => base64_encode( get_post_meta( get_the_ID(), 'email', true ) )
		];

		echo '<meta itemprop="staffDetails" data-details="' . esc_attr( json_encode( $details ) ) . '">';
	}

	/**
	 * Plugin setup entry hub
	 *
	 * @return void
	 */
	public function maybe_setup() {
		if ( ! $this->check_required_plugins() ) {
			return;
		}

		$this->includes();
		$this->actions();
	}

	/**
	 * Actions that must run through the `init` hook
	 *
	 * @return void
	 * @author costmo
	 */
	public function maybe_init() {

		if ( ! $this->check_required_plugins() ) {
			return;
		}

	}

	/**
	 * `wp_enqueue_scripts` actions for the app's compiled sources
	 *
	 * @return void
	 * @author costmo
	 */
	public function scripts() {
		$this->enqueue->enqueue( 'styles', 'main', [] );

		if ( Settings::get( 'use_email_modal', false ) ) {
			$this->enqueue->enqueue( 'scripts', 'main', [ 'js_dep' => [ 'jquery', 'jquery-ui-dialog', 'jquery-form' ] ] );
		}

		if( Settings::get( 'enable_captcha', 'on' ) == 'on' ) {
			$site_key = Settings::get( 'captcha_site_key', '' );
			if( ! empty( $site_key ) ) {
				wp_localize_script( 'grecaptcha-site-key', 'recaptchaSiteKey', $site_key );
				wp_enqueue_script( 'grecaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $site_key );
			}
		}
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
		$this->setup = Setup\Init::get_instance();
	}

	protected function actions() {
		add_action( 'cp_staff_send_email', [ $this, 'maybe_send_email' ] );
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
		$is_hidden_att = Settings::get( 'show_staff_email', 'off' ) == 'on' ? '' : 'hidden';
		?>
		<div id="cp-staff-email-modal-template" style="display:none;">
			<div class="cp-staff-email-modal">
				<form class="cp-staff-email-form"
					  action="<?php echo esc_url( add_query_arg( 'cp_action', 'cp_staff_send_email', admin_url( 'admin-ajax.php' ) ) ); ?>"
					  method="post" enctype="multipart/form-data">

					<?php wp_nonce_field( 'cp_staff_send_email', 'cp_staff_send_email_nonce' ); ?>

					<div class="cp-staff-email-form--name">
						<h4><?php _e( 'Send a message to', 'cp-staff' ); ?> <span class="staff-name"></span></h4>
					</div>

					<div class="cp-staff-email-form--email-to" <?php echo $is_hidden_att ?>>
						<label>
							<?php _e( 'To:', 'cp-staff' ); ?>
							<input type="hidden" name="email-to" class="staff-email-to" />
							<input type="text" disabled="disabled" class="staff-email-to"/>
							<div class="staff-copy-email"
								 title="Copy email address"><?php echo \ChurchPlugins\Helpers::get_icon( 'copy' ); ?></div>
						</label>
					</div>

					<div class="cp-staff-email-form--name">
						<label>
							<?php _e( 'Your Full Name:', 'cp-staff' ); ?>
							<input type="text" name="from-name" />
						</label>
					</div>

					<div class="cp-staff-email-form--email-from">
						<label>
							<?php _e( 'Your Email:', 'cp-staff' ); ?>
							<input type="text" name="email-from" class="staff-email-from"/>
						</label>
					</div>

					<div class='cp-staff-email-form--email-verify'>
						<label>
							<?php _e( 'Email Verify', 'cp-staff' ) ?>
							<input type='text' name='email-verify'>
						</label>
					</div>

					<div class="cp-staff-email-form--subject">
						<label>
							<?php _e( 'Email Subject:', 'cp-staff' ); ?>
							<input type="text" name="subject"/>
						</label>
					</div>

					<div class="cp-staff-email-form--message">
						<label>
							<?php _e( 'Email Message:', 'cp-staff' ); ?>
							<textarea name="message" rows="3"></textarea>
						</label>
					</div>

					<input class="cp-button is-large" type="submit" value="Send"/>

				</form>
			</div>
		</div>
		<?php
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
	 * Make sure required plugins are active
	 *
	 * @return bool
	 */
	protected function check_required_plugins() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// @todo check for requirements before loading
		if ( 1 ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'required_plugins' ) );

		return false;
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
		return '0.0.1';
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

}
