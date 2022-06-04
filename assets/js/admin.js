(function( $ ) {

	function runConditionalFieldsLogic () {
		var header_type 	= $('#wa_notifier_header_type').val();
		var header_text 	= $('#wa_notifier_header_text').val() || 'Header text here';
		var body_text 		= $('#wa_notifier_body_text').val() || 'Body text here';
		var footer_text 	= $('#wa_notifier_footer_text').val() || '';
		var button_type 	= $('#wa_notifier_button_type').val();
		var buttons 		= $('input[name="wa_notifier_button_num"]:checked').val();

		var button_1_type 	= $('#wa_notifier_button_1_type').val();
		
		switch(header_type) {
			case 'none' : 	$('.wa_notifier_media_type_field').hide();
							$('.wa_notifier_media_url_field').hide();
							$('.wa_notifier_header_text_field').hide();
							$('.wa-template-preview .message-head').hide();
							break;
			case 'text' : 	$('.wa_notifier_media_type_field').hide();
							$('.wa_notifier_media_url_field').hide();
							$('.wa_notifier_header_text_field').show();
							$('.wa-template-preview .message-head').show().text(header_text);
							break;
			case 'media' : 	$('.wa_notifier_media_type_field').show();
							$('.wa_notifier_media_url_field').show();
							$('.wa_notifier_header_text_field').hide();
							$('.wa-template-preview .message-head').hide();
							break;
		}

		if(button_type == 'none') {
			$('.wa_notifier_button_num_field').hide();
			$('.button-1-wrap').hide();
			$('.button-2-wrap').hide();
		}
		else {
			$('.wa_notifier_button_num_field').show();
			if(buttons == '1') {
	 			$('.button-1-wrap').show();
				$('.button-2-wrap').hide();
				$('#wa_notifier_button_1_type option').prop('disabled', false);
				$('#wa_notifier_button_2_type option').prop('disabled', false);
	 		}
	 		else {
	 			$('.button-1-wrap').show();
				$('.button-2-wrap').show();
				$('#wa_notifier_button_1_type option').not('option[value="'+ button_1_type +'"]').prop('disabled', true);
				$('#wa_notifier_button_2_type option').not('option[value="'+ button_1_type +'"]').prop('selected', true);
				$('#wa_notifier_button_2_type option[value="'+ button_1_type +'"]').prop('disabled', true);
				var button_2_type 	= $('#wa_notifier_button_2_type').val();
				if(button_2_type == 'visit') {
					$('.wa_notifier_button_2_url_field').show();
					$('.wa_notifier_button_2_phone_num_field').hide();
				}
				else if (button_2_type == 'call') {
					$('.wa_notifier_button_2_url_field').hide();
					$('.wa_notifier_button_2_phone_num_field').show();
				}
	 		}
		}

		if(button_1_type == 'visit') {
			$('.wa_notifier_button_1_url_field').show();
			$('.wa_notifier_button_1_phone_num_field').hide();
		}
		else if (button_1_type == 'call') {
			$('.wa_notifier_button_1_url_field').hide();
			$('.wa_notifier_button_1_phone_num_field').show();
		}

		$('.wa-template-preview .message-body').text(body_text);

		if(footer_text !== '') {
			$('.wa-template-preview .message-footer').show().text(footer_text);
		}
		else {
			$('.wa-template-preview .message-footer').hide();
		}

	}

	$(document).on('ready', function () {

		// Make the top admin header sticky
		var wpcontent_top = $('#wpcontent').offset().top;
		window.onscroll = function() {
			if (window.pageYOffset > wpcontent_top) {
				$('#wa-notifier-admin-header').addClass('sticky');
			} else {
				$('#wa-notifier-admin-header').removeClass('sticky');
			}
		};

		/** Dashboard **/

		// Toggle steps on dashboard page
		$('.toggle-step').on('click', function () {
			$(this).closest('.step').toggleClass('active');
			$(this).closest('.step').siblings().removeClass('active')
		})


		/** Message Templates **/

		var wan_menu_elem = $('#toplevel_page_wa-notifier');
		var mt_link = 'edit.php?post_type=wa_message_template';
		if( $('a[href="'+mt_link+'"]').closest('li').hasClass('current') && wan_menu_elem.hasClass('wp-not-current-submenu')) {
			wan_menu_elem
				.removeClass('wp-not-current-submenu')
				.addClass('wp-has-current-submenu')
				.children('a')
				.removeClass('wp-not-current-submenu')
				.addClass('wp-has-current-submenu');
		}

		// Make the WhatsApp preview sidebar sticky
		if( $('#wa-notifier-message-template-preview').length > 0 ) {
			var wa_preview = $('#wa-notifier-message-template-preview');
			var wa_preview_top = wa_preview.offset().top - 120;
			var wa_preview_width = wa_preview.width();
			wa_preview.width(wa_preview_width);
			window.onscroll = function() {
				if (window.pageYOffset > wa_preview_top) {
					wa_preview.addClass('sticky');
				} else {
					wa_preview.removeClass('sticky');
				}
			}
		}

		// Validate the message template name
		$('#wa_notifier_template_name').on('keyup', function() {
			var value = $(this).val();
			$(this).val(value.replace(' ', '_').replace(/[^a-zA-Z_]/g, '').toLowerCase());
		});

		// Trigger conditional logic and WhatsApp message template preview 
		if($('#wa-notifier-message-template-data').length > 0) {
			runConditionalFieldsLogic();
			$('#wa-notifier-message-template-data input, #wa-notifier-message-template-data textarea').on('keyup', function(){
				runConditionalFieldsLogic();
			});
			$('#wa-notifier-message-template-data select, #wa-notifier-message-template-data input[type="radio"], #wa-notifier-message-template-data input[type="checkbox"]').on('change', function(){
				runConditionalFieldsLogic();
			});
		}


	});

})( jQuery );
