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
		if(!isset($_POST['template_name'])) {
			wp_die();
		}

		$template_name =  $_POST['template_name'];

		$message_template = get_posts(
			array (
				'post_type' => 'wa_message_template',
				'post_status' => 'publish',
				'numberposts' => 1,
				'fields' => 'ids',
				'meta_query'	=> array(
				    array(
						'key'   => WA_NOTIFIER_PREFIX . 'template_name',
						'value' => $template_name,
				    ),
				)
			)
		);

		$post_id = $message_template[0];

		$template_meta = array('header_text', 'body_text', 'footer_text', 'button_type', 'button_num', 'button_1_type', 'button_1_text', 'button_2_type', 'button_2_text');

		$template_data = array ();
		foreach ($template_meta as $meta) {
			$template_data[$meta] = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . $meta, true);
		}

		$response = array (
			'status' => 'success',
			'data' => $template_data
		);

		echo wp_json_encode($response);
		wp_die();
	}

}
