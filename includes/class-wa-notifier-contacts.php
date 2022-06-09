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
	}

	/**
	 * Register custom post type
	 */
	public function register_cpt () {
		wa_notifier_register_post_type ( 'wa_contact', 'Contact', 'Contacts' );
		wa_notifier_register_taxonomy ( 'wa_contact_list', 'Contact List', 'Contact Lists', 'wa_contact' , array( 'hierarchical' => true ) );
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
			'cb'						=> $columns['cb'],
			'wa_contact_first_name' 	=> 'First Name',
			'wa_contact_last_name' 		=> 'Last Name',
			'wa_contact_phone_number' 	=> 'Phone Number'
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
		$templates['import_contact'] = '<a href="#" class="import-contacts page-title-action">Import Contacts</a>';
		return $templates;
	}

}
