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

$mt_status = get_post_meta ( $post_id, WA_NOTIFIER_PREFIX . 'status' , true);
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
						'id'                => WA_NOTIFIER_PREFIX . 'template_name',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'template_name', true),
						'label'             => 'Template name',
						'description'       => 'Spaces or special characters are not allowed.',
						'placeholder'       => 'template_name',
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="col">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'category',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'category', true),
						'label'             => 'Category',
						'description'       => '',
						'options'           => array (
							'MARKETING' => 'Marketing',
							'TRANSACTIONAL' => 'Transactional',
							//'OTP' => 'One Time Password (OTP)'
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
						'id'                => WA_NOTIFIER_PREFIX . 'language',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'language', true),
						'label'             => 'Language',
						'description'       => 'Select template language (More languages will be added in future plugin updates).',
						'options'           => array (
							'en_US' => 'English (en_US)',
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
		<p class="description">Add a title that you want to show in message header.</p>
		<div class="d-flex">
			<div class="col w-50">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'header_type',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'header_type', true),
						'label'             => 'Header Type',
						'description'       => 'Select the header type.',
						'options'           => array (
							'none' => 'None',
							'text' => 'Text',
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="col w-50">
				<?php
				wa_notifier_wp_text_input(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'header_text',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'header_text', true),
						'label'             => 'Header Text',
						'description'       => 'Enter header text.',
						'limit'             => 60,
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'header_type',
								'operator'	=> '==',
								'value'		=> 'text'
							)
						)
					)
				);
				?>
			</div>
			<div class="col w-50 hide">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'media_type',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'media_type', true),
						'label'             => 'Media Type',
						'description'       => '',
						'options'           => array (
							'IMAGE' => 'Image',
							'VIDEO' => 'Video',
							'DOCUMENT' => 'Document'
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="col w-50 hide">
				<?php
				wa_notifier_wp_text_input(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'media_url',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'media_url', true),
						'label'             => 'Media URL',
						'description'       => '',
						'data_type'         => 'url',
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
						'id'                => WA_NOTIFIER_PREFIX . 'body_text',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'body_text', true),
						'label'             => 'Body content',
						'description'       => 'Enter body content. You can format the text using <a href="https://faq.whatsapp.com/general/chats/how-to-format-your-messages/?lang=en" target="_blank">WhatsApp\'s formatting options</a>. HTML not allowed.',
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
						'id'                => WA_NOTIFIER_PREFIX . 'footer_text',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'footer_text', true),
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
						'id'                => WA_NOTIFIER_PREFIX . 'button_type',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_type', true),
						'label'             => 'Button Type',
						'description'       => '',
						'options'           => array (
							'none' => 'None',
							'cta' => 'Call to action'
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="col">
				<?php
				$button_num = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_num', true);
				$button_num = ($button_num) ? $button_num : '1';
				wa_notifier_wp_radio(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'button_num',
						'value'             => $button_num,
						'label'             => 'Number of buttons',
						'description'       => '',
						'options'           => array (
							'1' => '1',
							'2' => '2',
						),
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'button_type',
								'operator'	=> '==',
								'value'		=> 'cta'
							)
						)
					)
				);
				?>
			</div>
		</div>

		<div class="d-flex">
			<div class="col w-50 button-1-wrap">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'button_1_type',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_1_type', true),
						'label'             => 'Button 1 Type',
						'description'       => '',
						'options'           => array (
							'URL' => 'Visit Website',
							'PHONE_NUMBER' => 'Call Phone Number'
						),
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'button_num',
								'operator'	=> '==',
								'value'		=> '1'
							),
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'button_num',
								'operator'	=> '==',
								'value'		=> '2'
							)
						)
					)
				);
				?>
				<div class="d-flex">
					<div class="col" style="max-width: 40%;">
					<?php
						wa_notifier_wp_text_input(
							array(
								'id'                => WA_NOTIFIER_PREFIX . 'button_1_text',
								'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_1_text', true),
								'label'             => 'Button Text',
								'description'       => '',
								'limit'             => 25,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_num',
										'operator'	=> '==',
										'value'		=> '1'
									),
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_num',
										'operator'	=> '==',
										'value'		=> '2'
									)
								)
							)
						);
					?>
					</div>
					<div class="col">
					<?php
						wa_notifier_wp_text_input(
							array(
								'id'                => WA_NOTIFIER_PREFIX . 'button_1_url',
								'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_1_url', true),
								'label'             => 'Website URL',
								'description'       => '',
								'placeholder'       => 'http://',
								'limit'             => 2000,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_1_type',
										'operator'	=> '==',
										'value'		=> 'URL'
									)
								)
							)
						);
						wa_notifier_wp_text_input(
							array(
								'id'                => WA_NOTIFIER_PREFIX . 'button_1_phone_num',
								'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_1_phone_num', true),
								'label'             => 'Phone Number',
								'description'       => '',
								'placeholder'       => 'Phone Number with Country Code',
								'limit'             => 20,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_1_type',
										'operator'	=> '==',
										'value'		=> 'PHONE_NUMBER'
									)
								)
							)
						);

					?>
					</div>
				</div>
			</div>
			<div class="col w-50 button-2-wrap">
				<?php
				wa_notifier_wp_select(
					array(
						'id'                => WA_NOTIFIER_PREFIX . 'button_2_type',
						'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_2_type', true),
						'label'             => 'Button 2 Type',
						'description'       => '',
						'options'           => array (
							'URL' => 'Visit Website',
							'PHONE_NUMBER' => 'Call Phone Number',
						),
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> WA_NOTIFIER_PREFIX . 'button_num',
								'operator'	=> '==',
								'value'		=> '2'
							)
						)
					)
				);
				?>
				<div class="d-flex">
					<div class="col" style="max-width: 40%;">
					<?php
						wa_notifier_wp_text_input(
							array(
								'id'                => WA_NOTIFIER_PREFIX . 'button_2_text',
								'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_2_text', true),
								'label'             => 'Button Text',
								'description'       => '',
								'limit'             => 25,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_num',
										'operator'	=> '==',
										'value'		=> '2'
									)
								)
							)
						);
					?>
					</div>
					<div class="col">
					<?php
						wa_notifier_wp_text_input(
							array(
								'id'                => WA_NOTIFIER_PREFIX . 'button_2_url',
								'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_2_url', true),
								'label'             => 'Website URL',
								'description'       => '',
								'placeholder'       => 'http://',
								'limit'             => 2000,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_2_type',
										'operator'	=> '==',
										'value'		=> 'URL'
									)
								)
							)
						);
						wa_notifier_wp_text_input(
							array(
								'id'                => WA_NOTIFIER_PREFIX . 'button_2_phone_num',
								'value'             => get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'button_2_phone_num', true),
								'label'             => 'Phone Number',
								'description'       => '',
								'placeholder'       => 'Phone Number with Country Code',
								'limit'             => 20,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> WA_NOTIFIER_PREFIX . 'button_2_type',
										'operator'	=> '==',
										'value'		=> 'PHONE_NUMBER'
									)
								)
							)
						);

					?>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>
