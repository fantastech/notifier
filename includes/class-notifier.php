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
		$this->define( 'NOTIFIER_VERSION', '0.1.1' );
		$this->define( 'NOTIFIER_NAME', 'notifier' );
		$this->define( 'NOTIFIER_PREFIX', 'notifier_' );
		$this->define( 'NOTIFIER_URL', trailingslashit( plugins_url( '', dirname(__FILE__) ) ) );
		$this->define( 'NOTIFIER_WA_API_VERSION', 'v14.0' );
		$this->define( 'NOTIFIER_WA_API_URL', 'https://graph.facebook.com/' . NOTIFIER_WA_API_VERSION . '/' );
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
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-message-templates.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-contacts.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notifications.php';
		/* ==Notifier_Pro_Code_Start== */
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-merge-tags.php';
		/* ==Notifier_Pro_Code_End== */
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-notification-triggers.php';
		require_once NOTIFIER_PATH . 'includes/classes/class-notifier-settings.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook ( NOTIFIER_FILE, array( $this, 'install') );

		add_action( 'plugins_loaded', array( 'Notifier_Dashboard', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Message_Templates', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Contacts', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Notifications', 'init' ) );
		/* ==Notifier_Pro_Code_Start== */
		add_action( 'plugins_loaded', array( 'Notifier_Notification_Merge_Tags', 'init' ) );
		/* ==Notifier_Pro_Code_End== */
		add_action( 'plugins_loaded', array( 'Notifier_Notification_Triggers', 'init' ) );
		add_action( 'plugins_loaded', array( 'Notifier_Settings', 'init' ) );
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
		$verify_token = get_option(NOTIFIER_PREFIX . 'verify_token');
		if (!$verify_token) {
			$bytes = random_bytes(20);
			$verify_token = NOTIFIER_NAME . '-' . substr(bin2hex($bytes), 0, 10);
			update_option(NOTIFIER_PREFIX . 'verify_token', $verify_token);
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

    	// Date / time picker
    	wp_enqueue_script( 'jquery-ui-datepicker' );
    	wp_enqueue_script(
    		NOTIFIER_NAME . '-timepicker-addon',
    		NOTIFIER_URL . 'assets/js/jquery-ui-timepicker-addon.min.js',
    		array( 'jquery-ui-datepicker' ),
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
    		'waNotifier',
    		apply_filters( 'notifier_js_variables', array('ajaxurl' => admin_url( 'admin-ajax.php' ) ) )
    	);

    	// Media upload library
    	wp_enqueue_media();

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
				<div class="header-menu-items w-40 d-flex justify-content-center">
					<?php
					if ( 'wa_contact' == $cpt ) {
						?>
							<ul>
								<li>
									<a class="<?php echo ('wa_contact_list' !== $tax && 'wa_contact_tag' !== $tax) ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url('edit.php?post_type=wa_contact') ); ?>" class="">Contacts</a>
								</li>
								<li>
									<a class="<?php echo ('wa_contact_list' == $tax) ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url('edit-tags.php?taxonomy=wa_contact_list&post_type=wa_contact') ); ?>">Lists</a>
								</li>
								<li>
									<a class="<?php echo ('wa_contact_tag' == $tax) ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url('edit-tags.php?taxonomy=wa_contact_tag&post_type=wa_contact') ); ?>">Tags</a>
								</li>
							</ul>
						<?php
					}
					?>
				</div>
				<div class="header-action-links w-30 d-flex justify-content-end">
					<span class="header-version">Version: <?php echo esc_html(NOTIFIER_VERSION); ?> (beta)</span>
					<a href="mailto:ram@fantastech.co?subject=%5BWA%20Notifier%5D%20Help%20Needed%20on<?php echo esc_url(get_site_url()); ?>">Help</a>
					<a href="admin.php?page=notifier&show=disclaimer">Disclaimer</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle response from Whatsapp
	 */
	public static function handle_webhook_requests () {
		if ( ! isset($_GET['notifier']) ) {
			return;
		}

		if ( ! isset($_POST) ) {
			return;
		}

		$hub_mode = isset($_GET['hub_mode']) ? sanitize_text_field( wp_unslash( $_GET['hub_mode'] ) ) : '';
		$hub_verify_token = isset($_GET['hub_verify_token']) ? sanitize_text_field( wp_unslash( $_GET['hub_verify_token'] ) ) : '';
		$hub_challenge = isset($_GET['hub_challenge']) ? sanitize_text_field( wp_unslash( $_GET['hub_challenge'] ) ) : '';

		/* Validate WhastApp API webbook */
		if ( 'subscribe' === $hub_mode ) {
			$verify_token = get_option(NOTIFIER_PREFIX . 'verify_token');
			if ($hub_verify_token == $verify_token) {
				echo esc_html($hub_challenge);
			}
		}

		exit;
	}

	/**
	 * For sending requests to Cloud API
	 */
	public static function wa_cloud_api_request ( $endpoint, $args = array(), $method = 'POST', $headers = array() ) {
		$phone_number_id = get_option('notifier_phone_number_id');
		$request_url = NOTIFIER_WA_API_URL . $phone_number_id . '/' . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method, $headers);
	}

	/**
	 * For sending requests to WA Business API
	 */
	public static function wa_business_api_request ( $endpoint, $args = array(), $method = 'POST', $headers = array() ) {
		$business_account_id = get_option('notifier_business_account_id');
		$request_url = NOTIFIER_WA_API_URL . $business_account_id . '/' . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method, $headers);
	}

	/**
	 * For sending graph api requests
	 */
	public static function wa_graph_api_request ( $endpoint, $args = array(), $method = 'POST', $headers = array() ) {
		$request_url = NOTIFIER_WA_API_URL . untrailingslashit($endpoint);
		return self::send_api_request($request_url, $args, $method, $headers);
	}

	/**
	 * For sending API requests
	 */
	private static function send_api_request ( $request_url, $args, $method, $headers ) {
		$permanent_access_token = get_option('notifier_permanent_access_token');
		$headers = wp_parse_args($headers, array(
	        'Authorization' => 'Bearer ' . $permanent_access_token
	    ));
		$request_args = array(
		    'method' 	=> $method,
		    'headers' 	=> $headers,
		    'timeout'   => 120,
		    'body' 		=> $args
	    );
		$response = wp_remote_request( $request_url, $request_args);
		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
			return false;
		} elseif (isset($response->error)) {
			error_log($response->error_user_title . ' - ' . $response->eerror_user_msg);
			return json_decode($response_body);
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			return json_decode($response_body);
		}
	}

	/**
	 * For uploading media to WhatsApp
	 */
	public static function wa_cloud_api_upload_media($attachment_id) {
		$file_path = get_attached_file($attachment_id);
		$file_size = filesize($file_path);

		if (!$file_path) {
			return;
		}

		$file_args = array (
			'file_length' 	=> $file_size,
			'file_type'		=> get_post_mime_type($attachment_id)
		);

		$permanent_access_token = get_option('notifier_permanent_access_token');

		// Create session ID for upload
		$upload_session = self::wa_graph_api_request('app/uploads', $file_args);
		if (!isset($upload_session->id)) {
			error_log('Error creating WhatsApp image upload session: ' . $upload_session);
			return false;
		}

		// Upload the file
		$file = @fopen( $file_path, 'r' );
		$file_data = fread( $file, $file_size );

		$upload_headers = array(
			'Authorization' => 'OAuth ' . $permanent_access_token,
			'file_offset'	=> 0,
			'Connection'	=> 'Close',
			'Host'			=> 'graph.facebook.com',
			'Content-Type'	=> 'multipart/form-data'
		);

		$upload_handle = self::wa_graph_api_request( $upload_session->id, $file_data, null, $upload_headers );
		if (!isset($upload_handle->h)) {
			error_log('Error while uploading profile image: ' . $upload_handle);
			return false;
		}

		return $upload_handle->h;
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
		if ( class_exists( 'WooCommerce' ) ) {
			require_once NOTIFIER_PATH . 'includes/classes/class-notifier-woocommerce.php';
			Notifier_Woocommerce::init();
		}
	}

	/**
	 * Add admin html templates to footer
	 */
	public static function add_admin_html_templates () {
		if (!self::is_notifier_page()) {
			return;
		}

		$templates = apply_filters('notifier_admin_html_templates', array());

		if (count($templates) == 0) {
			return;
		}

		echo '<div class="notifier-templates">';
		foreach ($templates as $template) {
			$file_path = NOTIFIER_PATH . '/views/templates/admin-' . $template . '.php';
			if ( ! file_exists($file_path) ) {
				continue;
			}
			echo '<template id="notifier-template-' . esc_attr($template) . '">';
			require_once $file_path;
			echo '</template>';
		}
		echo '</div>';
	}

}