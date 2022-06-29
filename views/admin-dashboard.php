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
					<h3>IMPORTANT DISCLAIMER!</h3>
					<p>Please read and accept the disclaimer before you start using this plugin.</p>
				</div>
				<div class="onboarding-body">
					<div class="onboarding-disclaimer">
						<h4>1. Phone Number</h4>
						<p>This plugin uses <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/overview" target="_blank">WhatsApp Cloud API</a> to send notifications directly from WordPress backend. In order to use the API you need to register your phone number with them to be able to send notifications.</p>
						<p>If you want to use a phone number that you're already using with WhastApp Business app on you phone, Meta <a href="https://developers.facebook.com/docs/whatsapp/phone-numbers">requires you</a> to <b>delete that account from phone</b> which also deletes all it's message history.</p>
						<p>If you're ok with deleting the account or you're using a different phone number and understand that the number will only be used for <b>one-way notification sending</b> via this plugin, you can proceed.</p>
						<p>But if you want to keep using that phone number on the phone app to communicate with your customers, <b>we suggest using an alternate number</b> for this plugin.</p>
						<p>Note that we have a plan to introduce <b>full-fledged WhatsApp inbox system</b> with this plugin that'll allow you <b>two-way communication</b> right from the WordPress dashbaord. But that'll take time to launch so be thoughtful about the phone number that you use with this plugin.</p>
						<h4>2. Metered Billing by Meta</h4>
						<p>Notifications you send from the app are <a href="https://developers.facebook.com/docs/whatsapp/pricing">metered by Meta</a>. First 1000 messages per month are free to send but you're charged for the messages post that.</p>
						<p>If you understand and accept these points, you can click on button below to get started.</p>
					</div>
				</div>
				<div class="onboarding-footer">
					<?php if('accepted' != $disclaimer) : ?>
						<form method="POST" action="" enctype="multipart/form-data">
							<button name="disclaimer" class="button-primary" type="submit" value="">I Understand & Accept</button>
	            			<?php wp_nonce_field( WA_NOTIFIER_NAME . '-disclaimer' ); ?>
						</form>
					<?php else : ?>
						<button class="button" disabled="disabled">Already Accepted</button>
						<a class="button-primary" href="admin.php?page=wa-notifier">Back to Dashboard</a>
					<?php endif; ?>
				</div>
			</div>

		<?php elseif (!$api_credentials_validated): ?>

			<div class="onboarding">
				<div class="onboarding-head">
					<h3>Let's Get Started!</h3>
					<p>Follow the steps below to setup your WhastApp Business account and the plugin to start sending notifications. Estimated time needed for setup: 8 to 10 minutes.</p>
				</div>
				<div class="onboarding-body">
					<div class="step active">
						<div class="step-head">
							<div class="step-title"><b>Step 1: Create Meta account and an app for WhastApp Cloud API</b></div>
							<button class="toggle-step"></button>
						</div>
						<div class="step-body">
							<ol>
								<li>Create a free <a href="https://developers.facebook.com/apps" target="_blank">Meta developer account</a>, if you don't already have one.</li>
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
							<div class="step-title"><b>Step 2: Setup your phone number in WhastApp Cloud API</b></div>
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
								<li>After selecting your phone number the <b>Phone number ID</b> and <b>WhatsApp Business Account ID</b> values will get updated below it. Copy those values and add them on the <a href="<?php echo get_admin_url( null, 'admin.php?page=' . WA_NOTIFIER_NAME . '-settings'); ?>" target="_blank">Settings</a> page.</li>
							</ol>
						</div>
					</div>
					<div class="step">
						<div class="step-head">
							<div class="step-title"><b>Step 3: Configure webhook and permanent token</b></div>
							<button class="toggle-step"></button>
						</div>
						<div class="step-body">
							<b>Webhook:</b>
							<ol>
								<li>From the <b>Get Started</b> screen's left sidebar, click on <b>Whatsapp > Configuration</b> link.</li>
								<li>Click on the <b>Configure a webhook</b> link. That'll open a popup.</li>
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
								<li>Enter a name for your <b>System user name</b>, select <b>System user role</b> as <b>Administrator</b> and click on <b>Create system user</b>.</li>
								<li>Click on <b>Add Assets</b> button. Under <b>Select asset type</b> click on <b>Apps</b>, select your app from <b>Select assets</b> column and then enable <b>Full control > Manage App</b>. Click on <b>Save Changes</b>.</li>
								<li>Now click on <b>Generate Token</b> button. Select your app from the dropdown and click on <b>Generate Token</b>. From the list of permissions under <b>Available Permissions:</b> select <em>whatsapp_business_messaging</em> and <em>whatsapp_business_management</em> and then click on <b>Generate Token</b>.</li>
								<li>Copy the <b>Access token</b>. Open this plugin's <a href="<?php echo get_admin_url( null, 'admin.php?page=' . WA_NOTIFIER_NAME . '-settings'); ?>" target="_blank">Settings</a> page, add it in the <b>Permanent Access Token</b> field and save.</li>
							</ol>
							
						</div>
					</div>
					<div class="step">
						<div class="step-head">
							<div class="step-title"><b>Step 4: Validate and complete</b></div>
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
				<div class="col w-30">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h2>You're all set!</h2>
						</div>
						<div class="dashboard-box-body">
							<p>Next steps:</p>
							<ol>
								<li>Create a new <a href="<?php echo admin_url( 'edit.php?post_type=wa_message_template' ); ?>">Message Template</a>.</li>
								<li>Add / import <a href="<?php echo admin_url( 'edit.php?post_type=wa_contact' ); ?>">Contacts</a>.</li>
								<li>Create and send your first <a href="<?php echo admin_url( 'edit.php?post_type=wa_notification' ); ?>">Notification</a>.</li>
							</ol>
						</div>
					</div>
				</div>
				<div class="col w-30">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h2>Message Templates</h2>
						</div>
						<div class="dashboard-box-body">

						</div>
					</div>
				</div>
				<div class="col w-40">
					<div class="dashboard-box">
						<div class="dashboard-box-head">
							<h2>Contacts</h2>
						</div>
						<div class="dashboard-box-body">

						</div>
					</div>
				</div>
			</div>

		<?php endif; ?>

	</div>
</div>
