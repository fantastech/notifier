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
$activated = get_option(NOTIFIER_PREFIX . 'api_activated');
?>
<div class="wrap notifier">



	<div class="notifier-wrapper">
		<?php if ('' == $activated) : ?>
			<div class="onboarding">
				<div class="onboarding-body">
					<h3>Enter your WANotifier.com API Key</h3>
					<p>You can find your <b>WANotifier.com API key</b> on your <a href="https://app.wanotifier.com/settings/api/" target="_blank">Settings</a> page. If you do not have an account yet you can create one for FREE at <a href="https://wanotifier.com/" target="_blank">WANotifier.com</a>.</p>
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
							<p>On this page, you'll find all the available triggers on this website that you can use to trigger a notification from your <a href="https://wanotifier.com/" target="_blank">WANotifier.com</a> account.</p>
							<p>The triggers you <b>enable and save</b> here will be available to use on your account when you create a <a href="https://app.wanotifier.com/notifications/add/">new Notification</a>.</p>
							<p>Currently we only support the shown <b>WordPress</b> and <b>Woocommerce</b> triggers. If you want more triggers, your can request us <a href="https://wanotifier.com/support/">here</a> or contact your developer to add custom triggers using our filter hooks.</p>
							<p><em><b>Disclaimer:</b> By enabling the triggers you accept and agree that the associated data and recipient fields will be sent to our server for use with the message template when sending notifications. We do not store any data other than what is required for sending a notification.</em></p>
						</div>
					</div>
				</div>
			</form>
		<?php endif; ?>

	</div>
</div>
