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

$staff_title = get_post_meta( get_the_ID(), 'title', true );

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
	</div>
</div>