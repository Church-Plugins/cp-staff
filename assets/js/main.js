

(function($){
	'use strict'

	$(document).ready(function() {

		let $staff = $('.cp_staff');

		if ( ! $staff.length ) {
			return;
		}

		$staff.each(function() {
			let $this = $(this);
			let $details = $this.find('[itemprop=staffDetails]')

			if ( ! $details.length ) {
				return;
			}

			let data = $details.data('details' );

			if ( undefined === data.name || undefined === data.email || '' === data.email ) {
				return;
			}

			data.email = atob( data.email );

			$this.addClass( 'cp-staff--has-email' );

			$this.on( 'click', '.cp-staff-card--mail-icon', function(e) {
				e.preventDefault();

				let $modalElem = $('.staff-modal-' + data.id);

				if ( ! $modalElem.length ) {
					$modalElem = $('#cp-staff-email-modal-template > div').clone();
					$modalElem.addClass( 'staff-modal-' + data.id );
				}

				$modalElem.find('.staff-name').html(data.name);
				$modalElem.find('.staff-email-to').val(data.email);

				$modalElem.dialog({
					title        : '',
					dialogClass  : 'cp-staff--email-modal-popup',
					autoOpen     : false,
					draggable    : false,
					width        : 500,
					modal        : true,
					resizable    : false,
					closeOnEscape: true,
					position     : {
						my: 'center',
						at: 'center',
						of: window
					},
					open         : function (event, ui) {
						// close dialog by clicking the overlay behind it
						$('.ui-widget-overlay').bind('click', function () {
							$modalElem.dialog('close');
						});

						$(event.target).dialog('widget')
							.css({position: 'fixed'})
							.position({my: 'center', at: 'center', of: window});
					},

				});

				$modalElem.dialog('open');

				$modalElem.on('click', '.staff-copy-email', function (e) {
					let response = navigator.clipboard.writeText(data.email);
					response.finally(() => $(this).addClass('is-copied'));
				});

				CP_Staff_Mail.init($modalElem);

			} );
		});

		$staff.each(function() {
			const $infoModal = $(this).find('.cp-staff-info-modal');

			if ( ! $infoModal.length ) {
				return;
			}

			$infoModal.dialog({
				title        : '',
				dialogClass  : 'cp-staff-info-modal--popup',
				autoOpen     : false,
				draggable    : false,
				width        : 'min(90%, 1250px)',
				modal        : true,
				resizable    : false,
				closeOnEscape: true,
				position     : {
					my: 'center',
					at: 'center',
					of: window
				}
			})

			$(this).on('click', 'a:not(.cp-staff-card--mail-icon)', function(e) {
				e.preventDefault();
				$infoModal.dialog('open');
			});

			$infoModal.find('.cp-staff-info-modal--close-btn').on('click', function(e) {
				e.preventDefault();
				$infoModal.dialog('close');
			})
		});

	} );

})(jQuery);

window.CP_Staff_Mail = {
	$modal: false,
	$form : false,

	init: function ($modal) {
		this.$modal = $modal;
		this.submit();
	},

	submit: function () {
		const self = this;

		this.$form = this.$modal.find('.cp-staff-email-form');

		this.$form.unbind('submit')
		this.$form.on('submit', async function(e) {
			e.preventDefault()

			const form = self.$form

			await self.before_submit( form )

			form.ajaxSubmit({
				success     : self.success,
				complete    : self.complete,
				dataType    : 'json',
				error       : self.error,
			})
		})

	},

	before_submit: function (form) {
		form.find('.notice-wrap').remove();
		form.append('<div class="notice-wrap"><div class="update success"><p>Sending message.</p></div>');

		if(!window.recaptchaSiteKey) {
			return true
		}

		return new Promise((resolve) => {
			grecaptcha.ready(() => {
				grecaptcha.execute(window.recaptchaSiteKey, { action: 'contact_staff' } ).then((token) => {
					this.$form.prepend('<input type="hidden" name="token" value="' + token + '">');
					this.$form.prepend('<input type="hidden" name="action" value="contact_staff">');
					resolve(true)
				})
			})
		})
	},

	success: function (responseText, statusText, xhr, form) {},

	complete: function (xhr) {
		const self = jQuery(this),
			response = jQuery.parseJSON(xhr.responseText);

		if (response.success) {
			CP_Staff_Mail.$form.find('.notice-wrap').html('<div class="update success"><p>' + response.data.success + '</p></div>');
			let modalElem = CP_Staff_Mail.$form.parents('.ui-dialog-content');

			if (undefined !== modalElem.dialog( "instance" )) {
				setTimeout(() => modalElem.dialog('close'), 3000 );
			}
		} else {
			CP_Staff_Mail.error(xhr);
		}
	},

	error: function (xhr) {
		// Something went wrong. This will display error on form

		const response = jQuery.parseJSON(xhr.responseText);
		const import_form = CP_Staff_Mail.$form;
		const notice_wrap = import_form.find('.notice-wrap');

		if (response.data.error) {
			notice_wrap.html('<div class="update error"><p>' + response.data.error + '</p></div>');
		} else {
			notice_wrap.remove();
		}
	},

};
