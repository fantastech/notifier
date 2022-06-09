(function( $ ) {

  function runConditionalFieldsLogic () {
    var header_type   = $('#wa_notifier_header_type').val();
    var header_text   = $('#wa_notifier_header_text').val() || 'Header text here';
    var body_text     = $('#wa_notifier_body_text').val() || 'Body text here';
    var footer_text   = $('#wa_notifier_footer_text').val() || '';
    var button_type   = $('#wa_notifier_button_type').val();

    var buttons     = $('input[name="wa_notifier_button_num"]:checked').val();

    var button_1_type   = $('#wa_notifier_button_1_type :selected').val();
    var button_1_text   = $('#wa_notifier_button_1_text').val() || '';
    var button_1_url  = $('#wa_notifier_button_1_url').val() || '';
    var button_1_phone_num  = $('#wa_notifier_button_1_phone_num').val() || '';

    var button_2_text   = $('#wa_notifier_button_2_text').val() || '';
    var button_2_url  = $('#wa_notifier_button_2_url').val() || '';
    var button_2_phone_num  = $('#wa_notifier_button_2_phone_num').val() || '';

    // Header
    $('.wa-template-preview .message-head').show().text(header_text)

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

    // Body
    $('.wa-template-preview .message-body').text(body_text);

    // Footer
    if(footer_text !== '') {
      $('.wa-template-preview .message-footer').show().text(footer_text);
    }
    else {
      $('.wa-template-preview .message-footer').hide();
    }

    // Button
    if(button_type == 'none') {
      $('.wa_notifier_button_num_field').hide();
      $('.button-1-wrap').hide();
      $('.button-2-wrap').hide();
      $('.wa-template-preview .message-buttons').hide();
    }
    else {
      $('.wa_notifier_button_num_field').show();
      $('.wa-template-preview .message-buttons').show();

      if(button_1_type == 'URL') {
        $('.wa_notifier_button_1_url_field').show();
        $('.wa_notifier_button_1_phone_num_field').hide();
        if(button_1_text == ''){
          button_1_text = 'Visit Website';
        }
        $('.wa-template-preview .message-button-1 .message-button-img').removeClass('call').addClass('visit');
        $('.wa-template-preview .message-button-1 .message-button-text').text(button_1_text);
      }
      else if (button_1_type == 'PHONE_NUMBER') {
        $('.wa_notifier_button_1_url_field').hide();
        $('.wa_notifier_button_1_phone_num_field').show();
        if(button_1_text == ''){
          button_1_text = 'Call';
        }
        $('.wa-template-preview .message-button-1 .message-button-img').removeClass('visit').addClass('call');
        $('.wa-template-preview .message-button-1 .message-button-text').text(button_1_text);
      }

      if(buttons == '1') {
        $('.button-1-wrap').show();
        $('.button-2-wrap').hide();
        $('#wa_notifier_button_1_type option').prop('disabled', false);
        $('#wa_notifier_button_2_type option').prop('disabled', false);
        $('.wa-template-preview .message-button-2').hide();
      }
      else {
        $('.button-1-wrap').show();
        $('.button-2-wrap').show();
        $('#wa_notifier_button_1_type option').not('option[value="'+ button_1_type +'"]').prop('disabled', true);
        $('#wa_notifier_button_2_type option').not('option[value="'+ button_1_type +'"]').prop('selected', true);
        $('#wa_notifier_button_2_type option[value="'+ button_1_type +'"]').prop('disabled', true);
        var button_2_type   = $('#wa_notifier_button_2_type :selected').val();
        $('.wa-template-preview .message-button-2').show();
        if(button_2_type == 'URL') {
          $('.wa_notifier_button_2_url_field').show();
          $('.wa_notifier_button_2_phone_num_field').hide();
          if(button_2_text == ''){
            button_2_text = 'Visit Website';
          }
          $('.wa-template-preview .message-button-2 .message-button-img').removeClass('call').addClass('visit');
          $('.wa-template-preview .message-button-2 .message-button-text').text(button_2_text);
        }
        else if (button_2_type == 'PHONE_NUMBER') {
          $('.wa_notifier_button_2_url_field').hide();
          $('.wa_notifier_button_2_phone_num_field').show();
          if(button_2_text == ''){
            button_2_text = 'Call';
          }
          $('.wa-template-preview .message-button-2 .message-button-img').removeClass('visit').addClass('call');
          $('.wa-template-preview .message-button-2 .message-button-text').text(button_2_text);
        }
      }
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

    /** Highlight menu items **/
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
        runConditionalFieldsLogic();
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
        $(".edit-php.post-type-wa_message_template .wrap .page-title-action")
          .after(waNotifierTemplates.refresh_mt_status);

      $(".edit-php.post-type-wa_contact .wrap .page-title-action")
          .after(waNotifierTemplates.import_contact);

  });

})( jQuery );
