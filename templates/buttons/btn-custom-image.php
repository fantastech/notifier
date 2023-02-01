<?php
/**
 * Button View: Click to Chat Custom image Button.
 *
 * @package Notifier
 */

$whatsapp_number = !empty(get_option('notifier_user_whatsapp_number')) ? get_option('notifier_user_whatsapp_number') : '';
$greeting_text = !empty(get_option('notifier_greeting_text')) ? get_option('notifier_greeting_text') : '';

$custom_chat_button_image  = !empty(get_option('notifier_custom_chat_button_image')) ? get_option('notifier_custom_chat_button_image'): '';

$url = 'https://wa.me/'.$whatsapp_number.'?text='.$greeting_text;

?>
<div class="wrap-click-to-chat-btn" id="click-to-chat-style-custom">
	<a href="<?php echo esc_url($url); ?>" target="_blank">
		<span> <img src="<?php echo $custom_chat_button_image; ?>" alt="Whatsapp"></span>
	</a>
</div>
