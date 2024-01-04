<?php
/**
 * Tools page class
 *
 * @package    Wa_Notifier
 */
class Notifier_Tools {

	/**
	 * Init
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'admin_init', array( __CLASS__ , 'export_customers') );
	}

	/**
	 * Add settings page to men
	 */
	public static function setup_admin_page () {
		if (!Notifier::is_api_active()){
			return;
		}

		add_submenu_page( NOTIFIER_NAME, 'Tools', 'Tools', 'manage_options', NOTIFIER_NAME . '-tools', array( __CLASS__, 'output' ) );
	}

	/**
	 * Output
	 */
	public static function output() {
        include_once NOTIFIER_PATH . '/views/admin-tools.php';
	}


	/**
	 * Check if on settings page
	 */
	public static function is_tools_page() {
		$current_page = isset($_GET['page']) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		return strpos($current_page, NOTIFIER_NAME . '-tools') !== false;
	}

    /**
     * Export WooCommerce Customers
     */
    public static function export_customers() {
        if ( ! self::is_tools_page() ) {
            return;
        }
    
        if ( ! isset( $_POST['export_customer'] ) ) {
            return;
        }
    
        //phpcs:ignore
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-tools-export-customers' ) ) {
            return;
        }
        
        // Get the current domain name
        $domain_name = $_SERVER['HTTP_HOST'];
    
        // Generate a unique filename with a timestamp
        $filename = $domain_name . '-woocommerce-customers-' . current_time( 'timestamp' ) . '.csv';
    
        // Set the appropriate headers for a CSV file download
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
    
        // Open a file handle for writing
        $file_handle = fopen( 'php://output', 'w' );
        
        global $wpdb;

        // Query WooCommerce customers
        $customers = $wpdb->get_results("
            SELECT u.ID, u.user_email
            FROM {$wpdb->users} u
            WHERE EXISTS (
                SELECT 1
                FROM {$wpdb->usermeta} um
                WHERE u.ID = um.user_id
                AND um.meta_key = 'wp_capabilities'
            )
            ORDER BY u.ID ASC
        ");
    

        // Prepare CSV data
        $csv_data = array();
        $csv_data[] = array(
            'First Name',
            'Last Name',
            'WhatsApp Number',
            'Status',
            'List Name',
            'Tags',
            'Billing Address 1',
            'Billing Address 2',
            'Billing City',
            'Billing State',
            'Billing Country',
        );
    
        foreach ( $customers as $customer ) {
            $billing_phone = get_user_meta( $customer->ID, 'billing_phone', true );
            $country_code   = get_user_meta( $customer->ID, 'billing_postcode', true ) ?: '';
            $csv_data[] = array(
                get_user_meta( $customer->ID, 'billing_first_name', true ),
                get_user_meta( $customer->ID, 'billing_last_name', true ),
                $country_code . $billing_phone,
                'Subscribed',
                'WooCommerce Customer',
                '',
                get_user_meta( $customer->ID, 'billing_address_1', true ) ?: '',
                get_user_meta( $customer->ID, 'billing_address_2', true ) ?: '',
                get_user_meta( $customer->ID, 'billing_city', true ) ?: '',
                get_user_meta( $customer->ID, 'billing_state', true ) ?: '',
                get_user_meta( $customer->ID, 'billing_country', true ) ?: '',
            );
        }
    
        // Generate CSV file
        foreach ( $csv_data as $row ) {
            fputcsv( $file_handle, $row );
        }
    
        // Close the file handle
        fclose( $file_handle );
    
        // Ensure that further execution is halted after file download
        exit();
    }
     
}
