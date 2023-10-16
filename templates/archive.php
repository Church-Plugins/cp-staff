<?php
/**
 * CP Staff archive page template
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/archive.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


$departments = get_terms(
	array(
		'taxonomy'   => cp_staff()->setup->taxonomies->department->taxonomy,
		'hide_empty' => true,
	)
);

?>

<div class="cp-staff-archive--title-wrapper">
	<h1 class="cp-staff-archive--title"><?php esc_html_e( 'Staff', 'cp-staff' ); ?></h1>
</div>

<?php

foreach ( $departments as $department ) {
	$staff = new WP_Query(
		array(
			'post_type'      => cp_staff()->setup->post_types->staff->post_type,
			'posts_per_page' => -1,
			'tax_query'      => array(
				array(
					'taxonomy' => cp_staff()->setup->taxonomies->department->taxonomy,
					'field'    => 'slug',
					'terms'    => $department->slug,
				),
			),
		)
	);

	if ( $staff->have_posts() ) {
		?>
		<h3 class="cp-staff-department-heading"><?php echo esc_html( $department->name ); ?></h3>
		<div class="cp-staff-grid">
			<?php while ( $staff->have_posts() ) : ?>
				<?php $staff->the_post(); ?>
				<?php cp_staff()->templates->get_template_part( 'parts/staff-card' ); ?>
			<?php endwhile; ?>
		</div>
		<?php
	}
}

wp_reset_postdata();
