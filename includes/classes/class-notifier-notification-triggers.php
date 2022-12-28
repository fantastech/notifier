<?php
/**
 * Notification Triggers class
 *
 * @package    Wa_Notifier
 */
class Notifier_Notification_Triggers {

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__ , 'register_cpt') );
		add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
		add_action( 'plugins_loaded', array( __CLASS__ , 'setup_triggers_action_hooks' ), 100 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'create_meta_box' ) );
		add_action( 'post_submitbox_minor_actions', array( __CLASS__ , 'add_submitbox_meta' ) );
		add_action( 'save_post_wa_notifier_trigger', array(__CLASS__, 'save_meta'), 10, 2 );
		add_filter( 'bulk_actions-wa_notifier_trigger', array( __CLASS__, 'remove_bulk_actions' ) );
		add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit') , 10, 2);
		add_filter( 'gettext', array(__CLASS__, 'change_texts') , 10, 2 );
		add_filter( 'post_updated_messages', array(__CLASS__, 'update_save_messages') );
		add_filter( 'enter_title_here', array(__CLASS__, 'change_title_text') );
		add_action( 'wp_ajax_notifier_change_trigger_status', array(__CLASS__, 'notifier_change_trigger_status'));
		add_action( 'wp_ajax_notifier_fetch_trigger_fields', array(__CLASS__, 'notifier_fetch_trigger_fields'));
		add_filter( 'manage_wa_notifier_trigger_posts_columns', array( __CLASS__ , 'add_columns' ) );
		add_action( 'manage_wa_notifier_trigger_posts_custom_column', array( __CLASS__ , 'add_column_content' ) , 10, 2 );
	}

	/**
	 * Register custom post type
	 */
	public static function register_cpt () {
		if (!Notifier::is_api_active()){
			return;
		}

		notifier_register_post_type('wa_notifier_trigger', 'Trigger', 'Triggers');
	}

	/**
	 * Add page to admin menu
	 */
	public static function setup_admin_page () {
		if (!Notifier::is_api_active()){
			return;
		}

		add_submenu_page( NOTIFIER_NAME, 'Trigger', 'Triggers', 'manage_options', 'edit.php?post_type=wa_notifier_trigger' );
		add_submenu_page( NOTIFIER_NAME, 'Add trigger', 'Add trigger', 'manage_options', 'post-new.php?post_type=wa_notifier_trigger' );
	}

	/**
	 * Create meta boxes
	 */
	public static function create_meta_box () {
		add_meta_box(
	        NOTIFIER_NAME . '-trigger-data',
	        'Trigger Settings',
	        'Notifier_Notification_Triggers::output',
	        'wa_notifier_trigger'
	    );

	    remove_meta_box( 'submitdiv', 'wa_notifier_trigger', 'side' );
    	add_meta_box( 'submitdiv', 'Save Trigger', 'post_submit_meta_box', 'wa_notifier_trigger', 'side', 'high' );
	}

	/**
	 * Add message template meta data in submit box
	 */
	public static function add_submitbox_meta () {
		if ( 'wa_notifier_trigger' != get_post_type() ) {
			return;
		}
		global $post_id;

		if(!isset($post_id)){
			return;
		}

		$enabled = get_post_meta( $post_id, NOTIFIER_PREFIX . 'trigger_enabled' , true);
		$checked = '';
		if('yes' == $enabled) {
			$checked = 'checked="checked"';
		}
		echo '<div class="notifier-trigger-status d-flex justify-content-between align-items-center">';
		echo '<b>Enable:</b>';
		echo '<div class="notifier-toggle-switch"><input class="notifier-enable-trigger" type="checkbox" '.$checked.' data-post-id="'.$post_id.'"></div>';
		echo '</div>';
	}

	/**
	 * Output meta box
	 */
	public static function output () {
		include_once NOTIFIER_PATH . 'views/admin-triggers.php';
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

		$trigger_data = array();

		foreach ($_POST as $key => $data) {
			if (strpos($key, NOTIFIER_PREFIX) !== false) {
				if(is_array($data)) {
					$trigger_data[$key] = notifier_sanitize_array($data);
				}
				else{
					$trigger_data[$key] = sanitize_text_field( wp_unslash ($data) );
				}
			    update_post_meta( $post_id, $key, $trigger_data[$key]);
			}
		}

		$all_triggers = self::get_notification_triggers();

		$notifier_trigger = $trigger_data[ NOTIFIER_PREFIX . 'trigger' ];
		$data_fields = isset($trigger_data[ NOTIFIER_PREFIX . 'data_fields' ]) ? $trigger_data[ NOTIFIER_PREFIX . 'data_fields' ] : array();
		$recipient_fields = isset($trigger_data[ NOTIFIER_PREFIX . 'recipient_fields' ]) ? $trigger_data[ NOTIFIER_PREFIX . 'recipient_fields' ] : array();

		$selected_trigger = self::get_notification_trigger($notifier_trigger);
		$other_triggers = self::get_other_notification_triggers($post_id);
		$final_triggers[] = $selected_trigger;

        foreach ($other_triggers as $other_trigger) {
            if ($other_trigger['id'] != $selected_trigger['id']) {
                $final_triggers[] = $other_trigger;
            }
        }

		$triggers_array = array();

		foreach ($all_triggers as $key => $triggers) {
			foreach ($triggers as $trigger){
				foreach($final_triggers as $final_trigger){
					if( $final_trigger['id'] != $trigger['id'] ){
						continue;
					}
					unset($final_trigger['action']);
					$final_trigger_merge_tags = array();
					if(!empty($final_trigger['merge_tags'])){
						$default_tags = Notifier_Notification_Merge_Tags::get_merge_tags(array('WordPress'));
						$final_trigger['merge_tags'] = array_merge($final_trigger['merge_tags'], $default_tags);
						foreach($final_trigger['merge_tags'] as $merge_tags_group => $merge_tags){
							foreach($merge_tags as $merge_tag){
								if(in_array($merge_tag['id'], $data_fields)){
									$final_trigger_merge_tags[$merge_tags_group][] = array(
										'id'			=> $merge_tag['id'],
										'label'			=> $merge_tag['label'],
										'preview_value'	=> isset($merge_tag['preview_value']) ? $merge_tag['preview_value'] : '',
										'return_type'	=> isset($merge_tag['return_type']) ? $merge_tag['return_type'] : '',
									);
								}
							}

						}
					}
					$final_trigger_recipient_fields = array();
					if(!empty($final_trigger['recipient_fields'])){
						foreach($final_trigger['recipient_fields'] as $recipient_tags_group => $recipient_tags){
							foreach($recipient_tags as $recipient_tag){
								if(in_array($recipient_tag['id'], $recipient_fields)){
									$final_trigger_recipient_fields[$recipient_tags_group][] = array(
										'id'		=> $recipient_tag['id'],
										'label'		=> $recipient_tag['label'],
										'preview_value'	=> isset($recipient_tag['preview_value']) ? $recipient_tag['preview_value'] : '',
										'return_type'	=> isset($merge_tag['return_type']) ? $merge_tag['return_type'] : '',
									);
								}
							}

						}
					}
					$final_trigger['merge_tags'] = $final_trigger_merge_tags;
					$final_trigger['recipient_fields'] = $final_trigger_recipient_fields;
					$triggers_array[$key][] = $final_trigger;
				}
			}
		}

		$params = array(
			'site_url'	=> site_url(),
			'source'	=> 'wp',
			'triggers'	=> $triggers_array
    	);

		$response = Notifier::send_api_request( 'update_triggers', $params, 'POST' );

		if($response->error){
			$notices[] = array(
				'message' => $response->message,
				'type' => 'error'
			);
			new Notifier_Admin_Notices($notices, true);
		}
		else{
			$notices[] = array(
				'message' => 'Trigger details saved and synced with your WANotifier.com account successfully!',
				'type' => 'success'
			);
			new Notifier_Admin_Notices($notices, true);
		}
	}

	/**
	 * Update save action messages
	 */
	public static function update_save_messages ($messages) {
		$messages['wa_notifier_trigger'][1] = '';
	    $messages['wa_notifier_trigger'][6] = '';
		return $messages;
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
    	if ('wa_notifier_trigger' == $post->post_type){
	     	unset($actions['inline hide-if-no-js']);
	    }
    	return $actions;
	}

	/**
	 * Change text of buttons and links
	 */
	public static function change_texts( $translation, $text ) {
		if ( 'wa_notifier_trigger' == get_post_type() ) {
			if ( $text == 'Update' ) {
				return 'Update';
			}
			elseif ($text == 'Publish') {
				return 'Save';
			}
		}
		return $translation;
	}

	/**
	 * Change add title here text
	 */
	public static function change_title_text( $title ){
		$screen = get_current_screen();
		if  ( 'wa_notifier_trigger' == $screen->post_type ) {
		  $title = 'Enter a trigger name';
		}
		return $title;
	}

	/**
	 * Add columns to list page
	 */
	public static function add_columns ($columns) {
		$date_col = $columns['date'];
		unset($columns['date']);
		$columns['trigger'] = 'Trigger';
		$columns['trigger_enabled'] = 'Status';
		$columns['date'] = $date_col;
		return $columns;
	}

	/**
	 * Add column content
	 */
	public static function add_column_content ( $column, $post_id ) {
		if ( 'trigger' === $column ) {
			$trigger = get_post_meta( $post_id, NOTIFIER_PREFIX . 'trigger' , true);
			echo self::get_notification_trigger_display_name($trigger);
		}
		if ( 'trigger_enabled' === $column ) {
		    $enabled = get_post_meta( $post_id, NOTIFIER_PREFIX . 'trigger_enabled' , true);
			$checked = '';
			if('yes' == $enabled) {
				$checked = 'checked="checked"';
			}
			echo '<div class="notifier-toggle-switch"><input class="notifier-enable-trigger" type="checkbox" '.$checked.' data-post-id="'.$post_id.'"></div>';
		}
	}

	/**
	 * Setup triggers action hooks
	 */
	public static function setup_triggers_action_hooks() {
		$main_triggers = self::get_notification_triggers();
		foreach ($main_triggers as $key => $triggers) {
			foreach ($triggers as $trigger) {
				$hook 		= isset($trigger['action']['hook']) ? $trigger['action']['hook'] : '';
				$callback 	= isset($trigger['action']['callback']) ? $trigger['action']['callback'] : '';
				$priority 	= isset($trigger['action']['priority']) ? $trigger['action']['priority'] : 10;
				$args_num 	= isset($trigger['action']['args_num']) ? $trigger['action']['args_num'] : 1;
				if ('' == $hook || '' == $callback) {
					continue;
				}
				if(self::is_trigger_enabled($trigger['id'])){
					add_action($hook, $callback, $priority, $args_num);
				}
			}
		}
	}

	/**
	 * Get notification triggers
	 */
	public static function get_notification_triggers() {
		$triggers = array();
		$triggers['WordPress'] = array (
			 array(
			 	'id'			=> 'new_post',
				'label' 		=> 'New post is published',
				'description'	=> 'Trigger notification when a new blog post is published.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('Post') ),
				'recipient_fields'	=> array(),
				'action'		=> array (
					'hook'		=> 'transition_post_status',
					'args_num'	=> 3,
					'callback' 	=> function ( $new_status, $old_status, $post ) {
						if ('post' !== get_post_type($post)) {
							return;
						}

						if ( 'publish' === $new_status && 'publish' !== $old_status ) {
							$args = array (
								'object_type' 	=> 'post',
								'object_id'		=> $post->ID
							);
							$merge_tags = Notifier_Notification_Merge_Tags::get_merge_tags( array('Post') );
							self::send_trigger_request('new_post', $args, $merge_tags);
						}
					}
				)
			),
			array(
			 	'id'			=> 'new_comment',
				'label' 		=> 'New comment is added',
				'description'	=> 'Trigger notification when a new comment is added.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('Comment') ),
				'recipient_fields'	=> array(),
				'action'		=> array (
					'hook'		=> 'comment_post',
					'args_num'	=> 3,
					'callback' 	=> function ( $comment_id, $comment_approved, $commentdata ) {
						if ( 'spam' != $comment_approved) {
							$args = array (
								'object_type' 	=> 'comment',
								'object_id'		=> $comment_id
							);
							$merge_tags = Notifier_Notification_Merge_Tags::get_merge_tags( array('Comment') );
							self::send_trigger_request('new_comment', $args, $merge_tags);
						}
					}
				)
			),
			array(
			 	'id'			=> 'new_user',
				'label' 		=> 'New user is registered',
				'description'	=> 'Trigger notification when a new user is created.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('User') ),
				'recipient_fields'	=> array(),
				'action'		=> array (
					'hook'		=> 'user_register',
					'callback' 	=> function ( $user_id ) {
						$args = array (
							'object_type' 	=> 'user',
							'object_id'		=> $user_id
						);
						$merge_tags = Notifier_Notification_Merge_Tags::get_merge_tags( array('User') );
						self::send_trigger_request('new_user', $args, $merge_tags);
					}
				)
			)
		);
		return apply_filters('notifier_notification_triggers', $triggers);
	}

	/**
	 * Get notification triggers for dropdown
	 */
	public static function get_notification_triggers_dropdown() {
		$main_triggers = self::get_notification_triggers();
		$dropdown_triggers = array('' => 'Select trigger');
		foreach ($main_triggers as $key => $triggers) {
			$dropdown_triggers[$key] = wp_list_pluck($triggers, 'label', 'id');
		}
		return $dropdown_triggers;
	}

	/**
	 * Get notification trigger
	 */
	public static function get_notification_trigger($trigger) {
		$found_trigger = array();
		$main_triggers = self::get_notification_triggers();
		foreach ($main_triggers as $key => $triggers) {
			foreach($triggers as $the_trigger){
				if($the_trigger['id'] == $trigger){
					$found_trigger = $the_trigger;
					break 2;
				}
			}
		}
		return $found_trigger;
	}

	/**
	 * Get notification trigger display name
	 */
	public static function get_notification_trigger_display_name($trigger){
		$main_triggers = self::get_notification_triggers();
		foreach ($main_triggers as $key => $triggers) {
			foreach($triggers as $the_trigger){
				if($the_trigger['id'] == $trigger){
					$name = $the_trigger['label'];
					break 2;
				}
			}
		}
		return $name;
	}

	/**
	 * Get enabled notification trigger
	 */
	public static function get_other_notification_triggers($post_id) {
		$enabled_triggers = array();
		$enabled_post_ids = get_posts(array (
			'post_type' 	=> 'wa_notifier_trigger',
			'post_status' 	=> 'publish',
			'numberposts' 	=> -1,
			'fields' 		=> 'ids',
			'post__not_in'	=> array($post_id)
		));
		if(! empty($enabled_post_ids)){
			foreach($enabled_post_ids as $enabled_post_id){
				 $trigger_name = get_post_meta($enabled_post_id, NOTIFIER_PREFIX . 'trigger', true);
				 $enabled_triggers[] = self::get_notification_trigger($trigger_name);
			}
		}
		return $enabled_triggers;
	}

	/**
	 * Check whether current trigger is enabled
	 */
	public static function is_trigger_enabled($trigger) {
		$enabled_triggers = get_option('notifier_enabled_triggers');

		if(empty($enabled_triggers)){
			return false;
		}

		if(in_array($trigger, $enabled_triggers)){
			return true;
		}

		return false;
	}

	/**
	 * Send triggered notifications
	 */
	public static function send_trigger_request($trigger, $context_args, $merge_tags, $recipient_fields = array()) {
		if(empty($context_args) || empty($merge_tags)){
			return false;
		}

		$data = array();
		$recipient_data = array();

		foreach($merge_tags as $group_name => $group_tags){
			foreach($group_tags as $tag){
				$data[$tag['id']] = Notifier_Notification_Merge_Tags::get_trigger_merge_tag_value($tag['id'], $context_args);
			}
		}

		foreach($recipient_fields as $recipient_field){
			foreach($recipient_field as $field){
				$recipient_data[$field['id']] = self::get_trigger_recipient_field_value($field['id'], $context_args);
			}
		}

		$params = array(
			'site_url'			=> site_url(),
			'source'			=> 'wp',
			'trigger'			=> $trigger,
			'merge_tags_data'	=> $data,
			'recipient_fields'	=> $recipient_data
    	);

		$response = Notifier::send_api_request( 'fire_notification', $params, 'POST' );

		if($response->error){
			error_log($response->message);
		}

	}

	/*
	 * Get recipient fields
	 */
	public static function get_recipient_fields($types = array()){
		$recipient_fields = array();
		$recipient_fields = apply_filters('notifier_notification_recipient_fields', $recipient_fields);

		$final_recipient_fields = array();

		if (empty($types)) {
			$final_recipient_fields = $recipient_fields;
		} else {
			foreach ($types as $type) {
				$final_recipient_fields[$type] = $recipient_fields[$type];
			}
		}

		return $recipient_fields;
	}

	/*
	 * Get trigger recipient fields
	 */
	public static function get_trigger_recipient_fields($trigger){
		$recipient_fields = array();
		$main_triggers = self::get_notification_triggers();
		foreach ($main_triggers as $key => $triggers) {
			foreach ($triggers as $t) {
				if ( $trigger == $t['id'] ) {
					$recipient_fields = $t['recipient_fields'];
					break 2;
				}
			}
		}

		$final_fields = array();

		foreach ($recipient_fields as $field_key => $recipient_fields_list) {
			foreach($recipient_fields_list as $field) {
				$final_fields[$field_key][$field['id']] = $field['label'];
			}
		}

		return $final_fields;
	}

	/**
	 * Get recipient_field value
	 */
	public static function get_trigger_recipient_field_value($recipient_field, $context_args) {
		$value = '';
		$main_triggers = self::get_notification_triggers();
		foreach ($main_triggers as $key => $triggers) {
			foreach ($triggers as $t) {
				$recipient_fields = (!empty($t['recipient_fields'])) ? $t['recipient_fields'] : array();
				if(empty($recipient_fields)){
					continue;
				}
				foreach($recipient_fields as $fields){
					foreach($fields as $field){
						if ( isset($field['id']) && $recipient_field == $field['id'] ) {
							$value = $field['value']($context_args);
							break 2;
						}
					}
				}
			}
		}
		return $value;
	}

	/**
	 * Enable / disable trigger
	 */
	public static function notifier_change_trigger_status(){
		$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
		$enabled = isset($_POST['enabled']) ? $_POST['enabled'] : 'no';
		if('wa_notifier_trigger' != get_post_type($post_id)){
			wp_send_json( array(
				'error' => true,
				'message'  => 'Invalid post type.'
			) );
		}
		update_post_meta( $post_id, NOTIFIER_PREFIX . 'trigger_enabled' , $enabled);
		wp_send_json( array(
			'error' => false,
			'message'  => 'Updated'
		) );
	}

	/**
	 * Fetch trigger fields for a speicific trigger
	 */
	public static function notifier_fetch_trigger_fields(){
		$post_id =  isset($_POST['post_id']) ? $_POST['post_id'] : 0;
		$trigger =  isset($_POST['trigger']) ? $_POST['trigger'] : '';

		$data_fields = get_post_meta( $post_id, NOTIFIER_PREFIX . 'data_fields', true);
		$recipient_fields = get_post_meta( $post_id, NOTIFIER_PREFIX . 'recipient_fields', true);

		$merge_tags = Notifier_Notification_Merge_Tags::get_trigger_merge_tags($trigger);
		$recipient_fields = Notifier_Notification_Triggers::get_trigger_recipient_fields($trigger);

		ob_start();

		echo '<div class="trigger-fields-wrap"><div class="d-flex justify-content-between"><label class="form-label w-auto">Data fields</label><div class="small"><a href="#" class="notifier-select-all-checkboxes">select all</a> / <a href="#" class="notifier-unselect-all-checkboxes">unselect all</a></div></div>';
		echo '<div class="notifier-merge-tags-wrap">';
		foreach($merge_tags as $merge_tag_group => $merge_tag){
			echo '<div class="notifier-merge-tags d-flex">';
			notifier_wp_multi_checkboxes( array(
				'id'                => NOTIFIER_PREFIX . 'data_fields',
	            'name'              => NOTIFIER_PREFIX . 'data_fields[]',
				'value'             => $data_fields,
				'label'             => $merge_tag_group,
				'description'       => '',
				'options'           => $merge_tag,
				'show_wrapper'		=> false
	        ) );
	        echo '</div>';
		}
		echo '</div>';
		echo '<span class="description">Select the data fields that you want to send to WANotifier.com when this is triggered. The fields you select here will be available to map with message templates variables when you create a <a href="https://app.wanotifier.com/notifications/add/" target="_blank">new notification</a>.</span></div>';

		if(!empty($recipient_fields)){
			echo '<div class="trigger-fields-wrap"><div class="d-flex justify-content-between"><label class="form-label w-auto">Recipient fields</label><div class="small"><a href="#" class="notifier-select-all-checkboxes">select all</a> / <a href="#" class="notifier-unselect-all-checkboxes">unselect all</a></div></div>';
			echo '<div class="notifier-merge-tags-wrap">';
			foreach($recipient_fields as $recipient_group_name => $recipient_group_fields){
				echo '<div class="notifier-merge-tags d-flex">';
				notifier_wp_multi_checkboxes( array(
					'id'                => NOTIFIER_PREFIX . 'recipient_fields',
		            'name'              => NOTIFIER_PREFIX . 'recipient_fields[]',
					'value'             => $recipient_fields,
					'label'             => $recipient_group_name,
					'description'       => '',
					'options'           => $recipient_group_fields,
					'show_wrapper'		=> false
		        ) );
		        echo '</div>';
			}
			echo '</div>';
			echo '<span class="description">Select the phone number fields that you want to send to WANotifier.com when this is triggered. The fields you select here will be available under Recipients when you create a notification.</span></div>';
		}

		$html = ob_get_clean();

		wp_send_json( array(
			'status' 	=> 'success',
			'html'		=> $html
		) );
	}

}
