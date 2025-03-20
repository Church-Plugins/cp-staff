<?php
/**
 * CP Staff archive page template
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/archive.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display staff for a specific department
 * 
 * @param int $department_id Department term ID
 * @return bool True if staff were displayed, false if no staff found
 */
function cp_staff_display_department($department_id, $department_name, $heading_level = 3) {
	$staff = new WP_Query(
		array(
			'post_type'      => cp_staff()->setup->post_types->staff->post_type,
			'posts_per_page' => 999,
			'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
			'tax_query'      => array(
				array(
					'taxonomy'         => cp_staff()->setup->taxonomies->department->taxonomy,
					'field'            => 'term_id',
					'terms'            => [ $department_id ],
					'include_children' => false,
				),
			),
		)
	);

	if ( !$staff->have_posts() ) {
		return false;
	}
	
	$tag = "h{$heading_level}";
	?>
	<<?php echo $tag; ?> class="cp-staff-department-heading"><?php echo esc_html( $department_name ); ?></<?php echo $tag; ?>>
	<div class="cp-staff-grid cp-staff-department-children cp-staff-department-children--depth-<?php echo absint( $heading_level ); ?>">
		<?php while ( $staff->have_posts() ) : ?>
			<?php $staff->the_post(); ?>
			<?php cp_staff()->templates->get_template_part( 'parts/staff-card' ); ?>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return true;
}

/**
 * Display a department and its children recursively
 *
 * @param int $parent_id Parent department ID (0 for top level)
 * @param int $depth Current depth level
 */
function cp_staff_display_hierarchical_departments($parent_id = 0, $depth = 3) {
	// Get direct child departments
	$departments_args = array(
		'taxonomy'   => cp_staff()->setup->taxonomies->department->taxonomy,
		'hide_empty' => true,
		'parent'     => $parent_id,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	
	// Apply filters to allow customization of department query
	$departments_args = apply_filters( 'cp_staff_departments_args', $departments_args, $parent_id, $depth );
	
	$child_departments = get_terms( $departments_args );

	if ( empty($child_departments) ) {
		return;
	}

	foreach ( $child_departments as $department ) {
		echo '<div class="cp-staff-department-wrapper">';
		// Display staff for this department
		$has_staff = cp_staff_display_department($department->term_id, $department->name, $depth);
		
		// If department has staff, increase the heading level for child departments
		$next_depth = $has_staff ? $depth + 1 : $depth;
		
		// Display child departments (recursively)
		cp_staff_display_hierarchical_departments($department->term_id, $next_depth);
		echo '</div>';
	}
}

?>

<?php if ( is_post_type_archive( cp_staff()->setup->post_types->staff->post_type ) ) : ?>
	<div class="cp-staff-archive--title-wrapper">
		<h1 class="cp-staff-archive--title"><?php echo apply_filters( 'cp_staff_archive_title', cp_staff()->setup->post_types->staff->plural_label ); ?></h1>
	</div>
<?php endif; ?>

<?php 
// Start with top-level departments (parent = 0)
cp_staff_display_hierarchical_departments(0, apply_filters( 'cp_staff_archive_starting_heading_level', 3 ) );