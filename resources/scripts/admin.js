/* global waNotifier */
(function($) {

	// Make standard ajax calls
	function notifierAjax(data, callback){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: notifierObj.ajaxurl,
			data: data,
			success: callback
		});
	}

	// Show / hide fields as per conditional logic
  function conditionallyShowFields() {
    if ($('.meta-fields').length == 0) {
      return;
    }

    $('.form-field').each(function() {
      var thisField = $(this).find(':input');

      // Conditionally show/hide fields
      var conditions = $(this).attr('data-conditions') || '';
      var conditionsOperator = $(this).attr('data-conditions-operator') || 'OR';
      if (conditions !== '') {
        var fieldElem = $(this);
        var conditionsArray = JSON.parse(conditions);
        var fieldConditionResults = [];

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

          fieldConditionResults.push(showThis);
        });

        if('OR' == conditionsOperator){
          var showField = false;
        }
        else if('AND' == conditionsOperator){
          var showField = fieldConditionResults[0];
        }

        fieldConditionResults.forEach(function(showThis){
          if('OR' == conditionsOperator){
            showField = showField || showThis;
          }
          else if('AND' == conditionsOperator){
            showField = showField && showThis;
          }
        });

        if(showField){
          fieldElem.show();
          var disabled = fieldElem.find(':input').attr('data-disabled') || 'no';
          if('no' == disabled){
            fieldElem.find(':input').removeAttr('disabled');
          }
        }
        else{
          fieldElem.hide();
          fieldElem.find(':input').attr('disabled', 'disabled');
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

	// Fetch and display trigger fields
  function fetchAndDisplayTriggerFields(){
		const post_id = $('#post_ID').val() || 0;
		const trigger = $('#notifier_trigger').val() || '';

		if('' == trigger) {
			$('.notifier-trigger-merge-tags').html('');
			return;
		}

		$('.notifier-trigger-merge-tags').html('<div class="loader"></div>');

		var data = {
			action: 			'notifier_fetch_trigger_fields',
			trigger: 			trigger,
			post_id: 			post_id,
		};

		notifierAjax( data, function(response) {
			if (response.status == 'success') {
				$('.notifier-trigger-merge-tags').html(response.html);
				conditionallyShowFields();
			}
		});

  }

	// Uploading media to WP using wp.media
  var file_frame;
  function uploadMediaFile( button, preview_media ) {
      var button_id = button.attr('id');
      var field_id = button_id.replace( '_button', '' );
      var preview_id = button_id.replace( '_button', '_preview' );

      // Create the media frame.
      file_frame = wp.media.frames.file_frame = wp.media({
        title: button.data( 'uploader_title' ),
        button: {
          text: button.data( 'uploader_button_text' ),
        },
        library: {
        	type: button.data( 'uploader_supported_file_types' ).split(',')
        },
        multiple: false
      });

      // When an image is selected, run a callback.
      file_frame.on( 'select', function() {
        attachment = file_frame.state().get('selection').first().toJSON();
        jQuery("#"+field_id).attr('data-type', attachment.type);
        jQuery("#"+field_id).attr('data-subtype', attachment.subtype);
        jQuery("#"+field_id).siblings('.notifier-media-preview').find('.notifier-media-preview-item').addClass('hide');
        if( preview_media ) {
        if('image' == attachment.type || ('application' == attachment.type && 'pdf' == attachment.subtype) ) {
			jQuery("#"+field_id).attr('data-url', attachment.sizes.full.url);
			jQuery("#"+preview_id+'_image').removeClass('hide').attr('src', attachment.sizes.thumbnail.url);
        }
        else if ('video' == attachment.type) {
        	jQuery("#"+field_id).attr('data-url', attachment.url);
        	jQuery("#"+preview_id+'_video').removeClass('hide').find('source').attr('src', attachment.url);
        	jQuery("#"+preview_id+'_video')[0].load();
        }
      }
      jQuery("#"+field_id).val(attachment.id).change();
      });

      // Finally, open the modal
      file_frame.open();
  }

	$(document).on('ready', function() {

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
		const notifier_cpts = ['wa_notifier_trigger'];
		const current_cpt = $('#notifier-admin-header').data('post-type') || '';
		if (notifier_cpts.includes(current_cpt)) {
			wan_menu_elem.removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
		}

		// Toggle trigger info
		$('.notifier-show-trigger-info').click(function(e){
			e.preventDefault();
			$(this).closest('.notifier-trigger-wrap').find('.notifier-trigger-info').toggleClass('hide');
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

		/*****************
		 * Settings page
		 ****************/

	    $('.notifier-media-upload-button').click(function() {
	        uploadMediaFile( $(this), true );
	    });

	    $('.notifier-media-delete-button').click(function() {
	        $(this).next( '.notifier-media-attachment-id' ).val( '' ).attr('data-tyoe', '').attr('data-subtyoe', '').attr('data-url', '').change();
	        $(this).siblings( '.notifier-media-preview' ).find('img').attr('src', '').addClass('hide');
	        $(this).siblings( '.notifier-media-preview' ).find('source').attr('src', '').parent().addClass('hide');
	        return false;
	    });

		/*****************
		 * Triggers page
		 ****************/

	    $(document).on('change', '.notifier-enable-trigger', function(){
	    	var enabled = ($(this).prop('checked')) ? 'yes' : 'no';
	    	var postId = $(this).data('post-id');
	    	var $this = $(this);
	    	data = {
	    		action: 'notifier_change_trigger_status',
	    		post_id: postId,
	    		enabled: enabled
	    	}
	    	notifierAjax(data, function(response){});
	  	});

  		// Do stuff if on the trigger edit page
		if ($('#notifier-trigger-data').length > 0) {
			fetchAndDisplayTriggerFields(); // Fetch and display data and recipient fields
			$('#notifier-trigger-data :input').on('change keyup', function(){
				if('notifier_trigger' == $(this).attr('name')){
					fetchAndDisplayTriggerFields();
				}
			});
		}

		// Select all checkbox fields
		$(document).on('click', '.notifier-select-all-checkboxes', function(e){
			e.preventDefault();
			$(this).closest('.trigger-fields-wrap').find('input[type="checkbox"]').prop('checked', true);
		});

		// Unselect all checkbox fields
		$(document).on('click', '.notifier-unselect-all-checkboxes', function(e){
			e.preventDefault();
			$(this).closest('.trigger-fields-wrap').find('input[type="checkbox"]').prop('checked', false);
		});

	});

})(jQuery);
