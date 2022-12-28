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
				<div class="onboarding-body">
					<h3>Let's Get Started</h3>
					<p><a href="https://wanotifier.com/" target="_blank">WA Notifier</a> is a SaaS tool that allows you to send WhatsApp marketing and transactional messages to your customers using <b>WhatsApp's official Cloud APIs</b>.</p>
					<p>To use this plugin you need to create an account with us and connect it with this plugin. Once you complete the setup, you'll be able to trigger transactional WhatsApp notification messages from this website.</p>
					<p>Please follow the instructions below to setup this plugin. The setup might take <b>15 - 30 min</b> depending on your pace.</p>
					<ol>
						<li>Create a <b>FREE account</b> on WANotifier.com by <a href="https://app.wanotifier.com/create-account/" target="_blank">clicking here</a>.</li>
						<li>After you create your account, go through the on-boarding steps to the setup up of your <b>test WhatsApp Cloud API account</b>.</li>
						<li>Once the test account is ready, follow the instructions on screen to set up <b>your own phone number</b> with the Cloud API (and get out of <b>Test Mode</b>).</li>
						<li>After that is done, go to the <a href="https://app.wanotifier.com/settings/api/" target="_blank">Settings > API</a> to get your <b>WANotifier.com API key</b>.</li>
						<li>Copy and paste that key in the text box below and click on <b>Save and Validate</b> to continue.</li>
					</ol>
					<p><b>Feeling overwhelmed?</b> Don't worry, we can help you setup this plugin for FREE. Just submit a support request <a href="https://wanotifier.com/support/" target="_blank">here</a> or start a chat with us on our <a href="https://wanotifier.com/" target="_blank">website</a> and we'll help you with this.</p>
					<p><hr></p>
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
			<form class="dashboard-boxes" method="POST" action="" enctype="multipart/form-data">
				<div class="col w-70">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h3>Available Triggers</h3>
						</div>
						<div class="dashboard-box-body">
							<?php
								foreach($available_triggers as $group_name => $group_triggers) {
									?>
									<div class="notifier-triggers-group-wrap">
										<h3><?php echo $group_name; ?></h3>
										<?php
										foreach($group_triggers as $trigger){
											$merge_tags = Notifier_Notification_Merge_Tags::get_trigger_merge_tags($trigger['id']);
											$recipient_fields = Notifier_Notification_Triggers::get_trigger_recipient_fields($trigger['id']);
											?>
											<div class="notifier-trigger-wrap">
												<label for="notifier_trigger_<?php echo $trigger['id']; ?>" class="notifier-trigger-label d-flex align-items-center">
													<input type="checkbox" name="notifier_triggers[]" class="enable-trigger" value="<?php echo $trigger['id']; ?>" <?php checked(in_array($trigger['id'], $enabled_triggers)); ?>>
													<?php echo $trigger['label'] ?>
													<button class="notifier-show-trigger-info ms-auto"><span class="dashicons dashicons-info-outline"></span></button>
												</label>
												<div class="notifier-trigger-info hide">
													<table>
														<tr>
															<th>Trigger description:</th>
															<td><?php echo $trigger['description'] ?></td>
														</tr>
														<tr>
															<th>Available data fields:</th>
															<td>
																<?php foreach($merge_tags as $tag_group_name => $group_tags): ?>
																	<b><?php echo $tag_group_name; ?>: </b>
																	<?php echo implode(', ', $group_tags); ?><br />
																<?php endforeach; ?>
															</td>
														</tr>
														<tr>
															<th>Available recipient fields:</th>
															<td>
																<?php
																if(!empty($recipient_fields)){
																	foreach($recipient_fields as $recipient_group_name => $recipient_group_fields):
																		echo '<b>' . $recipient_group_name . ': </b>';
																		echo implode(', ', $recipient_group_fields);
																	endforeach;
																}
																else{
																	echo 'No fields';
																}
																?>
															</td>
														</tr>
													</table>
												</div>
											</div>
											<?php
									}
									?>
								</div>
								<?php
								}
							?>
						</div>
					</div>
				</div>
				<div class="col w-30">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h3>Actions</h3>
						</div>
						<div class="dashboard-box-footer">
							<div class="dashboard-box-buttons-wrap">
								<button name="notifier_save_triggers" class="button-primary" type="submit" value="">Save</button>
            					<?php wp_nonce_field( NOTIFIER_NAME . '-save-triggers' ); ?>
							</div>
						</div>
					</div>
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h3>How to?</h3>
						</div>
						<div class="dashboard-box-body">
							<p>Follow the instructions below to setup your transactional messages:</p>
							<ol>
								<li>Enable the triggers on this page that you want to use to trigger a notification on <a href="https://wanotifier.com/" target="_blank">WANotifier.com</a> and hit the <b>Save</b> button. It's important to Save before you go to next step.</li>
								<li>Create a new Message Template <a href="https://app.wanotifier.com/templates/add/" target="_blank">by clicking here</a> that you want to send when the notification is triggered. Create one to start with.</li>
								<li>Then <a href="https://app.wanotifier.com/notifications/add/" target="_blank">create a new notification</a>, give it a title and select <b>Notifiacation Type</b> as <b>Transactional</b>.</li>
								<li>In the <b>Trigger</b> dropdown field, you will see all the triggers that you have enabled on the page. Select the one you want to use with the notification.</li>
								<li>After that you select the <b>Recipients</b> and the <b>Message Template</b> that you want to send and save the notification by clicking on the <b>Save</b> button.</li>
							</ol>
							<p>That's it. Your notification will be sent when it gets triggered from this website!</p>
							<p>Currently we only support the shown <b>WordPress</b> and <b>Woocommerce</b> triggers. If you want more triggers, your can request us <a href="https://wanotifier.com/support/">here</a> or contact your developer to add custom triggers using our filter hooks.</p>
							<p><em><b>Disclaimer:</b> By enabling the triggers you accept and agree that the associated data and recipient fields will be sent to our server for use with the message template when sending notifications. We do not store any data other than what is required for sending a notification.</em></p>
						</div>
					</div>
				</div>
			</form>
		<?php endif; ?>

	</div>
</div>
