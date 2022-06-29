/* global waNotifierTemplates */
(function($) {

	// Show / hide fields as per conditional logic
	function conditionallyShowFields() {
		$('.form-field').each(function() {
			var thisField = $(this).find(':input');

			// Conditionally show/hide fields
			var conditions = $(this).attr('data-conditions') || '';
			if (conditions !== '') {
				var showField = false;
				var fieldElem = $(this);
				var conditionsArray = JSON.parse(conditions);

				conditionsArray.forEach(function(condition, index) {
					var fieldClass = '.' + condition.field + '_field';
					var fieldInput = $(fieldClass + ' :input');
					var fieldInputType = fieldInput.prop('type');

					// Do not fetch value of hidden fields.
					if(!$(fieldClass).is(':visible')){
						return;
					}

					// Get field value
					var fieldVal = fieldInput.val();

					// Get field value if it's radio or checkbox
					if($.inArray(fieldInputType, ['radio', 'checkbox']) !== -1) {
						fieldVal = $(fieldClass + ' :input:checked').val();
					}

					var showThis = false;
					if(condition.operator == '==') {
						showThis = (fieldVal == condition.value) ? true : false;
					}
					else if(condition.operator == '!=') {
						showThis = (fieldVal != condition.value) ? true : false;
					}
					showField = showField || showThis;
				});

				if(showField){
					fieldElem.show();
				}
				else{
					fieldElem.hide();
				}
			}

			// Apply data limit on fields
			if (thisField.hasClass('force-text-limit')) {
				var content = thisField.val();
				var contentLength = content.length;
				var limit = thisField.attr('data-limit');
				if (contentLength >= limit) {
					thisField.siblings('label').find('.limit-used').text(limit);
					thisField.val(content.substr(0, limit));
				} else {
					thisField.siblings('label').find('.limit-used').text(contentLength);
				}
			}
		});
	}

	// Fetch message template data and generate preview
	function fetcDataAndPreviewTemplate() {
		let previewData = {};
		previewData.header_type = $('#wa_notifier_header_type').val();
		previewData.header_text = $('#wa_notifier_header_text').val() || 'Header text here';
		previewData.body_text = $('#wa_notifier_body_text').val() || 'Body text here';
		previewData.footer_text = $('#wa_notifier_footer_text').val() || '';
		previewData.button_type = $('#wa_notifier_button_type').val();

		previewData.button_num = $('input[name="wa_notifier_button_num"]:checked').val();

		previewData.button_1_type = $('#wa_notifier_button_1_type :selected').val();
		previewData.button_1_text = $('#wa_notifier_button_1_text').val() || '';
		previewData.button_2_text = $('#wa_notifier_button_2_text').val() || '';

		// Button
		if (previewData.button_type != 'none') {
			if (previewData.button_1_type == 'URL') {
				if (previewData.button_1_text == '') {
					previewData.button_1_text = 'Visit Website';
				}
			} else if (previewData.button_1_type == 'PHONE_NUMBER') {
				if (previewData.button_1_text == '') {
					previewData.button_1_text = 'Call';
				}
			}

			if (previewData.button_num == '1') {
				$('#wa_notifier_button_1_type option').prop('disabled', false);
				$('#wa_notifier_button_2_type option').prop('disabled', false);
			} else {
				$('#wa_notifier_button_1_type option').not('option[value="' + previewData.button_1_type + '"]').prop('disabled', true);
				$('#wa_notifier_button_2_type option').not('option[value="' + previewData.button_1_type + '"]').prop('selected', true);
				$('#wa_notifier_button_2_type option[value="' + previewData.button_1_type + '"]').prop('disabled', true);
				previewData.button_2_type = $('#wa_notifier_button_2_type :selected').val();
				if (previewData.button_2_type == 'URL') {
					if (previewData.button_2_text == '') {
						previewData.button_2_text = 'Visit Website';
					}
				} else if (previewData.button_2_type == 'PHONE_NUMBER') {
					if (previewData.button_2_text == '') {
						previewData.button_2_text = 'Call';
					}
				}
			}
		}

		// Render message preview from the data
		renderMessagePreview(previewData);
	}

	// Render message preview
	function renderMessagePreview(previewData) {
		// Header
		switch (previewData.header_type) {
			case 'text':
				$('.wa-template-preview .message-head').show().text(previewData.header_text);
				break;
			default:
				$('.wa-template-preview .message-head').hide();
				break;
		}

		// Body
		previewData.body_text = previewData.body_text.replace(/(<([^>]+)>)/gi, "")
			.replace(/(?:\*)(?:(?!\s))((?:(?!\*|\n).)+)(?:\*)/g,'<b>$1</b>')
	   	.replace(/(?:_)(?:(?!\s))((?:(?!\n|_).)+)(?:_)/g,'<i>$1</i>')
	   	.replace(/(?:~)(?:(?!\s))((?:(?!\n|~).)+)(?:~)/g,'<s>$1</s>')
	   	.replace(/(?:--)(?:(?!\s))((?:(?!\n|--).)+)(?:--)/g,'<u>$1</u>')
	   	.replace(/(?:```)(?:(?!\s))((?:(?!\n|```).)+)(?:```)/g,'<tt>$1</tt>');

		$('.wa-template-preview .message-body').html(previewData.body_text);

		// Footer
		if (previewData.footer_text !== '') {
			$('.wa-template-preview .message-footer').show().text(previewData.footer_text);
		} else {
			$('.wa-template-preview .message-footer').hide();
		}

		// Buttons
		if (previewData.button_type == 'none') {
			$('.wa-template-preview .message-buttons').hide();
		} else {
			$('.wa-template-preview .message-buttons').show();
			if (previewData.button_1_type == 'URL') {
				$('.wa-template-preview .message-button-1 .message-button-img').removeClass('call').addClass('visit');
				$('.wa-template-preview .message-button-1 .message-button-text').text(previewData.button_1_text);
			} else if (previewData.button_1_type == 'PHONE_NUMBER') {
				$('.wa-template-preview .message-button-1 .message-button-img').removeClass('visit').addClass('call');
				$('.wa-template-preview .message-button-1 .message-button-text').text(previewData.button_1_text);
			}

			if (previewData.button_num == '1') {
				$('.wa-template-preview .message-button-2').hide();
			} else {
				$('.wa-template-preview .message-button-2').show();
				if (previewData.button_2_type == 'URL') {
					$('.wa-template-preview .message-button-2 .message-button-img').removeClass('call').addClass('visit');
					$('.wa-template-preview .message-button-2 .message-button-text').text(previewData.button_2_text);
				} else if (previewData.button_2_type == 'PHONE_NUMBER') {
					$('.wa-template-preview .message-button-2 .message-button-img').removeClass('visit').addClass('call');
					$('.wa-template-preview .message-button-2 .message-button-text').text(previewData.button_2_text);
				}
			}
		}
	}

	// Fetch and display message template
	function fetchAndDisplayMessageTemplate(template_id = '') {
		if (template_id == '') {
			template_id = $('#wa_notifier_notification_message_template :selected').val();
			if (template_id == '') {
				$('#wa-notifier-message-template-preview').addClass('hide');
				return false;
			}
		}
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: waNotifier.ajaxurl,
			data: {
				action: 'fetch_message_template_data',
				template_id: template_id
			},
			success: function(response) {
				if (response.status == 'success') {
					$('#wa-notifier-message-template-preview').addClass('d-block');
					renderMessagePreview(response.data);
				}
			},
		});
	}

	function updateNotificationSaveButtonText() {
		var type = $('#wa_notifier_notification_type :checked').val();
		var when = $('#wa_notifier_notification_when :checked').val();
		var btnText = '';
		if(type == 'transactional'){
			btnText = 'Save';
		}
		else {
			btnText = (when == 'now') ? 'Save & Send' : 'Save & Schedule';
		}
		$('#publish').val(btnText);
	}

	$(document).on('ready', function() {

		/*****************
		 * Global
		 ****************/

		// Make the top admin header sticky
		var wpcontent_top = $('#wpcontent').offset().top;
		window.onscroll = function() {
			if (window.pageYOffset > wpcontent_top) {
				$('#wa-notifier-admin-header').addClass('sticky');
			} else {
				$('#wa-notifier-admin-header').removeClass('sticky');
			}
		};

		// Show feilds conditionally
		if ($('.meta-fields').length > 0) {
			conditionallyShowFields();
			$('.meta-fields :input').on('change keyup', function() {
				conditionallyShowFields();
			});
		}

		/*****************
		 * Dashboard page
		 ****************/

		// Toggle steps on dashboard page
		$('.toggle-step').on('click', function() {
			$(this).closest('.step').toggleClass('active');
			$(this).closest('.step').siblings().removeClass('active');
		});

		// Highlight menu items
		const wan_menu_elem = $('#toplevel_page_wa-notifier');
		const wa_notifier_cpts = ['wa_message_template', 'wa_contact', 'wa_notification'];
		const current_cpt = $('#wa-notifier-admin-header').data('post-type') || '';
		if (wa_notifier_cpts.includes(current_cpt)) {
			wan_menu_elem
				.removeClass('wp-not-current-submenu')
				.addClass('wp-has-current-submenu')
				.children('a')
				.removeClass('wp-not-current-submenu')
				.addClass('wp-has-current-submenu');
			wan_menu_elem.find('a[href*="' + current_cpt + '"]').addClass('current').closest('li').addClass('current');
		}

		/*************************
		 * Message Template page
		 *************************/

		// Make the WhatsApp preview sidebar sticky
		if ($('#wa-notifier-message-template-preview').length > 0) {
			var wa_preview = $('#wa-notifier-message-template-preview');
			var wa_preview_top = wa_preview.offset().top - 120;
			var wa_preview_width = wa_preview.width();
			wa_preview.width(wa_preview_width);
			window.onscroll = function() {
				if (window.pageYOffset > wa_preview_top && window.innerWidth > 850) {
					wa_preview.addClass('sticky');
				} else {
					wa_preview.removeClass('sticky');
				}
			};
		}

		// Validate the message template name
		$('#wa_notifier_template_name').on('keyup', function() {
			var value = $(this).val();
			$(this).val(value.replace(' ', '_').replace(/[^a-zA-Z_]/g, '').toLowerCase());
		});

		// Trigger conditional logic and WhatsApp message template preview
		if ($('#wa-notifier-message-template-data').length > 0) {
			fetcDataAndPreviewTemplate();
			$('#wa-notifier-message-template-data :input').on('change keyup', function() {
				fetcDataAndPreviewTemplate();
			});
		}

		// Message template publish confirmation
		$('.post-type-wa_message_template #publish').on('click', function() {
			const template_name = $('#wa_notifier_template_name').val() || '';
			const body_text = $('#wa_notifier_body_text').val() || '';
			if ('' === template_name && '' === body_text) {
				alert('Template name and Body text are required fields.');
				return false;
			}
			if ('' === template_name) {
				alert('Template name is a required field.');
				return false;
			}
			if ('' === body_text) {
				alert('Body text is a required field.');
				return false;
			}
			return confirm('IMPORTANT NOTE:\n\nClicking "OK" will send your template data to WhatsApp for approval. It might take between 30 minutes to 24 hours for them to review it.\n\nYou\'ll get confirmation email from them after they complete their review. You will be able to send this template to your contacts only after their approval.');
		});

		// Message template delete confirmation
		$('.post-type-wa_message_template .row-actions .delete').on('click', function() {
			return confirm('Deleting from here will also delete this template from WhatsApp server.');
		});

		// Add Refresh Status button to the message template lisitng page
		$('.edit-php.post-type-wa_message_template .wrap .page-title-action').after(waNotifierTemplates.refresh_mt_status);

		/***************
		 * Contact page
		 **************/

		// Add Import button and HTML to the Contacts lisitng page
		$('.edit-php.post-type-wa_contact .wrap .page-title-action').after(waNotifierTemplates.import_contact);

		// Show import options
		$(document).on('click', '#import-contacts', function(e) {
			e.preventDefault();
			$('.contact-import-options').toggleClass('hide');
		});

		// Select import method
		$(document).on('change', '.csv-import-method', function() {
			var value = $(this).val();
			$('.col-import').addClass('hide');
			$('.col-import-' + value).removeClass('hide');
		});

		// On submission of CSV import form
		$(document).on('submit', '#import-contacts-csv', function(e) {
			var file = $('#wa-notifier-contacts-csv').val();
			if (file == '') {
				alert('Please select a file to upload.');
				return false;
			} else {
				var file_size = $('#wa-notifier-contacts-csv')[0].files[0].size / 1024 / 1024;
				if (file_size > 24) {
					alert('Please select CSV file smaller than 24MB.');
					return false;
				}
				var allowedExtension = /(\.csv)$/i;
				if (!allowedExtension.exec(file)) {
					alert('Please select a CSV file.');
					$('#wa-notifier-contacts-csv').val('');
					return false;
				}
			}
		});

		// On submission of users import form
		$(document).on('submit', '#import-contacts-users', function(e) {
			const wa_contact_first_name_key = $('#wa_contact_first_name_key').val() || '';
			const wa_contact_last_name_key = $('#wa_contact_last_name_key').val() || '';
			const wa_contact_wa_number_key = $('#wa_contact_wa_number_key').val() || '';
			// const wa_contact_list_name = $('#wa_contact_list_name').val() || '';
			// const wa_contact_tags = $('#wa_contact_tags').val() || '';

			if (wa_contact_first_name_key == '' || wa_contact_last_name_key == '' || wa_contact_wa_number_key == '') {
				alert('Please enter all user meta keys.');
				return false;
			}
		});

		// Contact publish confirmation
		$('.post-type-wa_contact #publish').on('click', function() {
			const first_name = $('#wa_notifier_first_name').val() || '';
			const last_name = $('#wa_notifier_last_name').val() || '';
			const wa_number = $('#wa_notifier_wa_number').val() || '';

			if (first_name == '' || last_name == '' || wa_number == '') {
				alert('First Name, Last Name and Phone Number are required fields.');
				return false;
			}
		});

		/********************
		 * Notification page
		 ********************/

		if ($('#wa-notifier-notification-data').length > 0) {
			// Fetch and display message template preview.
			fetchAndDisplayMessageTemplate();
			$('#wa_notifier_notification_message_template').on('change', function() {
				const template_id = $(this).find(':selected').val();
				fetchAndDisplayMessageTemplate(template_id);
			});

			// Update button text as per user selection
			updateNotificationSaveButtonText();
			$('#wa-notifier-notification-data :input').on('change keyup', function(){
				updateNotificationSaveButtonText();
			});

			var dateToday = new Date();
			$('#wa_notifier_notification_datetime').datetimepicker({
				minDate: dateToday,
				defaultDate: 1
			});
		}

		// Notification publish confirmation
		$('.post-type-wa_notification #publish').on('click', function() {
			const type = $('#wa_notifier_notification_type').val() || '';
			const template_name = $('#wa_notifier_notification_message_template').val() || '';

			if ('' === type) {
				alert('Please select a notification type.');
				return false;
			}
			else if ('transactional' == type) {
				const trigger = $('#wa_notifier_notification_trigger').val() || '';
				if ('' === trigger) {
					alert('Please select a Trigger.');
					return false;
				}
			}
			else if ('marketing' == type) {
				const list = $('#wa_notifier_notification_list').val() || '';
				if ('' === list) {
					alert('Please select a Contact List.');
					return false;
				}
			}

			if ('' === template_name) {
				alert('Please select a Message Template.');
				return false;
			}


		});

	});

})(jQuery);
