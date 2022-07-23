<?php
/**
 * Plugin Name: WA Notifier Pro
 * Plugin URI: https://wanotifier.com
 * Description: Send bulk broadcast messages or transactional notifications to your contacts and Woocommerce customers on WhatsApp using their offical Cloud API.
 * Version: 0.1
 * Author: WANotifier.com
 * Author URI: https://wanotifier.com
 * Text Domain: wa-notifier
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
if ( ! defined( 'WA_NOTIFIER_FILE' ) ) {
   define( 'WA_NOTIFIER_FILE', __FILE__ );
}

if ( ! defined( 'WA_NOTIFIER_PATH' ) ) {
    define('WA_NOTIFIER_PATH', plugin_dir_path( __FILE__ ));
}

/**
 * Load the core plugin file.
 */
require WA_NOTIFIER_PATH . 'includes/class-wa-notifier.php';

/**
 * Begin execution of the plugin.
 *
 * @since   0.1
 */
function run_wa_notifier() {
	$plugin = WA_Notifier::get_instance();
}
run_wa_notifier();
