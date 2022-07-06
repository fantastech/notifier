<?php
/**
 * Contacts CPT class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Contacts {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_contact', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit') , 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts') , 10, 2 );
		add_filter( 'manage_wa_contact_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_contact_posts_custom_column', array( __CLASS__ , 'add_column_content' ) , 10, 2 );
		add_action( 'save_post_wa_contact', array(__CLASS__, 'save_meta'), 10, 2 );
		add_filter( 'wa_notifier_admin_html_templates', array(__CLASS__, 'admin_html_templates') );
		add_filter( 'admin_post_wa_notifier_import_contacts_csv', array( __CLASS__ , 'import_contacts_csv') );
		add_filter( 'admin_post_wa_notifier_import_contacts_users', array( __CLASS__ , 'import_contacts_users') );
		add_action( 'admin_notices', array( __CLASS__, 'show_admin_notices') );
	}

	/**
	 * Register custom post type
	 */
	public function register_cpt () {
		wa_notifier_register_post_type ( 'wa_contact', 'Contact', 'Contacts' );
		wa_notifier_register_taxonomy ( 'wa_contact_list', 'Contact List', 'Contact Lists', 'wa_contact' , array( 'hierarchical' => true, 'default_term' => array('name' => 'Default List', 'slug' => 'default_list') ) );
		wa_notifier_register_taxonomy ( 'wa_contact_tag', 'Contact Tag', 'Contact Tags', 'wa_contact' );
	}
	
	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( WA_NOTIFIER_PREFIX . 'api_credentials_validated');
		if(!$api_credentials_validated) {
			return;
		}
		add_submenu_page( WA_NOTIFIER_NAME, 'Contacts', 'Contacts', 'manage_options', 'edit.php?post_type=wa_contact' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        WA_NOTIFIER_NAME . '-contact-data',
	        'Contact Details',
	        'WA_Notifier_Contacts::output',
	        'wa_contact'
	    );

	    remove_meta_box( 'submitdiv', 'wa_contact', 'side' );
    	add_meta_box( 'submitdiv', 'Save Contact', 'post_submit_meta_box', 'wa_contact', 'side', 'high' );
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once WA_NOTIFIER_PATH . 'views/admin-contacts-meta-box.php';
	}
	
	/**
	 * Add columns to list page
	 */
	public static function add_columns ($columns) {
		$new_columns = array (
			'cb'							=> $columns['cb'],
			'wa_contact_first_name' 		=> 'First Name',
			'wa_contact_last_name' 			=> 'Last Name',
			'wa_contact_phone_number' 		=> 'Phone Number',
			'wa_contact_associated_user' 	=> 'Associated User'
		);

		unset($columns['cb']);
		unset($columns['title']);
		unset($columns['date']);

		$columns = $new_columns + $columns;

		return $columns;
	}

	/**
	 * Add column content
	 */
	public static function add_column_content ( $column, $post_id ) {
		if ( 'wa_contact_first_name' === $column ) {
		    $first_name = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'first_name', true);
		    echo $first_name;
		}

		if ( 'wa_contact_last_name' === $column ) {
		    $last_name = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'last_name', true);
		    echo $last_name;
		}

		if ( 'wa_contact_phone_number' === $column ) {
		    $wa_number = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'wa_number', true);
		    echo $wa_number;
		}

		if ( 'wa_contact_associated_user' === $column ) {
		    $user_id = get_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'associated_user', true);
		    if($user_id) {
		    	$user = get_user_by('id', $user_id);
		    	echo '<a href="'.get_edit_user_link($user_id).'">'.$user->display_name.'</a>';
		    }
		    else {
		    	echo '—';
		    }
		}
	}

	/**
	 * Remove inline edit from Bulk Edit
	 */
	public static function remove_bulk_actions( $actions ){
        unset( $actions['inline'] );
        return $actions;
    }

    /**
	 * Remove inline Quick Edit
	 */
    public static function remove_quick_edit( $actions, $post ) { 
    	unset($actions['inline hide-if-no-js']);
    	return $actions;
	}

	/**
	 * Change text of buttons and links
	 */
	public static function change_texts( $translation, $text ) {
		if ( 'wa_contact' == get_post_type() ) {
			if ( $text == 'Update' ) {
				return 'Update Contact'; 
			}
			elseif ($text == 'Publish') {
				return 'Save Contact'; 
			}
		}
		return $translation;
	}

	/**
	 * Save meta
	 */
	public static function save_meta( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$contact_data = array();

		foreach ($_POST as $key => $data) {
			if (strpos($key, WA_NOTIFIER_PREFIX) !== false) {
				$contact_data[$key] = sanitize_text_field( wp_unslash ($data) );
			    update_post_meta( $post_id, $key, $contact_data[$key]);
			}
		}
	}

	/**
	 * Admin HTML templates
	 */
	public static function admin_html_templates($templates) {
		$import_from_users_url = '?' . http_build_query(array_merge($_GET, array("import_contacts_from_users"=>"1")));
		ob_start();
		?>
		<a href="#" class="page-title-action" id="import-contacts">Import Contacts</a>
		<div class="contact-import-options hide">
			<div class="p-20">
				<h3>Select an import method:</h3>
				<label><input type="radio" name="csv_import_method" class="csv-import-method" value="csv" checked="checked"> Import from CSV file</label>
				<label><input type="radio" name="csv_import_method" class="csv-import-method" value="users"> Import from Users</label>
				<div class="col-import col-import-csv">
					<p><a href="<?php echo WA_NOTIFIER_URL.'contacts-import-sample.csv'; ?>">Click here</a> to download sample CSV file. Fill in the CSV with your contact data wtihtout changing the format of the CSV. In the WhatsApp number column, add phone numbers with country codes (but without the + sign).</p>
					<form id="import-contacts-csv" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" enctype="multipart/form-data">
						<input type="file" name="wa_notifier_contacts_csv" id="wa-notifier-contacts-csv" />
						<input type="submit" name="upload_csv" value="Import CSV" class="button-primary">
						<?php wp_nonce_field('wa_notifier_contacts_csv'); ?>
						<input type="hidden" name="action" value="wa_notifier_import_contacts_csv" />
					</form>
				</div>
				<div class="col-import col-import-users hide">
					<p>Import contact data from exisiting website <a href="users.php">Users</a>. Map the suitable <a href="https://developer.wordpress.org/plugins/users/working-with-user-metadata/" target="_blank">user meta key</a> with the respective <b>Contact</b> field and click the button below to import.</p>
					<form id="import-contacts-users" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" enctype="multipart/form-data">
						<table class="users-import-table">
							<tr>
								<td><label for="wa_contact_first_name_key">First Name:</label></td>
								<td><input type="text" id="wa_contact_first_name_key" name="wa_contact_first_name_key" placeholder="Enter user meta key for first name. E.g. first_name"></td>
							</tr>
							<tr>
								<td><label for="wa_contact_last_name_key">Last Name:</label></td>
								<td><input type="text" id="wa_contact_last_name_key" name="wa_contact_last_name_key" placeholder="Enter user meta key for last name. E.g. last_name"></td>
							</tr>
							<tr>
								<td><label for="wa_contact_wa_number_key">WhatsApp Number (with ext code):</label></td>
								<td><input type="text" id="wa_contact_wa_number_key" name="wa_contact_wa_number_key" placeholder="Enter user meta key for WhatsApp number. E.g. phone_number"></td>
							</tr>
							<tr>
								<td><label for="wa_contact_list_name">List Name:</label></td>
								<td>
									<input type="text" id="wa_contact_list_name" name="wa_contact_list_name" placeholder="Enter a list name. E.g. Website Leads"></td>
							</tr>
							<tr>
								<td><label for="wa_contact_tags">Tags:</label></td>
								<td><input type="text" id="wa_contact_tags" name="wa_contact_tags" placeholder="Enter comma separated list of tags"></td>
							</tr>
						</table>
						<input type="submit" name="upload_csv" value="Import from Users" class="button-primary">
						<?php wp_nonce_field('wa_notifier_contacts_users'); ?>
						<input type="hidden" name="action" value="wa_notifier_import_contacts_users" />
					</form>
				</div>
			</div>
		</div>
		<?php
		$templates['import_contact'] = ob_get_clean();
		return $templates;
	}

	/**
	 * Show user meta keys dropdown
	 */
	public static function show_user_meta_keys_dropdown ($id = '') {
		$meta_keys = array_keys( get_user_meta( get_current_user_id() ) );
		$meta_keys = apply_filters('wa_notifier_user_meta_keys', $meta_keys);
		echo '<select id="'.$id.'" name="'.$id.'">';
		foreach($meta_keys as $key) {
			echo '<option value="'.$key.'">'.$key.'</option>';
		}
		echo '</select>';
	}

	/**
	 * Handle CSV import
	 */
	public static function import_contacts_csv() {
		if(!isset($_FILES['wa_notifier_contacts_csv'])) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		if(!check_admin_referer('wa_notifier_contacts_csv')) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		$tmpName = $_FILES['wa_notifier_contacts_csv']['tmp_name'];
		$contact_data = array_map('str_getcsv', file($tmpName));
		$first_row = $contact_data[0];
		if($first_row[0] != 'First Name' || $first_row[1] != 'Last Name') {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=2'));
		}
		unset($contact_data[0]);
		$count = 0;
		$skipped = 0;
		foreach($contact_data as $contact) {
			$first_name = isset($contact[0]) ? sanitize_text_field( wp_unslash ($contact[0]) ) : '';
			$last_name = isset($contact[1]) ? sanitize_text_field( wp_unslash ($contact[1]) ) : '';
			$phone_number = isset($contact[2]) ? '+'. (int) $contact[2] : '';
			$list = isset($contact[3]) ? sanitize_text_field( wp_unslash ($contact[3]) ) : '';
			$tags = isset($contact[4]) ? explode( ',', sanitize_text_field( wp_unslash ($contact[4]) ) ) : '';

			if('' == $phone_number){
				$skipped++;
				continue;
			}

			$count++;

			$existing_contact = get_posts( array(
				'post_type'		=> 'wa_contact',
				'fields'		=> 'ids',
				'numberposts'	=> 1,
				'meta_query'	=> array(
				    array(
						'key'   => WA_NOTIFIER_PREFIX . 'wa_number',
						'value' => $phone_number,
				    ),
				)
			) );

			if(empty($existing_contact)) {
				$post_id = wp_insert_post ( array(
					'post_title' => $first_name . ' ' . $last_name,
					'post_type' => 'wa_contact',
					'post_status' => 'publish'
				) );
			}
			else {
				$post_id = $existing_contact[0];
				wp_update_post ( array(
					'ID'         => $post_id,
					'post_title' => $first_name . ' ' . $last_name
				) );
				unset($existing_contact);
			}

			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'first_name', $first_name);
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'last_name', $last_name);
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'wa_number', $phone_number);
			$term_id = wp_create_term($list, 'wa_contact_list');
			wp_set_post_terms( $post_id, $term_id, 'wa_contact_list');
			wp_set_post_terms( $post_id, $tags, 'wa_contact_tag');

			unset($post_id);

		}

		wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=1&wa_import_count='.$count.'&wa_import_skipped='.$skipped));
	}

	/**
	 * Handle users import
	 */
	public static function import_contacts_users() {
		if(!check_admin_referer('wa_notifier_contacts_users')) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		$data = $_POST;

		$first_name_key = isset($_POST['wa_contact_first_name_key']) ? sanitize_text_field( wp_unslash ($_POST['wa_contact_first_name_key']) ) : '';
		$last_name_key = isset($_POST['wa_contact_last_name_key']) ? sanitize_text_field( wp_unslash ($_POST['wa_contact_last_name_key']) ) : '';
		$phone_number_key = isset($_POST['wa_contact_wa_number_key']) ? sanitize_text_field( wp_unslash ($_POST['wa_contact_wa_number_key']) ) : '';
		$list = isset($_POST['wa_contact_list_name']) ? sanitize_text_field( wp_unslash ($_POST['wa_contact_list_name']) ) : '';
		$tags = isset($_POST['wa_contact_tags']) ? explode( ',', sanitize_text_field( wp_unslash ($_POST['wa_contact_tags']) ) ) : '';

		if('' == $first_name_key || '' == $last_name_key || '' == $phone_number_key) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=3'));
			die;
		}

		global $wp_roles;
     	$all_roles = array_keys($wp_roles->get_names());
		$user_ids = get_users( array(
			'number' => -1,
			'fields'	=> 'ids',
			'role__in'	=> $all_roles
		));

		$count = 0;
		$skipped = 0;
		foreach($user_ids as $uid) {
			$first_name = get_user_meta($uid, $first_name_key, true);
			$last_name = get_user_meta($uid, $last_name_key, true);
			$phone_number = get_user_meta($uid, $phone_number_key, true);

			if('' == $phone_number){
				$skipped++;
				continue;
			}
			$count++;

			$existing_contact = get_posts( array(
				'post_type'		=> 'wa_contact',
				'fields'		=> 'ids',
				'numberposts'	=> 1,
				'meta_query'	=> array(
				    array(
						'key'   => WA_NOTIFIER_PREFIX . 'wa_number',
						'value' => $phone_number,
				    ),
				)
			) );

			if(empty($existing_contact)) {
				$post_id = wp_insert_post ( array(
					'post_title' => $first_name . ' ' . $last_name,
					'post_type' => 'wa_contact',
					'post_status' => 'publish'
				) );
			}
			else {
				$post_id = $existing_contact[0];
				wp_update_post ( array(
					'ID'         => $post_id,
					'post_title' => $first_name . ' ' . $last_name
				) );
				unset($existing_contact);
			}

			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'first_name', $first_name);
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'last_name', $last_name);
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'wa_number', $phone_number);
			update_post_meta( $post_id, WA_NOTIFIER_PREFIX . 'associated_user', $uid);
			$term_id = wp_create_term($list, 'wa_contact_list');
			wp_set_post_terms( $post_id, $term_id, 'wa_contact_list');
			wp_set_post_terms( $post_id, $tags, 'wa_contact_tag');

			unset($post_id);
			unset($uid);
		}

		wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=1&wa_import_count='.$count.'&wa_import_skipped='.$skipped));
	}

	/**
	 * Show admin notices
	 */
	public static function show_admin_notices () {
		$current_screen = get_current_screen();
		$cpt = ( '' !== $current_screen->post_type) ? $current_screen->post_type : '';
		if ( 'wa_contact' !== $cpt ) {
 			return;
 		}

 		if ( ! isset($_GET['wa_contacts_import']) ) {
 			return;
 		}

 		if('1' == $_GET['wa_contacts_import']) {
 			$count = isset($_GET['wa_import_count']) ? $_GET['wa_import_count'] : 0;
 			$skipped = isset($_GET['wa_import_skipped']) ? $_GET['wa_import_skipped'] : 0;
 			if($count != 0){
 				$message = $count . ' contacts imported / updated. ';
 				if($skipped) {
 					$message .= $skipped . ' contacts skipped.';
 				}
 			}
 			else {
 				$message = 'No new contacts were imported / updated.';
 			}
 			?>
			<div class="notice notice-success is-dismissible">
			    <p><?php echo $message; ?></p>
			</div>
			<?php
 		}
 		elseif('2' == $_GET['wa_contacts_import']) {
 			?>
			<div class="notice notice-error is-dismissible">
			    <p>There was an error during the import. Please make sure your CSV format matches the <a href="<?php echo WA_NOTIFIER_URL.'/contacts-import-sample.csv'; ?>">sample document</a> format before uploading.</p>
			</div>
			<?php
 		}
 		elseif('3' == $_GET['wa_contacts_import']) {
 			?>
			<div class="notice notice-error is-dismissible">
			    <p>There was an error during the import. Please enter all user meta keys before you start the import.</p>
			</div>
			<?php
 		}
	}

	/**
	 * Get contact lists
	 */
	public static function get_contact_lists ($show_select = false, $show_count = false)	 {
		$contact_list_terms = get_terms( array(
		    'taxonomy' => 'wa_contact_list',
		    'hide_empty' => true,
		) );

		$contact_lists = array();

		if ($show_select) {
			$contact_lists[''] = 'Select list';
		}

		foreach ($contact_list_terms as $term) {
			$contact_lists[$term->slug] = $term->name;
			if($show_count) {
				$contact_lists[$term->slug] .= ' ('.$term->count.' contacts)';
			}
		}

		return $contact_lists;
	}

	/**
	 * Get website users list
	 */
	public static function get_website_users_list ($show_select = false )	 {
		$users = get_users();

		$users_list = array();

		if ($show_select) {
			$users_list[''] = 'None';
		}

		foreach ($users as $user) {
			$users_list[$user->id] = $user->display_name;
		}

		return $users_list;
	}

}
