<?php
/**
 * Admin View: Dashboard
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$disclaimer = get_option(NOTIFIER_PREFIX . 'disclaimer');
$verify_token = get_option(NOTIFIER_PREFIX . 'verify_token');
$api_credentials_validated = get_option(NOTIFIER_PREFIX . 'api_credentials_validated');
$show_disclaimer = ( isset($_GET['show']) && $_GET['show'] == 'disclaimer' ) ? true : false;
$phone_number_id = get_option( NOTIFIER_PREFIX . 'phone_number_id' );
$phone_number_details = get_option( NOTIFIER_PREFIX . 'phone_number_details');
?>
<div class="wrap notifier">

	<h1>Dashboard</h1>

	<div class="notifier-wrapper">

		<?php if ('accepted' != $disclaimer || $show_disclaimer) : ?>
			<div class="onboarding">
				<div class="onboarding-head">
					<h3>Enter your WANotifier.com API Key</h3>
					<p>You can find your WANotifier.com API key on your <a href="https://app.wanotifier.com/settings/api/" target="_blank">Settings</a> page.</p>
					<p>If you do have an account yet you can create one for FREE at <a href="https://wanotifier.com/" target="_blank">WANotifier.com</a>.</p>
				</div>
				<div class="onboarding-body">
					<form method="POST" action="" enctype="multipart/form-data">
						<input type="text" name="notifier_api_key" id="notifier-api-key" placeholder="Enter your WA Notifier API key here" />
						<button name="disclaimer" class="button-primary" type="submit" value="">Submit and validate</button>
            			<?php wp_nonce_field( NOTIFIER_NAME . '-disclaimer' ); ?>
					</form>
				</div>
			</div>
		<?php else : ?>		
			<div class="dashboard-boxes">
				<div class="col w-100">
					<div class="dashboard-box dashboard-box-top">
						<div class="dashboard-box-body d-flex w-100">
							<div class="w-25">
								<b>Phone Number:</b>
								<?php echo esc_html($phone_number_details[$phone_number_id]['display_num']); ?>
							</div>
							<div class="w-25">
								<b>Display Name:</b>
								<?php echo esc_html($phone_number_details[$phone_number_id]['display_name']); ?>
							</div>
							<div class="w-25">
								<b>Status:</b>
								<?php echo esc_html($phone_number_details[$phone_number_id]['phone_num_status']); ?>
							</div>
							<div class="w-25">
								<b>Qaulity Rating:</b>
								<span class="quantity-rating quantity-rating-<?php echo esc_attr( strtolower($phone_number_details[$phone_number_id]['quality_rating']) ); ?>"></span>
							</div>
						</div>
					</div>
				</div>
				<?php
					$message_templates = get_posts(
						array (
							'post_type' => 'wa_message_template',
							'post_status' => 'publish',
							'numberposts' => 5,
						)
					);
					$message_templates_count = count($message_templates);

					$contacts = get_posts(
						array (
							'post_type' => 'wa_contact',
							'post_status' => 'publish',
							'numberposts' => 5,
						)
					);
					$contacts_count = count($contacts);

					$notifications = get_posts(
						array (
							'post_type' => 'wa_notification',
							'post_status' => 'publish',
							'numberposts' => 5,
						)
					);
					$notifications_count = count($notifications);
				?>
				<?php if ($message_templates_count == 0 || $contacts_count == 0 || $notifications_count == 0) : ?>
					<div class="col w-33">
						<div class="dashboard-box">
							<div class="dashboard-box-head">
								<h2>You're all set! Here are the next steps...</h2>
							</div>
							<div class="dashboard-box-body">
								<p>
									<span class="dashicons <?php echo ($message_templates_count == 0) ? 'dashicons-marker' : 'dashicons-yes-alt'; ?>"> </span>
									<b>STEP 1</b> - Create your first <a href="<?php echo esc_url(admin_url( 'edit.php?post_type=wa_message_template' )); ?>" target="_blank">Message Template</a>.
								</p>
								<p>
									<span class="dashicons <?php echo ($contacts_count == 0) ? 'dashicons-marker' : 'dashicons-yes-alt'; ?>"> </span>
									<b>STEP 2</b> - Add / import <a href="<?php echo esc_url(admin_url( 'edit.php?post_type=wa_contact' )); ?>" target="_blank">Contacts</a>.
								</p>
								<p>
									<span class="dashicons <?php echo ($notifications_count == 0) ? 'dashicons-marker' : 'dashicons-yes-alt'; ?>"> </span>
									<b>STEP 3</b> - Create and send your first <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wa_notification' ) ); ?>" target="_blank">Notification</a>.
								</p>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($message_templates_count > 0) : ?>
					<div class="col w-33">
						<div class="dashboard-box">
							<div class="dashboard-box-head">
								<h2>Message Templates</h2>
							</div>
							<div class="dashboard-box-body">
								<table>
									<tr>
										<th>Name</th>
										<th>Status</th>
									</tr>
									<?php
									foreach ($message_templates as $template) {
										$status = get_post_meta( $template->ID, NOTIFIER_PREFIX . 'status', true);
			    						$status_text = ($status) ? '<span class="status status-' . esc_attr(strtolower($status)) . '">' . esc_html($status) . '</span>' : '-';
										echo '<tr><td><a href="' . esc_url(get_edit_post_link($template->ID)) . '">' . esc_html($template->post_title) . '</a></td><td>' . wp_kses_post($status_text) . '</td></tr>';
									}
									?>
								</table>
							</div>
							<div class="dashboard-box-footer">
								<div class="dashboard-box-buttons-wrap">
									<a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=wa_message_template')); ?>">View All</a>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($contacts_count > 0) : ?>
					<div class="col w-33">
						<div class="dashboard-box">
							<div class="dashboard-box-head">
								<h2>Contacts</h2>
							</div>
							<div class="dashboard-box-body">
								<table>
									<tr>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Number</th>
									</tr>
									<?php
									foreach ($contacts as $contact) {
										$first_name = get_post_meta( $contact->ID, NOTIFIER_PREFIX . 'first_name', true);
										$last_name = get_post_meta( $contact->ID, NOTIFIER_PREFIX . 'last_name', true);
										$wa_number = get_post_meta( $contact->ID, NOTIFIER_PREFIX . 'wa_number', true);
										echo '<tr><td><a href="' . esc_url( get_edit_post_link($contact->ID) ) . '">' . esc_html($first_name) . '</a></td><td>' . esc_html($last_name) . '</td><td>' . esc_html($wa_number) . '</td></tr>';
									}
									?>
								</table>
							</div>
							<div class="dashboard-box-footer">
								<div class="dashboard-box-buttons-wrap">
									<a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=wa_contact')); ?>">View All</a>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($notifications_count > 0) : ?>
					<div class="col w-33">
						<div class="dashboard-box">
							<div class="dashboard-box-head">
								<h2>Notifications</h2>
							</div>
							<div class="dashboard-box-body">
								<table>
									<tr>
										<th>Name</th>
										<th>Status</th>
										<th>Stats</th>
									</tr>
									<?php
									foreach ($notifications as $notification) {
										$status = get_post_meta( $notification->ID, NOTIFIER_PREFIX . 'notification_status', true);
										$sent = get_post_meta( $notification->ID, NOTIFIER_PREFIX . 'notification_sent_contact_ids', true);
										$sent_count = ($sent && is_array($sent)) ? count($sent) : '0';
										$unsent = get_post_meta( $notification->ID, NOTIFIER_PREFIX . 'notification_unsent_contact_ids', true);
										$unsent_count = ($unsent && is_array($unsent)) ? count($unsent) : '0';
										echo '<tr>';
										echo '<td><a href="' . esc_url( get_edit_post_link($notification->ID) ) . '">' . esc_html($notification->post_title) . '</a></td>';
										echo '<td>' . esc_html($status) . '</td>';
										echo '<td>Sent: ' . esc_html($sent_count) . ' / Failed: ' . esc_html($unsent_count) . '</td>';
										echo '</tr>';
									}
									?>
								</table>
							</div>
							<div class="dashboard-box-footer">
								<div class="dashboard-box-buttons-wrap">
									<a class="button" href="<?php echo esc_url( admin_url('edit.php?post_type=wa_notification') ); ?>">View All</a>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	</div>
</div>
