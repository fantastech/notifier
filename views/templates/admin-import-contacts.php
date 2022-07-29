<?php
/**
 * Admin side template to load Import Contacts feature
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$import_from_users_url = '?' . http_build_query(array_merge($_GET, array('import_contacts_from_users'=>'1')));
?>
<a href="#" class="page-title-action" id="import-contacts">Import Contacts</a>
<div class="contact-import-options hide">
	<div class="p-20">
		<h3>Select an import method:</h3>
		<label><input type="radio" name="csv_import_method" class="csv-import-method" value="csv" checked="checked"> Import from CSV file</label>
		<?php
			$disable_woo = '';
		if ( ! class_exists( 'WooCommerce' ) ) {
			$disable_woo = 'disabled="disabled" title="Woocommerce not installed."';
		}
		?>
		<label><input type="radio" name="csv_import_method" class="csv-import-method" value="users" <?php echo esc_attr($disable_woo); ?>> Import from WooCommerce</label>
		<div class="col-import col-import-csv">
			<form id="import-contacts-csv" class="contacts-import-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" enctype="multipart/form-data">
				<p><a href="<?php echo esc_url( NOTIFIER_URL . 'contacts-import-sample.csv' ); ?>">Click here</a> to download sample CSV file. Fill in the CSV with your contact data wtihtout changing the format of the CSV. <b>IMPORTANT:</b> In the WhatsApp number column, add phone numbers WITH country codes (but WITHOUT the plus + sign).</p>
				<p><input type="file" name="notifier_contacts_csv" id="notifier-contacts-csv" /></p>
				<p><input type="submit" name="upload_csv" value="Import CSV" class="button-primary"></p>
				<?php wp_nonce_field('notifier_contacts_csv'); ?>
				<input type="hidden" name="action" value="notifier_import_contacts_csv" />
			</form>
		</div>
		<div class="col-import col-import-users hide">
			<form id="import-contacts-users" class="meta-fields contacts-import-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" enctype="multipart/form-data">
				<p>Import contact data (billing name and phone number) from your Woocommerce <a href="admin.php?page=wc-admin&path=%2Fcustomers" target="_blank">customers</a>. Note that the customers with empty or incorrectly filled phone number fields will not be imported.</p>
				<?php
					$contact_lists = Notifier_Contacts::get_contact_lists(true, true);
					$contact_lists = array_slice($contact_lists, 0, 1, true) + array('_add_new' => 'Add new list') + array_slice($contact_lists, 1, null, true);
					notifier_wp_select(
						array(
							'id'                => 'wa_contact_list_name',
							'value'             => '',
							'label'             => 'Contact list:',
							'description'       => 'Select the contact list where you want to import the contacts. Select <b>Add new list</b> if you want to add a new list.',
							'options'           => $contact_lists
						)
					);
					notifier_wp_text_input(
						array(
							'id'                => 'wa_contact_list_name_input',
							'value'             => '',
							'label'             => 'Enter list name:',
							'description'       => 'Enter the Contact List name that you want to create.',
							'placeholder'		=> 'E.g. Woocommerce Customers',
							'conditional_logic'		=> array (
								array (
									'field'		=> 'wa_contact_list_name',
									'operator'	=> '==',
									'value'		=> '_add_new'
								)
							),
						)
					);
					notifier_wp_text_input(
						array(
							'id'                => 'wa_contact_tags',
							'value'             => '',
							'label'             => 'Tags:',
							'description'       => 'Enter comma separated list of tags.',
							'placeholder'		=> 'E.g. tag1, tag2, tag3'
						)
					);
					?>
				<input type="submit" value="Import" class="button-primary">
				<?php wp_nonce_field('notifier_contacts_users'); ?>
				<input type="hidden" name="action" value="notifier_import_contacts_woocommerce" />
			</form>
		</div>
	</div>
</div>
