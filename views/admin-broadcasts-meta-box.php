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
                        'id'                => WA_NOTIFIER_PREFIX . 'broadcast_message_template',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'seleted_message_template', true),
                        'label'             => 'First Name',
                        'description'       => ''
                    )
                );
                ?>
            </div>
            <div class="col w-50">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'broadcast_action',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'broadcast_action', true),
                        'label'             => 'Last Name',
                        'description'       => ''
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>