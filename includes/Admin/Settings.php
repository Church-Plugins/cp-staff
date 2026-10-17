<?php

namespace CP_Staff\Admin;

/**
 * Plugin settings
 *
 */
class Settings {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of \CP_Staff\Settings
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Settings ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get a value from the options table
	 *
	 * @param $key
	 * @param $default
	 * @param $group
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public static function get( $key, $default = '', $group = 'cp_staff_main_options' ) {
		$options = get_option( $group, [] );

		if ( isset( $options[ $key ] ) ) {
			$value = $options[ $key ];
		} else {
			$value = $default;
		}

		return apply_filters( 'cpl_settings_get', $value, $key, $group );
	}

	public static function get_staff( $key, $default = '' ) {
		return self::get( $key, $default, 'cp_staff_staff_options' );
	}

	/**
	 * Whether a checkbox setting is enabled.
	 *
	 * A missing value uses $default. Captcha and staff-email protection
	 * historically treated a missing value as on, because CMB2 drops
	 * unchecked checkboxes instead of saving them.
	 *
	 * @param string $key
	 * @param string $default
	 * @param string $group
	 *
	 * @return bool
	 */
	public static function is_on( $key, $default = 'on', $group = 'cp_staff_main_options' ) {
		return self::get( $key, $default, $group ) === 'on';
	}

	/**
	 * Captcha runs only when it is enabled and both reCAPTCHA keys are set.
	 *
	 * The script enqueue and the server-side check must use this same rule.
	 * A secret key on its own must not reject every message.
	 *
	 * @return bool
	 */
	public static function is_captcha_active() {
		if ( ! self::is_on( 'enable_captcha', 'on' ) ) {
			return false;
		}

		$site_key   = self::get( 'captcha_site_key', '' );
		$secret_key = self::get( 'captcha_secret_key', '' );

		return $site_key !== '' && $secret_key !== '';
	}

	/**
	 * Save an explicit off when a default-on checkbox is unchecked.
	 *
	 * CMB2's checkbox sanitizer returns false for an unchecked box, and the
	 * options saver then deletes the key. A missing key is still read as on,
	 * so the box could not be turned off.
	 *
	 * @param mixed $value Submitted field value. Null when the box is unchecked.
	 * @param array $field_args CMB2 field arguments.
	 * @param mixed $field CMB2 field object.
	 *
	 * @return string 'on' or 'off'
	 */
	public static function sanitize_on_off_checkbox( $value, $field_args = array(), $field = null ) {
		return $value === 'on' ? 'on' : 'off';
	}

	/**
	 * Show a default-on checkbox as checked unless it was saved as off.
	 *
	 * CMB2 treats any non-empty value as checked, so the stored off value
	 * has to render as empty. A missing value still displays as checked,
	 * matching the effective on state.
	 *
	 * @param mixed $value Stored field value. Empty when the setting was never saved.
	 * @param array $field_args CMB2 field arguments.
	 * @param mixed $field CMB2 field object.
	 *
	 * @return string 'on' when checked, otherwise an empty string
	 */
	public static function escape_on_off_checkbox( $value, $field_args = array(), $field = null ) {
		return $value === 'off' ? '' : 'on';
	}

	/**
	 * Fill in the historical on state for settings that were never saved.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public static function with_legacy_feature_defaults( array $options ) {
		foreach ( array( 'enable_captcha', 'block_staff_emails' ) as $key ) {
			if ( ! array_key_exists( $key, $options ) ) {
				$options[ $key ] = 'on';
			}
		}

		return $options;
	}

	/**
	 * Class constructor. Add admin hooks and actions
	 *
	 */
	protected function __construct() {
		add_action( 'cmb2_admin_init', [ $this, 'register_main_options_metabox' ] );
		add_action( 'cmb2_save_options_page_fields', 'flush_rewrite_rules' );
	}

