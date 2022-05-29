<?php
/**
 * Admin dashboard page class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Dashboard {

	/**
	 * Output
	 */
	public static function output() {
        include_once WA_NOTIFIER_PATH . '/views/admin-dashboard.php';
	}

	/**
	 * Handle dashboard forms
	 */
	public function handle_dashboard_forms () {
		if ( ! self::is_dashboard_page() ) {
			return;
		}

		if ( ! isset( $_POST['disclaimer'] ) ) {
			return;
		}

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], WA_NOTIFIER_NAME . '-disclaimer' ) ) {
			return;	
		}

		update_option(WA_NOTIFIER_SETTINGS_PREFIX . 'disclaimer', 'accepted');

	}

	/**
	 * Check if on dashboard page
	 */
	public function is_dashboard_page() {
		$current_page = isset($_GET['page']) ? $_GET['page'] : '';
		if($current_page == WA_NOTIFIER_NAME){
			return true;
		}
		return false;
	}
	
}
