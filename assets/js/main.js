(function($){
	'use strict'
	
	$(document).ready(function() {

		
		
		$('.cp_staff').each(function() {
			let $this = $(this);
			let $details = $this.find('[itemprop=staffDetails]')
			
			if ( ! $details.length ) {
				return;
			}
			
			let data = $details.data('details' );
			
			if ( undefined === data.name || undefined === data.email ) {
				return;
			}
			
			data.email = atob( data.email );
			
			$this.addClass( 'cp-staff--has-email' );
			
			$this.on( 'click', 'a', function(e) {
				e.preventDefault();
				
				let $modalElem = $('#cp-staff-email-modal-template > div').clone();
				
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
			} );
		});
		
		
		$('.cp_staff a').on('click', function(e) {
			e.preventDefault();
		});

	} );
	
	
})(jQuery);