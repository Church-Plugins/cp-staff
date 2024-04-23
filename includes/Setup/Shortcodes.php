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

		$static = 'true' === $atts['static'];

		$query_args = array(
			'post_type'      => cp_staff()->setup->post_types->staff->post_type,
			'posts_per_page' => 999,
		);

		$term     = false;
		$children = [];

		if ( ! empty( $atts['department'] ) ) {
			$term = get_term_by( 'slug', $atts['department'], cp_staff()->setup->taxonomies->department->taxonomy );

			// check if term has children
			$children = get_term_children( $term->term_id, cp_staff()->setup->taxonomies->department->taxonomy );
		}

		// if there are no children, get all staff that have this department
		if ( $term ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy'         => cp_staff()->setup->taxonomies->department->taxonomy,
					'field'            => 'term_id',
					'terms'            => [ $term->term_id ],
					'include_children' => false,
				),
			);
		}

		// groupings of staff
		$groupings = [];

		// add top-level grouping
		$groupings[] = [
			'title' => empty( $children ) ? false : $term->name,
			'posts' => get_posts( $query_args ),
		];

		foreach ( $children as $department ) {
			$posts = get_posts(
				[
					'post_type'      => cp_staff()->setup->post_types->staff->post_type,
					'posts_per_page' => 6,
					'tax_query'      => [
						[
							'taxonomy'         => cp_staff()->setup->taxonomies->department->taxonomy,
							'field'            => 'term_id',
							'terms'            => [ $department ],
							'include_children' => false,
						],
					],
				]
			);

			$groupings[] = [
				'title' => get_term( $department )->name,
				'posts' => $posts,
			];
		}

		ob_start();

		echo '<div class="cp-staff-list">';

		foreach ( $groupings as $group ) {
			if ( empty( $group['posts'] ) ) {
				continue;
			}

			if ( ! empty( $group['title'] ) ) {
				echo '<h3 class="cp-staff-department-heading">' . esc_html( $group['title'] ) . '</h3>';
			}

			echo '<div class="cp-staff-grid">';

			global $post;

			foreach ( $group['posts'] as $post ) {
				setup_postdata( $post );
				cp_staff()->templates->get_template_part( 'parts/staff-card', array( 'static' => $static ) );
			}

			echo '</div>';
		}

		echo '</div>';

		wp_reset_postdata();

		return ob_get_clean();
	}
}
