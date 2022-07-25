/* global waNotifier */
(function($) {

	// Show / hide fields as per conditional logic
	function conditionallyShowFields() {
		if ($('.meta-fields').length == 0) {
			return;
		}

		$('.form-field').each(function() {
			var thisField = $(this).find(':input');

			// Conditionally show/hide fields
			var conditions = $(this).attr('data-conditions') || '';
			if (conditions !== '') {
				var showField = false;
				var fieldElem = $(this);
				var conditionsArray = JSON.parse(conditions);

				conditionsArray.forEach(function(condition) {
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

			// Enable select2 for notification receiver contact dropdown
			if($('.notifier-recipient-contact').length > 0){
				$('.notifier-recipient-contact').each(function (i, obj) {
					if($(obj).closest('.send-to-fields-row-template').length > 0){
						return;
					}
					if (!$(obj).data('select2')){
						$(obj).select2({
							selectOnClose: true,
							width: '100%',
							placeholder: 'Search contact...',
							ajax: {
								url: waNotifier.ajaxurl,
								dataType: 'json',
								delay: 250,
								method: 'post',
								data: function (params) {
									return {
										s: params.term,
										action: 'get_wa_contacts_data',
									};
								},
								processResults: function( data ) {
									var options = [];
									if ( data ) {
										$.each( data, function( index, text ) {
											options.push( { id: index, text: text  } );
										});
									}
									return {
										results: options,
									};
								},
								cache: true,
							},
							minimumInputLength: 2,
						});
					}
				});
			}

		});
	}

	// Fetch message template data and generate preview
	function fetcDataAndPreviewTemplate() {
		let previewData = {};
		previewData.header_type = $('#notifier_header_type').val();
		previewData.header_text = $('#notifier_header_text').val() || 'Header text here';
		previewData.body_text = $('#notifier_body_text').val() || 'Body text here';
		previewData.footer_text = $('#notifier_footer_text').val() || '';
		previewData.button_type = $('#notifier_button_type').val();

		previewData.button_num = $('input[name="notifier_button_num"]:checked').val();

		previewData.button_1_type = $('#notifier_button_1_type :selected').val();
		previewData.button_1_text = $('#notifier_button_1_text').val() || '';
		previewData.button_2_text = $('#notifier_button_2_text').val() || '';

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
				$('#notifier_button_1_type option').prop('disabled', false);
				$('#notifier_button_2_type option').prop('disabled', false);
			} else {
				$('#notifier_button_1_type option').not('option[value="' + previewData.button_1_type + '"]').prop('disabled', true);
				$('#notifier_button_2_type option').not('option[value="' + previewData.button_1_type + '"]').prop('selected', true);
				$('#notifier_button_2_type option[value="' + previewData.button_1_type + '"]').prop('disabled', true);
				previewData.button_2_type = $('#notifier_button_2_type :selected').val();
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
		messageTemplatePreviewData = previewData;
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
		previewData.body_text = previewData.body_text.replace(/(<([^>]+)>)/gi, '')
			.replace(/(?:\r\n|\r|\n)/g, '<br>')
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

	// Fetch and display send to fields
	function fetchAndDisplaySendToFields() {
		const post_id = $('#post_ID').val() || 0;
		const notification_type = $('#notifier_notification_type').val() || '';
		const trigger = $('#notifier_notification_trigger').val() || '';

		if('marketing' == notification_type) {
			return;
		}

		$('.send-to-fields').html('<div class="loader"></div>');

		$.ajax({
			type: 'post',
			dataType: 'json',
			url: waNotifier.ajaxurl,
			data: {
				action: 			'fetch_send_to_fields',
				trigger: 			trigger,
				post_id: 			post_id,
			},
			success: function(response) {
				if (response.status == 'success') {
					$('.send-to-fields').html(response.html);
					conditionallyShowFields();
				}
			},
		});
	}

	// Fetch and display message template
	function fetchAndDisplayMessageTemplate() {
		const template_id = $('#notifier_notification_message_template :selected').val();
		if (template_id == '') {
			$('#notifier-message-template-preview').removeClass('d-block').addClass('hide');
			return false;
		}
		const post_id = $('#post_ID').val() || 0;
		const notification_type = $('#notifier_notification_type').val() || '';
		const trigger = $('#notifier_notification_trigger').val() || '';
		/* ==Notifier_Pro_Code_Start== */
		$('.variables-mapping-fields').html('<div class="loader"></div>');
		/* ==Notifier_Pro_Code_End== */
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: waNotifier.ajaxurl,
			data: {
				action: 			'fetch_message_template_data',
				notification_type: 	notification_type,
				template_id: 		template_id,
				post_id: 			post_id,
				trigger: 			trigger,
			},
			success: function(response) {
				if (response.status == 'success') {
					$('#notifier-message-template-preview').removeClass('hide').addClass('d-block');
					renderMessagePreview(response.data);
					/* ==Notifier_Pro_Code_Start== */
					// Add variable mapping fields
					$('.variables-mapping-fields').html(response.variable_mapping_html);
					/* ==Notifier_Pro_Code_End== */
				}
			},
		});
	}

	// Notifications page - change save button text
	function updateNotificationSaveButtonText() {
		var type = $('#notifier_notification_type :checked').val();
		var when = $('#notifier_notification_when :checked').val();
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

		window.messageTemplatePreviewData = {};

		/*****************
		 * Dashboard page
		 ****************/

		// Toggle steps on dashboard page
		$('.toggle-step').on('click', function() {
			$(this).closest('.step').toggleClass('active');
			$(this).closest('.step').siblings().removeClass('active');
		});

		// Highlight menu items
		const wan_menu_elem = $('#toplevel_page_notifier');
		const notifier_cpts = ['wa_message_template', 'wa_contact', 'wa_notification'];
		const current_cpt = $('#notifier-admin-header').data('post-type') || '';
		if (notifier_cpts.includes(current_cpt)) {
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
		if ($('#notifier-message-template-preview').length > 0) {
			var wa_preview = $('#notifier-message-template-preview');
			var wa_preview_top = wa_preview.offset().top - 50;
			var wa_preview_width = wa_preview.width();
			wa_preview.width(wa_preview_width);
			$(window).scroll( function() {
				if (window.pageYOffset > wa_preview_top && window.innerWidth > 850) {
					wa_preview.addClass('sticky');
				} else {
					wa_preview.removeClass('sticky');
				}
			});
		}

		// Validate the message template name
		$('#notifier_template_name').on('keyup', function() {
			var value = $(this).val();
			$(this).val(value.replace(' ', '_').replace(/[^a-zA-Z_]/g, '').toLowerCase());
		});

		// Trigger conditional logic and WhatsApp message template preview
		if ($('#notifier-message-template-data').length > 0) {
			fetcDataAndPreviewTemplate();
			$('#notifier-message-template-data :input').on('change keyup', function() {
				fetcDataAndPreviewTemplate();
			});
		}

		// Message template publish confirmation
		$('.post-type-wa_message_template #publish').on('click', function() {
			const template_name = $('#notifier_template_name').val() || '';
			const body_text = $('#notifier_body_text').val() || '';
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
			/* ==Notifier_Free_Code_Start== */
			var header_text = $('#notifier_header_text').val() || '';
			if(header_text.match(/{{.*?}}/g) !== null || body_text.match(/{{.*?}}/g) !== null) {
				alert('Free version of the plugin does not support variables. Please remove variables like {{}} and try again.');
				return false;
			}
			/* ==Notifier_Free_Code_End== */
			return confirm('IMPORTANT NOTE:\n\nClicking "OK" will send your template data to WhatsApp for approval. It might take between 30 minutes to 24 hours for them to review it.\n\nYou\'ll get confirmation email from them after they complete their review. You will be able to send this template to your contacts only after their approval.');
		});

		// Message template delete confirmation
		$('.post-type-wa_message_template .row-actions .delete').on('click', function() {
			return confirm('Deleting from here will also delete this template from WhatsApp server.');
		});

		// Add Refresh Status button to the message template lisitng page
		var refresh_mt_status_template = $('#refresh_mt_status').html().trim();
		$('.edit-php.post-type-wa_message_template .wrap .page-title-action').after(refresh_mt_status_template);

		/* ==Notifier_Pro_Code_Start== */
		// Add variable
		let bodyVar = 0;
		$('.add-variable').on('click', function(e){
			e.preventDefault();
			var type = $(this).data('type');
			if('header' == type) {
				var header_text = $('#notifier_header_text').val();
				var res_header = header_text.match(/{{.*?}}/g);
				if(res_header === null) {
					header_text = header_text + ' {{1}}';
				}
				else {
					header_text = header_text.replace(/{{.*?}}/g, '{{1}}');
				}
				$('#notifier_header_text').val(header_text).focus();
			}
			if('body' == type) {
				var body_text = $('#notifier_body_text').val();
				var res = body_text.match(/{{.*?}}/g);
				if(res === null) {
					bodyVar = 1;
				}
				else {
					bodyVar = res.length + 1;
					var x = 1;
					res.forEach(function(item) {
						body_text = body_text.replace( item, '{{'+x+'}}');
						x++;
					});
				}
				$('#notifier_body_text').val(body_text + ' {{'+bodyVar+'}}').focus();
			}
			fetcDataAndPreviewTemplate();
		});
		/* ==Notifier_Pro_Code_End== */
		/***************
		 * Contact page
		 **************/

		// Add Import button and HTML to the Contacts lisitng page
		var import_contact_template = $('#import_contact').html().trim();
		$('.edit-php.post-type-wa_contact .wrap .page-title-action').after(import_contact_template);

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
		$(document).on('submit', '#import-contacts-csv', function() {
			var file = $('#notifier-contacts-csv').val();
			if (file == '') {
				alert('Please select a file to upload.');
				return false;
			} else {
				var file_size = $('#notifier-contacts-csv')[0].files[0].size / 1024 / 1024;
				if (file_size > 24) {
					alert('Please select CSV file smaller than 24MB.');
					return false;
				}
				var allowedExtension = /(\.csv)$/i;
				if (!allowedExtension.exec(file)) {
					alert('Please select a CSV file.');
					$('#notifier-contacts-csv').val('');
					return false;
				}
			}
		});

		// On submission of users import form
		$(document).on('submit', '#import-contacts-users', function() {
			const wa_contact_list_name = $('#wa_contact_list_name').val() || '';
			if (wa_contact_list_name == '') {
				alert('Please enter a Contact List name.');
				return false;
			}
		});

		// Contact publish confirmation
		$('.post-type-wa_contact #publish').on('click', function() {
			const first_name = $('#notifier_first_name').val() || '';
			const last_name = $('#notifier_last_name').val() || '';
			const wa_number = $('#notifier_wa_number').val() || '';

			if (first_name == '' || last_name == '' || wa_number == '') {
				alert('First Name, Last Name and Phone Number are required fields.');
				return false;
			}
		});

		/********************
		 * Notification page
		 ********************/

		// Do stuff if on the notification edit page
		if ($('#notifier-notification-data').length > 0) {
			updateNotificationSaveButtonText(); // Update button text as per user selection
			fetchAndDisplaySendToFields(); // Fetch and display send to fields
			fetchAndDisplayMessageTemplate(); // Fetch and display message template preview + variable mapping fields
			$('#notifier-notification-data :input').on('change keyup', function(){
				updateNotificationSaveButtonText();

				if('notifier_notification_trigger' == $(this).attr('name')){
					fetchAndDisplaySendToFields();
					fetchAndDisplayMessageTemplate();
				}

				if('notifier_notification_message_template' == $(this).attr('name')){
					fetchAndDisplayMessageTemplate();
				}
			});

			var dateToday = new Date();
			$('#notifier_notification_datetime').datetimepicker({
				minDate: dateToday,
				defaultDate: 1,
			});
		}

		// Notification publish confirmation
		$('.post-type-wa_notification #publish').on('click', function() {
			const type = $('#notifier_notification_type').val() || '';
			const template_name = $('#notifier_notification_message_template').val() || '';

			if ('' === type) {
				alert('Please select a notification type.');
				return false;
			}
			else if ('transactional' == type) {
				const trigger = $('#notifier_notification_trigger :selected').val() || '';
				if ('' === trigger) {
					alert('Please select a trigger.');
					return false;
				}

				var recipientMissing = false;
				$('.notifier-recipient').each(function(){
					const recipient = $('option:selected',this).val() || '';
					if($(this).is(':visible') && '' == recipient){
						recipientMissing = true;
					}
				});

				if(recipientMissing){
					alert('Recipient field can not be empty.');
					return false;
				}
			}
			else if ('marketing' == type) {
				const list = $('#notifier_notification_list :selected').val() || '';
				if ('' === list) {
					alert('Please select a contact list.');
					return false;
				}
			}

			if ('' === template_name) {
				alert('Please select a Message Template.');
				return false;
			}

			if( $('.notifier-variable-mapping').length > 0 ){
				var recipientValue = false;
				$('.notifier-variable-mapping').each(function(){
					const value = $('option:selected',this).val() || '';
					if($(this).is(':visible') && '' == value){
						recipientValue = true;
					}
				});
				if(recipientValue){
					alert('Message template variable missing mapped value.');
					return false;
				}
			}

			/* ==Notifier_Free_Code_Start== */
			var header_text = messageTemplatePreviewData.header_text || '';
			var body_text = messageTemplatePreviewData.body_text || '';
			if(header_text.match(/{{.*?}}/g) !== null || body_text.match(/{{.*?}}/g) !== null) {
				alert('Free version of the plugin does not support sending message templates with variables. Please select a message template that does not use variables.');
				return false;
			}
			/* ==Notifier_Free_Code_End== */
		});

		// Add new Notification Send To receiver fields row
		$(document).on('click', '.add-recipient', function(e){
			e.preventDefault();
			var next = $('.send-to-fields .fields-repeater tr.row').length;
			var nextRowHtml = $('#notification_send_to_fields_row').html().replaceAll('row_num', next);
			$('.send-to-fields .fields-repeater tbody').append(nextRowHtml);
			conditionallyShowFields();
		});

		$(document).on('click', '.delete-repeater-field span', function(e){
			e.preventDefault();
			$(this).closest('tr.row').remove();
		});

		/*****************
		 * Global
		 ****************/

		// Make the top admin header sticky
		var wpcontent_top = $('#wpcontent').offset().top;
		$(window).scroll( function() {
			if (window.pageYOffset > wpcontent_top) {
				$('#notifier-admin-header').addClass('sticky');
			} else {
				$('#notifier-admin-header').removeClass('sticky');
			}
		});

		// Show feilds conditionally
		conditionallyShowFields();
		$(document).on('change keyup', '.meta-fields :input', function() {
			conditionallyShowFields();
		});

	});

})(jQuery);
