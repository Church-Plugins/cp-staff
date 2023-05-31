<?php

namespace CP_Staff\Admin;

use function PHPSTORM_META\map;

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
		return self::get( $key, $default, 'cp_staff_options' );
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

		/**
		 * Registers main options page menu item and form.
		 */
		$args = array(
			'id'           => 'cp_staff_main_options_page',
			'title'        => 'Settings',
			'object_types' => array( 'options-page' ),
			'option_key'   => 'cp_staff_main_options',
			'tab_group'    => 'cp_staff_main_options',
			'tab_title'    => 'Main',
			'parent_slug'  => 'edit.php?post_type=' . $post_type,
			'display_cb'   => [ $this, 'options_display_with_tabs'],
		);

		$main_options = new_cmb2_box( $args );

		$main_options->add_field( array(
			'name'         => __( 'Staff contact modal', 'cp-staff' ),
			'desc'         => __( 'If active, when a staff record has an email and a user clicks on their staff profile, then a contact form will display inside of a modal (in-browser window popup).', 'cp-staff' ),
			'id'           => 'use_email_modal',
			'type'         => 'checkbox',
			'default'      => 'on'
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
			'desc' => __( 'Will prevent users and bots from sending large amounts of emails', 'cp-staff' ),
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
			'parent_slug'  => 'cp_staff_main_options',
			'tab_group'    => 'cp_staff_main_options',
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
