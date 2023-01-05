<?php
namespace CP_Staff;

use ChurchPlugins\Helpers;

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
		add_action( 'plugins_loaded', [ $this, 'maybe_setup' ], - 9999 );
		add_action( 'init', [ $this, 'maybe_init' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_footer', [ $this, 'modal_template' ] );
		add_action( 'fl_after_schema_meta', [ $this, 'staff_meta' ] );
	}

	public function staff_meta() {
		if ( 'cp_staff' != get_post_type() ) {
			return;
		}
		
		$details = [
			'name' => get_the_title(),
			'email' => base64_encode( get_post_meta( get_the_ID(), 'email', true ) ),
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
		$this->enqueue->enqueue( 'scripts', 'main', [] );
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
	
	protected function actions() {}
	
	/**
	 * Required Plugins notice
	 *
	 * @return void
	 */
	public function required_plugins() {
		printf( '<div class="error"><p>%s</p></div>', __( 'Your system does not meet the requirements for Church Plugins - Staff', 'cp-staff' ) );
	}
	
	public function modal_template() {
		?>
		<div id="cp-staff-email-modal-template" style="display:none;">
			<div class="cp-staff-email-modal">
				<div class="cp-staff-email-form">
					<div class="cp-staff-email-form--name">
						<h4><?php _e( 'Send a message to', 'cp-staff' ); ?> <span class="staff-name"></span></h4>
					</div>
					
					<div class="cp-staff-email-form--email-to">
						<label>
							<?php _e( 'To:', 'cp-staff' ); ?>	
							<input type="text" name="email-to" disabled="disabled" class="staff-email-to"/>
							<div class="staff-copy-email" title="Copy email address"><?php echo Helpers::get_icon('copy'); ?></div>
						</label>
					</div>

					<div class="cp-staff-email-form--email-from">
						<label>
							<?php _e( 'From:', 'cp-staff' ); ?>	
							<input type="text" name="email-from" class="staff-email-from"/>
						</label>
					</div>

					<div class="cp-staff-email-form--subject">
						<label>
							<?php _e( 'Email Subject', 'cp-staff' ); ?>	
							<input type="text" name="subject" />
						</label>
					</div>

					<div class="cp-staff-email-form--message">
						<label>
							Message:
							<textarea name="message" rows="3"></textarea>
						</label>
					</div>
					
					<input class="cp-button is-large" type="submit" value="Send" />
				</div>
			</div>
		</div>
		<?php
	}
	
	/** Helper Methods **************************************/

	public function get_default_thumb() {
		return CP_STAFF_PLUGIN_URL . '/app/public/logo512.png';
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
