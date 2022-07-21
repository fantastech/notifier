<?php
/**
 * Plugin Name:       WA Notifier - Send Broadcast & Transational Notifications on WhatsApp
 * Plugin URI:        https://wanotifier.com
 * Description:       Send bulk WhatsApp broadcast messages or transactional notifications to your contacts and Woocommerce customers using WhatsApp's Cloud API.
 * Version:           0.1
 * Author:            WANotifier.com
 * Author URI:        https://wanotifier.com
 * Text Domain:       wp-whatsapp-notifications
 * Requires at least: 5.7
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
