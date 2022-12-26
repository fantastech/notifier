<?php
/**
 * Plugin Name: WANotifier - Send Message Notifications Using Cloud API for WordPress and Woocommerce
 * Plugin URI: https://wordpress.org/plugins/notifier/
 * Description: Send WhatsApp transactional notifications to your contacts and Woocommerce customers using WhatsApp Cloud API.
 * Version: 1.0.5
 * Author: WANotifier.com
 * Author URI: https://wanotifier.com
 * Text Domain: notifier
 * Requires at least: 5.7
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define constants
 */
if ( ! defined( 'NOTIFIER_FILE' ) ) {
	define( 'NOTIFIER_FILE', __FILE__ );
}

if ( ! defined( 'NOTIFIER_PATH' ) ) {
    define('NOTIFIER_PATH', plugin_dir_path( __FILE__ ));
}

/**
 * Load the core plugin file.
 */
require NOTIFIER_PATH . 'includes/class-notifier.php';

/**
 * Begin execution of the plugin.
 *
 * @since   0.1
 */
function run_notifier() {
	$plugin = Notifier::get_instance();
}
run_notifier();
