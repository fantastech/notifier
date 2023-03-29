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
		$this->define( 'NOTIFIER_VERSION', '2.1.3' );
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
		// Functions
		require_once NOTIFIER_PATH . 'includes/functions/functions-notifier-helpers.php';
		require_once NOTIFIER_PATH . 'includes/functions/functions-notifier-meta-box-fields.php';

		// Classes
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-admin-notices.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-dashboard.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-merge-tags.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-triggers.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-settings.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-frontend.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook ( NOTIFIER_FILE, array( $this, 'install') );

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

		// WPForms
		if ( class_exists( 'WPForms' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-wpforms.php';
			Notifier_WPForms::init();
		}

		// Ninja Forms
		if ( class_exists( 'Ninja_Forms' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-ninjaforms.php';
			Notifier_NinjaForms::init();
		}

		//FrmForm
		if ( class_exists( 'FrmForm' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-formidable.php';
			Notifier_Formidable::init();
		}

		//WPFluentForm
		if ( function_exists( 'fluentFormApi' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-fluentforms.php';
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

}
