<?php
/**
 * Notifications CPT class
 *
 * @package    Wa_Notifier
 */
class Notifier_Notifications {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_notification', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit'), 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts'), 10, 2 );
		add_action( 'wp_ajax_fetch_send_to_fields', array(__CLASS__, 'fetch_send_to_fields' ) );
		add_action( 'wp_ajax_fetch_message_template_data', array(__CLASS__, 'fetch_message_template_data') );
		add_filter( 'notifier_js_variables', array(__CLASS__, 'notifications_js_variables'));
		add_action( 'save_post_wa_notification', array(__CLASS__, 'save_meta'), 10, 2 );
		add_filter( 'post_updated_messages', array(__CLASS__, 'update_save_messages') );
		add_action( 'before_delete_post', array(__CLASS__, 'delete_from_active_triggers'), 10 );
		add_filter( 'manage_wa_notification_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_notification_posts_custom_column', array( __CLASS__ , 'add_column_content' ), 10, 2 );

		add_action( 'notifier_marketing_notification', array(__CLASS__, 'send_broadcast_notifications') );
		add_filter( 'admin_body_class', array(__CLASS__, 'admin_body_class'));
		add_action( 'wp_ajax_get_wa_contacts_data', array(__CLASS__, 'get_wa_contacts_data') );
	}

	/**
	 * Register custom post type
	 */
	public static function register_cpt () {
		notifier_register_post_type ('wa_notification', 'Notification', 'Notifications');
	}

	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( NOTIFIER_PREFIX . 'api_credentials_validated');
		if (!$api_credentials_validated) {
			return;
		}

		add_submenu_page( NOTIFIER_NAME, 'Notification', 'Notifications', 'manage_options', 'edit.php?post_type=wa_notification' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        NOTIFIER_NAME . '-notification-data',
	        'Notification Settings',
	        'Notifier_Notifications::output',
	        'wa_notification'
	    );

	    add_meta_box(
	        NOTIFIER_NAME . '-message-template-preview',
	        'Preview Template',
	        'Notifier_Message_Templates::output_preview',
	        'wa_notification',
	        'side'
	    );

	    remove_meta_box( 'submitdiv', 'wa_notification', 'side' );
    	add_meta_box( 'submitdiv', 'Save Notification', 'post_submit_meta_box', 'wa_notification', 'side', 'high' );
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once NOTIFIER_PATH . 'views/admin-notifications-meta-box.php';
	}

	/**
	 * Save meta
	 */
	public static function save_meta( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$notification_data = array();

		if (isset($_POST[NOTIFIER_PREFIX . 'notification_send_to']['row_num'])) {
			unset($_POST[NOTIFIER_PREFIX . 'notification_send_to']['row_num']);
		}

		foreach ($_POST as $key => $data) {
			if (strpos($key, NOTIFIER_PREFIX) !== false) {
				if (is_array($data)) {
					$notification_data[$key] = notifier_sanitize_array($data);
				} else {
					$notification_data[$key] = sanitize_text_field( wp_unslash ($data) );
				}
			    update_post_meta( $post_id, $key, $notification_data[$key]);
			}
		}

		if (!empty($notification_data)) {
			self::setup_notifcation($notification_data, $post_id);
		}
	}

	/**
	 * Update save action messages
	 */
	public static function update_save_messages ($messages) {
		$messages['wa_notification'][1] = 'Notification updated.';
	    $messages['wa_notification'][6] = 'Notification saved.';
		return $messages;
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
    	if ('wa_notification' == $post->post_type) {
	     	unset($actions['inline hide-if-no-js']);
	    }
    	return $actions;
	}

	/**
	 * Change text of buttons and links
	 */
	public static function change_texts( $translation, $text ) {
		if ( 'wa_notification' == get_post_type() ) {
			if ( 'Update' === $text ) {
				return 'Update';
			} elseif ( 'Publish' === $text ) {
				return 'Save';
			}
		}
		return $translation;
	}

	/**
	 * Notifications JS variables
	 */
	public static function notifications_js_variables ($variables) {
		return $variables;
	}

	/**
	 * Fetch and return message template data
	 */
	public static function fetch_message_template_data() {
		if (!isset($_POST['template_id'])) {
			wp_die();
		}

		$template_id =  intval($_POST['template_id']);

		if ('wa_message_template' != get_post_type( $template_id ) ) {
			wp_die();
		}

		$post_id =  isset($_POST['post_id']) ? intval( $_POST['post_id'] ) : 0;
		$notification_type =  isset($_POST['notification_type']) ? sanitize_text_field( wp_unslash($_POST['notification_type']) ) : '';
		$trigger =  isset($_POST['trigger']) ? sanitize_text_field( wp_unslash($_POST['trigger']) ) : '';

		if ('marketing' === $notification_type) {
			$trigger = '';
		}

		$template_meta = array(
			'header_type',
			'header_text',
			/* ==Notifier_Pro_Code_Start== */
			'media_type',
			'media_item_image',
			'media_item_document',
			'media_item_video',
			/* ==Notifier_Pro_Code_End== */
			'body_text',
			'footer_text',
			'button_type',
			'button_num',
			'button_1_type',
			'button_1_text',
			'button_2_type',
			'button_2_text'
		);

		$template_data = array ();
		foreach ($template_meta as $meta) {
			$template_data[$meta] = get_post_meta( $template_id, NOTIFIER_PREFIX . $meta, true);
		}

		/* ==Notifier_Pro_Code_Start== */
		switch (strtolower($template_data['media_type'])) {
			case 'image':
				$template_data['media_url'] = wp_get_attachment_image_url( $template_data[ 'media_item_image'], 'large' );
				break;

		    case 'document':
		    	$template_data['media_url'] = wp_get_attachment_thumb_url( $template_data[ 'media_item_document'] );
		    	break;

		    case 'video':
		    	$template_data['media_url'] = wp_get_attachment_url( $template_data[ 'media_item_video'] );
				break;
	  	}
	  	/* ==Notifier_Pro_Code_End== */
		$notification_status = get_post_meta ( $post_id, NOTIFIER_PREFIX . 'notification_status', true);
		if ($notification_status && in_array($notification_status, array('Sending', 'Sent', 'Scheduled'))) {
			$disabled = array (
				'disabled' => 'disabled'
			);
		} else {
			$disabled = array ();
		}

		$response = array (
			'status' => 'success',
			'data' => $template_data,
			'notification_status' => $notification_status,
			/* ==Notifier_Pro_Code_Start== */
			'variable_mapping_html' => self::get_notification_variable_mapping_fields_html($template_id, $post_id, $trigger, $disabled),
			/* ==Notifier_Pro_Code_End== */
		);

		echo json_encode($response);
		wp_die();
	}

	/**
	 * Setup notification
	 */
	public static function setup_notifcation($data, $id) {
		$type = isset($data['notifier_notification_type']) ? $data['notifier_notification_type'] : 'marketing';
		switch ($type) {
			case 'marketing':
				self::schedule_notification_to_list($data, $id);
				break;
			case 'transactional':
				self::setup_transactional_notification($data, $id);
				break;
		}
	}

	/**
	 * Schedule marketing notification to list
	 */
	public static function schedule_notification_to_list ($notification_data, $notification_id) {
		if ( as_has_scheduled_action( 'notifier_marketing_notification', array($notification_id), 'notifier' ) ) {
		 	as_unschedule_action('notifier_marketing_notification', array($notification_id), 'notifier');
		}

		update_post_meta ( $notification_id, NOTIFIER_PREFIX . 'notification_status', 'Scheduled');

		// Remove from triggers if this notification was earlier a transactional one
		$active_triggers = get_option('notifier_active_triggers');
		if (false !== $active_triggers) {
			foreach ($active_triggers as $trigger => $notif_ids) {
				$key = array_search($notification_id, $notif_ids);
				if (false !== $key) {
				    unset($notif_ids[$key]);
				}
				$active_triggers[$trigger] = $notif_ids;
			}
		}
		update_option('notifier_active_triggers', $active_triggers, true);

		$when = isset($notification_data['notifier_notification_when']) ? $notification_data['notifier_notification_when'] : 'now';
		if ('now' == $when) {
			as_enqueue_async_action( 'notifier_marketing_notification', array($notification_id), 'notifier' );
		} else {
			$datetime = isset($notification_data['notifier_notification_datetime']) ? $notification_data['notifier_notification_datetime'] : '';
			$timestamp = strtotime($datetime);
			as_schedule_single_action( $timestamp, 'notifier_marketing_notification', array($notification_id), 'notifier' );
		}
	}

	/**
	 * Setup transactional notification
	 */
	public static function setup_transactional_notification ($notification_data, $notification_id) {
		update_post_meta ( $notification_id, NOTIFIER_PREFIX . 'notification_status', 'On-going');
		$trigger = $notification_data['notifier_notification_trigger'];
		$active_triggers = get_option('notifier_active_triggers');
		if ( false !== $active_triggers ) {
			foreach ($active_triggers as $t => $notif_ids) {
				$key = array_search($notification_id, $notif_ids);
				if (false !== $key) {
				    unset($notif_ids[$key]);
				}
				$active_triggers[$t] = $notif_ids;
			}
			$active_triggers[$trigger][] = $notification_id;
		} else {
			$active_triggers = array(
				$trigger => array($notification_id)
			);
		}
		update_option('notifier_active_triggers', $active_triggers, true);
	}

	/**
	 * Delete notification ID from active triggers
	 */
	public static function delete_from_active_triggers ($post_id) {
		$active_triggers = get_option('notifier_active_triggers');
		if (false !== $active_triggers) {
			foreach ($active_triggers as $trigger => $notif_ids) {
				$key = array_search($post_id, $notif_ids);
				if (false !== $key) {
				    unset($notif_ids[$key]);
				}
				$active_triggers[$trigger] = $notif_ids;
			}
		}
		update_option('notifier_active_triggers', $active_triggers, true);
	}

	/**
	 * Send scheduled broadcast notifications
	 */
	public static function send_broadcast_notifications ($notification_id) {
		update_post_meta ( $notification_id, NOTIFIER_PREFIX . 'notification_status', 'Sending');

		$list_slug = get_post_meta($notification_id, 'notifier_notification_list', true);
		$template_id = get_post_meta($notification_id, 'notifier_notification_message_template', true);
		$list_offset = get_post_meta($notification_id, 'notifier_notification_list_offset', true);
		$sent_phone_numbers = get_post_meta($notification_id, 'notifier_notification_sent_phone_numbers', true);
		$unsent_phone_numbers = get_post_meta($notification_id, 'notifier_notification_unsent_phone_numbers', true);

		$sent_phone_numbers = (isset($sent_phone_numbers)) ? $sent_phone_numbers : array();
		$unsent_phone_numbers = (isset($unsent_phone_numbers)) ? $unsent_phone_numbers : array();

		$offset = (!$list_offset) ? 0 : (int) $list_offset;

		$count = 0;
		$limit = get_option( NOTIFIER_PREFIX . 'bulk_message_batch_limit', 50 );

		$contact_ids = get_posts( array(
			'post_type'			=> 'wa_contact',
			'post_status'		=> 'publish',
			'wa_contact_list'	=> $list_slug,
			'fields'			=> 'ids',
			'offset'			=> $offset,
			'posts_per_page'	=> $limit
		) );

		foreach ($contact_ids as $contact_id) {
			$count++;

			$phone_number = get_post_meta( $contact_id, NOTIFIER_PREFIX . 'wa_number', true);
			if ('' == $phone_number) {
				continue;
			}

			$message_sent = Notifier_Message_Templates::send_message_template_to_number($template_id, $notification_id, $phone_number);

			if ($message_sent) {
				$sent_numbers[] =  $phone_number;
			} else {
				$unsent_numbers[] =  $phone_number;
			}
		}

		update_post_meta($notification_id, 'notifier_notification_sent_phone_numbers', $sent_numbers);
		update_post_meta($notification_id, 'notifier_notification_unsent_phone_numbers', $unsent_numbers);

		$new_offset = $offset + $limit;
		update_post_meta($notification_id, 'notifier_notification_list_offset', $new_offset);
		if ($count == $limit) {
			as_enqueue_async_action( 'notifier_marketing_notification', array($notification_id), 'notifier' );
		} else {
			update_post_meta ( $notification_id, NOTIFIER_PREFIX . 'notification_status', 'Sent');
		}
	}

	/**
	 * Send triggered notifications
	 */
	public static function send_triggered_notification ($notification_id, $context_args) {
		$send_to = get_post_meta($notification_id, 'notifier_notification_send_to', true);
		$template_id = get_post_meta($notification_id, 'notifier_notification_message_template', true);

		$sent_phone_numbers = get_post_meta($notification_id, 'notifier_notification_sent_phone_numbers', true);
		$unsent_phone_numbers = get_post_meta($notification_id, 'notifier_notification_unsent_phone_numbers', true);

		$sent_phone_numbers = (!$sent_phone_numbers) ? array() : $sent_phone_numbers;
		$unsent_phone_numbers = (!$unsent_phone_numbers) ? array() : $unsent_phone_numbers;

		$list_ids = array();

		$phone_numbers = array();

		if (false !== $send_to && is_array($send_to)) {
			foreach ($send_to as $recipient) {
				switch ($recipient['type']) {
					case 'contact':
						$phone_number = get_post_meta( $recipient['recipient']['contact'], NOTIFIER_PREFIX . 'wa_number', true);
						if ($phone_number) {
							$phone_numbers[] = $phone_number;
						}
						break;

					case 'list':
						$list_ids[] = $recipient['recipient']['list'];
						break;

					case 'customer':
						$order = wc_get_order($context_args['object_id']);
						$phone_number = $order->get_billing_phone();
						$country_code = $order->get_billing_country();
						$phone_numbers[] = Notifier_Contacts::get_formatted_phone_number($phone_number, $country_code);
						break;
				}
			}
		}

		foreach ($phone_numbers as $phone_number) {
			$message_sent = Notifier_Message_Templates::send_message_template_to_number($template_id, $notification_id, $phone_number, $context_args);

			if ($message_sent) {
				$sent_numbers[] = $phone_number;
			} else {
				$unsent_numbers[] = $phone_number;
			}
		}

		update_post_meta($notification_id, 'notifier_notification_sent_phone_numbers', $sent_numbers);
		update_post_meta($notification_id, 'notifier_notification_unsent_phone_numbers', $unsent_numbers);
	}

	/**
	 * Add body class
	 */
	public static function admin_body_class ($classes) {
		global $post_id, $current_screen;
		if ( 'wa_notification' == $current_screen->id ) {
			$sent = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_status', true);
			if (in_array($sent, array('Sending', 'Sent', 'Scheduled'))) {
	 			$classes = $classes . ' disable-publishing';
	 		}
 		}
 		return $classes;
	}

	/**
	 * Add columns to list page
	 */
	public static function add_columns ($columns) {
		$columns['notification_type'] = 'Type';
		$columns['notification_status'] = 'Status';
		$columns['notification_stats'] = 'Stats';
		unset($columns['date']);
		return $columns;
	}

	/**
	 * Add column content
	 */
	public static function add_column_content ( $column, $post_id ) {
		if ( 'notification_type' === $column ) {
		    $notification_type = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_type', true);
		    echo ($notification_type) ? '<code>' . esc_html($notification_type) . '</code>' : '-';
		}

		if ( 'notification_status' === $column ) {
		    $notification_status = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_status', true);
		    echo ($notification_status) ? '<code>' . esc_html($notification_status) . '</code>' : '-';
		}

		if ( 'notification_stats' === $column ) {
		    $sent_phone_numbers = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_sent_phone_numbers', true);
		    echo '<b>Sent: </b>';
		    echo ($sent_phone_numbers && is_array($sent_phone_numbers)) ? count($sent_phone_numbers) : '0';
		    $unsent_phone_numbers = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_unsent_phone_numbers', true);
		    echo '<br /><b>Failed: </b>';
		    echo ($unsent_phone_numbers && is_array($unsent_phone_numbers)) ? count($unsent_phone_numbers) : '0';
		}
	}

	/**
	 * Get notification statuses
	 */
	public static function get_notification_statuses () {
		return array('Sending', 'Sent', 'Scheduled', 'On-going');
	}

	/**
	 * Fetch Send to fields
	 */
	public static function fetch_send_to_fields () {
		$post_id =  isset($_POST['post_id']) ? intval( $_POST['post_id'] ) : 0;
		$trigger =  isset($_POST['trigger']) ? sanitize_text_field( wp_unslash( $_POST['trigger'] ) ) : '';
		$send_to = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_send_to', true);
		$html = '<label>Recipients</label>';
		$html .= '<table class="fields-repeater">
			<tbody>
				<tr>
					<th>Type</th>
					<th>Recipient</th>
					<th></th>
				</tr>';

		if ($send_to && is_array($send_to)) {
			$row = 0;
			foreach ($send_to as $recipient) {
				$html .= self::get_notification_send_to_fields_row($row, $recipient, $post_id, $trigger, $disabled);
				$row++;
			}
		} else {
			$html .= self::get_notification_send_to_fields_row(0, null, $post_id, $trigger);
		}
		$html .= self::get_notification_send_to_fields_row('row_num', null, $post_id, $trigger);
		$html .= '</tbody>
		</table>
		<div class="d-flex">
			<p class="description">Add recipients for this notification. You can click on Add Recipient to add more than one recipient.</p>
			<a href="" class="button add-recipient">Add recipient</a>
		</div>';

		$response = array (
			'status' => 'success',
			'html'		=> $html
		);

		echo json_encode($response);
		wp_die();
	}

	/**
	 * Generates "Send to" field row
	 */
	public static function get_notification_send_to_fields_row ($num = 0, $data = array(), $post_id = 0, $trigger = '', $disabled = array()) {
		ob_start();
		require NOTIFIER_PATH . 'views/templates/admin-notification-send-to-fields-row.php';
		$html = ob_get_clean();
		return $html;
	}
	/* ==Notifier_Pro_Code_Start== */
	/**
	 * Generates variable mapping fields html
	 */
	public static function get_notification_variable_mapping_fields_html ($template_id, $post_id = 0, $trigger = '', $disabled = array()) {
		$data = array();
		if (0 != $post_id) {
			$data = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_variable_mapping', true);
		}

		$header_type = get_post_meta( $template_id, NOTIFIER_PREFIX . 'header_type', true);
		$header_text = get_post_meta( $template_id, NOTIFIER_PREFIX . 'header_text', true);
		$body_text = get_post_meta( $template_id, NOTIFIER_PREFIX . 'body_text', true);

		$total_header_vars = 0;
		$media_type = '';
		if ('text' == $header_type) {
			preg_match_all('~\{\{\s*(.*?)\s*\}\}~', $header_text, $header_var_matches);
			$header_vars = $header_var_matches[0];
			$total_header_vars = count($header_vars);
		}
		elseif ('media' == $header_type) {
			$media_type = get_post_meta( $template_id, NOTIFIER_PREFIX . 'media_type', true);
		}

		preg_match_all('~\{\{\s*(.*?)\s*\}\}~', $body_text, $body_var_matches);
		$body_vars = $body_var_matches[0];
		$total_body_vars = count($body_vars);

		$html = '';

		if ($total_header_vars > 0 || $total_body_vars > 0 || ('' != $media_type) ) {
			$html .= '<label>Map template variables with values</label>';
			$html .= '<table class="fields-repeater"><tbody>';
		}

		// Header variable mapping
		if ($total_header_vars > 0) {
			$header_var_map = isset($data['header'][0]) ? $data['header'][0] : '';
			$html .= '<tr class="header-variable"><th>Header Variable</th><th>Value</th></tr>';
			$html .= self::get_notification_variable_mapping_row(0, 'header', $header_var_map, $trigger, null, $disabled);
		}

		// Header media mapping
		if ('' != $media_type) {
			$header_var_map = isset($data['header']['media']) ? $data['header']['media'] : '';
			$html .= '<tr class="header-variable"><th>Header Media</th><th>Value</th></tr>';
			$html .= self::get_notification_variable_mapping_row('media', 'header', $header_var_map, $trigger, $media_type, $disabled);
		}

		// Body variables mapping
		if ($total_body_vars > 0) {
			$html .= '<tr class="body-variables"><th>Body Variables</th><th>Value</th></tr>';
			for ($num = 0; $num < $total_body_vars; $num++) {
				$body_var_map = isset($data['body'][$num]) ? $data['body'][$num] : '';
				$html .= self::get_notification_variable_mapping_row($num, 'body', $body_var_map, $trigger, null, $disabled);
			}
		}
		if ($total_header_vars > 0 || $total_body_vars > 0 ||  ('' != $media_type)) {
			$html .= '</tbody></table>';
			$html .= '<p class="description">Select the value that you want to pass to the respective message template variable when this notification gets triggered. You can request more values by <a href="mailto:ram@fantastech.co?subject=%5BNotifier%5D%20New%20Merge%20Tags%20Request" target="_blank">mailing us</a>.</p>';
		}
		return $html;
	}

	/**
	 * Generates variable mapping row
	 */
	public static function get_notification_variable_mapping_row ($num, $type, $selected_tag, $trigger = '', $media_type = '', $disabled = array()) {
		$default_tags = array();
		$supported_formats = array(
			'IMAGE' => 'JPEG and PNG',
			'VIDEO' => 'MP4',
			'DOCUMENT' => 'PDF'
		);
		if('media' === $num){
			$default_tags[''] = 'Select media';
			$default_tags['custom'] = 'Custom media URL';
			$merge_tag_type = 'media';
			$placeholder = 'Enter ' . strtolower($media_type) . ' URL here';
			$desc = 'Enter a ' . strtolower($media_type) . ' URL. You can also enter a shortcode that returns ' . strtolower($media_type) . ' URL. Supported format: ' . $supported_formats[$media_type];
		}
		else{
			$default_tags[''] = 'Select a value';
			$default_tags['custom'] = 'Custom value';
			$merge_tag_type = 'text';
			$placeholder = 'Enter custom value here';
			$desc = 'Enter a custom text value here. You can also enter a shortcode. HTML not allowed.';
		}

		$merge_tags = $default_tags + Notifier_Notification_Merge_Tags::get_notification_merge_tags($trigger, $merge_tag_type);

		ob_start();
		?>
		<tr class="row">
			<td>
				<?php
					if('media' === $num){
						if('' != $media_type){
							echo ucfirst(strtolower($media_type));
						}
						else {
							echo 'Media';
						}
					}
					else{
						$var_num = $num + 1;
						echo '<code>{{'. esc_html($var_num) . '}}</code>';
					}
				?>
			</td>
			<td>
			<?php
			 	notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_variable_mapping_' . $type . '_' . $num,
						'name'              => NOTIFIER_PREFIX . 'notification_variable_mapping[' . $type . '][' . $num . '][merge_tag]',
						'class'				=> 'notifier-variable-mapping',
						'value'             => isset($selected_tag['merge_tag']) ? $selected_tag['merge_tag'] : '',
						'label'             => '',
						'description'       => '',
						'options'           => $merge_tags,
						'custom_attributes' => $disabled
					)
				);
				notifier_wp_text_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_variable_mapping_' . $type . '_' . $num,
						'name'              => NOTIFIER_PREFIX . 'notification_variable_mapping[' . $type . '][' . $num . '][custom_value]',
						'class'				=> 'notifier-custom-value',
						'value'             => isset($selected_tag['custom_value']) ? $selected_tag['custom_value'] : '',
						'placeholder'		=> $placeholder,
						'description'       => $desc,
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'notification_variable_mapping_' . $type . '_' . $num,
								'operator'	=> '==',
								'value'		=> 'custom'
							)
						)
					)
				);
			?>
			</td>
		</tr>
		<?php
		$html = ob_get_clean();
		return $html;
	}
	/* ==Notifier_Pro_Code_End== */

	/**
	 * Handle AJAX call to get contacts / lists
	 */
	public static function get_wa_contacts_data() {
		$contacts = Notifier_Contacts::get_contacts();
		echo json_encode( $contacts );
		die;
	}

}
