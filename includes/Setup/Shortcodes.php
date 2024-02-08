<?php
/**
 * CP Staff shortcodes.
 *
 * @package cp-staff
 */

namespace CP_Staff\Setup;

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
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function staff_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'department' => '',
				'static'     => false,
			),
			$atts,
			'cp_staff_list'
		);

		$static = boolval( $atts['static'] === 'true' );

		$query_args = array(
			'post_type' => cp_staff()->setup->post_types->staff->post_type,
			'orderby'   => 'ID',
			'order'     => 'ASC',
		);

		$term = false;

		if ( ! empty( $atts['department'] ) ) {
			$term = get_term_by( 'slug', $atts['department'], cp_staff()->setup->taxonomies->department->taxonomy );
		}

		if ( $term ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy'         => cp_staff()->setup->taxonomies->department->taxonomy,
					'field'            => 'term_id',
					'terms'            => [ $term->term_id ],
					'include_children' => true,
				),
			);
		}

		$staff_query = new \WP_Query( $query_args );

		ob_start();

		echo '<div class="cp-staff-grid">';
		if ( $staff_query->have_posts() ) {
			while ( $staff_query->have_posts() ) {
				$staff_query->the_post();
				cp_staff()->templates->get_template_part( 'parts/staff-card', array( 'static' => $static ) );
			}
		} else {
			echo '<p>' . esc_html__( 'No staff found', 'cp-staff' ) . '</p>';
		}

		echo '</div>';

		wp_reset_postdata();

		return ob_get_clean();
	}
}
