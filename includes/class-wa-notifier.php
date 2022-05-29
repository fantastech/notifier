<?php
/**
 * The core plugin class.
 *
 * @package    WA_Notifier
 */
class WA_Notifier {

	/**
	 * The single instance of the class.
	 *
	 * @since 	 0.1
	 */
	protected static $_instance = null;

	/**
	 * WA Notifier Constructor.
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
		$this->define( 'WA_NOTIFIER_VERSION', '0.1' );
		$this->define( 'WA_NOTIFIER_NAME', 'wa-notifier' );
		$this->define( 'WA_NOTIFIER_SETTINGS_PREFIX', 'wa_notifier_' );
		$this->define( 'WA_NOTIFIER_URL', trailingslashit( plugins_url( '' , dirname(__FILE__) ) ) );
		$this->define( 'WA_API_VERSION', 'v14.0' );
		$this->define( 'WA_API_URL', 'https://graph.facebook.com/' . WA_API_VERSION . '/' );
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
	 * Main WA Notifier Instance.
	 *
	 * @return WA Notifier instance.
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
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-dashboard.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-message-templates.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-settings.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-woocommerce.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook ( WA_NOTIFIER_FILE , array( $this, 'install') );

		add_action( 'admin_init', array( 'WA_Notifier_Dashboard', 'handle_dashboard_forms' ) );
		add_action( 'admin_init', array( 'WA_Notifier_Settings', 'save_settings_fields' ) );
		add_action( 'init', array( 'WA_Notifier_Woocommerce', 'init' ) );
		add_filter( 'init', array( $this , 'test_stuff') );
		add_filter( 'init', array( $this , 'handle_webhook_requests') );
		add_filter( 'init', array( $this , 'register_message_templates_cpt') );

		add_action( 'admin_enqueue_scripts', array( $this , 'admin_scripts') );
		add_action( 'admin_menu', array( $this , 'setup_admin_pages') );
		add_action( 'in_admin_header', array( $this , 'embed_page_header' ) );
	}

	/**
	 * Setup during plugin activation
	 */
	public function install() {
		$verify_token = get_option(WA_NOTIFIER_SETTINGS_PREFIX . 'verify_token');
		if(!$verify_token) {
			$bytes = random_bytes(20);
			$verify_token = WA_NOTIFIER_NAME . '-' . substr(bin2hex($bytes), 0, 10);
			update_option(WA_NOTIFIER_SETTINGS_PREFIX . 'verify_token', $verify_token);
		}
	}

	/**
	 * Register message templates custom post type
	 */
	public function register_message_templates_cpt () {
		$labels = array(
	        'name'                => 'Message Templates',
	        'singular_name'       => 'Message Template',
	        'menu_name'           => 'Message Templates',
	        'all_items'           => 'All Message Templates',
	        'view_item'           => 'View Message Template',
	        'add_new_item'        => 'Add Message Template',
	        'edit_item'           => 'Edit Message Template',
	        'update_item'         => 'Update Message Template',
	        'search_items'        => 'Search Message Template'
	    );
	    $args = array(
	        'label'               => 'Message Template',
	        'description'         => 'Whatsapp Message Templates',
	        'labels'              => $labels,
	        'supports'            => array( 'title'),
	        'hierarchical'        => false,
	        'public'              => false,
	        'show_ui'             => true,
	        'show_in_menu'        => false,
	        'show_in_nav_menus'   => false,
	        'show_in_admin_bar'   => false,
	        'menu_position'       => 5,
	        'can_export'          => true,
	        'has_archive'         => false,
	        'capability_type'     => 'post',
	        'show_in_rest' 		  => true,
	  
	    );
	    register_post_type( 'wa_message_template', $args );
	}


	/**
	 * Check if plugin's page
	 */
	public function is_wa_notifier_page() {
		$current_page = isset($_GET['page']) ? $_GET['page'] : '';
		return strpos($current_page, WA_NOTIFIER_NAME) !== false;
	}

