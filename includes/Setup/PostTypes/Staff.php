<?php
namespace CP_Staff\Setup\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use ChurchPlugins\Setup\Tables\SourceMeta;
use CP_Staff\Admin\Settings;

use ChurchPlugins\Setup\PostTypes\PostType;

/**
 * Setup for custom post type: Speaker
 *
 * @author costmo
 * @since 1.0
 */
class Staff extends PostType {
	
	/**
	 * Child class constructor. Punts to the parent.
	 *
	 * @author costmo
	 */
	protected function __construct() {
		$this->post_type = "cp_staff";

		$this->single_label = apply_filters( "cploc_single_{$this->post_type}_label", Settings::get_staff( 'singular_label', 'Staff' ) );
		$this->plural_label = apply_filters( "cploc_plural_{$this->post_type}_label", Settings::get_staff( 'plural_label', 'Staff' ) );

		parent::__construct();
	}

	public function add_actions() {
		add_filter( 'enter_title_here', [ $this, 'add_title' ], 10, 2 );
		add_filter( 'cp_location_taxonomy_types', [ $this, 'location_tax' ] );
		parent::add_actions();
	}

	/**
	 * Update title placeholder in edit page 
	 * 
	 * @param $title
	 * @param $post
	 *
	 * @return string|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function add_title( $title, $post ) {
		if ( get_post_type( $post ) != $this->post_type ) {
			return $title;
		}
		
		return __( 'Add name', 'cp-staff' );
	}

	/**
	 * Add Staff to locations taxonomy if it exists
	 * 
	 * @param $types
	 *
	 * @return mixed
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function location_tax( $types ) {
		$types[] = $this->post_type;
		return $types;
	}

	/**
	 * Get the slug for this taxonomy
	 * 
	 * @return false|mixed
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_slug() {
		if ( ! $type = get_post_type_object( $this->post_type ) ) {
			return false;
		}
		
		return $type->rewrite['slug'];
	}	
	
	/**
	 * Setup arguments for this CPT
	 *
	 * @return array
	 * @author costmo
	 */
	public function get_args() {
		$args               = parent::get_args();
		$args['menu_icon']  = apply_filters( "{$this->post_type}_icon", 'dashicons-id' );
		$args['has_archive'] = false;
		$args['supports'][] = 'page-attributes';
		return $args;
	}
	
	public function register_metaboxes() {
		$this->meta_details();
	}

	protected function meta_details() {
		$cmb = new_cmb2_box( [
			'id' => 'staff_meta',
			'title' => $this->single_label . ' ' . __( 'Details', 'cp-staff' ),
			'object_types' => [ $this->post_type ],
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true,
		] );

		$cmb->add_field( [
			'name' => __( 'Title', 'cp-staff' ),
			'desc' => __( 'The title for this staff member.', 'cp-staff' ),
			'id'   => 'title',
			'type' => 'text',
		] );

		$cmb->add_field( [
			'name' => __( 'Email', 'cp-staff' ),
			'desc' => __( 'The email address for this staff member.', 'cp-staff' ),
			'id'   => 'email',
			'type' => 'text_email',
		] );


		$cmb->add_field( [
			'name' => __( 'LinkedIn URL', 'cp-staff' ),
			'desc' => __( 'The URL to this staff member\'s LinkedIn profile.', 'cp-staff' ),
			'id'   => 'linkedin_url',
			'type' => 'text_url',
		] );

		$cmb->add_field( [
			'name' => __( 'Acronyms', 'cp-staff' ),
			'desc' => __( 'Staff member acronyms.', 'cp-staff' ),
			'id'   => 'acronyms',
			'type' => 'text',
		] );
	}
	
}
