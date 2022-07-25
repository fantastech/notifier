<?php
/**
 * Notifications CPT Meta Box
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post_id;

$notification_status = get_post_meta ( $post_id, NOTIFIER_PREFIX . 'notification_status' , true);
if(in_array($notification_status, array('Sending', 'Sent', 'Scheduled'))) {
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
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_type',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_type', true),
						'label'             => 'Notification type',
						'description'       => 'Select the type of notification you want to send.',
						'options'           => array (
							''				=> 'Select notification type ',
							'marketing' 	=> 'Marketing (send bulk broadcast messages to a contact list)',
							'transactional' => 'Transactional (send notification when a certain action is triggered)',
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="form-fields-transactional">
				<?php
					notifier_wp_select(
						array(
							'id'                => NOTIFIER_PREFIX . 'notification_trigger',
							'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_trigger', true),
							'label'             => 'Trigger',
							'description'       => 'Select a trigger when you want to send notification. You can request more triggers by <a href="mailto:ram@fantastech.co?subject=%5BWA%20Notifier%5D%20New%20Trigger%20Request" target="_blank">mailing us</a>.',
							'options'           => Notifier_Notification_Triggers::get_notification_triggers_dropdown(),
							'conditional_logic'		=> array (
								array (
									'field'		=> NOTIFIER_PREFIX . 'notification_type',
									'operator'	=> '==',
									'value'		=> 'transactional'
								)
							),
							'custom_attributes' => $disabled
						)
					);

					do_action('notifier_transactional_fields');

					$send_to_conditions = array (
						array (
							'field'		=> NOTIFIER_PREFIX . 'notification_trigger',
							'operator'	=> '!=',
							'value'		=> ''
						)
					);
				?>
				<div class="form-field send-to-fields" data-conditions="<?php echo esc_attr ( json_encode( $send_to_conditions ) ); ?>">

				</div>
			</div>
			<div class="form-fields-marketing">
				<?php
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_list',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_list', true),
						'label'             => 'Contact List',
						'description'       => 'Select the contact list you want to send this notification to. <a href="'. admin_url('edit-tags.php?taxonomy=wa_contact_list&post_type=wa_contact') .'">Click here</a> to create new list.',
						'options'			=> Notifier_Contacts::get_contact_lists(true, true),
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'notification_type',
								'operator'	=> '==',
								'value'		=> 'marketing'
							)
						),
						'custom_attributes' => $disabled
					)
				);
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_when',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_when', true),
						'label'             => 'Send this when?',
						'description'       => 'Select when you want to send this notification.',
						'options'			=> array(
							'now'	=> 'Send it now',
							'later'	=> 'Schedule for later',
						),
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'notification_list',
								'operator'	=> '!=',
								'value'		=> ''
							)
						),
						'custom_attributes' => $disabled
					)
				);
				notifier_wp_text_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_datetime',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_datetime', true),
						'label'             => 'Date and time',
						'description'       => 'Select the date and time when you want to send the notification. Notification will be sent as per your system\'s date and time. You can check / update your system date and time settings <a href="options-general.php" target="_blank">here</a>.',
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'notification_when',
								'operator'	=> '==',
								'value'		=> 'later'
							)
						),
						'custom_attributes' => $disabled
					)
				);
				do_action('notifier_marketing_fields');
				?>
			</div>
			<div class="form-fields-message-template">
				<?php
				$message_template_id = get_post_meta( $post_id, NOTIFIER_PREFIX . 'notification_message_template', true);
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'notification_message_template',
						'value'             => $message_template_id,
						'label'             => 'Message Template',
						'description'       => 'Select message template that you want to send.',
						'options'			=> Notifier_Message_Templates::get_approved_message_templates(true),
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'notification_trigger',
								'operator'	=> '!=',
								'value'		=> ''
							),
							array (
								'field'		=> NOTIFIER_PREFIX . 'notification_list',
								'operator'	=> '!=',
								'value'		=> ''
							)
						),
						'custom_attributes' => $disabled
					)
				);
				$variables_mapping_field_conditions = array (
					array (
						'field'		=> NOTIFIER_PREFIX . 'notification_message_template',
						'operator'	=> '!=',
						'value'		=> ''
					)
				);
				?>
				<div class="form-field variables-mapping-fields" data-conditions="<?php echo esc_attr ( json_encode( $variables_mapping_field_conditions ) ); ?>">

				</div>
			</div>
		</div>
	</div>
</div>
