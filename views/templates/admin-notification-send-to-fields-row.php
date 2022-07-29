<?php
/**
 * Admin side template to load Notification Send To fields row
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$num = isset($num) ? $num : 'row_num';
$recipient_type = isset($data['type']) ? $data['type'] : '';
$recipient = isset($data['recipient']) ? $data['recipient'] : array();
$recipient = wp_parse_args ($recipient, array (
	'contact'	=> '',
	'list'		=> ''
));
$send_to_types = array (
	'contact'	=> 'Contact',
	// 'list'		=> 'Contact List', // TODO: implement this on a later stage
);
$send_to_types = apply_filters( NOTIFIER_PREFIX . 'notification_send_to_types', $send_to_types, $post_id, $trigger );

?>
<tr class="row">
	<td>
		<?php
			notifier_wp_select(
				array(
					'id'                => NOTIFIER_PREFIX . 'notification_send_to_' . $num . '_type',
					'name'              => NOTIFIER_PREFIX . 'notification_send_to[' . $num . '][type]',
					'value'             => $recipient_type,
					'label'             => '',
					'description'       => 'Select recipient type',
					'options'           => $send_to_types,
					'custom_attributes' => $disabled
				)
			);
			?>
	</td>
	<td>
		<?php
			$contacts_array = array();
		if ('' != $recipient['contact']) {
			$contact = get_post($recipient['contact']);
			if (!empty($contact)) {
				$first_name = get_post_meta( $contact->ID, NOTIFIER_PREFIX . 'first_name', true);
				$last_name = get_post_meta( $contact->ID, NOTIFIER_PREFIX . 'last_name', true);
				$wa_number = get_post_meta( $contact->ID, NOTIFIER_PREFIX . 'wa_number', true);
				$contacts_array[$contact->ID] = $first_name . ' ' . $last_name . ' (' . $wa_number . ')';
			}
		}
			do_action('notifier_notification_before_send_to_reciever_fields', $num);
			notifier_wp_select(
				array(
					'id'                => NOTIFIER_PREFIX . 'notification_send_to_' . $num . '_recipient_contact',
					'name'              => NOTIFIER_PREFIX . 'notification_send_to[' . $num . '][recipient][contact]',
					'class'				=> 'notifier-recipient notifier-recipient-contact',
					'value'             => $recipient['contact'],
					'label'             => '',
					'description'       => 'Select one of your <a href="' . admin_url('edit.php?post_type=wa_contact') . '">Contacts</a>',
					'options'           => $contacts_array,
					'conditional_logic'	=> array (
						array (
							'field'		=> NOTIFIER_PREFIX . 'notification_send_to_' . $num . '_type',
							'operator'	=> '==',
							'value'		=> 'contact'
						)
					),
					'custom_attributes' => $disabled
				)
			);
			notifier_wp_select(
				array(
					'id'                => NOTIFIER_PREFIX . 'notification_send_to_' . $num . '_recipient_list',
					'name'              => NOTIFIER_PREFIX . 'notification_send_to[' . $num . '][recipient][list]',
					'class'				=> 'notifier-recipient notifier-recipient-list',
					'value'             => $recipient['list'],
					'label'             => '',
					'description'       => 'Select one of your Contact <a href="' . admin_url('edit-tags.php?taxonomy=wa_contact_list&post_type=wa_contact') . '">Lists</a>',
					'options'           => Notifier_Contacts::get_contact_lists(true, true),
					'conditional_logic'	=> array (
						array (
							'field'		=> NOTIFIER_PREFIX . 'notification_send_to_' . $num . '_type',
							'operator'	=> '==',
							'value'		=> 'list'
						)
					),
					'custom_attributes' => $disabled
				)
			);
			do_action('notifier_notification_after_send_to_reciever_fields', $num);
			?>
	</td>
	<td class="delete-repeater-field">
		<?php if (0 !== $num) : ?>
			<span class="dashicons dashicons-trash"></span>
		<?php endif; ?>
	</td>
</tr>
