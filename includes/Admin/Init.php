<?php

namespace CP_Staff\Admin;

/**
 * Admin-only plugin initialization
 */
class Init {

	/**
	 * @var Init
	 */
	protected static $_instance;

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
	 * Class constructor
	 *
	 */
	protected function __construct() {
		$this->includes();
		$this->actions();
	}

	/**
	 * Admin init includes
	 *
	 * @return void
	 */
	protected function includes() {
		Settings::get_instance();
	}

	/**
	 * Admin init actions
	 *
	 * @return void
	 */
	protected function actions() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/** Actions ***************************************************/

	function enqueue_scripts() {
		wp_enqueue_script( 
			'cmb2_conditional_logic', 
			plugins_url( 'assets/js/cmb2-conditional-logic.js', plugin_dir_path( __DIR__ ) ),
			array( 'jquery' )
		);
	}

}
