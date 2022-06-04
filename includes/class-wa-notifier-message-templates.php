<?php
/**
 * Message templates page class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Message_Templates {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
        add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
        add_action( 'wp_trash_post', array( __CLASS__, 'delete_message_template' ) );
		add_filter( 'bulk_actions-wa_message_template', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit') , 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts') , 10, 2 );
	}


	/**
	 * Register custom post type
	 */
	public function register_cpt () {
		wa_notifier_register_post_type ( 'wa_message_template', 'Message Template', 'Message Templates');
	}
	
	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		$api_credentials_validated = get_option( WA_NOTIFIER_SETTINGS_PREFIX . 'api_credentials_validated');
		if(!$api_credentials_validated) {
			return;
		}

		add_submenu_page( WA_NOTIFIER_NAME, 'Whatsapp Message Templates', 'Message Templates', 'manage_options', 'edit.php?post_type=wa_message_template' );

	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        WA_NOTIFIER_NAME . '-message-template-data',
	        'Template Data',
	        'WA_Notifier_Message_Templates::output',
	        'wa_message_template'
	    );

	    add_meta_box(
	        WA_NOTIFIER_NAME . '-message-template-preview',
	        'Preview Template',
	        'WA_Notifier_Message_Templates::output_preview',
	        'wa_message_template',
	        'side'
	    );

	    remove_meta_box( 'submitdiv', 'wa_message_template', 'side' );
    	add_meta_box( 'submitdiv', 'Template Actions', 'post_submit_meta_box', 'wa_message_template', 'side', 'high' );
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once WA_NOTIFIER_PATH . 'views/admin-message-templates-meta-box.php';
	}

	/**
	 * Output preview meta box
	 */
	public static function output_preview () {
		?>
			<div class="wa-template-preview">
				<div class="message-container">
					<div class="message-head">
						Header text here
					</div>
					<div class="message-body">
						Body text here
					</div>
					<div class="message-footer">
						Footer text text
					</div>
					<div class="message-date">
						10:00 AM
					</div>
				</div>
			</div>
		<?php
	}
	
	/**
	 * Delete message template from WP and Meta
	 */
	public static function delete_message_template($post_id) {
	    // if (get_post_type($post_id) == 'wa_message_template') {
	    //     wp_delete_post( $post_id, true );
	    // }
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
		if ( 'wa_message_template' == get_post_type() ) {
			if ( $text == 'Update' || $text == 'Publish') {
				return 'Submit for Approval'; 
			}
			elseif ($text == 'Move to Trash') {
				return 'Delete'; 
			}
		}
		return $translation;
		
	}

}
