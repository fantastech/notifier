<?php
/**
 * Notifications CPT class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Notifications {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_notification', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit') , 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts') , 10, 2 );
		add_action( 'wp_ajax_fetch_message_template_data', array(__CLASS__, 'fetch_message_template_data') );
		add_filter( 'wa_notifier_js_variables', array(__CLASS__, 'notifications_js_variables'));
		add_action( 'save_post_wa_notification', array(__CLASS__, 'save_meta'), 10, 2 );
		add_filter( 'manage_wa_notification_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_notification_posts_custom_column', array( __CLASS__ , 'add_column_content' ) , 10, 2 );

		add_action( 'wa_notifier_marketing_notification', array(__CLASS__, 'send_scheduled_notification') );
		add_filter( 'admin_body_class', array(__CLASS__, 'admin_body_class'));
		add_action( 'wp_ajax_get_wa_contacts_data', array(__CLASS__, 'get_wa_contacts_data') );
	}

	/**
	 * Register custom post type
	 */
	public function register_cpt () {
		wa_notifier_register_post_type ('wa_notification', 'Notification', 'Notifications');
	}
	
	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( WA_NOTIFIER_PREFIX . 'api_credentials_validated');
		if(!$api_credentials_validated) {
			return;
		}

		add_submenu_page( WA_NOTIFIER_NAME, 'Notification', 'Notifications', 'manage_options', 'edit.php?post_type=wa_notification' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        WA_NOTIFIER_NAME . '-notification-data',
	        'Notification Settings',
	        'WA_Notifier_Notifications::output',
	        'wa_notification'
	    );

	    add_meta_box(
	        WA_NOTIFIER_NAME . '-message-template-preview',
	        'Preview Template',
	        'WA_Notifier_Message_Templates::output_preview',
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
		include_once WA_NOTIFIER_PATH . 'views/admin-notifications-meta-box.php';
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

		foreach ($_POST as $key => $data) {
			if (strpos($key, WA_NOTIFIER_PREFIX) !== false) {
				$notification_data[$key] = sanitize_text_field( wp_unslash ($data) );
			    update_post_meta( $post_id, $key, $notification_data[$key]);
			}
		}

		self::setup_notifcation($notification_data, $post_id);
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
		if ( 'wa_notification' == get_post_type() ) {
			if ( $text == 'Update' ) {
				return 'Update';
			}
			elseif ($text == 'Publish') {
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
		if(!isset($_POST['template_id'])) {
			wp_die();
		}

		if('wa_message_template' != get_post_type($_POST['template_id'])){
			wp_die();
		}

		$template_id =  $_POST['template_id'];

		$template_meta = array('header_text', 'body_text', 'footer_text', 'button_type', 'button_num', 'button_1_type', 'button_1_text', 'button_2_type', 'button_2_text');

		$template_data = array ();
		foreach ($template_meta as $meta) {
			$template_data[$meta] = get_post_meta( $template_id, WA_NOTIFIER_PREFIX . $meta, true);
		}

		$response = array (
			'status' => 'success',
			'data' => $template_data
		);

		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * Setup notification
	 */
	public static function setup_notifcation($data, $id) {
		// print_r($notification_data); die;
		$type = $data['wa_notifier_notification_type'];
		switch($type){
			case 'marketing':
				self::schedule_notification_to_list($data, $id);
				break;
			case 'translational':
				self::setup_transactional_notification($id);
				break;
		}
	}

	/**
	 * Schedule marketing notification to list
	 */
	public static function schedule_notification_to_list ($notification_data, $notification_id) {
		if ( as_has_scheduled_action( 'wa_notifier_marketing_notification', array($notification_id), 'wa-notifier' ) ) {
		 	as_unschedule_action('wa_notifier_marketing_notification', array($notification_id), 'wa-notifier');
		}

		update_post_meta ( $notification_id, WA_NOTIFIER_PREFIX . 'notification_status' , 'Scheduled');

		$when = $notification_data['wa_notifier_notification_when'];
		if('now' == $when) {
			as_enqueue_async_action( 'wa_notifier_marketing_notification', array($notification_id), 'wa-notifier' );
		}
		else {
			$timestamp = strtotime($notification_data['wa_notifier_notification_datetime']);
			as_schedule_single_action( $timestamp, 'wa_notifier_marketing_notification', array($notification_id), 'wa-notifier' );
		}
	}

	/**
	 * Setup transactional notification
	 */
	public static function setup_transactional_notification ($notification_id) {

	}

	/**
	 * Setup scheduled notification
	 */
	public static function send_scheduled_notification ($notification_id) {
		update_post_meta ( $notification_id, WA_NOTIFIER_PREFIX . 'notification_status' , 'Sending');

		$list_slug = get_post_meta($notification_id, 'wa_notifier_notification_list', true);
		$template_id = get_post_meta($notification_id, 'wa_notifier_notification_message_template', true);
		$list_offset = get_post_meta($notification_id, 'wa_notifier_notification_list_offset', true);
		$sent_contact_ids = get_post_meta($notification_id, 'wa_notifier_notification_sent_contact_ids', true);
		$unsent_contact_ids = get_post_meta($notification_id, 'wa_notifier_notification_unsent_contact_ids', true);

		$offset = (!$list_offset) ? 0 : (int)$list_offset;
		$sent_contact_ids = (!$sent_contact_ids) ? array() : $sent_contact_ids;
		$unsent_contact_ids = (!$unsent_contact_ids) ? array() : $unsent_contact_ids;

		$count = 0;
		$limit = 50; // Send to only 50 contacts at a time

		$contact_ids = get_posts( array(
			'post_type'			=> 'wa_contact',
			'post_status'		=> 'publish',
			'wa_contact_list'	=> $list_slug,
			'fields'			=> 'ids',
			'offset'			=> $offset,
			'posts_per_page'	=> $limit
		) );

		foreach($contact_ids as $contact_id) {
			$count++;

			$phone_number = get_post_meta( $contact_id, WA_NOTIFIER_PREFIX . 'wa_number', true);
			if('' == $phone_number) {
				continue;
			}

			$message_sent = WA_Notifier_Message_Templates::send_message_template_to_number($template_id, $phone_number);

			if($message_sent) {
				$sent_contact_ids[] = $contact_id;
			}
			else {
				$unsent_contact_ids[] = $contact_id;
			}
		}

		$new_offset = $offset + $limit;
		update_post_meta($notification_id, 'wa_notifier_notification_list_offset', $new_offset);
		update_post_meta($notification_id, 'wa_notifier_notification_sent_contact_ids', $sent_contact_ids);
		update_post_meta($notification_id, 'wa_notifier_notification_unsent_contact_ids', $unsent_contact_ids);
		if($count == $limit){
			as_enqueue_async_action( 'wa_notifier_marketing_notification', array($notification_id), 'wa-notifier' );
		}
		else {
			update_post_meta ( $notification_id, WA_NOTIFIER_PREFIX . 'notification_status' , 'Sent');
		}
	}

	/**
	 * Add body class
	 */
	public static function admin_body_class ($classes) {
		global $post_id, $current_screen;
		if ( 'wa_notification' == $current_screen->id ) {
			$sent = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_status', true);
			$statuses = self::get_notification_statuses();
			if(in_array($sent, $statuses)) {
	 			$classes = $classes . ' disable-publishing';
	 		}
 		}
 		return $classes;
	}

	/**
	 * Add columns to list page
	 */
	public static function add_columns ($columns) {
		$columns['notification_status'] = 'Status';
		$columns['notification_stats'] = 'Stats';
		unset($columns['date']);
		return $columns;
	}

	/**
	 * Add column content
	 */
	public static function add_column_content ( $column, $post_id ) {
		if ( 'notification_status' === $column ) {
		    $notification_status = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_status', true);
		    echo ($notification_status) ? '<code>' . $notification_status . '</code>' : '-';
		}

		if ( 'notification_stats' === $column ) {
		    $sent_contact_ids = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_sent_contact_ids', true);
		    echo '<b>Sent: </b>';
		    echo ($sent_contact_ids && is_array($sent_contact_ids)) ? count($sent_contact_ids) : '0';
		    $unsent_contact_ids = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_unsent_contact_ids', true);
		    echo '<br /><b>Failed: </b>';
		    echo ($unsent_contact_ids && is_array($unsent_contact_ids)) ? count($unsent_contact_ids) : '0';
		}
	}

	/**
	 * Get notification statuses
	 */
	public static function get_notification_statuses () {
		return array('Sending', 'Sent', 'Scheduled');
	}

	public static function get_notification_send_to_fields_row ($num = 0, $data = array()) {
		ob_start();
		?>
		<tr class="row">
			<td>
				<?php
					wa_notifier_wp_select(
						array(
							'id'                => WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_type',
							'name'              => WA_NOTIFIER_PREFIX . 'notification_sent_to['.$num.'][type]',
							'value'             => '',
							'label'             => '',
							'description'       => 'Select the type of recipient',
							'options'           => array (
								'contact'	=> 'Contact',
								'list'		=> 'List',
								'user'		=> 'User'
							),
						)
					);
				?>
			</td>
			<td>
				<?php
					wa_notifier_wp_select(
						array(
							'id'                => WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_recipient_contact',
							'name'                => WA_NOTIFIER_PREFIX . 'notification_sent_to['.$num.'][recipient][contact]',
							'class'				=> 'wa-notifier-recipient-contact',
							'value'             => '',
							'label'             => '',
							'description'       => 'Select one of your saved <a href="'.admin_url('edit.php?post_type=wa_contact').'">Contacts</a>',
							'options'           => array (),
							'conditional_logic'	=> array (
								array (
									'field'		=> WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_type',
									'operator'	=> '==',
									'value'		=> 'contact'
								)
							)
						)
					);
				?>
				<?php
					wa_notifier_wp_select(
						array(
							'id'                => WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_recipient_list',
							'name'              => WA_NOTIFIER_PREFIX . 'notification_sent_to['.$num.'][recipient][list]',
							'class'				=> 'wa-notifier-recipient-list',
							'value'             => '',
							'label'             => '',
							'description'       => 'Select one of your contact <a href="'.admin_url('edit-tags.php?taxonomy=wa_contact_list&post_type=wa_contact').'">Lists</a>',
							'options'           => WA_Notifier_Contacts::get_contact_lists(true, true),
							'conditional_logic'	=> array (
								array (
									'field'		=> WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_type',
									'operator'	=> '==',
									'value'		=> 'list'
								)
							)
						)
					);
				?>
				<?php
					wa_notifier_wp_select(
						array(
							'id'                => WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_recipient_user',
							'name'                => WA_NOTIFIER_PREFIX . 'notification_sent_to['.$num.'][recipient][user]',
							'class'				=> 'wa-notifier-recipient-user',
							'value'             => '',
							'label'             => '',
							'description'       => 'Select the user',
							'options'           => array (),
							'conditional_logic'	=> array (
								array (
									'field'		=> WA_NOTIFIER_PREFIX . 'notification_sent_to_'.$num.'_type',
									'operator'	=> '==',
									'value'		=> 'user'
								)
							)
						)
					);
				?>
			</td>
			<td class="delete-repeater-field">
				<span class="dashicons dashicons-trash"></span>
			</td>
		</tr>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Handle AJAX call to get contacts / lists
	 */
	public static function get_wa_contacts_data(){
		$contacts = WA_Notifier_Contacts::get_contacts();
		echo json_encode( $contacts );
		die;
	}

}
