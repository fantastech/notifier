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

    public static function output() {
        if (class_exists('WooCommerce') && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            include_once NOTIFIER_PATH . '/views/admin-tools.php';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>WooCommerce plugin is not installed or active. This feature requires WooCommerce to be installed and active. Please install or activate WooCommerce to use this feature.</p></div>';
        }
    }

	/**
	 * Check if on tools page
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
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_country',
        );
    
        foreach ( $customers as $customer ) {
            $billing_phone = get_user_meta( $customer->ID, 'billing_phone', true );
            if(!$billing_phone || trim($billing_phone) == ''){
            	continue;
            }

			$country_code = get_user_meta( $customer->ID, 'billing_country', true );
            $state_code = get_user_meta( $customer->ID, 'billing_state', true );

			$country = '';
			$state = '';

            if($country_code && $country_code != ''){
				$country = WC()->countries->get_countries()[$country_code];
				if($state_code && $state_code != ''){
					$state = WC()->countries->get_states($country_code)[$state_code];
				}
            }

            $csv_data[] = array(
                get_user_meta( $customer->ID, 'billing_first_name', true ),
                get_user_meta( $customer->ID, 'billing_last_name', true ),
                str_replace('+','',Notifier_Woocommerce::get_formatted_phone_number($billing_phone, $country_code)),
                'subscribed',
                'WooCommerce Customers',
                '',
                get_user_meta( $customer->ID, 'billing_address_1', true ) ?: '',
                get_user_meta( $customer->ID, 'billing_address_2', true ) ?: '',
                get_user_meta( $customer->ID, 'billing_city', true ) ?: '',
                $state,
                $country,
            );
        }
    
        // Generate CSV file
        foreach ( $csv_data as $row ) {
            fputcsv( $file_handle, $row );
        }
        fclose( $file_handle );
        exit();
    }
     
}
