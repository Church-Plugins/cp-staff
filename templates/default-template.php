<?php
/**
 * Default CP Staff Content Template
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/default-template.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Allows filtering the classes for the main element.
 *
 * @param array<string> $classes An (unindexed) array of classes to apply.
 */
$classes = apply_filters( 'cp_staff_default_template_classes', array( 'cp-staff-pg-template', 'cp-pg-template' ) );

get_header();

/**
 * Provides an action that allows for the injection of HTML at the top of the template after the header.
 */
do_action( 'cp_staff_default_template_after_header' );
?>
<main id="cp-pg-template" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php echo apply_filters( 'cp_staff_default_template_before_content', '' ); ?>
	<?php cp_staff()->templates->get_view(); ?>
	<?php echo apply_filters( 'cp_staff_default_template_after_content', '' ); ?>
	<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</main> <!-- #cp-staff-pg-template -->
<?php

/**
 * Provides an action that allows for the injections of HTML at the bottom of the template before the footer.
 */
do_action( 'cp_staff_default_template_before_footer' );

get_footer();
