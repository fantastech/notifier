<?php
/**
 * Contacts CPT class
 *
 * @package    Wa_Notifier
 */
class Notifier_Contacts {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_contact', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit'), 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts'), 10, 2 );
		add_filter( 'manage_wa_contact_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_contact_posts_custom_column', array( __CLASS__ , 'add_column_content' ), 10, 2 );
		add_action( 'save_post_wa_contact', array(__CLASS__, 'save_meta'), 10, 2 );
		add_filter( 'post_updated_messages', array(__CLASS__, 'update_save_messages') );
		add_filter( 'notifier_admin_html_templates', array(__CLASS__, 'admin_html_templates') );
		add_filter( 'admin_post_notifier_import_contacts_csv', array( __CLASS__ , 'import_contacts_csv') );
		add_filter( 'admin_post_notifier_import_contacts_woocommerce', array( __CLASS__ , 'import_contacts_woocommerce') );
		add_action( 'admin_notices', array( __CLASS__, 'show_admin_notices') );
	}

	/**
	 * Register custom post type
	 */
	public static function register_cpt () {
		notifier_register_post_type ( 'wa_contact', 'Contact', 'Contacts' );
		notifier_register_taxonomy ( 'wa_contact_list', 'Contact List', 'Contact Lists', 'wa_contact', array( 'hierarchical' => true, 'default_term' => array('name' => 'Default List', 'slug' => 'default_list') ) );
		notifier_register_taxonomy ( 'wa_contact_tag', 'Contact Tag', 'Contact Tags', 'wa_contact' );
	}

	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( NOTIFIER_PREFIX . 'api_credentials_validated');
		if (!$api_credentials_validated) {
			return;
		}
		add_submenu_page( NOTIFIER_NAME, 'Contacts', 'Contacts', 'manage_options', 'edit.php?post_type=wa_contact' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        NOTIFIER_NAME . '-contact-data',
	        'Contact Details',
	        'Notifier_Contacts::output',
	        'wa_contact'
	    );

	    remove_meta_box( 'submitdiv', 'wa_contact', 'side' );
    	add_meta_box( 'submitdiv', 'Save Contact', 'post_submit_meta_box', 'wa_contact', 'side', 'high' );
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once NOTIFIER_PATH . 'views/admin-contacts-meta-box.php';
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
		    $first_name = get_post_meta( $post_id, NOTIFIER_PREFIX . 'first_name', true);
		    echo esc_html( $first_name );
		}

		if ( 'wa_contact_last_name' === $column ) {
		    $last_name = get_post_meta( $post_id, NOTIFIER_PREFIX . 'last_name', true);
		    echo esc_html( $last_name );
		}

		if ( 'wa_contact_phone_number' === $column ) {
		    $wa_number = get_post_meta( $post_id, NOTIFIER_PREFIX . 'wa_number', true);
		    echo esc_html( $wa_number );
		}

		if ( 'wa_contact_associated_user' === $column ) {
		    $user_id = get_post_meta( $post_id, NOTIFIER_PREFIX . 'associated_user', true);
		    if ($user_id) {
		    	$user = get_user_by('id', $user_id);
		    	echo '<a href="' . esc_url ( get_edit_user_link($user_id) ) . '">' . esc_html($user->display_name) . '</a>';
		    } else {
		    	echo '—';
		    }
		}
	}

	/**
	 * Remove inline edit from Bulk Edit
	 */
	public static function remove_bulk_actions( $actions ) {
        unset( $actions['inline'] );
        return $actions;
    }

    /**
	 * Remove inline Quick Edit
	 */
    public static function remove_quick_edit( $actions, $post ) {
    	if ('wa_contact' === $post->post_type) {
        	unset($actions['inline hide-if-no-js']);
        }
    	return $actions;
	}

	/**
	 * Change text of buttons and links
	 */
	public static function change_texts( $translation, $text ) {
		if ( 'wa_contact' === get_post_type() ) {
			if ( 'Update' === $text ) {
				return 'Update Contact';
			} elseif ( 'Publish' === $text ) {
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
			if (strpos($key, NOTIFIER_PREFIX) !== false) {
				$contact_data[$key] = sanitize_text_field( wp_unslash ($data) );
			    update_post_meta( $post_id, $key, $contact_data[$key]);
			}
		}
	}

	/**
	 * Update save action messages
	 */
	public static function update_save_messages ($messages) {
		$messages['wa_contact'][1] = 'Contact updated.';
	    $messages['wa_contact'][6] = 'Contact saved.';
		return $messages;
	}

	/**
	 * Admin HTML templates
	 */
	public static function admin_html_templates($templates) {
		$templates[] = 'import-contacts';
		return $templates;
	}

	/**
	 * Handle CSV import
	 */
	public static function import_contacts_csv() {
		if (!isset($_FILES['notifier_contacts_csv'])) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		if (!check_admin_referer('notifier_contacts_csv')) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		//phpcs:ignore
		if (!isset($_FILES['notifier_contacts_csv']['tmp_name'])) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		$temp_name = realpath( wp_unslash( $_FILES['notifier_contacts_csv']['tmp_name'] ) );

		$contact_data = array_map('str_getcsv', file($temp_name));
		$first_row = $contact_data[0];
		if ( 'First Name' !== $first_row[0] || 'Last Name' !== $first_row[1]) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=2'));
		}
		unset($contact_data[0]); // Remove first line
		$count = 0;
		$skipped = 0;
		foreach ($contact_data as $contact) {
			$first_name = isset($contact[0]) ? sanitize_text_field( wp_unslash ($contact[0]) ) : '';
			$last_name = isset($contact[1]) ? sanitize_text_field( wp_unslash ($contact[1]) ) : '';
			$phone_number = isset($contact[2]) ? '+' . (int) $contact[2] : '';
			$list = isset($contact[3]) ? sanitize_text_field( wp_unslash ($contact[3]) ) : '';
			$tags = isset($contact[4]) ? explode( ',', sanitize_text_field( wp_unslash ($contact[4]) ) ) : '';

			if ('' === $phone_number) {
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
						'key'   => NOTIFIER_PREFIX . 'wa_number',
						'value' => $phone_number,
				    ),
				)
			) );

			if (empty($existing_contact)) {
				$post_id = wp_insert_post ( array(
					'post_title' => $first_name . ' ' . $last_name,
					'post_type' => 'wa_contact',
					'post_status' => 'publish'
				) );
			} else {
				$post_id = $existing_contact[0];
				wp_update_post ( array(
					'ID'         => $post_id,
					'post_title' => $first_name . ' ' . $last_name
				) );
				unset($existing_contact);
			}

			update_post_meta( $post_id, NOTIFIER_PREFIX . 'first_name', $first_name);
			update_post_meta( $post_id, NOTIFIER_PREFIX . 'last_name', $last_name);
			update_post_meta( $post_id, NOTIFIER_PREFIX . 'wa_number', $phone_number);
			$term_id = wp_create_term($list, 'wa_contact_list');
			wp_set_post_terms( $post_id, $term_id, 'wa_contact_list');
			wp_set_post_terms( $post_id, $tags, 'wa_contact_tag');

			unset($post_id);

		}

		wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=1&wa_import_count=' . $count . '&wa_import_skipped=' . $skipped));
	}

	/**
	 * Handle Woocommerce customers import
	 */
	public static function import_contacts_woocommerce() {
		global $wpdb;
		if (!check_admin_referer('notifier_contacts_users')) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact'));
			die;
		}

		$list = isset($_POST['wa_contact_list_name']) ? sanitize_text_field( wp_unslash ($_POST['wa_contact_list_name']) ) : '';
		$list_name = isset($_POST['wa_contact_list_name_input']) ? sanitize_text_field( wp_unslash ($_POST['wa_contact_list_name_input']) ) : '';
		$tags = isset($_POST['wa_contact_tags']) ? explode( ',', sanitize_text_field( wp_unslash ($_POST['wa_contact_tags']) ) ) : '';

		if ('' == $list) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=3'));
			die;
		}

		$query_str = "
			SELECT
				post_id,
				max(case when meta_key = '_billing_first_name' then meta_value end) first_name,
				max(case when meta_key = '_billing_last_name' then meta_value end) last_name,
				max(case when meta_key = '_billing_country' then meta_value end) country_code,
				max(case when meta_key = '_billing_phone' then meta_value end) phone_number,
				max(case when meta_key = '_customer_user' then meta_value end) user_id
			FROM
				$wpdb->postmeta
			WHERE
				post_id IN
					(SELECT MAX($wpdb->posts.ID) FROM $wpdb->posts, $wpdb->postmeta
						WHERE $wpdb->posts.post_type = 'shop_order'
						AND ($wpdb->postmeta.meta_key = '_billing_phone' AND $wpdb->postmeta.meta_value IS NOT null )
						AND $wpdb->posts.ID = $wpdb->postmeta.post_id
						GROUP BY $wpdb->postmeta.meta_value)
			    AND ( meta_key = '_billing_first_name'
			       	OR meta_key = '_billing_last_name'
			        OR meta_key = '_billing_country'
			        OR meta_key = '_billing_phone'
			        OR meta_key = '_customer_user' )
			GROUP BY
				post_id";

		$customers = $wpdb->get_results($query_str);

		if (count($customers) == 0) {
			wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=4'));
			die;
		}

		$count = 0;
		$skipped = 0;
		foreach ($customers as $customer) {
			$first_name = $customer->first_name;
			$last_name = $customer->last_name;
			$phone_number = trim($customer->phone_number);
			$country_code = $customer->country_code;
			$user_id = $customer->user_id;

			if ('' == $phone_number) {
				$skipped++;
				continue;
			}

			// Cleanup phone number and combine with country code
			$phone_number = self::get_formatted_phone_number($phone_number, $country_code);

			$count++;

			$existing_contact = get_posts( array(
				'post_type'		=> 'wa_contact',
				'fields'		=> 'ids',
				'numberposts'	=> 1,
				'post_status'	=> 'publish',
				'meta_query'	=> array(
				    array(
						'key'   => NOTIFIER_PREFIX . 'wa_number',
						'value' => $phone_number,
				    ),
				)
			) );

			if (empty($existing_contact)) {
				$post_id = wp_insert_post ( array(
					'post_title' => $first_name . ' ' . $last_name,
					'post_type' => 'wa_contact',
					'post_status' => 'publish'
				) );
			} else {
				$post_id = $existing_contact[0];
				wp_update_post ( array(
					'ID'         => $post_id,
					'post_title' => $first_name . ' ' . $last_name
				) );
				unset($existing_contact);
			}

			update_post_meta( $post_id, NOTIFIER_PREFIX . 'first_name', $first_name);
			update_post_meta( $post_id, NOTIFIER_PREFIX . 'last_name', $last_name);
			update_post_meta( $post_id, NOTIFIER_PREFIX . 'wa_number', $phone_number);
			if ( 0 != $user_id ) {
				update_post_meta( $post_id, NOTIFIER_PREFIX . 'associated_user', $user_id);
			}

			if ('_add_new' == $list) {
				$term_id = wp_create_term($list_name, 'wa_contact_list');
			} else {
				$term = get_term_by( 'slug', $list, 'wa_contact_list' );
				$term_id = $term->term_id;
			}
			wp_set_post_terms( $post_id, $term_id, 'wa_contact_list');
			wp_set_post_terms( $post_id, $tags, 'wa_contact_tag');

			unset($post_id);
			unset($user_id);
		}

		wp_safe_redirect(admin_url('edit.php?post_type=wa_contact&wa_contacts_import=1&wa_import_count=' . $count . '&wa_import_skipped=' . $skipped));
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

 		if ('1' == $_GET['wa_contacts_import']) {
 			$count = isset($_GET['wa_import_count']) ? intval($_GET['wa_import_count']) : 0;
 			$skipped = isset($_GET['wa_import_skipped']) ? intval($_GET['wa_import_skipped']) : 0;
 			if (0 != $count) {
 				$message = $count . ' contacts imported / updated. ';
 				if ($skipped) {
 					$message .= $skipped . ' contacts skipped.';
 				}
 			} else {
 				$message = 'No new contacts were imported / updated.';
 			}
 			?>
			<div class="notice notice-success is-dismissible">
			    <p><?php echo esc_html($message); ?></p>
			</div>
			<?php
 		} elseif ('2' == $_GET['wa_contacts_import']) {
 			?>
			<div class="notice notice-error is-dismissible">
			    <p>There was an error during the import. Please make sure your CSV format matches the <a href="<?php echo esc_url( NOTIFIER_URL . '/contacts-import-sample.csv' ); ?>">sample document</a> format before uploading.</p>
			</div>
			<?php
 		} elseif ('3' == $_GET['wa_contacts_import']) {
 			?>
			<div class="notice notice-error is-dismissible">
			    <p>There was an error during the import. Please enter a List name before you start the import.</p>
			</div>
			<?php
 		} elseif ('4' == $_GET['wa_contacts_import']) {
 			?>
			<div class="notice notice-error is-dismissible">
			    <p>No customers found.</p>
			</div>
			<?php
 		}
	}

	/**
	 * Get contacts
	 */
	public static function get_contacts ($show_select = false) {
		global $wpdb;
		$search_keyword = isset($_POST['s']) ? sanitize_text_field ( wp_unslash($_POST['s']) ) : '';

		$querystr = "
			SELECT DISTINCT $wpdb->posts.ID
	    	FROM $wpdb->posts, $wpdb->postmeta
            WHERE ($wpdb->posts.post_content LIKE '%$search_keyword%'
            	OR $wpdb->posts.post_title LIKE '%$search_keyword%'
            	OR $wpdb->postmeta.meta_value LIKE '%$search_keyword%')
            AND $wpdb->posts.post_type = 'wa_contact'
            AND $wpdb->posts.post_status = 'publish'
	        AND $wpdb->posts.ID = $wpdb->postmeta.post_id
	        LIMIT 20
		";

		$contacts = $wpdb->get_results($querystr);
		$contacts = wp_list_pluck ( $contacts, 'ID');

		$contacts_data = array();

		if ($show_select) {
			$contacts_data[''] = 'Select contact';
		}

		foreach ($contacts as $contact_id) {
			$first_name = get_post_meta( $contact_id, NOTIFIER_PREFIX . 'first_name', true);
			$last_name = get_post_meta( $contact_id, NOTIFIER_PREFIX . 'last_name', true);
			$wa_number = get_post_meta( $contact_id, NOTIFIER_PREFIX . 'wa_number', true);
			$contacts_data[$contact_id] = $first_name . ' ' . $last_name . ' (' . $wa_number . ')';
		}

		return $contacts_data;
	}

	/**
	 * Get contact lists
	 */
	public static function get_contact_lists ($show_select = false, $show_count = false) {
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
			if ($show_count) {
				$contact_lists[$term->slug] .= ' (' . $term->count . ' contacts)';
			}
		}

		return $contact_lists;
	}

	/**
	 * Get website users list
	 */
	public static function get_website_users_list ($show_select = false ) {
		$users = get_users();

		$users_list = array();

		if ($show_select) {
			$users_list[''] = 'None';
		}

		foreach ($users as $user) {
			$users_list[$user->ID] = $user->display_name;
		}

		return $users_list;
	}

	/**
	 * Get phone number with country extension code
	 */
	public static function get_formatted_phone_number ($phone_number, $country_code) {
		$phone_number = self::sanitize_phone_number( $phone_number );
		$phone_number = ltrim($phone_number, '0');
		if ( in_array( $country_code, array( 'US', 'CA' ), true ) ) {
			$phone_number = ltrim( $phone_number, '+1' );
		} else {
			$calling_code = WC()->countries->get_country_calling_code( $country_code );
			$calling_code = is_array( $calling_code ) ? $calling_code[0] : $calling_code;

			if ( $calling_code ) {
				$phone_number = str_replace( $calling_code, '', preg_replace( '/^0/', '', $phone_number ) );
			}
		}
		$phone_number = ltrim($phone_number, '0');
		return $calling_code . $phone_number;
	}

	/**
	 * Sanitize phone number
	 */
	public static function sanitize_phone_number ($phone_number) {
		return preg_replace( '/[^\d+]/', '', $phone_number );
	}

}
