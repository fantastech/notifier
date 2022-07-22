<?php
/**
 * Admin View: Dashboard
 *
 * @package WA_Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$disclaimer = get_option(WA_NOTIFIER_PREFIX . 'disclaimer');
$verify_token = get_option(WA_NOTIFIER_PREFIX . 'verify_token');
$api_credentials_validated = get_option(WA_NOTIFIER_PREFIX . 'api_credentials_validated');
$show_disclaimer = ( isset($_GET['show']) && $_GET['show'] == 'disclaimer' ) ? true : false;
$phone_number_id = get_option( WA_NOTIFIER_PREFIX . 'phone_number_id' );
$phone_number_details = get_option( WA_NOTIFIER_PREFIX . 'phone_number_details');
?>
<div class="wrap wa-notifier">

	<h1>Dashboard</h1>

	<div class="wa-notifier-wrapper">

		<?php if('accepted' != $disclaimer || $show_disclaimer) : ?>

			<div class="onboarding">
				<div class="onboarding-head">
					<h3>!!! [IMPORTANT DISCLAIMER] DO NOT SKIP WITHOUT READING !!!</h3>
					<p>Please read and accept the following 3 points before you start using this plugin.</p>
				</div>
				<div class="onboarding-body">
					<div class="onboarding-disclaimer">
						<h4>1. PHONE NUMBER WILL RESTRICTION</h4>
						<p>This plugin uses WhatsApp's official <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/overview" target="_blank">Cloud API</a> to send notifications from your WordPress backend. In order to use the API, you need to register your phone number with them to be able to send notifications.</p>
						<p>Once the phone number is setup in their app to use with this plugin, you will <b>not be able to use the number</b> on the <a href="https://business.whatsapp.com/products/business-app" target="_blank">WhastApp Business mobile app</a>.</p>
						<p>If you want to use the phone number that you were using on their phone app, WhatsApp <a href="https://developers.facebook.com/docs/whatsapp/phone-numbers" target="_blank">requires you</a> to <b>delete that account from phone</b> (along with it's message history) to be able to use it via the API.</p>
						<p>If you're using a different number that's not used in the mobile app or if you're okay to delete the phone app account, you can proceed with using this plugin.</p>
						<hr />
						<h4>2. ONE-WAY COMMUNICATION ONLY</h4>
						<p>Note that this plugin currently allows only <b>one-way communication</b>, that is, you can only <b>send notifications</b> on WhatsApp but <b>can not receive / read replies</b>.</p>
						<p>If you want to use your phone number for two-way communication with your customers, we suggest using their mobile app. But if you're okay with the one-way communication restriction, you can proceed with using this plugin.</p>
						<p>Note that we have plans to introduce <b>full-fledged WhatsApp inbox system</b> with this plugin that'll allow you <b>two-way communication</b> from WordPress dashbaord. But that'll take time to launch so be thoughtful about the phone number that you use with this plugin.</p>
						<hr />
						<h4>3. METERED BILLING BY WHATSAPP</h4>
						<p>Notifications you send from the app are <a href="https://developers.facebook.com/docs/whatsapp/pricing" target="_blank">metered by WhatsApp</a>. <b>First 1000 messages per month are <em>free</em></b> to send but you're charged for the messages you send post that. The billing is handled by WhatsApp on their site and not by this plugin.</p>
						<hr />
						<p>If you understand and accept these points, click on button below to get started.</p>
					</div>
				</div>
				<div class="onboarding-footer">
					<?php if('accepted' != $disclaimer) : ?>
						<form method="POST" action="" enctype="multipart/form-data">
							<button name="disclaimer" class="button-primary" type="submit" value="">I Understand & Accept</button>
	            			<?php wp_nonce_field( WA_NOTIFIER_NAME . '-disclaimer' ); ?>
						</form>
					<?php else : ?>
						<button class="button" disabled="disabled">Already accepted</button>
						<a class="button-primary" href="admin.php?page=wa-notifier">Back to Dashboard</a>
					<?php endif; ?>
				</div>
			</div>

		<?php elseif (!$api_credentials_validated): ?>

			<div class="onboarding">
				<div class="onboarding-head">
					<h3>Let's Configure The Plugin!</h3>
					<p>Follow the steps below to configure the plugin to start sending WhatsApp notifications. Estimated time: 8 to 10 minutes.</p>
				</div>
				<div class="onboarding-body">
					<div class="step active">
						<div class="step-head">
							<div class="step-title text-uppercase"><b>Step 1: Create Meta account and an app for WhastApp Cloud API</b></div>
							<button class="toggle-step"></button>
						</div>
						<div class="step-body">
							<ol>
								<li>Create a free <a href="https://developers.facebook.com/async/registration/" target="_blank">Meta developer account</a>, if you don't already have one.</li>
								<li>Login to the Meta developer portal and click on <a href="https://developers.facebook.com/apps/create/" target="_blank">Create App</a> button to create a new app.</li>
								<li>On this page, select the <b>Business</b> option and click on the <b>Next</b> button at the bottom.</li>
								<li>On the next <b>Provide basic information</b> page, fill in the details and click on <b>Create App</b> when done.</li>
								<li>This will redirect you to app page where you need to <b>Add products to your app</b>. Scroll to the bottom to find "WhatsApp" product and click <b>Set up</b>.</li>
								<li>Next, you will see the option to select an existing Business Manager (if you have one) or, if you would like, the onboarding process will create one automatically for you (you can customize your business later, if needed). Make a selection and click <b>Continue</b>.</li>
								<li>You will be redirected to the <b>Get Started</b> page.</li>
							</ol>
						</div>
					</div>
					<div class="step">
						<div class="step-head">
							<div class="step-title text-uppercase"><b>Step 2: Setup your phone number in WhastApp Cloud API</b></div>
							<button class="toggle-step"></button>
						</div>
						<div class="step-body">
							<ol>
								<li>Scroll down on the <b>Get Started</b> screen and click on <b>Add phone number</b> button.</li>
								<li>Fill in your business information and click <b>Next</b>.</li>
								<li>On the next screen, you'll be prompted to create a WhatsApp Business profile. Enter the details and click <b>Next</b>.</li>
								<li>Next, you'll need to add your phone number. Enter your phone number and select a verification method to verify the number.</li>
								<li>Enter the verfication code and click <b>Next</b>.</li>
								<li>Once the phone number is added, scroll up on the <b>Get Started</b> page and select your added number from the dropdown under <b>Send and receive messages</b>.</li>
								<li>After selecting your phone number the <b>Phone number ID</b> and <b>WhatsApp Business Account ID</b> values will get updated below it. Copy those values and add them on the <a href="<?php echo get_admin_url( null, 'admin.php?page=' . WA_NOTIFIER_NAME . '-settings'); ?>" target="_blank">Settings</a> page. We'll add <b>Permanent Access Token</b> in the next step.</li>
							</ol>
						</div>
					</div>
					<div class="step">
						<div class="step-head">
							<div class="step-title text-uppercase"><b>Step 3: Configure webhook and permanent token</b></div>
							<button class="toggle-step"></button>
						</div>
						<div class="step-body">
							<p>In this step, we'll setup the Webhook and Permanent Token.</p>
							<b>Webhook:</b>
							<ol>
								<li>From the <b>Get Started</b> screen's left sidebar, click on <b>Whatsapp > Configuration</b> link.</li>
								<li>Click on the <b>Edit</b> button. That'll open a popup.</li>
								<li>Enter the following in the <b>Callback URL</b> field: <code><?php echo site_url('/?wa_notifier'); ?></code></li>
								<li>In the <b>Verify Token</b> field enter: <code><?php echo $verify_token ?></code> and click <b>Verify</b>.</li>
								<li>Under <b>Webhook fields:</b> click on <b>Manage</b>.</li>
								<li>Click on <b>Subscribe</b> button in front of all fields and then click on <b>Done</b>.</li>
							</ol>
						
							<b>Permanent Token:</b>
							<ol>
								<li><a href="https://business.facebook.com/settings" target="_blank">Click here</a> to open <b>Business Settings</b> for your <b>Business Manager</b> account and select your account.</li>
								<li>Under <b>Users</b> in left sidebar, click on <b>System users</b>. Click on <b>Add</b> to add a new user.</li>
								<li>Click on <b>I Accept</b> button if you see non-discrimation policy popup.</li>
								<li>Enter a name for your <b>System user name</b> (you can keep it admin or system, this will not be shown to your users). Select <b>System user role</b> as <b>Administrator</b> and click on <b>Create system user</b>.</li>
								<li>Click on <b>Add Assets</b> button. Under <b>Select asset type</b> click on <b>Apps</b>, select your app from <b>Select assets</b> column and then enable <b>Full control > Manage App</b>. Click on <b>Save Changes</b>.</li>
								<li>Now click on <b>Generate Token</b> button. Select your app from the dropdown and click on <b>Generate Token</b>. From the list of permissions under <b>Available Permissions:</b> select <em>whatsapp_business_messaging</em> and <em>whatsapp_business_management</em> and then click on <b>Generate Token</b>.</li>
								<li>Copy the <b>Access token</b>. Open this plugin's <a href="<?php echo get_admin_url( null, 'admin.php?page=' . WA_NOTIFIER_NAME . '-settings'); ?>" target="_blank">Settings</a> page, add it in the <b>Permanent Access Token</b> field and save.</li>
							</ol>
							
						</div>
					</div>
					<div class="step">
						<div class="step-head">
							<div class="step-title text-uppercase"><b>Step 4: Validate and complete</b></div>
							<button class="toggle-step"></button>
						</div>
						<div class="step-body">
							<p>After you've completed the steps above and added the <b>WhastApp Phone Number ID</b>, <b>WhastApp Business Account ID</b> and the <b>Permanent Access Token</b>, click on the link below to validate.</p>
							<p>Once you click the button below, the plugin will connect to your account via Cloud API to validate the details. Once validated, it'll create draft <b>Message Templates</b> for you to start sending notifications.</p>
							<form method="POST" action="" enctype="multipart/form-data">
								<button name="validate" class="button-primary" type="submit" value="">Validate and Complete Setup</button>
		            			<?php wp_nonce_field( WA_NOTIFIER_NAME . '-validate' ); ?>
							</form>
						</div>
					</div>
				</div>
			</div>

		<?php else: ?>		
			<div class="dashboard-boxes">
				<div class="col w-100">
					<div class="dashboard-box dashboard-box-top">
						<div class="dashboard-box-body d-flex w-100">
							<div class="w-25">
								<b>Phone Number:</b>
								<?php echo $phone_number_details[$phone_number_id]['display_num']; ?>
							</div>
							<div class="w-25">
								<b>Display Name:</b>
								<?php echo $phone_number_details[$phone_number_id]['display_name']; ?>
							</div>
							<div class="w-25">
								<b>Status:</b>
								<?php echo $phone_number_details[$phone_number_id]['phone_num_status']; ?>
							</div>
							<div class="w-25">
								<b>Qaulity Rating:</b>
								<span class="quantity-rating quantity-rating-<?php echo strtolower($phone_number_details[$phone_number_id]['quality_rating']); ?>"></span>
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
				<?php if($message_templates_count == 0 || $contacts_count == 0 || $notifications_count == 0): ?>
					<div class="col w-33">
						<div class="dashboard-box">
							<div class="dashboard-box-head">
								<h2>You're all set! Here are the next steps...</h2>
							</div>
							<div class="dashboard-box-body">
								<p>
									<span class="dashicons <?php echo ($message_templates_count == 0) ? 'dashicons-marker' : 'dashicons-yes-alt'; ?>"> </span>
									<b>STEP 1</b> - Create your first <a href="<?php echo admin_url( 'edit.php?post_type=wa_message_template' ); ?>" target="_blank">Message Template</a>.
								</p>
								<p>
									<span class="dashicons <?php echo ($contacts_count == 0) ? 'dashicons-marker' : 'dashicons-yes-alt'; ?>"> </span>
									<b>STEP 2</b> - Add / import <a href="<?php echo admin_url( 'edit.php?post_type=wa_contact' ); ?>" target="_blank">Contacts</a>.
								</p>
								<p>
									<span class="dashicons <?php echo ($notifications_count == 0) ? 'dashicons-marker' : 'dashicons-yes-alt'; ?>"> </span>
									<b>STEP 3</b> - Create and send your first <a href="<?php echo admin_url( 'edit.php?post_type=wa_notification' ); ?>" target="_blank">Notification</a>.
								</p>
							</div>
						</div>
					</div>
				<?php endif;?>
				<?php if($message_templates_count > 0): ?>
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
									foreach($message_templates as $template) {
										$status = get_post_meta( $template->ID, WA_NOTIFIER_PREFIX . 'status', true);
			    						$status_text = ($status) ? '<span class="status status-'.strtolower($status).'">'.$status.'</span>' : '-';
										echo '<tr><td><a href="'.get_edit_post_link($template->ID).'">'.$template->post_title.'</a></td><td>'.$status_text.'</td></tr>';
									}
									?>
								</table>
							</div>
							<div class="dashboard-box-footer">
								<div class="dashboard-box-buttons-wrap">
									<a class="button" href="<?php echo admin_url('edit.php?post_type=wa_message_template'); ?>">View All</a>
								</div>
							</div>
						</div>
					</div>
				<?php endif;?>
				<?php if($contacts_count > 0): ?>
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
									foreach($contacts as $contact) {
										$first_name = get_post_meta( $contact->ID, WA_NOTIFIER_PREFIX . 'first_name', true);
										$last_name = get_post_meta( $contact->ID, WA_NOTIFIER_PREFIX . 'last_name', true);
										$wa_number = get_post_meta( $contact->ID, WA_NOTIFIER_PREFIX . 'wa_number', true);
										echo '<tr><td><a href="'.get_edit_post_link($contact->ID).'">'.$first_name . '</a></td><td>'.$last_name.'</td><td>'.$wa_number.'</td></tr>';
									}
									?>
								</table>
							</div>
							<div class="dashboard-box-footer">
								<div class="dashboard-box-buttons-wrap">
									<a class="button" href="<?php echo admin_url('edit.php?post_type=wa_contact'); ?>">View All</a>
								</div>
							</div>
						</div>
					</div>
				<?php endif;?>
				<?php if($notifications_count > 0): ?>
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
									foreach($notifications as $notification) {
										$status = get_post_meta( $notification->ID, WA_NOTIFIER_PREFIX . 'notification_status', true);
										$sent = get_post_meta( $notification->ID, WA_NOTIFIER_PREFIX . 'notification_sent_contact_ids', true);
										$sent_count = ($sent && is_array($sent)) ? count($sent) : '0';
										$unsent = get_post_meta( $notification->ID, WA_NOTIFIER_PREFIX . 'notification_unsent_contact_ids', true);
										$unsent_count = ($unsent && is_array($unsent)) ? count($unsent) : '0';
										echo '<tr>';
										echo '<td><a href="'.get_edit_post_link($notification->ID).'">'.$notification->post_title . '</a></td>';
										echo '<td>'.$status.'</td>';
										echo '<td>Sent: '.$sent_count.' / Failed: '.$unsent_count.'</td>';
										echo '</tr>';
									}
									?>
								</table>
							</div>
							<div class="dashboard-box-footer">
								<div class="dashboard-box-buttons-wrap">
									<a class="button" href="<?php echo admin_url('edit.php?post_type=wa_notification'); ?>">View All</a>
								</div>
							</div>
						</div>
					</div>
				<?php endif;?>
			</div>

		<?php endif; ?>

	</div>
</div>
