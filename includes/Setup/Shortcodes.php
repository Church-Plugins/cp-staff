<?php
/**
 * CP Staff shortcodes.
 *
 * @package cp-staff
 */

namespace CP_Staff\Setup;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Shortcodes class
 *
 * @since 1.2.0
 */
class Shortcodes {

	/**
	 * Class instance
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Get instance of class
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Shortcodes constructor.
	 */
	public function __construct() {
		add_shortcode( 'cp_staff_list', array( $this, 'staff_list' ) );
		add_shortcode( 'cp_staff_archive', array( $this, 'staff_archive' ) );
	}

	/**
	 * Staff archive shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function staff_archive( $atts ) {
		ob_start();
		cp_staff()->templates->get_template_part( 'archive' );
		return ob_get_clean();
	}

	/**
	 * Staff list shortcode
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Added support for filtering by taxonomies.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function staff_list( $atts ) {
		// get associated taxonomies
		$taxonomies = get_object_taxonomies( cp_staff()->setup->post_types->staff->post_type );

		$allowed_atts = array(
			'static' => false
		);

		foreach ( $taxonomies as $taxonomy ) {
			$allowed_atts[$taxonomy] = '';
			$allowed_atts["exclude_$taxonomy"] = '';
		}

		// backward compatibility width < 1.2.0
		if ( isset( $atts['department'] ) ) {
			$atts['cp_department'] = $atts['department'];
			unset( $atts['department'] );
		}

		$atts = shortcode_atts( $allowed_atts, $atts, 'cp_staff_list' );

		$static = 'true' === $atts['static'];

		$tax_query = array( 'relation' => 'AND' );

		// build tax query from shortcode attributes
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! empty( $atts[$taxonomy] ) ) {
				$tax_query[] = array(
					'taxonomy'         => $taxonomy,
					'field'            => 'slug',
					'terms'            => explode( ',', $atts[$taxonomy] ),
					'include_children' => false,
				);
			}

			if ( ! empty( $atts["exclude_$taxonomy"] ) ) {
				$tax_query[] = array(
					'taxonomy'         => $taxonomy,
					'field'            => 'slug',
					'terms'            => explode( ',', $atts["exclude_$taxonomy"] ),
					'operator'         => 'NOT IN',
					'include_children' => false,
				);
			}
		}

		$query_args = array(
			'post_type'      => cp_staff()->setup->post_types->staff->post_type,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => 999,
		);

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		/**
		 * Filter the query arguments for the staff list shortcode.
		 *
		 * @since 1.2.0
		 * @param array $query_args Query arguments.
		 * @param array $atts Shortcode attributes.
		 */
		$query_args = apply_filters( 'cp_staff_list_query_args', $query_args, $atts );

		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return '';
		}

		ob_start();

		echo '<div class="cp-staff-grid">';

		while ( $query->have_posts() ) {
			$query->the_post();
			cp_staff()->templates->get_template_part( 'parts/staff-card', array( 'static' => $static ) );
		}

		wp_reset_postdata();

		return ob_get_clean();
	}
}
