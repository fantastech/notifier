<?php
/**
 * Broadcasts CPT Meta Box
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
         <div class="d-flex">
            <div class="col w-50">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'notification_message_template',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_message_template', true),
                        'label'             => 'Message Template',
                        'description'       => ''
                    )
                );
                ?>
            </div>
            <div class="col w-50">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'notification_action',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'notification_action', true),
                        'label'             => 'Action',
                        'description'       => ''
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
