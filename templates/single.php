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
?>

<div class="cp-staff-single">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="cp-staff-single--image-wrapper">
			<?php the_post_thumbnail( 'medium', array( 'class' => 'cp-staff-single--image' ) ); ?>
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
		<?php if ( ! empty( $social_links ) ) : ?>
			<div class="cp-staff-single--social-links">
				<?php foreach ( $social_links as $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ); ?>" class="cp-staff-single--social-link">
						<?php echo \ChurchPlugins\Helpers::get_icon( $link['network'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>