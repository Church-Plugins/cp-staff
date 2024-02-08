<?php
/**
 * Template for a single Staff member
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/parts/staff-card.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$static      = boolval( isset( $args['static'] ) ? $args['static'] : false );
$staff_title = get_post_meta( get_the_ID(), 'title', true );
?>

<div class="cp-staff-card cp_staff type-cp_staff">
	<?php cp_staff()->staff_meta(); ?>
	<div class="cp-staff-card--image-wrapper">
		<?php echo get_the_post_thumbnail( get_the_ID(), 'medium', array( 'class' => 'cp-staff-card--image' ) ); ?>
		<div class="cp-staff-card--image-overlay"></div>
		<?php if ( ! $static ) : ?>
			<a href="<?php echo esc_url( get_permalink() ); ?>" class="cp-staff-card--mail-icon">
				<i data-feather="mail"></i>
			</a>
		<?php endif; ?>
	</div>
	<?php if ( ! $static ) echo '<a class="cp-staff-card--name-link" href="' . esc_url( get_permalink() ) . '">'; ?>
	<h4 class="cp-staff-card--name">
		<?php the_title(); ?>
	</h4>
	<?php if ( ! $static ) echo '</a>'; ?>
	<div class="cp-staff-card--role">
		<?php echo esc_html( $staff_title ); ?>
	</div>
</div>
