<?php
/**
 * Message Template CPT Meta Box
 *
 * @package WA_Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post_id;

$mt_status = get_post_meta ( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'status' , true);
$disable_states = array ('APPROVED', 'IN_APPEAL', 'PENDING', 'PENDING_DELETION', 'DELETED', 'DISABLED', 'LOCKED');

if(in_array($mt_status, $disable_states)) {
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
         <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'template_name',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'template_name', true),
                        'label'             => 'Unique template name',
                        'description'       => '',
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
            <div class="col">
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'category',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'category', true),
                        'label'             => 'Category',
                        'description'       => '',
                        'options'           => array (
                            'ACCOUNT_UPDATE' => 'Account Update',
                            'PAYMENT_UPDATE' => 'Payment Update',
                            'PERSONAL_FINANCE_UPDATE' => 'Personal Finance Update',
                            'SHIPPING_UPDATE' => 'Shipping Update',
                            'RESERVATION_UPDATE' => 'Reservation Update',
                            'ISSUE_RESOLUTION' => 'Issue Resolution',
                            'APPOINTMENT_UPDATE' => 'Appointment Update',
                            'TRANSPORTATION_UPDATE' => 'Transportation Update',
                            'TICKET_UPDATE' => 'Ticket Update',
                            'ALERT_UPDATE' => 'Alert Update',
                            'AUTO_REPLY' => 'Auto Reply',
                            'TRANSACTIONAL' => 'Transactional',
                            'MARKETING' => 'Marketing',
                            'OTP' => 'One Time Password (OTP)'
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
            <div class="col">
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'language',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'language', true),
                        'label'             => 'Language',
                        'description'       => '',
                        'options'           => array (
                            'en_US' => 'English',
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>
    </div>
    
    <hr />

    <div class="header-fields">
        <h3>Header <span class="optional-text">(Optional)</span></h3>
        <p class="description">Add a title or choose media type that you want to show in message header.</p>
        <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'header_type',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'header_type', true),
                        'label'             => 'Type',
                        'description'       => '',
                        'options'           => array (
                            'none' => 'None',
                            'text' => 'Text',
                            'media' => 'Media'
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
            <div class="col">
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'media_type',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'media_type', true),
                        'label'             => 'Media Type',
                        'description'       => '',
                        'options'           => array (
                            'image' => 'Image',
                            'video' => 'Video',
                            'document' => 'Document'
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>
        <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'media_url',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'media_url', true),
                        'label'             => 'Media URL',
                        'description'       => '',
                        'data_type'         => 'url',
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>
        <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'header_text',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'header_text', true),
                        'label'             => 'Header Text',
                        'description'       => '',
                        'limit'             => 60,
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>
    </div>

    <hr />

    <div class="body-fields">
        <h3>Body</h3>
        <p class="description">Enter the text for your message in the language you've selected.</p>
        <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_textarea_input(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'body_text',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'body_text', true),
                        'label'             => 'Body content',
                        'description'       => '',
                        'rows'              => 4,
                        'limit'             => 1024,
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>
    </div>

    <hr />

    <div class="footer-fields">
        <h3>Footer <span class="optional-text">(Optional)</span></h3>
        <p class="description">Add a short line of text to the bottom of your message template.</p>
        <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_text_input(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'footer_text',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'footer_text', true),
                        'label'             => 'Footer text',
                        'description'       => '',
                        'limit'             => 60,
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>
    </div>

    <hr />

    <div class="button-fields">
        <h3>Button <span class="optional-text">(Optional)</span></h3>
        <p class="description">Create up to 2 buttons that let customers respond to your message or take action.</p>
        <div class="d-flex">
            <div class="col">
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_type',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'media_type', true),
                        'label'             => 'Media Type',
                        'description'       => '',
                        'options'           => array (
                            'none' => 'None',
                            'cta' => 'Call to action',
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
            <div class="col">
                <?php
                wa_notifier_wp_radio(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_num',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_num', true) || '1',
                        'label'             => 'Number of buttons',
                        'description'       => '',
                        'options'           => array (
                            '1' => '1',
                            '2' => '2',
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
            </div>
        </div>

        <div class="d-flex">
            <div class="col button-1-wrap">
                <h4>Button 1</h4>
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_type',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_type', true) || 'visit',
                        'label'             => 'Type',
                        'description'       => '',
                        'options'           => array (
                            'visit' => 'Visit Website',
                            'call' => 'Call Phone Number',
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
                <div class="d-flex">
                    <div class="col" style="max-width: 40%;">
                    <?php
                        wa_notifier_wp_text_input(
                            array(
                                'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_text',
                                'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_text', true),
                                'label'             => 'Button Text',
                                'description'       => '',
                                'custom_attributes' => $disabled
                            )
                        );
                    ?>
                    </div>
                    <div class="col">
                    <?php
                        wa_notifier_wp_text_input(
                            array(
                                'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_url',
                                'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_url', true),
                                'label'             => 'Website URL',
                                'description'       => '',
                                'placeholder'       => 'http://',
                                'custom_attributes' => $disabled
                            )
                        );
                        wa_notifier_wp_text_input(
                            array(
                                'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_phone_num',
                                'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_1_phone_num', true),
                                'label'             => 'Phone Number',
                                'description'       => '',
                                'placeholder'       => 'Phone Number with Country Code',
                                'custom_attributes' => $disabled
                            )
                        );

                    ?>
                    </div>
                </div>
            </div>
            <div class="col button-2-wrap">
                <h4>Button 2</h4>
                <?php
                wa_notifier_wp_select(
                    array(
                        'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_type',
                        'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_type', true) || 'visit',
                        'label'             => 'Type',
                        'description'       => '',
                        'options'           => array (
                            'visit' => 'Visit Website',
                            'call' => 'Call Phone Number',
                        ),
                        'custom_attributes' => $disabled
                    )
                );
                ?>
                <div class="d-flex">
                    <div class="col" style="max-width: 40%;">
                    <?php
                        wa_notifier_wp_text_input(
                            array(
                                'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_text',
                                'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_text', true),
                                'label'             => 'Button Text',
                                'description'       => '',
                                'custom_attributes' => $disabled
                            )
                        );
                    ?>
                    </div>
                    <div class="col">
                    <?php
                        wa_notifier_wp_text_input(
                            array(
                                'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_url',
                                'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_url', true),
                                'label'             => 'Website URL',
                                'description'       => '',
                                'placeholder'       => 'http://',
                                'custom_attributes' => $disabled
                            )
                        );
                        wa_notifier_wp_text_input(
                            array(
                                'id'                => WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_phone_num',
                                'value'             => get_post_meta( $post_id, WA_NOTIFIER_SETTINGS_PREFIX . 'button_2_phone_num', true),
                                'label'             => 'Phone Number with Country Code',
                                'description'       => '',
                                'placeholder'       => 'Phone Number with Country Code',
                                'custom_attributes' => $disabled
                            )
                        );

                    ?>
                    </div>
                </div>

            </div>

        </div>
        
    </div>

</div>