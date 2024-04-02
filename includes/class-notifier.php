<?php
/**
 * The core plugin class.
 *
 * @package    Notifier
 */
class Notifier {
	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	/**
	 * Notifier Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define Constants.
	 */
	private function define_constants() {
		$this->define( 'NOTIFIER_VERSION', '2.5.2' );
		$this->define( 'NOTIFIER_NAME', 'notifier' );
		$this->define( 'NOTIFIER_PREFIX', 'notifier_' );
		$this->define( 'NOTIFIER_URL', trailingslashit( plugins_url( '', dirname(__FILE__) ) ) );
		$this->define( 'NOTIFIER_APP_API_URL', 'https://app.wanotifier.com/api/v1/' );
		$this->define( 'NOTIFIER_ACTIVITY_TABLE_NAME', 'notifier_activity_log' );
		$this->define( 'CLEAN_OLD_LOGS_HOOK', 'notifier_clean_old_logs' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Main Notifier Instance.
	 *
	 * @return Notifier instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Functions
		require_once NOTIFIER_PATH . 'includes/functions/functions-notifier-helpers.php';
		require_once NOTIFIER_PATH . 'includes/functions/functions-notifier-meta-box-fields.php';
		require_once NOTIFIER_PATH . 'libraries/action-scheduler/action-scheduler.php';

		// Classes
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-admin-notices.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-dashboard.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-merge-tags.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-triggers.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-settings.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-frontend.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-tools.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook ( NOTIFIER_FILE, array( $this, 'install') );
		register_deactivation_hook ( NOTIFIER_FILE, array( $this, 'unschedule_recurring_task') );

		add_action( 'after_setup_theme', array( 'Notifier_Admin_Notices', 'init' ) );
		add_action( 'after_setup_theme', array( 'Notifier_Dashboard', 'init' ) );
		add_action( 'after_setup_theme', array( 'Notifier_Notification_Merge_Tags', 'init' ) );
		add_action( 'after_setup_theme', array( 'Notifier_Notification_Triggers', 'init' ) );
		add_action( 'after_setup_theme', array( 'Notifier_Settings', 'init' ) );
		add_action( 'after_setup_theme', array( 'Notifier_Frontend', 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this , 'admin_scripts') );
		add_action( 'wp_enqueue_scripts', array( $this , 'frontend_scripts') );
		add_action( 'in_admin_header', array( $this , 'embed_page_header' ) );

		add_action( 'after_setup_theme', array( $this, 'maybe_include_integrations' ) );
		add_action( 'after_setup_theme', array( 'Notifier_Tools', 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'setup_activity_log' ) );
		add_action( 'notifier_clean_old_logs', array( $this, 'notifier_delete_old_activity_logs' ) );
	}

	/**
	 * Setup during plugin activation
	 */
	public function install() {
		$args = array();
		if ( false === as_next_scheduled_action(CLEAN_OLD_LOGS_HOOK) ) {
			as_schedule_recurring_action( time(), DAY_IN_SECONDS, CLEAN_OLD_LOGS_HOOK, $args );
		}
	}

	/**
	 * Setup during plugin deactivation
	 */
	public function unschedule_recurring_task() {
		as_unschedule_action(CLEAN_OLD_LOGS_HOOK);
	}

    /**
     * Check if the plugin was updated and run the upgrade routine if necessary.
     */
	public function setup_activity_log() {
		$is_table_created = get_option('notifier_is_activity_log_tbl_created');
		if ('yes' !== $is_table_created) {
			self::create_notifier_activity_log_table();
		}
	}

	/**
	 * Check if plugin's page
	 */
	public static function is_notifier_page() {
		$current_screen = get_current_screen();
		if ( strpos($current_screen->id, NOTIFIER_NAME) !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Add admin scripts and styles
	 */
	public function admin_scripts () {
		if (!self::is_notifier_page()) {
			return;
		}

    	// Select2
    	wp_enqueue_script(
    		NOTIFIER_NAME . '-select2-js',
    		NOTIFIER_URL . 'assets/js/select2.min.js',
    		array('jquery'),
    		NOTIFIER_VERSION,
    		true
    	);

    	// Admin JS file
    	wp_enqueue_script(
    		NOTIFIER_NAME . '-admin-js',
    		NOTIFIER_URL . 'assets/js/admin.js',
    		array('jquery'),
    		NOTIFIER_VERSION,
    		true
    	);
    	wp_localize_script(
    		NOTIFIER_NAME . '-admin-js',
    		'notifierObj',
    		apply_filters( 'notifier_js_variables', array('ajaxurl' => admin_url( 'admin-ajax.php' ) ) )
    	);

    	// Styles
	    wp_enqueue_style(
	    	NOTIFIER_NAME . '-admin-css',
	    	NOTIFIER_URL . 'assets/css/admin.css',
	    	null,
	    	NOTIFIER_VERSION
	    );
	}

	/**
	 * Add frontend scripts and styles
	 */
	public function frontend_scripts () {
		if('yes' === get_option('notifier_ctc_enable')){
	    	// Styles
		    wp_enqueue_style(
		    	NOTIFIER_NAME . '-frontend-css',
		    	NOTIFIER_URL . 'assets/css/frontend.css',
		    	null,
		    	NOTIFIER_VERSION
		    );
		}
	}

	/**
	 * Set up a div for the header embed to render into.
	 * The initial contents here are meant as a place loader for when the PHP page initialy loads.
	 */
	public static function embed_page_header() {
		if (!self::is_notifier_page()) {
			return;
		}
		$current_screen = get_current_screen();
		$cpt = ( '' !== $current_screen->post_type) ? $current_screen->post_type : '';
		$tax = ( '' !== $current_screen->taxonomy) ? $current_screen->taxonomy : '';
		?>
		<div id="notifier-admin-header" data-post-type="<?php echo esc_attr($cpt); ?>">
			<div class="notifier-admin-header-content">
				<div class="header-page-title w-30 d-flex align-content-center">
					<a class="header-logo" href="admin.php?page=notifier"><img src="<?php echo NOTIFIER_URL; ?>assets/images/favicon.svg"></a>
					<h2><?php echo esc_attr(get_admin_page_title()); ?></h2>
				</div>
				<div class="header-action-links w-30 d-flex justify-content-end">
					<span class="review-us-link">Review us: <a href="https://wordpress.org/support/plugin/notifier/reviews/#new-post" target="_blank">⭐⭐⭐⭐⭐</a></span>
					<span class="header-version">Version: <?php echo esc_html(NOTIFIER_VERSION); ?></span>
					<a href="https://wanotifier.com/support/" target="_blank">Help</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * For sending API requests
	 */
	public static function send_api_request ( $endpoint, $args, $method = 'POST', $headers = array() ) {
		$api_key = get_option('notifier_api_key');
		$api_key = trim($api_key);
		if('' == $api_key) {
			return false;
		}

		if(empty($headers)){
			$headers = array(
				'Content-Type' => 'application/json; charset=utf-8'
			);
		}

		$request_url = NOTIFIER_APP_API_URL . $endpoint . '?key=' . $api_key;
		$request_args = array(
		    'method' 	=> $method,
		    'headers' 	=> $headers,
		    'timeout'   => 120,
		    'body' 		=> json_encode($args),
		    'sslverify'	=> false
	    );

		$response = wp_remote_request( $request_url, $request_args );

		$response_body = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
			return false;
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			return json_decode($response_body);
		}
	}

	/**
	 * Load Woocommerce class if it's present is activated
	 */
	public static function maybe_include_integrations () {
		// Woocommerce
		if ( class_exists( 'WooCommerce' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-woocommerce.php';
			Notifier_Woocommerce::init();
		}
		if ( ! class_exists( 'WC_Session' ) ) {
		    include_once( WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-session.php' );
		}

		// WooCommerce Cart Abandonment Recovery by CartFlows
		if ( class_exists( 'CARTFLOWS_CA_Loader' ) && defined('CARTFLOWS_CA_VER') && version_compare(CARTFLOWS_CA_VER, '1.2.25', '>=')) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-wcar.php';
			Notifier_WCAR::init();
		}

		// Gravity Forms
		if ( class_exists( 'GFCommon' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-gravityforms.php';
			Notifier_GravityForms::init();
		}

		// Contact Form 7
		if ( class_exists( 'WPCF7' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-cf7.php';
			Notifier_ContactForm7::init();
		}

		// WPForms
		if ( class_exists( 'WPForms' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-wpforms.php';
			Notifier_WPForms::init();
		}

		// Ninja Forms
		if ( class_exists( 'Ninja_Forms' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-ninjaforms.php';
			Notifier_NinjaForms::init();
		}

		//FrmForm
		if ( class_exists( 'FrmForm' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-formidable.php';
			Notifier_Formidable::init();
		}

		//WPFluentForm
		if ( function_exists( 'fluentFormApi' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/integrations/class-notifier-fluentforms.php';
			Notifier_FluentForms::init();
		}
	}

	/**
	 * Is connection to WANotifier.com active?
	 */
	public static function is_api_active () {
		$activated = get_option(NOTIFIER_PREFIX . 'api_activated');
		if('yes' == $activated) {
			return true;
		}
		else{
			return false;
		}
	}

	/**
     * Create New db table-- wanotifier_activity_log
     */
	public static function create_notifier_activity_log_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . NOTIFIER_ACTIVITY_TABLE_NAME;
	
		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				log_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				timestamp timestamp NOT NULL,
				message text NOT NULL,
				type varchar(16) NOT NULL,
				PRIMARY KEY  (log_id),
				INDEX idx_timestamp (timestamp)
			) $charset_collate;";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
	
			update_option('notifier_is_activity_log_tbl_created', 'yes');
		}
	}

	/**
     * Delete old log from activity table according to interval
     */
	public function notifier_delete_old_activity_logs() {
		global $wpdb;
		$table_name = $wpdb->prefix . NOTIFIER_ACTIVITY_TABLE_NAME;
		$wpdb->query($wpdb->prepare("DELETE FROM `$table_name` WHERE timestamp <= DATE_SUB(NOW(), INTERVAL 7 DAY)"));
	}
}
