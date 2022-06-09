<?php
/**
 * The core plugin class.
 *
 * @package    WA_Notifier
 */
class WA_Notifier {
	/**
	 * The single instance of the class.
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
		$this->define( 'WA_NOTIFIER_PREFIX', 'wa_notifier_' );
		$this->define( 'WA_NOTIFIER_URL', trailingslashit( plugins_url( '' , dirname(__FILE__) ) ) );
		$this->define( 'WA_NOTIFIER_WA_API_VERSION', 'v14.0' );
		$this->define( 'WA_NOTIFIER_WA_API_URL', 'https://graph.facebook.com/' . WA_NOTIFIER_WA_API_VERSION . '/' );
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
		require_once WA_NOTIFIER_PATH . 'includes/wa-notifier-helper-functions.php';
		require_once WA_NOTIFIER_PATH . 'includes/wa-notifier-meta-box-functions.php';

		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-admin-notices.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-dashboard.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-message-templates.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-contacts.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-notifications.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-settings.php';
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-woocommerce.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook ( WA_NOTIFIER_FILE , array( $this, 'install') );

		add_action( 'plugins_loaded', array( 'WA_Notifier_Dashboard', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Message_Templates', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Contacts', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Notifications', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Settings', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Woocommerce', 'init' ) );

		add_filter( 'init', array( $this , 'test_stuff') );
		add_filter( 'init', array( $this , 'handle_webhook_requests') );

		add_action( 'admin_enqueue_scripts', array( $this , 'admin_scripts') );
		add_action( 'in_admin_header', array( $this , 'embed_page_header' ) );
	}

	/**
	 * Setup during plugin activation
	 */
	public function install() {
		$verify_token = get_option(WA_NOTIFIER_PREFIX . 'verify_token');
		if(!$verify_token) {
			$bytes = random_bytes(20);
			$verify_token = WA_NOTIFIER_NAME . '-' . substr(bin2hex($bytes), 0, 10);
			update_option(WA_NOTIFIER_PREFIX . 'verify_token', $verify_token);
		}
	}

	/**
	 * Check if plugin's page
	 */
	public function is_wa_notifier_page() {
		$current_screen = get_current_screen();
		if ( strpos($current_screen->id, WA_NOTIFIER_NAME) !== false) {
			return true;
		}

		$plugin_ctps = array( 'wa_message_template', 'wa_contact', 'wa_notification' );
		if ( '' !== $current_screen->post_type && in_array( $current_screen->post_type, $plugin_ctps ) ) {
			return true;
		}

		return false;
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
    	wp_localize_script( WA_NOTIFIER_NAME . '-admin-js', 'waNotifierTemplates', apply_filters( 'wa_notifier_admin_html_templates', array() ) );
	}

	/**
	 * Set up a div for the header embed to render into.
	 * The initial contents here are meant as a place loader for when the PHP page initialy loads.
	 */
	public static function embed_page_header() {
		if(!self::is_wa_notifier_page()){
			return;
		}
		$current_screen = get_current_screen();
		$cpt = ( '' !== $current_screen->post_type) ? $current_screen->post_type : '';
		?>
		<div id="wa-notifier-admin-header" data-post-type="<?php echo $cpt; ?>">
			<h2><?php echo get_admin_page_title(); ?></h2>
			<div class="header-action-links">
				<span class="header-version">Version: 0.1 (beta)</span>
				<a href="mailto:ram@fantastech.co?subject=Regarding%20WA%20Notifier%20on%20<?php echo get_site_url(); ?>">Help</a>
				<a href="admin.php?page=wa-notifier&show=disclaimer">Disclaimer</a>
			</div>
		</div>
		<?php
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
			$verify_token = get_option(WA_NOTIFIER_PREFIX . 'verify_token');
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

		$args = array (
			'messaging_product' => 'whatsapp',
			'recipient_type' => 'individual',
			'to' => '+917828699878',
			'type' => 'template',
			'template' => array (
				'name' => 'received_request',
				'language' => array (
					'code' => 'en_US'
				)
			)
		);

		$response = self::wa_cloud_api_request('messages', $args);

		print_r($response);
		
		die;
	}

	/**
	 * For sending requests to Cloud API
	 */
	public function wa_cloud_api_request ( $endpoint, $args = array(), $method = 'POST' ) {
		$phone_number_id = get_option('wa_notifier_phone_number_id');
		$request_url = WA_NOTIFIER_WA_API_URL . $phone_number_id . '/' . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method);
	}

	/**
	 * For sending requests to WA Business API
	 */
	public function wa_business_api_request ( $endpoint, $args = array(), $method = 'POST' ) {
		$business_account_id = get_option('wa_notifier_business_account_id');
		$request_url = WA_NOTIFIER_WA_API_URL . $business_account_id . '/' . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method);
	}

	/**
	 * For sending API requests
	 */
	private function send_api_request ( $request_url, $args, $method ) {
		$permanent_access_token = get_option('wa_notifier_permanent_access_token');
		$request_args = array(
		    'method' => $method,
		    'headers'     => array(
		        'Authorization' => 'Bearer ' . $permanent_access_token ,
		    ),
		    'body' => $args
	    );
		$response = wp_remote_request( $request_url, $request_args);
		if ( is_wp_error( $response ) ) {
			echo $response->get_error_message();
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			return json_decode($response_body);
		}
	}

}
