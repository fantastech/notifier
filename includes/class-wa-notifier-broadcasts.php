<?php
/**
 * Broadcasts CPT class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Broadcasts {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_filter( 'bulk_actions-wa_broadcast', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit') , 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts') , 10, 2 );
	}

	/**
	 * Register custom post type
	 */
	public function register_cpt () {
		wa_notifier_register_post_type ('wa_broadcast', 'Broadcast', 'Broadcasts');
	}
	
	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( WA_NOTIFIER_PREFIX . 'api_credentials_validated');
		if(!$api_credentials_validated) {
			return;
		}

		add_submenu_page( WA_NOTIFIER_NAME, 'Broadcast', 'Broadcast', 'manage_options', 'edit.php?post_type=wa_broadcast' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        WA_NOTIFIER_NAME . '-broadcast-data',
	        'Broadcast Data',
	        'WA_Notifier_Broadcasts::output',
	        'wa_broadcast'
	    );

	    remove_meta_box( 'submitdiv', 'wa_broadcast', 'side' );
    	add_meta_box( 'submitdiv', 'Save Broadcast', 'post_submit_meta_box', 'wa_broadcast', 'side', 'high' );
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once WA_NOTIFIER_PATH . 'views/admin-broadcasts-meta-box.php';
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
		if ( 'wa_broadcast' == get_post_type() ) {
			if ( $text == 'Update' ) {
				return 'Update Broadcast'; 
			}
			elseif ($text == 'Publish') {
				return 'Save Broadcast'; 
			}
		}
		return $translation;
	}

}
