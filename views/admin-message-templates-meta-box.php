<?php
/**
 * Message Template CPT Meta Box
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post_id;

$mt_status = get_post_meta ( $post_id, NOTIFIER_PREFIX . 'status', true);
$disable_states = array ('APPROVED', 'IN_APPEAL', 'PENDING', 'PENDING_DELETION', 'DELETED', 'DISABLED', 'LOCKED');

if (in_array($mt_status, $disable_states)) {
	$disabled = array (
		'disabled' => 'disabled'
	);
} else {
	$disabled = array ();
}
?>
<div class="meta-fields">
	<div class="general-fields">
		 <div class="d-flex">
			<div class="col">
				<?php
				notifier_wp_text_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'template_name',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'template_name', true),
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
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'category',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'category', true),
						'label'             => 'Category',
						'description'       => '',
						'options'           => array (
							'MARKETING' => 'Marketing',
							'TRANSACTIONAL' => 'Transactional',
							// 'OTP' => 'One Time Password (OTP)'
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="col">
				<?php
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'language',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'language', true),
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
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'header_type',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'header_type', true),
						'label'             => 'Header Type',
						'description'       => 'Select the header type.',
						'options'           => array (
							'none' 	=> 'None',
							'text' 	=> 'Text',
							/* ==Notifier_Pro_Code_Start== */
							'media'	=> 'Media'
							/* ==Notifier_Pro_Code_End== */
						),
						'custom_attributes' => $disabled
					)
				);
				?>
			</div>
			<div class="col w-50">
				<?php
				notifier_wp_text_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'header_text',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'header_text', true),
						'label'             => 'Header Text',
						'description'       => 'Enter header text.',
						'limit'             => 60,
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'header_type',
								'operator'	=> '==',
								'value'		=> 'text'
							)
						)
					)
				);
				/* ==Notifier_Pro_Code_Start== */
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'media_type',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'media_type', true),
						'label'             => 'Media Type',
						'description'       => '',
						'options'           => array (
							'IMAGE' => 'Image',
							'VIDEO' => 'Video',
							'DOCUMENT' => 'Document'
						),
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'header_type',
								'operator'	=> '==',
								'value'		=> 'media'
							)
						)
					)
				);
				/* ==Notifier_Pro_Code_End== */
				?>
			</div>
			<!-- ==Notifier_Pro_Code_Start== -->
			<div class="col w-50">
				<?php
				notifier_wp_file_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'media_item_image',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'media_item_image', true),
						'label'             => 'Upload example image',
						'description'       => 'Provide an example image for WhatsApp to check if it meets their guidelines. Supported formats: JPEG and PNG.',
						'custom_attributes' => $disabled,
						'uploader_title'	=> 'Upload Image',
						'uploader_button_text'	=> 'Select',
						'uploader_supported_file_types' => 'image/jpeg',
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'media_type',
								'operator'	=> '==',
								'value'		=> 'IMAGE'
							)
						)
					)
				);

				notifier_wp_file_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'media_item_video',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'media_item_video', true),
						'label'             => 'Upload example video',
						'description'       => 'Provide an example video for WhatsApp to check if it meets their guidelines. Supported format: MP4',
						'custom_attributes' => $disabled,
						'uploader_title'	=> 'Upload Video',
						'uploader_button_text'	=> 'Select',
						'uploader_supported_file_types' => 'video/mp4',
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'media_type',
								'operator'	=> '==',
								'value'		=> 'VIDEO'
							)
						)
					)
				);

				notifier_wp_file_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'media_item_document',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'media_item_document', true),
						'label'             => 'Upload example document',
						'description'       => 'Provide an example document for WhatsApp to check if it meets their guidelines. Supported format: PDF',
						'custom_attributes' => $disabled,
						'uploader_title'	=> 'Upload Document',
						'uploader_button_text'	=> 'Select',
						'uploader_supported_file_types' => 'application/pdf',
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'media_type',
								'operator'	=> '==',
								'value'		=> 'DOCUMENT'
							)
						)
					)
				);
				?>
			</div>
			<!-- ==Notifier_Pro_Code_End== -->
		</div>
	</div>

	<hr />

	<div class="body-fields">
		<h3>Body</h3>
		<p class="description">Enter the text for your message in the language you've selected.</p>
		<div class="d-flex">
			<div class="col">
				<?php
				notifier_wp_textarea_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'body_text',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'body_text', true),
						'label'             => 'Body content',
						'description'       => 'Enter body content. HTML not allowed. You can format the text using following shorthands:<br><br>Bold: *text* will become <b>text</b><br>Italics: _text_ will become <em>text</em><br>Strikethrough: ~text~ will become <s>text</s><br>Monospace or code: ```text``` will become <code style="background-color: transparent;">text</code>',
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
				notifier_wp_text_input(
					array(
						'id'                => NOTIFIER_PREFIX . 'footer_text',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'footer_text', true),
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
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'button_type',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_type', true),
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
				$button_num = get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_num', true);
				$button_num = ($button_num) ? $button_num : '1';
				notifier_wp_radio(
					array(
						'id'                => NOTIFIER_PREFIX . 'button_num',
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
								'field'		=> NOTIFIER_PREFIX . 'button_type',
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
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'button_1_type',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_1_type', true),
						'label'             => 'Button 1 Type',
						'description'       => '',
						'options'           => array (
							'URL' => 'Visit Website',
							'PHONE_NUMBER' => 'Call Phone Number'
						),
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'button_num',
								'operator'	=> '==',
								'value'		=> '1'
							),
							array (
								'field'		=> NOTIFIER_PREFIX . 'button_num',
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
						notifier_wp_text_input(
							array(
								'id'                => NOTIFIER_PREFIX . 'button_1_text',
								'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_1_text', true),
								'label'             => 'Button Text',
								'description'       => '',
								'limit'             => 25,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_num',
										'operator'	=> '==',
										'value'		=> '1'
									),
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_num',
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
						notifier_wp_text_input(
							array(
								'id'                => NOTIFIER_PREFIX . 'button_1_url',
								'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_1_url', true),
								'label'             => 'Website URL',
								'description'       => '',
								'placeholder'       => 'http://',
								'limit'             => 2000,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_1_type',
										'operator'	=> '==',
										'value'		=> 'URL'
									)
								)
							)
						);
						notifier_wp_text_input(
							array(
								'id'                => NOTIFIER_PREFIX . 'button_1_phone_num',
								'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_1_phone_num', true),
								'label'             => 'Phone Number',
								'description'       => '',
								'placeholder'       => 'Phone Number with Country Code',
								'limit'             => 20,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_1_type',
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
				notifier_wp_select(
					array(
						'id'                => NOTIFIER_PREFIX . 'button_2_type',
						'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_2_type', true),
						'label'             => 'Button 2 Type',
						'description'       => '',
						'options'           => array (
							'URL' => 'Visit Website',
							'PHONE_NUMBER' => 'Call Phone Number',
						),
						'custom_attributes' => $disabled,
						'conditional_logic'		=> array (
							array (
								'field'		=> NOTIFIER_PREFIX . 'button_num',
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
						notifier_wp_text_input(
							array(
								'id'                => NOTIFIER_PREFIX . 'button_2_text',
								'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_2_text', true),
								'label'             => 'Button Text',
								'description'       => '',
								'limit'             => 25,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_num',
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
						notifier_wp_text_input(
							array(
								'id'                => NOTIFIER_PREFIX . 'button_2_url',
								'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_2_url', true),
								'label'             => 'Website URL',
								'description'       => '',
								'placeholder'       => 'http://',
								'limit'             => 2000,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_2_type',
										'operator'	=> '==',
										'value'		=> 'URL'
									)
								)
							)
						);
						notifier_wp_text_input(
							array(
								'id'                => NOTIFIER_PREFIX . 'button_2_phone_num',
								'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'button_2_phone_num', true),
								'label'             => 'Phone Number',
								'description'       => '',
								'placeholder'       => 'Phone Number with Country Code',
								'limit'             => 20,
								'custom_attributes' => $disabled,
								'conditional_logic'		=> array (
									array (
										'field'		=> NOTIFIER_PREFIX . 'button_2_type',
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
