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
        add_action( 'wp_ajax_fetch_activity_logs_by_date', array(__CLASS__, 'fetch_activity_logs_by_date'));
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
     * Insert New log into table wanotifier_activity_log
     */    
    public static function insert_activity_log( $type = 'debug', $message ) {
        if('yes' === get_option('notifier_enable_activity_log')){
            global $wpdb;
            $table_name = $wpdb->prefix . 'wanotifier_activity_log';
            $data = array(
                'message' => $message,
                'type' => $type,
            );
        
            $format = array('%s', '%s');
            $wpdb->insert($table_name, $data, $format);
        }
    }

    /**
     * Fetch all dates info for current user
     */
    public static function get_logs_date_list_adjusted_for_timezone() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wanotifier_activity_log';
        
        $dates_query = "SELECT DISTINCT DATE(timestamp) as log_date FROM `$table_name` ORDER BY timestamp DESC";
        $dates = $wpdb->get_results($dates_query);

        $timezone_string = get_option('timezone_string') ?: 'UTC';
        $timezone = new DateTimeZone($timezone_string);
        $utc_timezone = new DateTimeZone('UTC');
    
        $adjusted_dates = [];
        foreach ($dates as $date) {
            // Convert log_date from UTC to the WordPress site's timezone
            $date_utc = new DateTime($date->log_date, $utc_timezone);
            $date_utc->setTimezone($timezone);
            
            // Format the date and store it for display
            $adjusted_dates[] = $date_utc->format('Y-m-d');
        }
    
        return $adjusted_dates;
    }

    /**
     * Fetch all activity info for current user by date
     */
    public static function fetch_activity_logs_by_date() {
        $selected_date = isset($_POST['activity_date']) ? sanitize_text_field($_POST['activity_date']) : '';
        
        // Determine WordPress timezone setting
        $timezone_string = get_option('timezone_string');
        if (empty($timezone_string)) {
            $gmt_offset = get_option('gmt_offset');
            $timezone_string = $gmt_offset ? 'GMT' . ($gmt_offset >= 0 ? '+' : '') . $gmt_offset : 'UTC';
        }
        $timezone = new DateTimeZone($timezone_string);
        
        // Assume the selected date is in the site's local timezone
        $date = new DateTime($selected_date, $timezone);
        
        // Convert to UTC for querying the database
        $utc_timezone = new DateTimeZone('UTC');
        $date->setTimezone($utc_timezone);
        $selected_date_utc = $date->format('Y-m-d');
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'wanotifier_activity_log';

        $query = $wpdb->prepare(
            "SELECT * FROM `$table_name` WHERE DATE(timestamp) = %s ORDER BY timestamp DESC",
            $selected_date_utc
        );
        $logs = $wpdb->get_results($query);

        $logs_preview_htm = '<div class="activity-logs">';
        if (!empty($logs)){
            foreach ($logs as $log){
                $logs_preview_htm .= '<div class="activity-record">';
                $logs_preview_htm .= '<strong>'.esc_html(date('Y-m-d H:i:s', strtotime($log->timestamp))).'</strong>: '; 
                $logs_preview_htm .= esc_html($log->message);
                $logs_preview_htm .= '</div>';
            }
        } else {
            $logs_preview_htm .= '<div class="no-records-found"> No Activity Found...</div>';
        }

        $logs_preview_htm .= '</div>';

		wp_send_json( array(
			'preview'  => $logs_preview_htm
		) );

    }
    
}
