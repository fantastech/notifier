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
                        'label'             => 'Notification Type',
                        'description'       => 'Select <b>Transactional</b> if want to send notification to individual contacts / users on specific action triggers (e.g. during registration, form fill-up, order confirmation etc). Select <b>Marketing</b> if you want to send a bulk notification messages to contacts in your lists.',
                        'options'           => array (
                            'transactional' => 'Transactional (Action based)',
                            'marketing' => 'Marketing (Broadcast)',
                        )
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
                        'description'       => 'Select a trigger when you want to send notification',
                        'options'           => array (
                            '' => 'Select a trigger',
                            'user_registration' => 'User registration',
                            'marketing' => 'New order',
                        )
                    )
                );
                ?>
            </div>
            <div class="form-fields-marketing">
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'notification_list',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_list', true),
                        'label'             => 'Contact List',
                        'description'       => '',
                        'options'			=> WA_Notifier_Contacts::get_contact_lists(true, true)
                    )
                );
                ?>
            </div>
            <div class="form-fields-message-template">
            	<?php
            	wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'notification_message_template',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_message_template', true),
                        'label'             => 'Message Template',
                        'description'       => '',
                        'options'			=> WA_Notifier_Message_Templates::get_approved_message_templates(true)
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
