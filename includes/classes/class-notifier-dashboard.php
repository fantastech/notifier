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
        add_action( 'admin_init', array( __CLASS__ , 'handle_disclaimer_form' ) );
        add_action( 'admin_init', array( __CLASS__ , 'handle_validation_form' ) );
	}

	/**
	 * Add dashboard page to admin menu
	 */
	public static function setup_admin_page () {
		add_menu_page( 'Notifier Pro', 'Notifier Pro', 'manage_options', NOTIFIER_NAME, array( __CLASS__ , 'output'), 'dashicons-megaphone', '51' );
	}

	/**
	 * Output
	 */
	public static function output() {
        include_once NOTIFIER_PATH . '/views/admin-dashboard.php';
	}

	/**
	 * Handle displaimer forms
	 */
	public static function handle_disclaimer_form () {
		if ( ! self::is_dashboard_page() ) {
			return;
		}
		if ( ! isset( $_POST['disclaimer'] ) ) {
			return;
		}
		//phpcs:ignore
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-disclaimer' ) ) {
			return;
		}

		update_option(NOTIFIER_PREFIX . 'disclaimer', 'accepted');
	}

	/**
	 * Handle validation forms
	 */
	public static function handle_validation_form () {
		if ( ! self::is_dashboard_page() ) {
			return;
		}
		if ( ! isset( $_POST['validate'] ) ) {
			return;
		}
		//phpcs:ignore
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-validate' ) ) {
			return;
		}

		$phone_number_id = get_option( NOTIFIER_PREFIX . 'phone_number_id' );
		$business_account_id = get_option( NOTIFIER_PREFIX . 'business_account_id' );
		$permanent_access_token = get_option( NOTIFIER_PREFIX . 'permanent_access_token' );

		if ('' == $phone_number_id || '' == $business_account_id || '' == $permanent_access_token) {
			$notices[] = array(
				'message' => 'Setup not complete. Please follow the steps shown below to create <b>Phone number ID</b> , <b>Business Account ID</b> and <b>Permanent Access Token</b>. Then add these details on the <a href="admin.php?page=' . NOTIFIER_NAME . '-settings">Settings</a> page before proceeding for validation.',
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices);
			return;
		}

		// Fetch phone number details
		$response = Notifier::wa_cloud_api_request('', array(), 'GET');
		if ($response->error) {
			$notices[] = array(
				'message' => 'API request can not be validated. Error Code ' . $response->error->code . ': ' . $response->error->message ,
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices);
			return;
		} else {
			$phone_number_details[$phone_number_id] = array (
				'display_num'		=> $response->display_phone_number,
				'display_name'		=> $response->verified_name,
				'phone_num_status'	=> $response->code_verification_status,
				'quality_rating'	=> $response->quality_rating
			);
			update_option( NOTIFIER_PREFIX . 'phone_number_details', $phone_number_details );
		}

		$response = ''; // reset

		// Fetch message templates
		$response = Notifier::wa_business_api_request('message_templates', array(), 'GET');
		if ($response->error) {
			$notices[] = array(
				'message' => 'API request can not be validated. Error Code ' . $response->error->code . ': ' . $response->error->message ,
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices);
			return;
		} else {
			$message_templates = $response->data;
			foreach ($message_templates as $template) {
				if ('hello_world' != $template->name) {
					continue;
				}

				$post_id = wp_insert_post ( array (
					'post_title' 	=> 'Hello World!',
					'post_status' 	=> 'publish',
					'post_type' 	=> 'wa_message_template'
				) );

				update_post_meta ( $post_id, NOTIFIER_PREFIX . 'template_name', $template->name);
				update_post_meta ( $post_id, NOTIFIER_PREFIX . 'category', $template->category);
				update_post_meta ( $post_id, NOTIFIER_PREFIX . 'status', $template->status);
				update_post_meta ( $post_id, NOTIFIER_PREFIX . 'template_id', $template->id);
				update_post_meta ( $post_id, NOTIFIER_PREFIX . 'language', $template->language);

				foreach ($template->components as $component) {
					switch ($component->type) {
						case 'HEADER':
							update_post_meta ( $post_id, NOTIFIER_PREFIX . 'header_type', 'text');
							update_post_meta ( $post_id, NOTIFIER_PREFIX . 'header_text', $component->text);
							break;

						case 'BODY':
							update_post_meta ( $post_id, NOTIFIER_PREFIX . 'body_text', $component->text);
							break;

						case 'FOOTER':
							update_post_meta ( $post_id, NOTIFIER_PREFIX . 'footer_text', $component->text);
					}
				}
			}
			update_option( NOTIFIER_PREFIX . 'api_credentials_validated', 'yes');
			wp_redirect( admin_url( '/admin.php?page=' . NOTIFIER_NAME ) );
        	exit;
		}
	}

	/**
	 * Check if on dashboard page
	 */
	public static function is_dashboard_page() {
		$current_page = isset($_GET['page']) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if (NOTIFIER_NAME === $current_page) {
			return true;
		}
		return false;
	}

}