	public function register_main_options_metabox() {

		$post_type = cp_staff()->setup->post_types->staff->post_type;

		$this->staff_options();

		/**
		 * Registers main options page menu item and form.
		 */
		$args = array(
			'id'           => 'cp_staff_main_options_page',
			'title'        => 'Settings',
			'object_types' => array( 'options-page' ),
			'option_key'   => 'cp_staff_main_options',
			'tab_group'    => 'cp_staff_staff_options',
			'tab_title'    => 'Advanced',
			'parent_slug'  => 'cp_staff_staff_options',
			'display_cb'   => [ $this, 'options_display_with_tabs'],
		);

		$main_options = new_cmb2_box( $args );

		$main_options->add_field( array(
			'name'    => __( 'Staff click action', 'cp-staff' ),
			'desc'    => __( 'What happens when a user clicks on a staff member card.', 'cp-staff' ),
			'id'      => 'click_action',
			'type'    => 'radio',
			'default' => 'none',
			'options' => array(
				'none'  => __( 'None', 'cp-staff' ),
				'link'  => __( 'Link to single staff page (if content exists for Staff member)', 'cp-staff' ),
				'modal' => __( 'Display popup modal', 'cp-staff' ),
			),
		) );

		$main_options->add_field( array(
			'name'         => __( 'Staff contact modal', 'cp-staff' ),
			'desc'         => __( 'If active, when a staff record has an email and a user clicks on their staff profile, then a contact form will display inside of a modal (in-browser window popup).', 'cp-staff' ),
			'id'           => 'use_email_modal',
			'type'         => 'checkbox',
			'default_cb'   => [ $this, 'default_checked' ]
		) );

		$main_options->add_field( array(
			'name' => __( 'Display staff\'s email address', 'cp-staff' ),
			'desc' => __( 'If checked, the staff\'s email address will be visible inside the contact form', 'cp-staff' ),
			'type' => 'checkbox',
			'id' => 'show_staff_email',
			'attributes' => array(
				'data-conditional-id' => 'use_email_modal',
				'data-conditionl-value' => 'on'
			)
		) );

		$main_options->add_field( array(
			'name' => __( 'Enable staff contact form throttling', 'cp-staff' ),
			'desc' => __( 'Limit the number of submissions an email or IP address can send in a day.', 'cp-staff' ),
			'type' => 'checkbox',
			'id'   => 'throttle_staff_emails'
		) );

		$main_options->add_field( array(
			'name' => __( 'Max submissions per day from same user', 'cp-staff' ),
			'type' => 'select',
			'id'   => 'throttle_amount',
			'options' => $this->range_options(2, 10),
			'default' => '3',
			'attributes' => array(
				'data-conditional-id' => 'throttle_staff_emails',
				'data-conditional-value' => 'on'
			)
		) );

		$main_options->add_field( array(
			'name' => __( 'Prevent staff from sending emails', 'cp-staff' ),
			'description' => __( 'Blocks messages from email addresses that contain the site domain', 'cp-staff' ),
			'type' => 'checkbox',
			'id'   => 'block_staff_emails',
			'sanitization_cb' => array( __CLASS__, 'sanitize_on_off_checkbox' ),
			'escape_cb'       => array( __CLASS__, 'escape_on_off_checkbox' ),
		) );


		$main_options->add_field( array(
			'name' => __( 'Enable captcha on message form', 'cp-staff' ),
			'type' => 'checkbox',
			'id'   => 'enable_captcha',
			'sanitization_cb' => array( __CLASS__, 'sanitize_on_off_checkbox' ),
			'escape_cb'       => array( __CLASS__, 'escape_on_off_checkbox' ),
		) );

		$main_options->add_field( array(
			'name' => __( 'Recaptcha site key', 'cp-staff' ),
			'type' => 'text',
			'id'   => 'captcha_site_key',
			'attributes' => array(
				'data-conditional-id' => 'enable_captcha',
				'data-conditional-value' => 'on'
			)
		) );

		$main_options->add_field( array(
			'name' => __( 'Recaptcha secret key', 'cp-staff' ),
			'type' => 'text',
			'id'   => 'captcha_secret_key',
			'attributes' => array(
				'data-conditional-id' => 'enable_captcha',
				'data-conditional-value' => 'on'
			)
		) );

		$main_options->add_field( array(
			'name'         => __( 'From Address', 'cp-staff' ),
			'desc'         => __( 'The from email address to use when sending staff emails. Will use the site admin email if this is blank.', 'cp-staff' ),
			'id'           => 'from_email',
			'type'         => 'text',
		) );

		$main_options->add_field( array(
			'name'         => __( 'From Name', 'cp-staff' ),
			'desc'         => __( 'The from name to use when sending staff emails. Will use the site title if this is blank.', 'cp-staff' ),
			'id'           => 'from_name',
			'type'         => 'text',
		) );

		$this->license_fields();
	}

