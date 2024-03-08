<?php
/**
 * Template for a single Staff member
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/parts/staff-card.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$static      = boolval( isset( $args['static'] ) ? $args['static'] : false );
$staff_title = get_post_meta( get_the_ID(), 'title', true );
$staff_email = get_post_meta( get_the_ID(), 'email', true );
$staff_phone = get_post_meta( get_the_ID(), 'phone', true );
?>

<div class="cp-staff-card cp_staff type-cp_staff">
	<?php cp_staff()->staff_meta(); ?>
	<div class="cp-staff-card--image-wrapper">
		<a href="<?php echo esc_url( get_permalink() ); ?>">
			<?php
			echo get_the_post_thumbnail(
				get_the_ID(),
				'medium',
				array(
					'class' => 'cp-staff-card--image',
					/* translators: %s: staff member's name */
					'alt'   => sprintf( __( 'Photo of %s', 'cp-staff' ), get_the_title() ),
				)
			);
			?>
		</a>
		<div class="cp-staff-card--image-overlay">
			<?php if ( ! $static && ! empty( $staff_email ) ) : ?>
				<a href="javascript:void(0);" class="cp-staff-card--action-icon" data-action="email">
					<i data-feather="mail"></i>
				</a>
			<?php endif; ?>
			<?php if ( ! $static && ! empty( $staff_phone ) ) : ?>
				<a href="<?php echo 'tel:' . esc_attr( $staff_phone ); ?>" class="cp-staff-card--action-icon" data-action="phone">
					<i data-feather="phone"></i>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( ! $static ) echo '<a class="cp-staff-card--name-link" href="' . esc_url( get_permalink() ) . '">'; ?>
	<h4 class="cp-staff-card--name">
		<?php the_title(); ?>
	</h4>
	<?php if ( ! $static ) echo '</a>'; ?>
	<div class="cp-staff-card--role">
		<?php echo esc_html( $staff_title ); ?>
	</div>
	<?php if ( ! empty( get_the_content() ) ) : ?>
		<?php cp_staff()->templates->get_template_part( 'parts/info-modal' ); ?>
	<?php endif; ?>
</div>