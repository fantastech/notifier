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
	}

	/**
	 * Add dashboard page to admin menu
	 */
	public static function setup_admin_page () {
		add_menu_page( 'WANotifier', 'WANotifier', 'manage_options', NOTIFIER_NAME, array( __CLASS__ , 'output'), 'dashicons-megaphone', '51' );
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
			'site_url'	=> site_url(),
			'source'	=> 'wp'
    	);

		$response = Notifier::send_api_request( 'verify_api', $params, 'POST' );

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

}
