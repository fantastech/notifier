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
		$this->define( 'WA_NOTIFIER_NAME', 'wp-whatsapp-notifications' );
		$this->define( 'WA_API_ENDPOINT', '' );
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
		require_once WA_NOTIFIER_PATH . 'includes/class-wa-notifier-woocommerce.php';
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'init', array( 'WA_Notifier_Woocommerce', 'init' ) );
		//add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );
		//add_action( 'admin_notices', array( $this, 'build_dependencies_notice' ) );
	}

}
