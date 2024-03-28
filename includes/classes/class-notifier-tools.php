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
        add_action( 'admin_init', array( __CLASS__ , 'fetch_user_activity_logs_by_date') );
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
	 * Check if on tools page
	 */
	public static function is_tools_page() {
		$current_page = isset($_GET['page']) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		return strpos($current_page, NOTIFIER_NAME . '-tools') !== false;
	}

	/**
	 * Output
	 */
	public static function output() {
        include_once NOTIFIER_PATH . '/views/admin-tools.php';
	}

	/**
	 * Get tools tabs
	 */
	private static function get_tools_tabs() {
		$tabs = array(
			'export_woo_customer'  => 'Export Woocommerce customer',
			'activity_log' => 'Activity Log',
		);
		return $tabs;
	}

	/**
	 * Handle tools tab preview
	 */
	private static function tools_tab_output($tab) {
		switch ($tab) {
			case 'export_woo_customer':
                if (class_exists('WooCommerce') && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    include_once NOTIFIER_PATH . '/views/admin-tools-export-woo-customer-tab.php';
                } else {
                    echo '<div class="notice notice-error is-dismissible"><p>WooCommerce plugin is not installed or active. For export customer feature requires WooCommerce to be installed and active. Please install or activate WooCommerce to use this feature.</p></div>';
                }               
                break;
			case 'activity_log':
                include_once NOTIFIER_PATH . '/views/admin-tools-activity-log-tab.php';
				break;				
		}
    }
   
    /**
     * Export WooCommerce Customers
     */
    public static function export_customers() {
        if ( ! self::is_tools_page() ) {
            return;
        }

        if ( ! class_exists('WooCommerce') || ! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
            return '<div class="notice notice-error is-dismissible"><p>WooCommerce plugin is not installed or active. For export customer feature requires WooCommerce to be installed and active. Please install or activate WooCommerce to use this feature.</p></div>';
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

    /**
     * Create New db table-- wanotifier_activity_log
     */
    public static function create_wanotifier_activity_log_table() {
        global $wpdb;
    
        $table_name = $wpdb->prefix . 'wanotifier_activity_log';
    
        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                log_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                message longtext NOT NULL,
                user_id bigint(20) NOT NULL,
                PRIMARY KEY  (log_id)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    /**
     * Insert New log into table wanotifier_activity_log
     */    
    public static function insert_activity_log($message) {
        if('yes' === get_option('notifier_enable_activity_log')){
            global $wpdb;
            $table_name = $wpdb->prefix . 'wanotifier_activity_log';
            $user_id = get_current_user_id();
            $data = array(
                'message' => $message,
                'user_id' => $user_id,
            );
        
            $format = array('%s', '%d');
            $wpdb->insert($table_name, $data, $format);
        }
    }

    /**
     * Fetch all dates info for current user
     */
    public static function get_activity_log_dates_for_current_user() {
        global $wpdb;
        $current_user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'wanotifier_activity_log';
    
        $dates_query = $wpdb->prepare(
            "SELECT DISTINCT DATE(timestamp) as log_date FROM `$table_name` WHERE user_id = %d ORDER BY timestamp DESC",
            $current_user_id
        );
        return $wpdb->get_results($dates_query);
    }

    /**
     * Fetch all activity info for current user by date
     */
    public static function fetch_user_activity_logs_by_date() {
        if ( ! self::is_tools_page() ) {
            return;
        }
        
        if ( ! isset( $_POST['get_activity_logs'] ) ) {
            return;
        }
  
        //phpcs:ignore
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-tools-activity-log' ) ) {
            return;
        }
        $selected_date = isset($_POST['activity_date']) ? sanitize_text_field($_POST['activity_date']) : '';

        global $wpdb;
        $current_user_id = 1;
        $table_name = $wpdb->prefix . 'wanotifier_activity_log';

        $query = $wpdb->prepare(
            "SELECT * FROM `$table_name` WHERE user_id = %d AND DATE(timestamp) = %s ORDER BY timestamp DESC",
            $current_user_id,
            $selected_date
        );
        $logs = $wpdb->get_results($query);

        return $logs;
    }
}
