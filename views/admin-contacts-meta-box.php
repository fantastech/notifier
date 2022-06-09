<?php
/**
 * Contact CPT Meta Box
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
                        'id'                => WA_NOTIFIER_PREFIX . 'first_name',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'first_name', true),
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
                        'id'                => WA_NOTIFIER_PREFIX . 'last_name',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'last_name', true),
                        'label'             => 'Last Name',
                        'description'       => ''
                    )
                );
                ?>
            </div>
            <div class="col w-50">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'wa_number',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'wa_number', true),
                        'label'             => 'Whatsapp Number',
                        'description'       => '',
                        'placeholder'       => 'WhatsApp number with country code'
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>