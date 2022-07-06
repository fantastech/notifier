<?php
/**
 * Woocommerce notifications
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Woocommerce {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'wa_notifier_wp_select', array( __CLASS__ , 'add_notification_triggers'), 10, 2 );
	}

	/**
	 * Add notification trigger Woocommerce
	 */
	public static function add_notification_triggers ($field, $post) {
		if('wa_notifier_notification_trigger' != $field['id']) {
			return $field;
		}

		$field['options']['WooCommerce'] = self::get_woocommerce_triggers();
		return $field;
	}

	/**
	 * Get Woocommerce triggers
	 */
	public static function get_woocommerce_triggers () {
		$triggers = array (
			'new_order' 		=> 'New order',
			'cancelled_order' 	=> 'Cancelled order',
			'failed_order'		=> 'Failed order',
			'on_hold_order'		=> 'Order on-hold',
			'processing_order'	=> 'Processing order',
			'completed_order'	=> 'Completed order',
			'refunded_order'	=> 'Refunded order',
			'customer_note'		=> 'Customer Note'
		);
		return apply_filters('wa_notifier_woocommerce_triggers', $triggers);
	}

}