	protected function staff_options() {
		/**
		 * Registers secondary options page, and set main item as parent.
		 */
		$args = array(
			'id'           => 'cp_staff_staff_options_page',
			'title'        => 'Settings',
			'object_types' => array( 'options-page' ),
			'option_key'   => 'cp_staff_staff_options',
			'tab_group'    => 'cp_staff_staff_options',
			'tab_title'    => cp_staff()->setup->post_types->staff->plural_label,
			'parent_slug'  => 'edit.php?post_type=' . cp_staff()->setup->post_types->staff->post_type,
			'display_cb'   => [ $this, 'options_display_with_tabs' ],
		);

		$options = new_cmb2_box( $args );

		$options->add_field( array(
			'name' => __( 'Labels' ),
			'id'   => 'labels',
			'type' => 'title',
		) );

		$options->add_field( array(
			'name'    => __( 'Singular Label', 'cp-staff' ),
			'id'      => 'singular_label',
			'type'    => 'text',
			'default' => cp_staff()->setup->post_types->staff->single_label,
		) );

		$options->add_field( array(
			'name'    => __( 'Plural Label', 'cp-staff' ),
			'id'      => 'plural_label',
			'desc'    => __( 'Caution: changing this value will also adjust the url structure and may affect your SEO.', 'cp-staff' ),
			'type'    => 'text',
			'default' => cp_staff()->setup->post_types->staff->plural_label,
		) );

		$options->add_field( array(
			'name'    => __( 'Disable Archive Page', 'cp-staff' ),
			'id'      => 'disable_archive',
			'desc'    => sprintf( __( 'Check this box to disable the /%s/ archive page. Use this option if you want to use the %s shortcode on a page that you create.', 'cp-staff' ), strtolower( cp_staff()->setup->post_types->staff->plural_label ), cp_staff()->setup->post_types->staff->single_label ),
			'type'    => 'checkbox',
		) );

	}


	/**
	 * Setting a checkbox to be on by default doesn't work in CMB2, this is a way to get around that
	 */
	public function default_checked() {
		return isset( $_GET['page'] ) ? '' : true;
	}

	protected function license_fields() {
		$license = new \ChurchPlugins\Setup\Admin\License( 'cp_staff_license', 444, CP_STAFF_STORE_URL, CP_STAFF_PLUGIN_FILE, get_admin_url( null, 'admin.php?page=cp_staff_license' ) );

		/**
		 * Registers settings page, and set main item as parent.
		 */
		$args = array(
			'id'           => 'cp_staff_license_options_page',
			'title'        => 'CP Staff Settings',
			'object_types' => array( 'options-page' ),
			'option_key'   => 'cp_staff_license',
			'parent_slug'  => 'cp_staff_staff_options',
			'tab_group'    => 'cp_staff_staff_options',
			'tab_title'    => 'License',
			'display_cb'   => [ $this, 'options_display_with_tabs' ]
		);

		$options = new_cmb2_box( $args );
		$license->license_field( $options );
	}

	protected function range_options( $min, $max ) {
		$range = array();

		for ( $val = $min; $val <= $max; $val++ ) {
			$val_str = strval( $val );
			$range[$val_str] = $val_str;
		}

		return $range;
	}

	/**
	 * A CMB2 options-page display callback override which adds tab navigation among
	 * CMB2 options pages which share this same display callback.
	 *
	 * @param \CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
	 */
	public function options_display_with_tabs( $cmb_options ) {
		$tabs = $this->options_page_tabs( $cmb_options );
		?>
		<div class="wrap cmb2-options-page option-<?php echo $cmb_options->option_key; ?>">
			<?php if ( get_admin_page_title() ) : ?>
				<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
			<?php endif; ?>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $option_key => $tab_title ) : ?>
					<a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>"
					   href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
				<?php endforeach; ?>
			</h2>
			<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST"
				  id="<?php echo $cmb_options->cmb->cmb_id; ?>" enctype="multipart/form-data"
				  encoding="multipart/form-data">
				<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
				<?php $cmb_options->options_page_metabox(); ?>
				<?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Gets navigation tabs array for CMB2 options pages which share the given
	 * display_cb param.
	 *
	 * @param \CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
	 *
	 * @return array Array of tab information.
	 */
	public function options_page_tabs( $cmb_options ) {
		$tab_group = $cmb_options->cmb->prop( 'tab_group' );
		$tabs      = array();

		foreach ( \CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
			if ( $tab_group === $cmb->prop( 'tab_group' ) ) {
				$tabs[ $cmb->options_page_keys()[0] ] = $cmb->prop( 'tab_title' )
					? $cmb->prop( 'tab_title' )
					: $cmb->prop( 'title' );
			}
		}

		return $tabs;
	}


}