	/**
	 * Add admin scripts and styles
	 */
	public function admin_scripts () {
		if(!self::is_wa_notifier_page()){
			return;
		}
		wp_enqueue_style( WA_NOTIFIER_NAME . '-admin-css', WA_NOTIFIER_URL . 'assets/css/admin.css' );
    	wp_enqueue_script( WA_NOTIFIER_NAME . '-admin-js', WA_NOTIFIER_URL . 'assets/js/admin.js' );
	}

	/**
	 * Adds admin pages
	 */
	public function setup_admin_pages () {
		add_menu_page( 'WA Notifier', 'WA Notifier', 'manage_options', WA_NOTIFIER_NAME, array($this, 'dashboard_page') , null, '58' );
		add_submenu_page( WA_NOTIFIER_NAME, 'Whatsapp Message Templates', 'Message Templates', 'manage_options', 'edit.php?post_type=wa_message_template' );
		add_submenu_page( WA_NOTIFIER_NAME, 'WA FIlter Settings', 'Settings', 'manage_options', WA_NOTIFIER_NAME . '-settings', array( $this, 'settings_page' ) );

	}

	/**
	 * Set up a div for the header embed to render into.
	 * The initial contents here are meant as a place loader for when the PHP page initialy loads.
	 */
	public static function embed_page_header() {
		if(!self::is_wa_notifier_page()){
			return;
		}
		?>
		<div id="wa-notifier-admin-header">
			<h2><?php echo get_admin_page_title(); ?></h2>
			<div class="">
				<a href="">Help</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Show dashboard page
	 */
	public function dashboard_page () {
		WA_Notifier_Dashboard::output();
	}

	/**
	 * Show settings page
	 */
	public function settings_page () {
		WA_Notifier_Settings::output();
	}

	/**
	 * Handle response from Whatsapp
	 */
	public function handle_webhook_requests () {
		if( ! isset($_GET['wa_notifier']) ) {
			return;
		}

		if( ! isset($_POST) ) {
			return;
		}

		/* Validate WhastApp API webbook */
		if(isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {
			$verify_token = get_option(WA_NOTIFIER_SETTINGS_PREFIX . 'verify_token');
			if(isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $verify_token) {
				echo isset($_GET['hub_challenge']) ? $_GET['hub_challenge'] : '';	
			}
			
		}

		exit;
	}

	/**
	 * Remove before pushing live
	 */
	public function test_stuff () {
		if(!isset($_GET['test_stuff'])){
			return;
		}

		$response = $this->wa_api_request('messages');

		print_r($response);
		
		die;
	}

	/**
	 * For sending requests to Whatsapp
	 */
	private function wa_api_request ( $endpoint ) {
		$token = 'EAAPSXxc2UzgBAAx0k6VC7ZAI2rJPjWg4gslbgGLvsRJ6B4VWec2sazc7IaeEESTUYwWqFqpOAlbsJCkF1tWOg8xpmsv7YIZAie28AiK2LAnwANG9N00d8jXeQXUiZCplFHTkeKhAQCKjIlEalekjYvZCHbZCDehezAUJ8ZBWfnGndpZAbweXuO0H2xFdHRZBOlLNU3bUWWGtTwZDZD';

		$request_url = $this->build_wa_request_url($endpoint);

		$default_request_body = array (
			'messaging_product' => 'whatsapp',
			'to' => '+911234567890',
			'type' => 'template',
			'template' => array (
				'name' => 'order_confirm',
				'language' => array (
					'code' => 'en'
				)
			)
		);

		$args = array(
		    'method' => 'POST',
		    'headers'     => array(
		        'Authorization' => 'Bearer ' . $token,
		    ),
		    'body' => $default_request_body
	    );

		$response = wp_remote_post( $request_url, $args);

		return $response['body'];

	}

	private function build_wa_request_url ($endpoint) {
		$phone_number_id = '114292714620122';
		$wa_business_account_id = '104305922298625';

		$request_url = WA_API_URL . $phone_number_id . '/' . untrailingslashit($endpoint);

		return $request_url;
	}

}
