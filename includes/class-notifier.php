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
		$this->define( 'NOTIFIER_VERSION', '2.0.0' );
		$this->define( 'NOTIFIER_NAME', 'notifier' );
		$this->define( 'NOTIFIER_PREFIX', 'notifier_' );
		$this->define( 'NOTIFIER_URL', trailingslashit( plugins_url( '', dirname(__FILE__) ) ) );
		$this->define( 'NOTIFIER_APP_API_URL', 'https://app.wanotifier.com/api/v1/' );
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
		// Libraries
		require_once NOTIFIER_PATH . 'libraries/action-scheduler/action-scheduler.php';

		// Functions
		require_once NOTIFIER_PATH . 'includes/functions/functions-notifier-helpers.php';
		require_once NOTIFIER_PATH . 'includes/functions/functions-notifier-meta-box-fields.php';

		// Classes
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-admin-notices.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-dashboard.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-merge-tags.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-triggers.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-settings.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook ( NOTIFIER_FILE, array( $this, 'install') );

		add_action( 'plugins_loaded', array( 'Notifier_Admin_Notices', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Dashboard', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Notification_Merge_Tags', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Notification_Triggers', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Settings', 'init' ) );

		add_action( 'plugins_loaded', array( $this, 'maybe_include_integrations' ) );

		add_action( 'admin_enqueue_scripts', array( $this , 'admin_scripts') );
		add_action( 'in_admin_header', array( $this , 'embed_page_header' ) );

		add_action( 'in_plugin_update_message-notifier/notifier.php', array( $this , 'plugin_upgrade_message' ) );
	}

	/**
	 * Setup during plugin activation
	 */
	public function install() {

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
				<div class="header-page-title w-30">
					<h2><?php echo esc_attr(get_admin_page_title()); ?></h2>
				</div>
				<div class="header-action-links w-30 d-flex justify-content-end">
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

		$response = wp_remote_request( $request_url, $request_args);

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
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-woocommerce.php';
			Notifier_Woocommerce::init();
		}
		if ( ! class_exists( 'WC_Session' ) ) {
		    include_once( WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-session.php' );
		}

		// Gravity Forms
		if ( class_exists( 'GFCommon' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-gravityforms.php';
			Notifier_GravityForms::init();
		}

		// Contact Form 7
		if ( class_exists( 'WPCF7' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-cf7.php';
			Notifier_ContactForm7::init();
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
	 * Plugin upgrade notice on Plugins page
	 */
	public static function plugin_upgrade_message () {
		if(version_compare(NOTIFIER_VERSION,'1.0.5', '<=') ){
			echo '<br><br><b>HEADS UP! Major update ahead.</b><br><br>v2.0.0 introduces major plugin updates. You\'ll need to <b>re-configure your triggers</b> after you upgrade to make sure the notifications are fired correctly.';
		}
	}

}
