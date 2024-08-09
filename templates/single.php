<?php
/**
 * Single staff template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/single.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$staff_title  = get_post_meta( get_the_ID(), 'title', true );
$social_links = get_post_meta( get_the_ID(), 'social', true );
$social_links = is_array( $social_links ) ? $social_links : array();
$email        = CP_Staff\Admin\Settings::get( 'use_email_modal', false ) ? get_post_meta( get_the_ID(), 'email', true ) : false;
$image        = get_post_meta( get_the_ID(), 'alt_image_id', true );
$image        = empty( $image ) ? get_the_post_thumbnail_url( get_the_ID(), 'large' ) : wp_get_attachment_image_url( $image, 'large' );


?>

<div class="cp-staff-single cp_staff">
	<?php cp_staff()->staff_meta(); ?>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="cp-staff-single--image-wrapper">
			<?php echo sprintf( '<img src="%s" class="cp-staff-single--image" alt="%s">', esc_url( $image ), esc_attr( get_the_title() ) ); ?>
		</div>
	<?php endif; ?>
	<div class="cp-staff-single--content">
		<h1 class="cp-staff-single--name"><?php the_title(); ?></h1>
		<div class="cp-staff-single--role">
			<?php echo esc_html( $staff_title ); ?>
		</div>
		<div class="cp-staff-single--bio">
			<?php the_content(); ?>
		</div>
		<div class="cp-staff-single--social-links">
			<?php if ( ! empty( $email ) ) : ?>
				<a href="javascript:void(0);" class="cp-staff--action-icon" data-action="email">
					<i data-feather="mail"></i>
				</a>
			<?php endif; ?>
			<?php foreach ( $social_links as $link ) : ?>
				<a href="<?php echo esc_url( $link['url'] ); ?>" class="cp-staff-single--social-link">
					<?php echo \ChurchPlugins\Helpers::get_icon( $link['network'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>