<?php
/**
 * Woocommerce notifications
 *
 * @package    Wa_Notifier
 */
class Notifier_Woocommerce {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'notifier_notification_triggers', array( __CLASS__ , 'add_notification_triggers'), 10 );
		add_filter( 'notifier_notification_send_to_types', array( __CLASS__ , 'add_notification_send_to_type'), 10, 3 );
		add_action( 'notifier_notification_after_send_to_reciever_fields', array( __CLASS__, 'send_to_customer_description' ), 10, 1 );
		/* ==Notifier_Pro_Code_Start== */
		add_filter( 'notifier_notification_merge_tags', array(__CLASS__, 'woocommerce_merge_tags') );
		/* ==Notifier_Pro_Code_End== */
	}

	/**
	 * Add Woocommerce notification triggers
	 */
	public static function get_woo_notification_triggers() {
		$merge_tag_types = array('WooCommerce', 'WooCommerce Order', 'WooCommerce Customer');
		$triggers = array (
			array(
				'id'			=> 'new_order',
				'label' 		=> 'New order is placed',
				'description'	=> 'When a new order is placed.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_new_order',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('new_order');
						if (empty($notif_ids)) {
							return;
						}

						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			),
			array(
				'id'			=> 'processing_order',
				'label' 		=> 'Order status changes to processing',
				'description'	=> 'When status of the order changes to Processing.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_order_status_processing',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('processing_order');
						if (empty($notif_ids)) {
							return;
						}

						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			),
			array(
				'id'			=> 'completed_order',
				'label' 		=> 'Order is completed',
				'description'	=> 'When status of the order changes to Completed.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_order_status_completed',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('completed_order');
						if (empty($notif_ids)) {
							return;
						}

						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			),
			array(
				'id'			=> 'cancelled_order',
				'label' 		=> 'Order is cancelled',
				'description'	=> 'When status of the order changes to Cancelled.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_order_status_cancelled',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('cancelled_order');
						if (empty($notif_ids)) {
							return;
						}
						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			),
			array(
				'id'			=> 'failed_order',
				'label' 		=> 'Order gets failed',
				'description'	=> 'When status of an order changes to Failed.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_order_status_failed',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('failed_order');
						if (empty($notif_ids)) {
							return;
						}
						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			),
			array(
				'id'			=> 'on_hold_order',
				'label' 		=> 'Order is on-hold',
				'description'	=> 'When status of an order changes to On-hold.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_order_status_on-hold',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('on_hold_order');
						if (empty($notif_ids)) {
							return;
						}
						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			),
			array(
				'id'			=> 'refunded_order',
				'label' 		=> 'Order is refunded',
				'description'	=> 'When status of an order changes to Refunded.',
				/* ==Notifier_Pro_Code_Start== */
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags($merge_tag_types),
				/* ==Notifier_Pro_Code_End== */
				'action'		=> array(
					'hook'		=> 'woocommerce_order_status_refunded',
					'callback' 	=> function ( $order_id ) {
						$notif_ids = Notifier_Notification_Triggers::trigger_has_active_notification('refunded_order');
						if (empty($notif_ids)) {
							return;
						}
						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'order',
								'object_id'		=> $order_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
					}
				)
			)
		);
		return $triggers;
	}

	/**
	 * Add notification trigger Woocommerce
	 */
	public static function add_notification_triggers ($triggers) {
		$triggers['WooCommerce'] = self::get_woo_notification_triggers();
		return $triggers;
	}

	/**
	 * Add notification send to type
	 */
	public static function add_notification_send_to_type ($send_to_types, $post_id, $trigger) {
		$woo_triggers = self::get_woo_notification_triggers();
		$woo_triggers = wp_list_pluck( $woo_triggers, 'id' );

		if (in_array($trigger, $woo_triggers)) {
			$send_to_types = array('customer' => 'WooCommerce Customer') + $send_to_types;
		}

		return $send_to_types;
	}
	/* ==Notifier_Pro_Code_Start== */
	/**
	 * Get Woocommerce merge tags
	 */
	public static function woocommerce_merge_tags($merge_tags) {
		$merge_tags['WooCommerce'] = array(
			array(
				'id' 			=> 'woo_store_url',
				'label' 		=> 'Shop page url',
				'preview_value' => 'https://example.com/shop/',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return get_permalink( wc_get_page_id( 'shop' ) );
				}
			),
			array(
				'id' 			=> 'woo_my_account_url',
				'label' 		=> 'My Account page url',
				'preview_value' => 'https://example.com/my-account/',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return get_permalink( wc_get_page_id( 'myaccount' ) );
				}
			)
		);

		$woo_fields = array ();
		$woo_fields['WooCommerce Order'] = array ( // Woocommerce order fields
			'id'					=> array(
				'preview'	=> '123',
				'label' 	=> 'Order ID'
			),
			'subtotal_to_display'	=> array(
				'preview'	=> '$50.00',
				'label' 	=> 'Order subtotal'
			),
			'coupon_codes'			=> array(
				'preview'	=> 'OFF50',
				'label' 	=> 'Order coupon codes',
				'value'		=> function ($order, $field_function) {
					return implode(',', $order->$field_function());
				}
			),
			'discount_to_display'	=> array(
				'preview'	=> '$5.00',
				'label' 	=> 'Order discount amount'
			),
			'shipping_method'		=> array(
				'preview'	=> 'Flat rate',
				'label' 	=> 'Order shipping method'
			),
			'shipping_total'		=> array(
				'preview'	=> '$2.50',
				'label' 	=> 'Order shipping amount',
				'value'		=> function ($order, $field_function) {
					return wc_price( $order->$field_function(), array( 'currency' => $order->get_currency() ) );
				}
			),
			'total_tax'				=> array(
				'preview'	=> '$5.00',
				'label' 	=> 'Order tax amount',
				'value'		=> function ($order, $field_function) {
					return wc_price( $order->$field_function(), array( 'currency' => $order->get_currency() ) );
				}
			),
			'formatted_order_total' => array(
				'preview'	=> '$50.00',
				'label' 	=> 'Order total amount'
			),
			'payment_method_title'	=> array(
				'preview'	=> 'Cash on delivery',
				'label' 	=> 'Order payment method'
			),
			'checkout_order_received_url'	=> array(
				'preview'	=> 'https://example.com/checkout/order-received/123/?key=wc_order_abcdef',
				'label' 	=> 'Order thank you page URL'
			),
			'view_order_url'				=> array(
				'preview'	=> 'https://example.com/my-account/view-order/123/',
				'label' 	=> 'Order view URL'
			),
			'status'			=>	array(
				'preview'	=> 'Processing',
				'label' 	=> 'Order status'
			),
			'first_product_image' => array(
				'preview'	=> '',
				'label' 	=> 'Order product image (first image)',
				'return_type'	=> 'media',
				'value'		=> function ($order, $field_function) {
					foreach($order->get_items() as $item){
						$first_product_id = $item->get_product_id();
						break;
					}
					$product = wc_get_product( $first_product_id );
	                return wp_get_attachment_url( $product->get_image_id() );
				}
			),
		);

		$woo_fields['WooCommerce Customer'] = array (
			'billing_first_name'	=> array('preview'	=> 'John'),
			'billing_last_name'		=> array('preview'	=> 'Doe'),
			'billing_company'		=> array('preview'	=> 'ABC Inc'),
			'billing_address_1'		=> array('preview'	=> '123, XYZ Street'),
			'billing_address_2'		=> array('preview'	=> 'XYZ Avenue'),
			'billing_city'			=> array('preview'	=> 'New York'),
			'billing_postcode'		=> array('preview'	=> '12345'),
			'billing_state'			=> array(
				'preview'	=> 'NY',
				'value'		=> function ($order, $field_function) {
					$country_function = str_replace('state', 'country', $field_function);
					$state_code = $order->$field_function();
					$country_code = $order->$country_function();
					return WC()->countries->states[$country_code][$state_code];
				}
			),
			'billing_country'		=> array(
				'preview'	=> 'US',
				'value'		=> function ($order, $field_function) {
					return WC()->countries->countries[$order->$field_function()];
				}
			),
			'billing_email'			=> array('preview'	=> 'john@example.com'),
			'billing_phone'			=> array('preview'	=> '987 654 321'),
			'formatted_billing_address' 	=> array(
				'preview'	=> '',
				'label'		=> 'Complete billing address',
				'value'		=> function ($order, $field_function) {
					return str_replace('<br/>', ', ', $order->$field_function());
				}
			),
			'shipping_first_name'	=> array('preview'	=> 'John'),
			'shipping_last_name'	=> array('preview'	=> 'Doe'),
			'shipping_company'		=> array('preview'	=> 'ABC Inc'),
			'shipping_address_1'	=> array('preview'	=> '123, XYZ Street'),
			'shipping_address_2'	=> array('preview'	=> 'XYZ Avenue'),
			'shipping_city'			=> array('preview'	=> 'New York'),
			'shipping_postcode'		=> array('preview'	=> '12345'),
			'shipping_state'		=> array(
				'preview'	=> 'NY',
				'value'		=> function ($order, $field_function) {
					$country_function = str_replace('state', 'country', $field_function);
					$state_code = $order->$field_function();
					$country_code = $order->$country_function();
					return WC()->countries->states[$country_code][$state_code];
				}
			),
			'shipping_country'		=> array(
				'preview'	=> 'US',
				'value'		=> '',
				'value'		=> function ($order, $field_function) {
					return WC()->countries->countries[$order->$field_function()];
				}
			),
			'formatted_shipping_address'	=> array(
				'preview'	=> '',
				'label'		=> 'Complete shipping address',
				'value'		=> function ($order, $field_function) {
					return str_replace('<br/>', ', ', $order->$field_function());
				}
			),

		);

		foreach ($woo_fields as $woo_field_key => $woo_field) {
			foreach ($woo_field as $field => $field_data) {
				if (isset($field_data['label'])) {
					$label = $field_data['label'];
				} else {
					$label = ucfirst( str_replace('_', ' ', $field) );
				}

				$merge_tags[$woo_field_key][] = array(
					'id' 			=> 'woo_order_' . $field,
					'label' 		=> $label,
					'preview_value' => isset($field_data['preview']) ? $field_data['preview'] : '',
					'return_type'	=> isset($field_data['return_type']) ? $field_data['return_type'] : 'text',
					'value'			=> function ($args) use ($field, $field_data) {
						$order = wc_get_order( $args['object_id'] );
						$field_function = 'get_' . $field;
						if (isset($field_data['value'])) {
							$value = $field_data['value']($order, $field_function);
						} else {
							$value = $order->$field_function();
						}
						return html_entity_decode(sanitize_text_field($value));
					}
				);
			}
		}

		return $merge_tags;
	}
	/* ==Notifier_Pro_Code_End== */

	/**
	 * Add the description text under Reciever
	 * column for customer Send to field
	 */
	public static function send_to_customer_description($num) {
		$conditional_logic = array (
			array (
				'field'		=> NOTIFIER_PREFIX . 'notification_send_to_' . $num . '_type',
				'operator'	=> '==',
				'value'		=> 'customer'
			)
		);
		echo '<div class="form-field" data-conditions="' . esc_attr ( json_encode( $conditional_logic ) ) . '">Notification will be sent to customer\'s <b>billing phone number</b>, if they set it during checkout.</div>';
	}
}