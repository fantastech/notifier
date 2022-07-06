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

$notification_status = get_post_meta ( $post_id, WA_NOTIFIER_PREFIX . 'notification_status' , true);
$statuses = WA_Notifier_Notifications::get_notification_statuses();
if(in_array($notification_status, $statuses)) {
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
							'transactional' => 'when a certain action is triggered',
							'marketing' 	=> 'to a list (marketing broadcast)',
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
						'label'             => 'Send it when...',
						'description'       => 'Select a trigger when you want to send notification. You can request more triggers by <a href="mailto:ram@fantastech.co?subject=%5BWA%20Notifier%5D%20New%20Trigger%20Request" target="_blank">mailing us</a>.',
						'options'           => array (
							'' => 'Select a trigger',
							'WordPress' => array (
								'new_post' => 'A new post is published',
								'new_user_registration' => 'A new user is registered',
							)
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
				?>
				<?php
					$send_to_conditions = array (
						array (
							'field'		=> WA_NOTIFIER_PREFIX . 'notification_trigger',
							'operator'	=> '!=',
							'value'		=> ''
						)
					);
				?>
				<div class="form-field send-to-fields" data-conditions="<?php echo esc_attr ( json_encode( $send_to_conditions ) ); ?>">
					<?php
						$send_to = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_send_to', true);
					?>
					<label>Send to...</label>
					<table class="fields-repeater">
						<tbody>
							<tr>
								<th>Type</th>
								<th>Recipient</th>
								<th></th>
							</tr>
							<?php
								if($send_to && is_array($send_to)) {
									foreach ($send_to as $recipient) {
										echo WA_Notifier_Notifications::get_notification_send_to_fields_row($row, $recipient);
									}
								}
								else {
									echo WA_Notifier_Notifications::get_notification_send_to_fields_row();
								}
							?>
							<tr class="row">
								<td>
									<select class="wa_notifier_notification_sent_to[0][type]" id="wa_notifier_notification_sent_to_0_type">
										<option value="contact">Contact</option>
										<option value="list">List</option>
										<option value="user">User / Customer</option>
									</select>
									<span class="description">Select the type of recipient.</span>
								</td>
								<td>
									<div class="wa_notifier_notification_sent_to_0_recipient_field">
										<input type="text" name="wa_notifier_notification_sent_to[0][recipient]" id="wa_notifier_notification_sent_to_0_recipient">
										<span class="description">Enter comma separated recipient numbers with country code. E.g. +919876543210,+918765432109</span>
									</div>
								</td>
								<td class="delete-repeater-field">

								</td>
							</tr>
						</tbody>
					</table>
					<div class="d-flex justify-content-end">
						<a href="" class="button add-recipient">Add recipient</a>
					</div>
				</div>
				<?php
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
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'notification_when',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_when', true),
						'label'             => 'Send this when?',
						'description'       => 'Select when you want to send this notification.',
						'options'			=> array(
							'now'	=> 'Send it now',
							'later'	=> 'Schedule for later',
						),
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'notification_list',
								'operator'	=> '!=',
								'value'		=> ''
							)
						),
						'custom_attributes' => $disabled
					)
				);
				wa_notifier_wp_text_input(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'notification_datetime',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_datetime', true),
						'label'             => 'Date and time',
						'description'       => 'Select the date and time when you want to send the notification. Notification will be sent as per your system\'s date and time. You can check / update your system date and time settings <a href="options-general.php" target="_blank">here</a>.',
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'notification_when',
								'operator'	=> '==',
								'value'		=> 'later'
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
