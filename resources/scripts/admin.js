/* global waNotifier */
(function($) {

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


	});

})(jQuery);
