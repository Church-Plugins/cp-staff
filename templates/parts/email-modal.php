<?php
/**
 * Displays the email form modal content.
 *
 * Override this template in your own theme by creating a file at [your-theme]/cp-staff/parts/email-modal.php
 *
 * @package cp-staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CP_Staff\Admin\Settings;

$is_hidden_att = Settings::get( 'show_staff_email', 'off' ) === 'on' ? '' : 'hidden';

?>
<div id="cp-staff-email-modal-template" style="display:none;">
	<div class="cp-staff-email-modal">
		<form class="cp-staff-email-form"
				action="<?php echo esc_url( add_query_arg( 'cp_action', 'cp_staff_send_email', admin_url( 'admin-ajax.php' ) ) ); ?>"
				method="post" enctype="multipart/form-data">

			<?php wp_nonce_field( 'cp_staff_send_email', 'cp_staff_send_email_nonce' ); ?>

			<div class="cp-staff-email-form--name">
				<h4><?php esc_html_e( 'Send a message to', 'cp-staff' ); ?> <span class="staff-name"></span></h4>
			</div>

			<div class="cp-staff-email-form--email-to" <?php echo esc_html( $is_hidden_att ); ?>>
				<label>
					<?php esc_html_e( 'To:', 'cp-staff' ); ?>
					<input type="hidden" name="email-to" class="staff-email-to" />
					<div class="cp-staff--input-wrapper">
						<input type="text" disabled="disabled" class="staff-email-to"/>
						<div class="staff-copy-email"
							title="Copy email address"><?php echo \ChurchPlugins\Helpers::get_icon( 'copy' ); // phpcs:ignore ?></div>
					</div>
				</label>
			</div>

			<div class="cp-staff-email-form--name">
				<label>
					<?php esc_html_e( 'Your Full Name:', 'cp-staff' ); ?>
					<input type="text" name="from-name" />
				</label>
			</div>

			<div class="cp-staff-email-form--email-from">
				<label>
					<?php esc_html_e( 'Your Email:', 'cp-staff' ); ?>
					<input type="text" name="email-from" class="staff-email-from"/>
				</label>
			</div>

			<div class='cp-staff-email-form--email-verify'>
				<label>
					<?php esc_html_e( 'Email Verify', 'cp-staff' ); ?>
					<input type='text' name='email-verify' tabindex="-1" autocomplete="off">
				</label>
			</div>

			<div class="cp-staff-email-form--subject">
				<label>
					<?php esc_html_e( 'Email Subject:', 'cp-staff' ); ?>
					<input type="text" name="subject"/>
				</label>
			</div>

			<div class="cp-staff-email-form--message">
				<label>
					<?php esc_html_e( 'Email Message:', 'cp-staff' ); ?>
					<textarea name="message" rows="3"></textarea>
				</label>
			</div>

			<input class="cp-button is-large" type="submit" value="Send"/>

		</form>
	</div>
</div>
