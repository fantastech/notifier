<?php
/**
 * Notifications CPT Meta Box
 *
 * @package WA_Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post_id;

$notification_sent = get_post_meta ( $post_id, WA_NOTIFIER_PREFIX . 'notification_sent' , true);

if('yes' == $notification_sent) {
	$disabled = array (
		'disabled' => 'disabled'
	);
}
else {
	$disabled = array ();
}

?>
<div class="meta-fields">
	<div class="general-fields">
		 <div>
			<div>
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'notification_type',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_type', true),
						'label'             => 'Send notification...',
						'description'       => '',
						'options'           => array (
							''				=> 'Select type ',
							'transactional' => 'when a certain action is performed',
							'marketing' 	=> 'to a list (broadcast)',
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="form-fields-transactional">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'notification_trigger',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_trigger', true),
						'label'             => 'Trigger',
						'description'       => 'Select a trigger when you want to send notification.',
						'options'           => array (
							'' => 'Select a trigger',
							'new_post' => 'New post is published',
							'woocommerce_new_order' => 'New Woocommerce order is placed',
						),
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'notification_type',
								'operator'	=> '==',
								'value'		=> 'transactional'
							)
						),
						'custom_attributes' => $disabled
					)
				);
				do_action('wa_notifier_transactional_fields');
				?>
			</div>
			<div class="form-fields-marketing">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'notification_list',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_list', true),
						'label'             => 'Contact List',
						'description'       => 'Select the contact list you want to send this notification to. <a href="'. admin_url('edit-tags.php?taxonomy=wa_contact_list&post_type=wa_contact') .'">Click here</a> to create new list.',
						'options'			=> WA_Notifier_Contacts::get_contact_lists(true, true),
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'notification_type',
								'operator'	=> '==',
								'value'		=> 'marketing'
							)
						),
						'custom_attributes' => $disabled
					)
				);
				do_action('wa_notifier_marketing_fields');
				?>
			</div>
			<div class="form-fields-message-template">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'notification_message_template',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_message_template', true),
						'label'             => 'Message Template',
						'description'       => 'Select message template that you want to send.',
						'options'			=> WA_Notifier_Message_Templates::get_approved_message_templates(true),
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'notification_trigger',
								'operator'	=> '!=',
								'value'		=> ''
							),
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'notification_list',
								'operator'	=> '!=',
								'value'		=> ''
							)
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
