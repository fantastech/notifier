<?php
/**
 * Woocommerce notifications
 *
 * @package    Wa_Notifier
 * @subpackage Wa_Notifier/includes
 * @author     WANotifier.com <contact@wanotifier.com>
 */
class WA_Notifier_Woocommerce {

	/**
	 * Init.
	 */
	public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__ , 'add_settings_tab') , 21 );
	}

	/*
	* Add setting tab in Woocommerce
	*/
	public static function add_settings_tab( $settings_tabs ) {
		$keys = array_keys( $settings_tabs );
		$index = array_search( 'email', $keys );
		$pos = false === $index ? count( $settings_tabs ) : $index + 1;
		$new_tab['settings_tab_whatsapp'] = __( 'WhatsApp', WA_NOTIFIER_NAME );
		$settings_tabs = array_merge( array_slice( $settings_tabs, 0, $pos ), $new_tab, array_slice( $settings_tabs, $pos ) );
        return $settings_tabs;
    }

}
