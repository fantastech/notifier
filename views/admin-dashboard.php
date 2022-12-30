<?php
/**
 * Admin View: Dashboard
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$api_key = get_option(NOTIFIER_PREFIX . 'api_key');
?>
<div class="wrap notifier">
	<div class="notifier-wrapper">
		<?php if (!Notifier::is_api_active()) : ?>
			<div class="onboarding">
				<div class="onboarding-head">
					<h3>Let's Get Started</h3>
				</div>
				<div class="onboarding-body">
					<p><a href="https://wanotifier.com/" target="_blank">WA Notifier</a> is a SaaS tool that allows you to send bulk WhatsApp marketing and transactional messages to your customers using the <b>WhatsApp's official Cloud APIs</b>.</p>
					<p>This plugin is an extension to our SaaS platform that allows you to <b>trigger WhatsApp message notifications</b> from your WordPress website.</p>
					<p>To use this plugin you'll need to <b>create an account with us</b> and connect it with this plugin.</p>
					<p>Following are the instructions to do the setup.</p>
					<ol>
						<li>Create a <b>FREE account</b> on WANotifier.com by <a href="https://app.wanotifier.com/create-account/" target="_blank">clicking here</a>.</li>
						<li>After you create your account, go through the on-boarding steps to the setup up of your <b>test WhatsApp Cloud API account</b>.</li>
						<li>Once the test account is ready, follow further instructions on our website to set up <b>your own phone number</b> with the Cloud API (and get out of <b>Test Mode</b>).</li>
						<li>After that is done, go to the <a href="https://app.wanotifier.com/settings/api/" target="_blank">Settings > API</a> and scroll to the bottom to get your <b>WANotifier.com API Key</b>.</li>
						<li>Copy and paste that key in the text box below and click on <b>Save and Validate</b> to continue.</li>
					</ol>
					<p><b>Setup time:</b> 15 - 30 min (depending on your pace)</p>
					<hr>
					<p><b>Feeling overwhelmed?</b> Don't worry, we can help you setup your account and this plugin for FREE. Just submit a support request <a href="https://wanotifier.com/support/" target="_blank">here</a> or start a chat with us on our <a href="https://wanotifier.com/" target="_blank">website</a> and we'll help you with the setup.</p>
				</div>
				<div class="onboarding-footer">
					<form method="POST" action="" enctype="multipart/form-data">
						<input type="text" name="notifier_api_key" id="wa-notifier-api-key" placeholder="Enter your API key here" value="<?php echo $api_key; ?>" />
						<button name="webhook_validation" class="button-primary" type="submit" value="">Submit and validate</button>
            			<?php wp_nonce_field( NOTIFIER_NAME . '-webhook-validation' ); ?>
					</form>
				</div>
			</div>
		<?php
			else :
				$available_triggers = Notifier_Notification_Triggers::get_notification_triggers();
				$enabled_triggers = get_option('notifier_enabled_triggers');
				$enabled_triggers = (!empty($enabled_triggers)) ? $enabled_triggers : array();
		?>
			<div class="dashboard-boxes">
				<div class="col w-70">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h3>How to use this plugin?</h3>
						</div>
						<div class="dashboard-box-body">
							<p>Follow the instructions below to setup your <b>Triggers</b> that'll fire WhatsApp notification messages:</p>
							<p><b>On this site</b></p>
							<ol>
								<li><a href="<?php echo admin_url('post-new.php?post_type=wa_notifier_trigger'); ?>" target="_blank">Click here</a> to add a new Trigger and select the <b>Trigger</b> from the dropdown when you want the notification to be fired.</li>
								<li>Enable the <b>Data fields</b> and <b>Recipient fields</b> that you want to send to WANotifier when notification is fired. The fields you enable here will be available for you to use when you create a notification.</li>
								<li>Then save and enable this trigger.</li>
							</ol>
							<p><b>On WANotifier portal</b></p>
							<ol start="4">
								<li>Create a new <b>Message Template</b> by <a href="https://app.wanotifier.com/templates/add/" target="_blank">clicking here</a>. This is the template that will be sent when a notification is triggered. You can create templates with variables (aka merge tags) and map those variables with the <b>Data fields</b> you enabled in step #3.</li>
								<li>Then <a href="https://app.wanotifier.com/notifications/add/" target="_blank">create a new Notification</a>. In the <b>Trigger</b> dropdown, you will see the trigger that you created in step #1. Select that.</li>
								<li>Then add <b>Recipients</b> to whom you want to send this notificaiton. You will find the <b>Recipient fields</b> here that you enabled in step #3.</li>
								<li>Then select the <b>Message Template</b> you created in step #4. If you created this message template with variables, you can map those variables with the available <b>Data fields</b> that you enabled in step #3 from the <b>Assign values to template variables</b> section.</li>
								<li>Click on the <b>Save</b> button to save this notification.</li>
							</ol>
							<p>That's it. Your notification will be sent when it gets triggered from this website!</p>
							<p><em><b>Disclaimer:</b> By enabling the triggers you accept and agree that the associated data and recipient fields will be sent to our server for use with the message template when sending notifications. We do not store any data other than what is required for sending a notification.</em></p>
							<!--<a class="notifier-overview-image" href="<?php echo NOTIFIER_URL; ?>assets/images/notifier-plugin-overview.svg" target="_blank">
								<img src="<?php echo NOTIFIER_URL; ?>assets/images/notifier-plugin-overview.svg">
							</a>-->
						</div>
					</div>
				</div>
				<div class="col w-30">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h3>Want more control?</h3>
						</div>
						<div class="dashboard-box-body">
							<p>This plugin provides you with simple triggers with a basic set of data fields to trigger notifications. If want more control over triggering your notifications, we suggest using <b>Webhooks</b> instead.</p>
							<p>With webhooks you can <b>add Contacts</b> and <b>trigger Notifications</b> from WordPress using action hooks or using 3rd party sites like Zapier, Pabbly, IFTTT etc!</p>
							<p><a href="https://app.wanotifier.com/contacts/import/" target="_blank">Click here</a> to view the webhook URL to <b>add contacts</b>. To trigger notification using webhook, <a href="https://app.wanotifier.com/notifications/add/" target="_blank">create a new Notification</a> and select <b>Trigger</b> option as <b>Via a Webhook URL</b>.</p>
						</div>
					</div>
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h3>Quick links</h3>
						</div>
						<div class="dashboard-box-body">
							<ul>
								<li><a href="https://app.wanotifier.com/inbox/" target="_blank">Inbox</a> - See customer replies to your notifications and message them back.</li>
								<li><a href="https://app.wanotifier.com/notifications/" target="_blank">Notifications</a> - See the notification delivery stats and much more.</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

	</div>
</div>
