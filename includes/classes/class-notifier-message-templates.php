<?php
/**
 * Message templates page class
 *
 * @package    Wa_Notifier
 */
class Notifier_Message_Templates {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
		add_filter( 'admin_head', array( __CLASS__ , 'show_transient_admin_notices') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_message_template', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit'), 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts'), 10, 2 );
		add_action( 'post_submitbox_minor_actions', array( __CLASS__ , 'add_submitbox_meta' ) );
		add_filter( 'manage_wa_message_template_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_message_template_posts_custom_column', array( __CLASS__ , 'add_column_content' ), 10, 2 );
		add_filter( 'bulk_post_updated_messages', array(__CLASS__, 'change_deletion_message'), 10, 2);
		add_action( 'save_post_wa_message_template', array(__CLASS__, 'save_meta'), 10, 2 );
		add_action( 'before_delete_post', array(__CLASS__, 'delete_template'), 10, 2 );
		add_filter( 'post_updated_messages', array(__CLASS__, 'update_save_messages') );
		add_filter( 'admin_body_class', array(__CLASS__, 'admin_body_class'));
		add_action( 'admin_head', array(__CLASS__, 'handle_refresh_status_request') );
		add_filter( 'notifier_admin_html_templates', array(__CLASS__, 'admin_html_templates') );
		add_action( 'notifier_refresh_mt_status', array(__CLASS__, 'refresh_mt_status') );
		/* ==Notifier_Pro_Code_Start== */
		add_action( 'notifier_after_meta_field', array(__CLASS__, 'add_variable_button'), 10, 2 );
		/* ==Notifier_Pro_Code_End== */
	}

	/**
	 * Register custom post type
	 */
	public static function register_cpt () {
		notifier_register_post_type ( 'wa_message_template', 'Message Template', 'Message Templates');
	}

	/**
	 * Show transient admin notices
	 */
	public static function show_transient_admin_notices () {
		global $post;
		if ('wa_message_template' != get_post_type()) {
			return;
		}
		$notice = get_transient( "mt_notice_{$post->ID}" );
		if ( $notice ) {
			delete_transient( "mt_notice_{$post->ID}" );
			new Notifier_Admin_Notices(array($notice));
		}
	}

	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( NOTIFIER_PREFIX . 'api_credentials_validated');
		if (!$api_credentials_validated) {
			return;
		}

		add_submenu_page( NOTIFIER_NAME, 'Whatsapp Message Templates', 'Message Templates', 'manage_options', 'edit.php?post_type=wa_message_template' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        NOTIFIER_NAME . '-message-template-data',
	        'Template Data',
	        'Notifier_Message_Templates::output',
	        'wa_message_template'
	    );

	    add_meta_box(
	        NOTIFIER_NAME . '-message-template-preview',
	        'Preview Template',
	        'Notifier_Message_Templates::output_preview',
	        'wa_message_template',
	        'side'
	    );

	    remove_meta_box( 'submitdiv', 'wa_message_template', 'side' );
    	add_meta_box( 'submitdiv', 'Template Actions', 'post_submit_meta_box', 'wa_message_template', 'side', 'high' );
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once NOTIFIER_PATH . 'views/admin-message-templates-meta-box.php';
	}

	/**
	 * Output preview meta box
	 */
	public static function output_preview () {
		?>
			<div class="wa-template-preview">
				<div class="message-container">
					<div class="message-content">
						<div class="message-head message-head-text">
							Header text here
						</div>
						<div class="message-head message-head-media">
							<div class="message-head-media-inner"></div>
							<div class="message-head-media-preview">
								<img src="" class="message-head-media-preview-image hide" />
								<video class="message-head-media-preview-video hide" controls muted width="100%" height="140">
									<source src="" type="video/mp4">
								</video>
							</div>
						</div>
						<div class="message-body">
							Body text here
						</div>
						<div class="message-footer">
							Footer text text
						</div>
						<div class="message-date">
							10:00 AM
						</div>
					</div>
					<div class="message-buttons">
						<div class="message-button-1">
							<span class="message-button-img"></span>
							<span class="message-button-text"></span>
						</div>
						<div class="message-button-2">
							<span class="message-button-img"></span>
							<span class="message-button-text"></span>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Remove inline edit from Bulk Edit
	 */
	public static function remove_bulk_actions( $actions ) {
        unset( $actions['inline'] );
        return $actions;
    }

    /**
	 * Remove inline Quick Edit
	 */
    public static function remove_quick_edit( $actions, $post ) {
    	if ('wa_message_template' == $post->post_type) {
	    	unset($actions['inline hide-if-no-js']);
	    }
    	return $actions;
	}

	/**
	 * Change text of buttons and links
	 */
	public static function change_texts( $translation, $text ) {
		if ( 'wa_message_template' == get_post_type() ) {
			if ( 'Publish' === $text) {
				return 'Submit for Approval';
			} elseif ( 'Update' === $text ) {
				return 'Submit for Review';
			} elseif ( 'Move to Trash' === $text ) {
				return 'Trash';
			}
		}
		return $translation;
	}

	/**
	 * Update save action messages
	 */
	public static function update_save_messages ($messages) {
		$messages['wa_message_template'][1] = '';
		$messages['wa_message_template'][6] = '';
		return $messages;
	}

	/**
	 * Add message template meta data in submit box
	 */
	public static function add_submitbox_meta () {
		global $post_id;
		$mt_status = get_post_meta ( $post_id, NOTIFIER_PREFIX . 'status', true);

		$refresh_url = '?' . http_build_query(array_merge($_GET, array('refresh_status'=>'1')));
		$refresh_button = '<a href="' . $refresh_url . '" class="refresh-status" title="Click here to refresh status"><img src="' . NOTIFIER_URL . '/assets/images/refresh.svg"></a>';

		if (!$mt_status) {
			$mt_status = 'DRAFT';
			$refresh_button = '';
		}

		echo '<div class="mt-status">';
		echo '<b>Status:</b> <span class="status status-' . esc_attr(strtolower($mt_status)) . '">' . esc_html($mt_status) . '</span>';
		if ('APPROVED' !== $mt_status) {
			echo wp_kses_post($refresh_button);
		}
		echo '</div>';

		if ('REJECTED' == $mt_status) {
			echo '<p><b>Note:</b> Your template was rejected by WhatsApp. Please make sure you\'re following their <a href="https://developers.facebook.com/docs/whatsapp/message-templates/guidelines" target="_blank">Message Template Guidelines</a> when creating the template. You can edit this template and re-submit it for review.</p>';
		}
	}

	/**
	 * Add columns to list page
	 */
	public static function add_columns ($columns) {
		$columns['mt_name'] = 'Template Name';
		$columns['mt_category'] = 'Category';
		$columns['mt_preview'] = 'Preview';
		$columns['mt_status'] = 'Status';
		unset($columns['date']);
		return $columns;
	}

	/**
	 * Add column content
	 */
	public static function add_column_content ( $column, $post_id ) {
		if ( 'mt_name' === $column ) {
		    $template_name = get_post_meta( $post_id, NOTIFIER_PREFIX . 'template_name', true);
		    echo ($template_name) ? '<code>' . esc_html( $template_name ) . '</code>' : '-';
		}

		if ( 'mt_category' === $column ) {
		    $category = get_post_meta( $post_id, NOTIFIER_PREFIX . 'category', true);
		    echo ($category) ? esc_html( $category ) : '-';
		}

		if ( 'mt_preview' === $column ) {
		    $preview = get_post_meta( $post_id, NOTIFIER_PREFIX . 'body_text', true);
		    echo ($preview) ? '<span class="truncate-string">' . esc_html( strip_tags( $preview ) ) . '</span>' : '-';
		}

		if ( 'mt_status' === $column ) {
		    $status = get_post_meta( $post_id, NOTIFIER_PREFIX . 'status', true);
		    echo ($status) ? '<span class="status status-' . esc_attr( strtolower($status) ) . '">' . esc_html( $status ) . '</span>' : '-';
		}
	}

	/**
	 * Change deletion message
	 */
	public static function change_deletion_message ( $bulk_messages, $count ) {
		if ( 'wa_message_template' !== get_post_type() ) {
			return $bulk_messages;
		}

		// translators: %s: number of templates
		$bulk_messages['post']['trashed'] = _n( '%s message template moved to the Trash. To delete it from the website and from WhatsApp server, permanently delete it from Trash.', '%s message templates moved to the Trash. To delete them from the website and from WhatsApp server, permanently delete it from Trash.', (int) $count, 'notifier' );

		// translators: %s: number of templates
		$bulk_messages['post']['deleted'] = _n( '%s message template deleted permanently from the website and from WhatsApp server.', '%s message template deleted permanently from the website and from WhatsApp server.', (int) $count, 'notifier' );

		return $bulk_messages;
	}

	/**
	 * Save meta
	 */
	public static function save_meta( $post_id, $post ) {
 		if ( isset($_GET['action']) && in_array( $_GET['action'], array('trash', 'untrash') ) ) {
	        return;
	    }

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$template_data = array();

		foreach ($_POST as $key => $data) {
			if (strpos($key, NOTIFIER_PREFIX) !== false) {
				if (NOTIFIER_PREFIX . 'body_text' == $key) {
					$template_data[$key] = sanitize_textarea_field( wp_unslash ($data) );
				} else {
					$template_data[$key] = sanitize_text_field( wp_unslash ($data) );
				}
			    update_post_meta( $post_id, $key, $template_data[$key]);
			}
		}
		self::submit_template_data_to_cloud_api($template_data);
	}

	/**
	 * Send template data to Cloud API
	 */
	public static function submit_template_data_to_cloud_api($data) {
		global $post;
		$post_id = isset($post->ID) ? $post->ID : 0;
		$args = array (
			'name' => isset($data[ NOTIFIER_PREFIX . 'template_name' ]) ? $data[ NOTIFIER_PREFIX . 'template_name' ] : '',
			'category' => isset($data[ NOTIFIER_PREFIX . 'category' ]) ? $data[ NOTIFIER_PREFIX . 'category' ] : '',
			'language' => isset($data[ NOTIFIER_PREFIX . 'language' ]) ? $data[ NOTIFIER_PREFIX . 'language' ] : ''
		);

		// Header
		if (isset($data[NOTIFIER_PREFIX . 'header_type'])) {
			if ('text' == $data[NOTIFIER_PREFIX . 'header_type']) {
				$args['components'][] = array (
					'type' => 'HEADER',
					'format' => 'TEXT',
					'text' => isset($data[NOTIFIER_PREFIX . 'header_text']) ? $data[NOTIFIER_PREFIX . 'header_text'] : ''
				);
			}
			/* ==Notifier_Pro_Code_Start== */
			elseif ('media' == $data[NOTIFIER_PREFIX . 'header_type']) {
				$format = isset($data[NOTIFIER_PREFIX . 'media_type']) ? $data[NOTIFIER_PREFIX . 'media_type'] : '';

				switch($format) {
					case 'IMAGE':
						$media_id = isset($data[NOTIFIER_PREFIX . 'media_item_image']) ? $data[NOTIFIER_PREFIX . 'media_item_image'] : 0;
						break;

					case 'VIDEO':
						$media_id = isset($data[NOTIFIER_PREFIX . 'media_item_video']) ? $data[NOTIFIER_PREFIX . 'media_item_video'] : 0;
						break;

					case 'DOCUMENT':
						$media_id = isset($data[NOTIFIER_PREFIX . 'media_item_document']) ? $data[NOTIFIER_PREFIX . 'media_item_document'] : 0;
						break;
				}

				$handle = Notifier::wa_cloud_api_upload_media($media_id);

				$args['components'][] = array (
					'type' => 'HEADER',
					'format' => $format,
					'example' => array(
						'header_handle' => array ( $handle )
					)
				);
			}
			/* ==Notifier_Pro_Code_End== */
		}

		// Body
		$args['components'][] = array (
			'type' => 'BODY',
			'text' => isset($data[NOTIFIER_PREFIX . 'body_text']) ? $data[NOTIFIER_PREFIX . 'body_text'] : ''
		);

		// Footer
		if (isset($data[NOTIFIER_PREFIX . 'footer_text']) && '' != $data[NOTIFIER_PREFIX . 'footer_text']) {
			$args['components'][] = array (
				'type' => 'FOOTER',
				'text' => isset($data[NOTIFIER_PREFIX . 'footer_text']) ? $data[NOTIFIER_PREFIX . 'footer_text'] : ''
			);
		}

		// Buttons
		if ( isset($data[NOTIFIER_PREFIX . 'button_type']) && 'none' !== $data[NOTIFIER_PREFIX . 'button_type']) {
			$button_component = array();
			$button_component['type'] = 'BUTTONS';

			$btn_1_type = isset($data[NOTIFIER_PREFIX . 'button_1_type']) ? $data[NOTIFIER_PREFIX . 'button_1_type'] : '';
			$btn_1_text = isset($data[NOTIFIER_PREFIX . 'button_1_text']) ? $data[NOTIFIER_PREFIX . 'button_1_text'] : '';

			$btn_1 = array (
				'type' => $btn_1_type,
				'text' => $btn_1_text,
			);

			if ('URL' == $btn_1_type) {
				$btn_1['url'] = isset($data[NOTIFIER_PREFIX . 'button_1_url']) ? $data[NOTIFIER_PREFIX . 'button_1_url'] : '';
			} elseif ('PHONE_NUMBER' == $btn_1_type) {
				$btn_1['phone_number'] = isset($data[NOTIFIER_PREFIX . 'button_1_phone_num']) ? $data[NOTIFIER_PREFIX . 'button_1_phone_num'] : '';
			}

			$button_component['buttons'][] = $btn_1;

		 	$btn_2 = array();
			if (isset($data[NOTIFIER_PREFIX . 'button_num']) && '2' == $data[NOTIFIER_PREFIX . 'button_num']) {
				$btn_2_type = isset($data[NOTIFIER_PREFIX . 'button_2_type']) ? $data[NOTIFIER_PREFIX . 'button_2_type'] : '';
				$btn_2_text = isset($data[NOTIFIER_PREFIX . 'button_2_text']) ? $data[NOTIFIER_PREFIX . 'button_2_text'] : '';

				$btn_2 = array (
					'type' => $btn_2_type,
					'text' => $btn_2_text,
				);

				if ('URL' == $btn_2_type) {
					$btn_2['url'] = isset($data[NOTIFIER_PREFIX . 'button_2_url']) ? $data[NOTIFIER_PREFIX . 'button_2_url'] : '';
				} elseif ('PHONE_NUMBER' == $btn_2_type) {
					$btn_2['phone_number'] = isset($data[NOTIFIER_PREFIX . 'button_2_phone_num']) ? $data[NOTIFIER_PREFIX . 'button_2_phone_num'] : '';
				}
				$button_component['buttons'][] = $btn_2;
			}

			$args['components'][] = $button_component;
		}

		$response = Notifier::wa_business_api_request( 'message_templates', $args );

		if (isset($response->error)) {
			$notice = array(
				'type' => 'error'
			);
			if ( isset($response->error->error_user_title) ) {
				$notice['message'] = '<b>' . $response->error->error_user_title . '</b> - ' . $response->error->error_user_msg;
			} elseif (isset($response->error->message)) {
				$notice['message'] = $response->error->message;
			}
			set_transient( 'mt_notice_' . $post_id, $notice, 60 );
		}

		if (isset($response->id)) {
			update_post_meta( $post_id, NOTIFIER_PREFIX . 'template_id', $response->id);
			update_post_meta( $post_id, NOTIFIER_PREFIX . 'status', 'PENDING');
			$notice = array(
				'message' => 'Template submitted to WhatsApp for approval. You\'ll get an email from WhatsApp about approval status.',
				'type' => 'success'
			);
			set_transient( 'mt_notice_' . $post_id, $notice, 60 );
			if ( false === as_has_scheduled_action( 'notifier_refresh_mt_status', array($post_id), 'notifier' ) ) {
			 	as_enqueue_async_action( 'notifier_refresh_mt_status', array($post_id), 'notifier' );
			}
		}
	}

	/**
	 * Delete template from Cloud API
	 */
	public static function delete_template ($post_id, $post) {
		if ( 'wa_message_template' !== get_post_type($post_id) ) {
 			return;
 		}

		$template_name = get_post_meta( $post_id, NOTIFIER_PREFIX . 'template_name', true);
		$args = array (
			'name' => $template_name
		);

		$response = Notifier::wa_business_api_request( 'message_templates', $args, 'DELETE' );

		if (isset($response->error)) {
			$notice = array(
				'message' => '<b>' . $response->error->error_user_title . '</b> - ' . $response->error->error_user_msg,
				'type' => 'error'
			);
			set_transient( 'mt_notice_' . $post_id, $notice, 60 );
		}
	}

	/**
	 * Add class to body tag
	 */
	public static function admin_body_class ($classes) {
		global $post_id, $current_screen;
		if ( 'wa_message_template' == $current_screen->id ) {
 			$status = get_post_meta( $post_id, NOTIFIER_PREFIX . 'status', true);
	 		if ('' != $status) {
	 			$classes = $classes . ' mt-status-' . strtolower($status);
	 		} else {
	 			$classes = $classes . ' mt-status-draft';
	 		}
 		}

 		if ( 'edit-wa_message_template' == $current_screen->id ) {
 			$classes = $classes . ' edit-wa_message_template';
 		}

 		return $classes;
	}

	/**
	 * Handle message tempalte status refresh request
	 */
	public static function handle_refresh_status_request () {
		global $current_screen;
		if ( 'wa_message_template' !== $current_screen->post_type ) {
 			return;
 		}

 		if (!isset($_GET['refresh_status'])) {
 			return;
 		}

 		$response = Notifier::wa_business_api_request('message_templates', array(), 'GET');

		if (isset($response->error)) {
			$notices[] = array(
				'message' => 'Status not refreshed. Error Code ' . $response->error->code . ': ' . $response->error->message ,
				'type' => 'error'
			);

			new Notifier_Admin_Notices($notices);
		} else {
			$wa_message_templates = $response->data;
			$local_message_templates = get_posts(
				array (
					'post_type' => 'wa_message_template',
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields' => 'ids'
				)
			);

			foreach ($local_message_templates as $post_id) {
				$template_name = get_post_meta ( $post_id, NOTIFIER_PREFIX . 'template_name', true);
				foreach ($wa_message_templates as $template) {
					if ($template_name == $template->name && 'en_US' == $template->language) {
						update_post_meta ( $post_id, NOTIFIER_PREFIX . 'status', $template->status);
						break;
					}
				}
			}

			$notices[] = array(
				'message' => 'Status updated successfully.' ,
				'type' => 'success'
			);

			new Notifier_Admin_Notices($notices);
		}
	}

	/**
	 * Refresh status of specific message template
	 */
	public static function refresh_mt_status ($mt_id) {
		$response = Notifier::wa_business_api_request('message_templates', array(), 'GET');
		if (!isset($response->error)) {
			$wa_message_templates = $response->data;
			$template_name = get_post_meta ( $mt_id, NOTIFIER_PREFIX . 'template_name', true);
			foreach ($wa_message_templates as $template) {
				if ($template_name == $template->name && 'en_US' == $template->language) {
					update_post_meta ( $mt_id, NOTIFIER_PREFIX . 'status', $template->status);
					break;
				}
			}
		} else {
			error_log('[WA Notifer] Message template status update error: ' . json_encode($response));
		}

		if ( 'PENDING' == $template->status ) {
		 	as_schedule_single_action( time() + 60, 'notifier_refresh_mt_status', array($mt_id), 'notifier' );
		}
	}

	/**
	 * Admin HTML templates
	 */
	public static function admin_html_templates($templates) {
		$templates[] = 'wa-mt-refresh';
		return $templates;
	}

	/**
	 * Get approved message templates
	 */
	public static function get_approved_message_templates ($show_select = false) {
		$message_templates = get_posts(
			array (
				'post_type' => 'wa_message_template',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields' => 'ids',
				'meta_query'	=> array(
				    array(
						'key'   => NOTIFIER_PREFIX . 'status',
						'value' => 'APPROVED',
				    ),
				)
			)
		);

		$templates = array();

		if ($show_select) {
			$templates[''] = 'Select message template';
		}

		foreach ($message_templates as $template_id) {
			$templates[$template_id] = get_the_title ($template_id);
		}

		return $templates;
	}

	/**
	 * Send message template to phone number
	 */
	public static function send_message_template_to_number ($template_id, $notification_id, $phone_number, $context_args = array()) {
		$template_name = get_post_meta( $template_id, NOTIFIER_PREFIX . 'template_name', true);
		$language = get_post_meta( $template_id, NOTIFIER_PREFIX . 'language', true);

		// Default message template sending args
		$args = array (
			'messaging_product' => 'whatsapp',
			'recipient_type' => 'individual',
			'to' => $phone_number,
			'type' => 'template',
			'template' => array (
				'name' => $template_name,
				'language' => array (
					'code' => $language
				)
			)
		);

		/* ==Notifier_Pro_Code_Start== */
		$header_type = get_post_meta( $template_id, NOTIFIER_PREFIX . 'header_type', true);
		$header_text = get_post_meta( $template_id, NOTIFIER_PREFIX . 'header_text', true);
		$body_text = get_post_meta( $template_id, NOTIFIER_PREFIX . 'body_text', true);
		$media_type = get_post_meta( $template_id, NOTIFIER_PREFIX . 'media_type', true);

		$variable_mapping = get_post_meta( $notification_id, NOTIFIER_PREFIX . 'notification_variable_mapping', true);

		$total_header_vars = 0;
		$total_body_vars = 0;

		if ('text' == $header_type) {
			preg_match_all('~\{\{\s*(.*?)\s*\}\}~', $header_text, $header_var_matches);
			$header_vars = $header_var_matches[0];
			$total_header_vars = count($header_vars);

			// If merge tag present in header
			if ($total_header_vars > 0 && is_array($variable_mapping)) {
				$header_merge_tag_id = isset($variable_mapping['header'][0]) ? $variable_mapping['header'][0] : '';
				$header_merge_tag_value = Notifier_Notification_Merge_Tags::get_notification_merge_tag_value($header_merge_tag_id, $context_args);
				if ($header_merge_tag_value) {
					$args['template']['components'][] = array (
						'type'			=> 'header',
						'parameters'	=> array(
							array(
								'type'		=> 'text',
								'text'		=> $header_merge_tag_value
							)
						)
					);
				}
			}
		}
		elseif ('media' == $header_type){
			$header_media_merge_tag = isset($variable_mapping['header']['media']) ? $variable_mapping['header']['media'] : '';
			$header_media_url = Notifier_Notification_Merge_Tags::get_notification_merge_tag_value($header_media_merge_tag, $context_args);
			if('' != $header_media_url) {
				$args['template']['components'][] = array (
					'type'			=> 'header',
					'parameters'	=> array(
						array(
							'type'		=> 'image',
							'image'		=> array(
								'link'	=> $header_media_url
							)
						)
					)
				);
			}
		}

		preg_match_all('~\{\{\s*(.*?)\s*\}\}~', $body_text, $body_var_matches);
		$body_vars = $body_var_matches[0];
		$total_body_vars = count($body_vars);

		// If merge tag present in body
		if ($total_body_vars > 0 && is_array($variable_mapping)) {
			$parameters = array();
			$body_merge_tag_values = array();
			for ($num = 0; $num < $total_body_vars; $num++) {
				$body_merge_tag_id = isset($variable_mapping['body'][$num]) ? $variable_mapping['body'][$num] : '';
				$parameters[$num]['type'] = 'text';
				$parameters[$num]['text'] = Notifier_Notification_Merge_Tags::get_notification_merge_tag_value($body_merge_tag_id, $context_args);
			}
			$args['template']['components'][] = array (
				'type'			=> 'body',
				'parameters'	=> $parameters
			);
		}
		/* ==Notifier_Pro_Code_End== */
		$response = Notifier::wa_cloud_api_request('messages', $args);
		if (isset($response->error)) {
			error_log('[Notifier] WhatsApp Send Error: ' . json_encode($response->error));
			return false;
		} else {
			return true;
		}
	}

	/* ==Notifier_Pro_Code_Start== */
	/**
	 * Add variable button to heading and body text fields.
	 */
	public static function add_variable_button ($field, $post) {
		if (NOTIFIER_PREFIX . 'header_text' == $field['id'] && 'disabled' != $field['custom_attributes']['disabled']) {
			echo '<button class="add-variable" data-type="header" title="Add Variable"><span class="dashicons dashicons-plus-alt2"></span><span class="hide">Add Variable</span></button>';
		}
		if (NOTIFIER_PREFIX . 'body_text' == $field['id'] && 'disabled' != $field['custom_attributes']['disabled']) {
			echo '<button class="add-variable" data-type="body" title="Add Variable"><span class="dashicons dashicons-plus-alt2"></span><span class="hide">Add Variable</span></button>';
		}
		return;
	}
	/* ==Notifier_Pro_Code_End== */
}
