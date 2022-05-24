<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wanotifier.com
 * @since      1.0.0
 *
 * @package    Wp_Whatsapp_Notifications
 * @subpackage Wp_Whatsapp_Notifications/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Whatsapp_Notifications
 * @subpackage Wp_Whatsapp_Notifications/includes
 * @author     WPNotifier.com <contact@wanotifier.com>
 */
class Wp_Whatsapp_Notifications_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-whatsapp-notifications',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
