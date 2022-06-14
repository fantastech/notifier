/* global waNotifierTemplates */
(function( $ ) {

  // Show / hide fields as per conditional logic
  function showMTFieldsConditionally () {
    //var header_type   = $('#wa_notifier_header_type').val();
    let previewData = {};
    previewData.header_text   = $('#wa_notifier_header_text').val() || 'Header text here';
    previewData.body_text     = $('#wa_notifier_body_text').val() || 'Body text here';
    previewData.footer_text   = $('#wa_notifier_footer_text').val() || '';
    previewData.button_type   = $('#wa_notifier_button_type').val();

    previewData.button_num     = $('input[name="wa_notifier_button_num"]:checked').val();

    previewData.button_1_type   = $('#wa_notifier_button_1_type :selected').val();
    previewData.button_1_text   = $('#wa_notifier_button_1_text').val() || '';
    previewData.button_2_text   = $('#wa_notifier_button_2_text').val() || '';

    // switch(header_type) {
    //  case 'none' :   $('.wa_notifier_media_type_field').hide();
    //          $('.wa_notifier_media_url_field').hide();
    //          $('.wa_notifier_header_text_field').hide();
    //          $('.wa-template-preview .message-head').hide();
    //          break;
    //  case 'text' :   $('.wa_notifier_media_type_field').hide();
    //          $('.wa_notifier_media_url_field').hide();
    //          $('.wa_notifier_header_text_field').show();
    //          $('.wa-template-preview .message-head').show().text(header_text);
    //          break;
    //  case 'media' :  $('.wa_notifier_media_type_field').show();
    //          $('.wa_notifier_media_url_field').show();
    //          $('.wa_notifier_header_text_field').hide();
    //          $('.wa-template-preview .message-head').hide();
    //          break;
    // }

    // Button
    if(previewData.button_type == 'none') {
      $('.wa_notifier_button_num_field').hide();
      $('.button-1-wrap').hide();
      $('.button-2-wrap').hide();
    }
    else {
      $('.wa_notifier_button_num_field').show();

      if(previewData.button_1_type == 'URL') {
        $('.wa_notifier_button_1_url_field').show();
        $('.wa_notifier_button_1_phone_num_field').hide();
        if(previewData.button_1_text == ''){
          previewData.button_1_text = 'Visit Website';
        }
      }
      else if (previewData.button_1_type == 'PHONE_NUMBER') {
        $('.wa_notifier_button_1_url_field').hide();
        $('.wa_notifier_button_1_phone_num_field').show();
        if(previewData.button_1_text == ''){
          previewData.button_1_text = 'Call';
        }
      }

      if(previewData.button_num == '1') {
        $('.button-1-wrap').show();
        $('.button-2-wrap').hide();
        $('#wa_notifier_button_1_type option').prop('disabled', false);
        $('#wa_notifier_button_2_type option').prop('disabled', false);
      }
      else {
        $('.button-1-wrap').show();
        $('.button-2-wrap').show();
        $('#wa_notifier_button_1_type option').not('option[value="'+ previewData.button_1_type +'"]').prop('disabled', true);
        $('#wa_notifier_button_2_type option').not('option[value="'+ previewData.button_1_type +'"]').prop('selected', true);
        $('#wa_notifier_button_2_type option[value="'+ previewData.button_1_type +'"]').prop('disabled', true);
        previewData.button_2_type   = $('#wa_notifier_button_2_type :selected').val();
        if(previewData.button_2_type == 'URL') {
          $('.wa_notifier_button_2_url_field').show();
          $('.wa_notifier_button_2_phone_num_field').hide();
          if(previewData.button_2_text == ''){
            previewData.button_2_text = 'Visit Website';
          }
        }
        else if (previewData.button_2_type == 'PHONE_NUMBER') {
          $('.wa_notifier_button_2_url_field').hide();
          $('.wa_notifier_button_2_phone_num_field').show();
          if(previewData.button_2_text == ''){
            previewData.button_2_text = 'Call';
          }
        }
      }
    }
    renderMessagePreview(previewData);
  }

  // Render message preview
  function renderMessagePreview (previewData) {
  	console.log(previewData);
  	// Header
    $('.wa-template-preview .message-head').show().text(previewData.header_text);

    // Body
    $('.wa-template-preview .message-body').text(previewData.body_text);

    // Footer
    if(previewData.footer_text !== '') {
      $('.wa-template-preview .message-footer').show().text(previewData.footer_text);
    }
    else {
      $('.wa-template-preview .message-footer').hide();
    }

    // Buttons
    if(previewData.button_type == 'none') {
      $('.wa-template-preview .message-buttons').hide();
    }
    else {
      $('.wa-template-preview .message-buttons').show();
      if(previewData.button_1_type == 'URL') {
        $('.wa-template-preview .message-button-1 .message-button-img').removeClass('call').addClass('visit');
        $('.wa-template-preview .message-button-1 .message-button-text').text(previewData.button_1_text);
      }
      else if (previewData.button_1_type == 'PHONE_NUMBER') {
        $('.wa-template-preview .message-button-1 .message-button-img').removeClass('visit').addClass('call');
        $('.wa-template-preview .message-button-1 .message-button-text').text(previewData.button_1_text);
      }

      if(previewData.button_num == '1') {
        $('.wa-template-preview .message-button-2').hide();
      }
      else {
        $('.wa-template-preview .message-button-2').show();
        if(previewData.button_2_type == 'URL') {
          $('.wa-template-preview .message-button-2 .message-button-img').removeClass('call').addClass('visit');
          $('.wa-template-preview .message-button-2 .message-button-text').text(previewData.button_2_text);
        }
        else if (previewData.button_2_type == 'PHONE_NUMBER') {
          $('.wa-template-preview .message-button-2 .message-button-img').removeClass('visit').addClass('call');
          $('.wa-template-preview .message-button-2 .message-button-text').text(previewData.button_2_text);
        }
      }
  	}
  }

  // Fetch and display message template
  function fetchAndDisplayMessageTemplate (template_name = '') {
  	if(template_name == '') {
  		template_name = $('#wa_notifier_notification_message_template :selected').val();
  		if(template_name == '') {
  			$('#wa-notifier-message-template-preview').addClass('hide');
  			return false;
  		}
  	}
  	$.ajax({
		type : "post",
		dataType : "json",
		url : waNotifier.ajaxurl,
		data : { action: 'fetch_message_template_data', template_name: template_name },
		success: function(response) {
			if(response.status == "success") {
				$('#wa-notifier-message-template-preview').addClass('d-block')
				renderMessagePreview(response.data);
			}
		}
	});
  }

  // Show notification fields conditionally
  function showNotificationFieldsConditionally() {
  	let notificationData = {};
  	notificationData.notification_type = $('#wa_notifier_notification_type').val();
  	console.log(notificationData.notification_type);
  	if(notificationData.notification_type == 'transactional') {
  		$('.form-fields-transactional').removeClass('hide');
  		$('.form-fields-marketing').addClass('hide');
  		notificationData.notification_trigger = $('#wa_notifier_notification_trigger :selected').val();
  		if(notificationData.notification_trigger != '') {
  			$('.form-fields-message-template').removeClass('hide');
  		}
  		else {
  			$('.form-fields-message-template').addClass('hide');
  		}
  		$('#publish').val('Save Notification');
  	}
  	else if(notificationData.notification_type == 'marketing') {
  		$('.form-fields-transactional').addClass('hide');
  		$('.form-fields-marketing').removeClass('hide');
  		notificationData.notification_list = $('#wa_notifier_notification_list :selected').val();
  		if(notificationData.notification_list != '') {
  			$('.form-fields-message-template').removeClass('hide');
  		}
  		else {
  			$('.form-fields-message-template').addClass('hide');
  		}
  		$('#publish').val('Send Notification');
  	}
  }

  $(document).on('ready', function () {

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

    /*****************
     * Dashboard page
     ****************/

    // Toggle steps on dashboard page
    $('.toggle-step').on('click', function () {
      $(this).closest('.step').toggleClass('active');
      $(this).closest('.step').siblings().removeClass('active');
    });

    // Highlight menu items
    const wan_menu_elem = $('#toplevel_page_wa-notifier');
    const wa_notifier_cpts = [ 'wa_message_template', 'wa_contact', 'wa_notification' ];
    const current_cpt = $('#wa-notifier-admin-header').data('post-type') || '';
    if( wa_notifier_cpts.includes(current_cpt) ) {
      wan_menu_elem
        .removeClass('wp-not-current-submenu')
        .addClass('wp-has-current-submenu')
        .children('a')
        .removeClass('wp-not-current-submenu')
        .addClass('wp-has-current-submenu');
      wan_menu_elem.find('a[href*="'+current_cpt+'"]').addClass('current').closest('li').addClass('current');
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
      };
    }

    // Validate the message template name
    $('#wa_notifier_template_name').on('keyup', function() {
      var value = $(this).val();
      $(this).val(value.replace(' ', '_').replace(/[^a-zA-Z_]/g, '').toLowerCase());
    });

    // Trigger conditional logic and WhatsApp message template preview
    if($('#wa-notifier-message-template-data').length > 0) {
      showMTFieldsConditionally();
      $('#wa-notifier-message-template-data input, #wa-notifier-message-template-data textarea').on('keyup', function(){
        showMTFieldsConditionally();
        if($(this).attr('data-limit').length > 0) {
          const text = $(this).val();
          const textlength = $(this).val().length;
          const limit = $(this).attr('data-limit');
          if(textlength >= limit) {
            $(this).siblings('label').find('.limit-used').text(limit);
              $(this).val(text.substr(0,limit));
              return false;
          }
          else {
            $(this).siblings('label').find('.limit-used').text(textlength);
              return true;
          }
        }
      });
      $('#wa-notifier-message-template-data select, #wa-notifier-message-template-data input[type="radio"], #wa-notifier-message-template-data input[type="checkbox"]').on('change', function(){
        showMTFieldsConditionally();
      });
    }

    // Message template publish confirmation
    $('.post-type-wa_message_template #publish').on('click', function () {
      const template_name = $('#wa_notifier_template_name').val() || '';
      const body_text = $('#wa_notifier_body_text').val() || '';
      if('' === template_name && '' === body_text) {
        alert('Template name and Body text are required fields.');
        return false;
      }
      if('' === template_name) {
        alert('Template name is a required field.');
        return false;
      }
      if('' === body_text) {
        alert('Body text is a required field.');
        return false;
      }
      return confirm('IMPORTANT NOTE:\n\nClicking "OK" will send your template data to WhatsApp for approval. It might take between 30 minutes to 24 hours for them to review it.\n\nYou\'ll get confirmation email from them after they complete their review. You will be able to send this template to your contacts only after their approval.');
    });

    // Message template delete confirmation
    $('.post-type-wa_message_template .row-actions .delete').on('click', function () {
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
	$(document).on('click', '#import-contacts', function(e){
		e.preventDefault();
		$('.contact-import-options').toggleClass('hide');
	});

	// Select import method
	$(document).on('change', '.csv-import-method', function(){
		var value = $(this).val();
		$('.col-import').addClass('hide');
		$('.col-import-' + value).removeClass('hide');
	});

	// On submission of CSV form
	$(document).on('submit', '#import-contacts-csv', function(e){
		var file = $('#wa-notifier-contacts-csv').val();
		if (file == "") {
			alert("Please select a file to upload.");
			return false;
		}
		else {
			var file_size = $('#wa-notifier-contacts-csv')[0].files[0].size / 1024 / 1024;
			if(file_size > 24) {
				alert("Please select CSV file smaller than 24MB.");
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

	/********************
     * Notification page
     ********************/

     if($('#wa-notifier-notification-data').length > 0) {
     	showNotificationFieldsConditionally();
     	fetchAndDisplayMessageTemplate();

     	$('#wa-notifier-notification-data input, #wa-notifier-notification-data textarea').on('keyup', function(){
	        showNotificationFieldsConditionally();
	    });

     	$('#wa-notifier-notification-data select, #wa-notifier-notification-data input[type="radio"], #wa-notifier-notification-data input[type="checkbox"]').on('change', function(){
	        showNotificationFieldsConditionally();
	    });

     	$('#wa_notifier_notification_message_template').on('change', function(){
     		const template_name = $(this).find(":selected").val();
     		fetchAndDisplayMessageTemplate(template_name);
     	});

     }

  });

})( jQuery );
