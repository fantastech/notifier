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
            <div class="col w-50">
         		<?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_PREFIX . 'association_user',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'association_user', true),
                        'label'             => 'Associated User',
                        'description'       => 'Attach a user with this contact. Associated contacts are useful for fetching additonal fields during transactional notifications.',
                        'options'           => WA_Notifier_Contacts::get_website_users_list(true)
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
