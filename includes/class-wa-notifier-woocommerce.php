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
        add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__ , 'add_wa_settings_tab') , 21 );
        add_action( 'woocommerce_settings_tabs_whatsapp', array( __CLASS__ , 'wa_settings_tab_fields') );
	}

	/*
	* Add setting tab in Woocommerce
	*/
	public static function add_wa_settings_tab( $settings_tabs ) {
		$keys = array_keys( $settings_tabs );
		$index = array_search( 'email', $keys );
		$pos = false === $index ? count( $settings_tabs ) : $index + 1;
		$new_tab['whatsapp'] = __( 'WhatsApp', WA_NOTIFIER_NAME );
		$settings_tabs = array_merge( array_slice( $settings_tabs, 0, $pos ), $new_tab, array_slice( $settings_tabs, $pos ) );
        return $settings_tabs;
    }

	public static function wa_settings_tab_fields() {
	    woocommerce_admin_fields( self::get_wa_settings() );
	}

	public static function get_wa_settings() {
	    $settings = array(
	        'section_title' => array(
	            'name'     => __( 'Section Title', 'woocommerce-settings-tab-demo' ),
	            'type'     => 'title',
	            'desc'     => '',
	            'id'       => 'wc_settings_tab_demo_section_title'
	        ),
	        'title' => array(
	            'name' => __( 'Title', 'woocommerce-settings-tab-demo' ),
	            'type' => 'text',
	            'desc' => __( 'This is some helper text', 'woocommerce-settings-tab-demo' ),
	            'id'   => 'wc_settings_tab_demo_title'
	        ),
	        'description' => array(
	            'name' => __( 'Description', 'woocommerce-settings-tab-demo' ),
	            'type' => 'textarea',
	            'desc' => __( 'This is a paragraph describing the setting. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda.', 'woocommerce-settings-tab-demo' ),
	            'id'   => 'wc_settings_tab_demo_description'
	        ),
	        'section_end' => array(
	             'type' => 'sectionend',
	             'id' => 'wc_settings_tab_demo_section_end'
	        )
	    );
	    return apply_filters( 'wc_settings_tab_demo_settings', $settings );
	}

}
