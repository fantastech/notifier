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
		// Libraries
		require_once WA_NOTIFIER_PATH . 'libraries/action-scheduler/action-scheduler.php';

		// Functions
		require_once WA_NOTIFIER_PATH . 'includes/functions/functions-wa-notifier-helpers.php';
		require_once WA_NOTIFIER_PATH . 'includes/functions/functions-wa-notifier-meta-box-fields.php';

		// Classes
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-admin-notices.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-dashboard.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-message-templates.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-contacts.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-notifications.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-notification-merge-tags.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-notification-triggers.php';
		require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-settings.php';
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
		add_action( 'plugins_loaded', array( 'WA_Notifier_Notification_Merge_Tags', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Notification_Triggers', 'init' ) );
		add_action( 'plugins_loaded', array( 'WA_Notifier_Settings', 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'maybe_include_woocoomerce_class' ) );

		add_filter( 'init', array( $this , 'handle_webhook_requests') );

		add_action( 'admin_enqueue_scripts', array( $this , 'admin_scripts') );
		add_action( 'in_admin_header', array( $this , 'embed_page_header' ) );

		add_action( 'removable_query_args', array( $this , 'remove_admin_query_args') );

		add_action( 'admin_footer', array($this, 'add_admin_html_templates') );
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
	public static function is_wa_notifier_page() {
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

    	// Select2
    	wp_enqueue_script(
    		WA_NOTIFIER_NAME . '-select2-js',
    		WA_NOTIFIER_URL . 'assets/js/select2.min.js',
    		array('jquery'),
    		WA_NOTIFIER_VERSION,
    		true
    	);
    	// Date / time picker
    	wp_enqueue_script( 'jquery-ui-datepicker' );
    	wp_enqueue_script(
    		WA_NOTIFIER_NAME . '-timepicker-addon',
    		WA_NOTIFIER_URL . 'assets/js/jquery-ui-timepicker-addon.min.js',
    		array( 'jquery-ui-datepicker' ),
    		WA_NOTIFIER_VERSION,
    		true
    	);
    	// Admin JS file
    	wp_enqueue_script(
    		WA_NOTIFIER_NAME . '-admin-js',
    		WA_NOTIFIER_URL . 'assets/js/admin.js',
    		array('jquery'),
    		WA_NOTIFIER_VERSION,
    		true
    	);
    	wp_localize_script(
    		WA_NOTIFIER_NAME . '-admin-js',
    		'waNotifier',
    		apply_filters( 'wa_notifier_js_variables', array('ajaxurl' => admin_url( 'admin-ajax.php' ) ) )
    	);

    	// Styles
    	wp_enqueue_style(
	    	WA_NOTIFIER_NAME . '-datepicker-style',
	    	'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
	    );
	    wp_enqueue_style(
	    	WA_NOTIFIER_NAME . '-admin-css',
	    	WA_NOTIFIER_URL . 'assets/css/admin.css'
	    );
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
		$tax = ( '' !== $current_screen->taxonomy) ? $current_screen->taxonomy : '';
		?>
		<div id="wa-notifier-admin-header" data-post-type="<?php echo $cpt; ?>">
			<div class="wa-notifier-admin-header-content">
				<div class="header-page-title w-30">
					<h2><?php echo get_admin_page_title(); ?></h2>
				</div>
				<div class="header-menu-items w-40 d-flex justify-content-center">
					<?php
						if ( 'wa_contact' == $cpt ) {
					?>
							<ul>
								<li>
									<a class="<?php echo ('wa_contact_list' !== $tax && 'wa_contact_tag' !== $tax) ? 'active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=wa_contact') ?>" class="">Contacts</a>
								</li>
								<li>
									<a class="<?php echo ('wa_contact_list' == $tax) ? 'active' : ''; ?>" href="<?php echo admin_url('edit-tags.php?taxonomy=wa_contact_list&post_type=wa_contact'); ?>">Lists</a>
								</li>
								<li>
									<a class="<?php echo ('wa_contact_tag' == $tax) ? 'active' : ''; ?>" href="<?php echo admin_url('edit-tags.php?taxonomy=wa_contact_tag&post_type=wa_contact'); ?>">Tags</a>
								</li>
							</ul>
					<?php
						}
					?>
				</div>
				<div class="header-action-links w-30 d-flex justify-content-end">
					<span class="header-version">Version: 0.1 (beta)</span>
					<a href="mailto:ram@fantastech.co?subject=%5BWA%20Notifier%5D%20Help%20Needed%20on<?php echo get_site_url(); ?>">Help</a>
					<a href="admin.php?page=wa-notifier&show=disclaimer">Disclaimer</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle response from Whatsapp
	 */
	public static function handle_webhook_requests () {
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
	 * For sending requests to Cloud API
	 */
	public static function wa_cloud_api_request ( $endpoint, $args = array(), $method = 'POST' ) {
		$phone_number_id = get_option('wa_notifier_phone_number_id');
		$request_url = WA_NOTIFIER_WA_API_URL . $phone_number_id . '/' . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method);
	}

	/**
	 * For sending requests to WA Business API
	 */
	public static function wa_business_api_request ( $endpoint, $args = array(), $method = 'POST' ) {
		$business_account_id = get_option('wa_notifier_business_account_id');
		$request_url = WA_NOTIFIER_WA_API_URL . $business_account_id . '/' . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method);
	}

	/**
	 * For sending API requests
	 */
	private static function send_api_request ( $request_url, $args, $method ) {
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
			return false;
		}
		else if(isset($response->error)) {
			error_log($response->error_user_title . ' - ' . $response->eerror_user_msg);
			return json_decode($response_body);
		}
		else {
			$response_body = wp_remote_retrieve_body( $response );
			return json_decode($response_body);
		}
	}

	/**
	 * Remove query args from WP backend
	 */
	public static function remove_admin_query_args ($args) {
		$remove_args = array('wa_import_count', 'wa_import_skipped', 'wa_contacts_import', 'import_contacts_from_users', 'refresh_status');
		return array_merge($args, $remove_args);
	}

	/**
	 * Load Woocommerce class if it's present is activated
	 */
	public static function maybe_include_woocoomerce_class () {
		if( class_exists( 'WooCommerce' ) ){
			require_once WA_NOTIFIER_PATH . 'includes/classes/class-wa-notifier-woocommerce.php';
			WA_Notifier_Woocommerce::init();
		}
	}

	/**
	 * Add admin html templates to footer
	 */
	public static function add_admin_html_templates (){
		if(!self::is_wa_notifier_page()){
			return;
		}

		$templates = apply_filters('wa_notifier_admin_html_templates', array());

		if(count($templates) == 0) {
			return;
		}

		echo '<div class="wa-notifier-templates">';
		foreach($templates as $key => $template) {
			echo '<template id="'.$key.'">'.$template.'</template>';
		}
		echo '</div>';
	}

}
