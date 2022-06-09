<?php
/**
 * Message templates page class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Message_Templates {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_message_template', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit') , 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts') , 10, 2 );
		add_action( 'post_submitbox_minor_actions', array( __CLASS__ , 'add_submitbox_meta' ) );
		add_filter( 'manage_wa_message_template_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_message_template_posts_custom_column', array( __CLASS__ , 'add_column_content' ) , 10, 2 );
		add_filter( 'bulk_post_updated_messages', array(__CLASS__, 'change_deletion_message'), 10, 2);
		add_filter( 'post_updated_messages', array(__CLASS__, 'update_template_save_messages') );
		add_action( 'save_post_wa_message_template', array(__CLASS__, 'save_meta'), 10, 2 );
		add_action( 'admin_notices', array(__CLASS__, 'show_admin_notices'), 10, 2 );
		add_action( 'before_delete_post', array(__CLASS__, 'delete_template'), 10, 2 );
		add_filter( 'admin_body_class', array(__CLASS__, 'admin_body_class'));
		add_action( 'admin_head', array(__CLASS__, 'handle_refresh_status') );
		add_filter( 'wa_notifier_admin_html_templates', array(__CLASS__, 'admin_html_templates') );
	}


	/**
	 * Register custom post type
	 */
	public function register_cpt () {
		wa_notifier_register_post_type ( 'wa_message_template', 'Message Template', 'Message Templates');
	}
	
	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( WA_NOTIFIER_PREFIX . 'api_credentials_validated');
		if(!$api_credentials_validated) {
			return;
		}

		add_submenu_page( WA_NOTIFIER_NAME, 'Whatsapp Message Templates', 'Message Templates', 'manage_options', 'edit.php?post_type=wa_message_template' );

	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        WA_NOTIFIER_NAME . '-message-template-data',
	        'Template Data',
	        'WA_Notifier_Message_Templates::output',
	        'wa_message_template'
	    );

	    add_meta_box(
	        WA_NOTIFIER_NAME . '-message-template-preview',
	        'Preview Template',
	        'WA_Notifier_Message_Templates::output_preview',
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
		include_once WA_NOTIFIER_PATH . 'views/admin-message-templates-meta-box.php';
	}

	/**
	 * Output preview meta box
	 */
	public static function output_preview () {
		?>
			<div class="wa-template-preview">
				<div class="message-container">
					<div class="message-content">
						<div class="message-head">
							Header text here
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
	public static function remove_bulk_actions( $actions ){
        unset( $actions['inline'] );
        return $actions;
    }

    /**
	 * Remove inline Quick Edit
	 */
    public static function remove_quick_edit( $actions, $post ) { 
    	unset($actions['inline hide-if-no-js']);
    	return $actions;
	}

	/**
	 * Change text of buttons and links
	 */
	public static function change_texts( $translation, $text ) {
		if ( 'wa_message_template' == get_post_type() ) {
			if ( $text == 'Publish') {
				return 'Submit for Approval'; 
			}
			elseif ( $text == 'Update' ) {
				return 'Submit for Review'; 
			}
			elseif ( $text == 'Move to Trash' ) {
				return 'Trash'; 
			}
		}
		return $translation;
		
	}

	/**
	 * Add message template meta data in submit box
	 */
	public static function add_submitbox_meta () {
		global $post_id;
		$mt_status = get_post_meta ( $post_id, WA_NOTIFIER_PREFIX . 'status' , true);
		if(!$mt_status) {
			$mt_status = 'DRAFT';
		}
		$refresh_url = '?' . http_build_query(array_merge($_GET, array("refresh_status"=>"1")));
		echo '<div class="mt-status">';
		echo '<b>Status:</b> <span class="status status-' . strtolower($mt_status) . '">' . $mt_status . '</span>';
		echo '<a href="'.$refresh_url.'" class="refresh-status" title="Click here to refresh status"><img src="'.WA_NOTIFIER_URL . '/assets/images/refresh.svg"></a>';
		echo '</div>';

		if('REJECTED' == $mt_status) {
			echo '<p>Your template was rejected by WhatsApp. Please check your email for details on why it was rejected. You can edit the template and submit it for review again.</p>';
		}
	}

	/**
	 * Add columns to list page
	 */
	public static function add_columns ($columns) {
		$columns['mt_name'] = 'Template Name';
		$columns['mt_status'] = 'Status';
		$columns['mt_category'] = 'Category';
		$columns['mt_preview'] = 'Preview';
		unset($columns['date']);
		return $columns;
	}

	/**
	 * Add column content
	 */
	public static function add_column_content ( $column, $post_id ) {
		if ( 'mt_name' === $column ) {
		    $template_name = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'template_name', true);
		    echo '<code>'.$template_name.'</code>';
		}

		if ( 'mt_status' === $column ) {
		    $status = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'status', true);
		    echo '<span class="status status-'.strtolower($status).'">'.$status.'</span>';
		}

		if ( 'mt_category' === $column ) {
		    $category = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'category', true);
		    echo $category;
		}

		if ( 'mt_preview' === $column ) {
		    $preview = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'body_text', true);
		    echo '<span class="truncate-string">' . strip_tags( $preview ) . '</span>'; 
		}
	}

	/**
	 * Add column content
	 */
	public static function change_deletion_message ( $bulk_messages, $count ) {
		if( 'wa_message_template' !== get_post_type() ) {
			return $bulk_messages;
		}
		
		$bulk_messages['post']['trashed'] = _n( '%s message template moved to the Trash. To delete it from the website and from WhatsApp server, permanently delete it from Trash.', '%s message templates moved to the Trash. To delete them from the website and from WhatsApp server, permanently delete it from Trash.', (int) $count );

		$bulk_messages['post']['deleted'] = _n( '%s message template deleted permanently from the website and from WhatsApp server.', '%s message template deleted permanently from the website and from WhatsApp server.', (int) $count );

		return $bulk_messages;
	}

	/**
	 * Add column content
	 */
	public static function update_template_save_messages ($messages) {
		if ( 'wa_message_template' !== get_post_type() ) {
 			return;
 		}

		unset($messages['post'][1]);
	    unset($messages['post'][6]);
		return $messages;
	}

	/**
	 * Save meta
	 */
	public static function save_meta( $post_id, $post ) {
 		if ( isset($_GET['action']) && in_array( $_GET['action'] , array('trash', 'untrash') ) ) {
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
			if (strpos($key, WA_NOTIFIER_PREFIX) !== false) {
				$template_data[$key] = sanitize_text_field( wp_unslash ($data) );
			    update_post_meta( $post_id, $key, $template_data[$key]);
			}
		}

		self::submit_template_data_to_cloud_api($template_data);

	}

	/**
	 * Send template data to Cloud API
	 */
	public static function submit_template_data_to_cloud_api($template_data) {
		global $post_id;
		$args = array (
			'name' => $template_data[ WA_NOTIFIER_PREFIX . 'template_name' ],
			'category' => $template_data[ WA_NOTIFIER_PREFIX . 'category' ],
			'language' => $template_data[ WA_NOTIFIER_PREFIX . 'language' ]
		);

		// Header
		// if( 'text' == $template_data[WA_NOTIFIER_PREFIX . 'header_type']) {
		// 	$args['components'][] = array (
		// 		'type' => 'HEADER',
		// 		'format' => 'TEXT',
		// 		'text' => $template_data[WA_NOTIFIER_PREFIX . 'header_text']
		// 	);
		// }
		// elseif( 'media' == $template_data[WA_NOTIFIER_PREFIX . 'header_type']) {
		// 	$args['components'][] = array (
		// 		'type' => 'HEADER',
		// 		'format' => $template_data[WA_NOTIFIER_PREFIX . 'media_type']
		// 	);
		// }
		$args['components'][] = array (
			'type' => 'HEADER',
			'format' => 'TEXT',
			'text' => $template_data[WA_NOTIFIER_PREFIX . 'header_text']
		);

		// Body
		$args['components'][] = array (
			'type' => 'BODY',
			'text' => $template_data[WA_NOTIFIER_PREFIX . 'body_text']
		);

		// Footer
		if('' !== $template_data[WA_NOTIFIER_PREFIX . 'footer_text']) {
			$args['components'][] = array (
				'type' => 'FOOTER',
				'text' => $template_data[WA_NOTIFIER_PREFIX . 'footer_text']
			);
		}

		// Buttons
		if('none' !== $template_data[WA_NOTIFIER_PREFIX . 'button_type']) {
			$button_component = array();
			$button_component['type'] = 'BUTTONS';

			$btn_1_type = $template_data[WA_NOTIFIER_PREFIX . 'button_1_type'];
			$btn_1_text = $template_data[WA_NOTIFIER_PREFIX . 'button_1_text'];

			$btn_1 = array (
				'type' => $btn_1_type,
				'text' => $btn_1_text,
			);

			if('URL' == $btn_1_type) {
				$btn_1['url'] = $template_data[WA_NOTIFIER_PREFIX . 'button_1_url'];
			}
			elseif ('PHONE_NUMBER' == $btn_1_type) {
				$btn_1['phone_number'] = $template_data[WA_NOTIFIER_PREFIX . 'button_1_phone_num'];
			}

			$button_component['buttons'][] = $btn_1;

		 	$btn_2 = array();
			if('2' == $template_data[WA_NOTIFIER_PREFIX . 'button_num']) {
				$btn_2_type = $template_data[WA_NOTIFIER_PREFIX . 'button_2_type'];
				$btn_2_text = $template_data[WA_NOTIFIER_PREFIX . 'button_2_text'];

				$btn_2 = array (
					'type' => $btn_2_type,
					'text' => $btn_2_text,
				);

				if('URL' == $btn_2_type) {
					$btn_2['url'] = $template_data[WA_NOTIFIER_PREFIX . 'button_2_url'];
				}
				elseif ('PHONE_NUMBER' == $btn_2_type) {
					$btn_2['phone_number'] = $template_data[WA_NOTIFIER_PREFIX . 'button_2_phone_num'];
				}
				$button_component['buttons'][] = $btn_2;
			}

			$args['components'][] = $button_component;
		}
		// echo "<pre>";
		// print_r($args); die;

		$response = WA_Notifier::wa_business_api_request( 'message_templates', $args );
		
		if(isset($response->error)) {
			$notice = array(
				'type' => 'error'
			);
			if( isset($response->error->error_user_title) ){
				$notice['message'] = '<b>' . $response->error->error_user_title . '</b> - ' . $response->error->error_user_msg;
			}
			elseif (isset($response->error->message)) {
				$notice['message'] = $response->error->message . 'Yaha';
			}
			set_transient( "mt_notice_$post_id", $notice, 60 );
		}

		if(isset($response->id)) {
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'template_id', $response->id);
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'status', 'PENDING');
			$notice = array(
				'message' => 'Template submitted to WhatsApp for approval. Check your email for updates from WhatsApp about it.',
				'type' => 'success'
			);
			set_transient( "mt_notice_$post_id", $notice, 60 );
			//WA_Notifier::wa_business_api_request( 'message_templates', $args );
		}
		
		//echo "<pre>"; print_r($response); die;
	}

	/**
	 * Show admin error noticces
	 */
	public static function show_admin_notices () {
		if ( 'wa_message_template' !== get_post_type() ) {
 			return;
 		}

 		global $post;
 		$notice = get_transient( "mt_notice_{$post->ID}" );
		if ( $notice ) {
			delete_transient( "mt_notice_{$post->ID}" );
			?>
			<div class="notice notice-<?php echo $notice['type']; ?> is-dismissible">
			    <p><?php echo $notice['message']; ?></p>
			</div>
			<?php	
		}
	}

	/**
	 * Delete template from Cloud API
	 */
	public static function delete_template ($post_id, $post) {
		if ( 'wa_message_template' !== get_post_type($post_id) ) {
 			return;
 		}

		$template_name = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'template_name', true);
		$args = array (
			'name' => $template_name
		);
		
		$response = WA_Notifier::wa_business_api_request( 'message_templates', $args, 'DELETE' );

		if(isset($response->error)) {
			$notice = array(
				'message' => '<b>' . $response->error->error_user_title . '</b> - ' . $response->error->error_user_msg,
				'type' => 'error'
			);
			set_transient( "mt_notice_$post_id", $notice, 60 );
		}
	}

	/**
	 * Delete template from Cloud API
	 */
	public static function admin_body_class ($classes) {
		global $post_id, $current_screen;
		if ( 'wa_message_template' == $current_screen->id ) {
 			$status = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'status', true);
	 		if('' != $status) {
	 			$classes = $classes . ' mt-status-' . strtolower($status);
	 		}
	 		else {
	 			$classes = $classes . ' mt-status-draft';	
	 		}
 		}

 		if ( 'edit-wa_message_template' == $current_screen->id ) {
 			$classes = $classes . ' edit-wa_message_template';
 		}

 		return $classes;
	}

	/**
	 * Refresh statuses of all message templates
	 */
	public static function handle_refresh_status () {
		global $current_screen;
		if ( 'wa_message_template' !== $current_screen->post_type ) {
 			return;
 		}

 		if(!isset($_GET['refresh_status'])) {
 			return;
 		}

 		$response = WA_Notifier::wa_business_api_request('message_templates', array(), 'GET');
		
		if($response->error) {
			$notices[] = array(
				'message' => 'Status not refreshed. Error Code ' . $response->error->code . ': ' . $response->error->message ,
				'type' => 'error'
			);
			new WA_Notifier_Admin_Notices($notices);
			return;
		}
		else {
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
				$template_name = get_post_meta ( $post_id, WA_NOTIFIER_PREFIX . 'template_name', true);
				foreach($wa_message_templates as $template) {
					if($template_name == $template->name && 'en_US' == $template->language) {
						update_post_meta ( $post_id, WA_NOTIFIER_PREFIX . 'status', $template->status);
						break;
					}
				}
			}

			$notices[] = array(
				'message' => 'Status updated successfully.' ,
				'type' => 'success'
			);
			new WA_Notifier_Admin_Notices($notices);
		}
	}

	/**
	 * Admin HTML templates
	 */
	public static function admin_html_templates($templates) {
		$refresh_url = '?' . http_build_query(array_merge($_GET, array("refresh_status"=>"1")));
		$templates['refresh_mt_status'] = '<a href="'.$refresh_url.'" class="refresh-status page-title-action">Refresh Status</a>';
		return $templates;
	}

}
