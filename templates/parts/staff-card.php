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

$click_action = \CP_Staff\Admin\Settings::get( 'click_action', 'none' );
$email_modal  = \CP_Staff\Admin\Settings::get( 'use_email_modal', false );

$static       = boolval( isset( $args['static'] ) ? $args['static'] : false );
$staff_title  = get_post_meta( get_the_ID(), 'title', true );
$staff_email  = get_post_meta( get_the_ID(), 'email', true );
$staff_phone  = get_post_meta( get_the_ID(), 'phone', true );
$staff_social = get_post_meta( get_the_ID(), 'social', true );
?>

<div class="cp-staff-card cp_staff type-cp_staff click-action-<?php echo $click_action; ?>">
	<?php cp_staff()->staff_meta(); ?>
	<div class="cp-staff-card--image-wrapper">
		<?php if ( 'none' !== $click_action ) : echo '<a href="' . esc_url( get_permalink() ) . '">'; endif; ?>
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
		<?php if ( 'none' !== $click_action ) : echo '</a>'; endif; ?>
		<div class="cp-staff-card--image-overlay">
				<?php if ( ! $static ) : ?>
					<div class="cp-staff-card--social-links">
						<?php if ( ! empty( $staff_email ) && $email_modal ) : ?>
							<a href="javascript:void(0);" class="cp-staff-card--action-icon" data-action="email">
								<i data-feather="mail"></i>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $staff_phone ) ) : ?>
							<a href="<?php echo 'tel:' . esc_attr( $staff_phone ); ?>" class="cp-staff-card--action-icon" data-action="phone">
								<i data-feather="phone"></i>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $staff_social ) && is_array( $staff_social ) ) : ?>
							<?php foreach ( $staff_social as $social_link ) : ?>
								<a href="<?php echo esc_url( $social_link['url'] ); ?>" class="cp-staff-card--action-icon" data-action="<?php echo esc_attr( $social_link['network'] ); ?>">
									<?php echo \ChurchPlugins\Helpers::get_icon( $social_link['network'] ); ?>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
		</div>
	</div>
	<?php if ( 'none' !== $click_action ) echo '<a class="cp-staff-card--name-link" href="' . esc_url( get_permalink() ) . '">'; ?>
	<h4 class="cp-staff-card--name">
		<?php the_title(); ?>
	</h4>
	<?php if ( 'none' !== $click_action ) echo '</a>'; ?>
	<div class="cp-staff-card--role">
		<?php echo esc_html( $staff_title ); ?>
	</div>
	<?php if ( ! empty( get_the_content() && 'modal' === $click_action ) ) : ?>
		<?php cp_staff()->templates->get_template_part( 'parts/info-modal' ); ?>
	<?php endif; ?>
</div>
