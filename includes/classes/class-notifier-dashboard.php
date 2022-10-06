<?php
/**
 * Admin dashboard page class
 *
 * @package    Wa_Notifier
 */
class Notifier_Dashboard {

	/**
	 * Init
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'admin_init', array( __CLASS__ , 'handle_webhook_validation_form' ) );
        add_action( 'admin_init', array( __CLASS__ , 'handle_save_triggers_form' ) );
	}

	/**
	 * Add dashboard page to admin menu
	 */
	public static function setup_admin_page () {
		add_menu_page( 'WA Notifier', 'WA Notifier', 'manage_options', NOTIFIER_NAME, array( __CLASS__ , 'output'), 'dashicons-megaphone', '51' );
	}

	/**
	 * Output
	 */
	public static function output() {
        include_once NOTIFIER_PATH . '/views/admin-dashboard.php';
	}

	/**
	 * Handle displaimer form
	 */
	public static function handle_webhook_validation_form () {
		if ( ! isset( $_POST['webhook_validation'] ) ) {
			return;
		}

		//phpcs:ignore
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-webhook-validation' ) ) {
			return;
		}

		$api_key = (isset($_POST['notifier_api_key'])) ? sanitize_text_field(wp_unslash($_POST['notifier_api_key'])) : '';

		if('' == trim($api_key)) {
			$notices[] = array(
				'message' => 'Please enter API key.',
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices, true);
			wp_redirect(admin_url('admin.php?page=notifier'));
			die;
		}

		update_option('notifier_api_key', $api_key);
		delete_option('notifier_enabled_triggers');

		$params = array(
			'action'    => 'verify_api',
			'site_url'	=> site_url(),
			'source'	=> 'wp'
    	);

		$response = Notifier::send_api_request( $params, 'POST' );

		if($response->error){
			$notices[] = array(
				'message' => $response->message,
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices, true);
			wp_redirect(admin_url('admin.php?page=notifier'));
			die;
		}

		update_option('notifier_api_activated', 'yes');

		$notices[] = array(
			'message' => $response->data,
			'type' => 'success'
		);
		new Notifier_Admin_Notices($notices, true);
		wp_redirect(admin_url('admin.php?page=notifier'));
		die;

	}

	/**
	 * Handle save triggers form
	 */
	public static function handle_save_triggers_form () {
		if ( ! isset( $_POST['notifier_save_triggers'] ) ) {
			return;
		}

		//phpcs:ignore
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-save-triggers' ) ) {
			return;
		}

		$notifier_triggers = (!empty($_POST['notifier_triggers'])) ? notifier_sanitize_array($_POST['notifier_triggers']) : array();
		$all_triggers = Notifier_Notification_Triggers::get_notification_triggers();
		$selected_triggers = array();
		foreach ($all_triggers as $key => $triggers) {
			foreach ($triggers as $trigger){
				if( ! in_array($trigger['id'], $notifier_triggers) ){
					continue;
				}
				unset($trigger['action']);
				$selected_triggers[$key][] = $trigger;
			}
		}

		$params = array(
			'action'	=> 'update_triggers',
			'site_url'	=> site_url(),
			'source'	=> 'wp',
			'triggers'	=> $selected_triggers
    	);

		$response = Notifier::send_api_request( $params, 'POST' );

		if($response->error){
			$notices[] = array(
				'message' => $response->message,
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices, true);
			wp_redirect(admin_url('admin.php?page=notifier'));
			die;
		}

		update_option('notifier_enabled_triggers', $notifier_triggers);

		$notices[] = array(
			'message' => $response->data,
			'type' => 'success'
		);
		new Notifier_Admin_Notices($notices, true);
		wp_redirect(admin_url('admin.php?page=notifier'));
		die;

	}

}
