<?php
/**
 * Adds an email modal to the single staff template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/parts/info-modal.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="cp-staff-info-modal" style="display: none;">
	<div class="cp-staff-info-modal--close-btn" tabindex="0" alt="<?php esc_attr_e( 'Close modal', 'cp-staff' ); ?>">
		<span class="material-icons">close</span>
	</div>
	<?php cp_staff()->templates->get_template_part( 'single' ); ?>
</div>
