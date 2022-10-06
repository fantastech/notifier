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
		add_action( 'plugins_loaded', array( __CLASS__ , 'setup_triggers_action_hooks' ), 100 );
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
				if(self::is_enabled_trigger($trigger['id'])){
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
							self::send_trigger_request($args, $merge_tags);
						}
					}
				)
			),
			array(
			 	'id'			=> 'new_comment',
				'label' 		=> 'New comment is added',
				'description'	=> 'Trigger notification when a new comment is added.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('Comment') ),
				'action'		=> array (
					'hook'		=> 'comment_post',
					'args_num'	=> 3,
					'callback' 	=> function ( $comment_id, $comment_approved, $commentdata ) {
						if ( 'spam' != $comment_approved) {
							$args = array (
								'object_type' 	=> 'comment',
								'object_id'		=> $comment_id
							);
							$merge_tags = Notifier_Notification_Merge_Tags::get_merge_tags( array('Post') );
							self::send_trigger_request($args, $merge_tags);
						}
					}
				)
			),
			array(
			 	'id'			=> 'new_user',
				'label' 		=> 'New user is registered',
				'description'	=> 'Trigger notification when a new user is created.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('User') ),
				'action'		=> array (
					'hook'		=> 'user_register',
					'callback' 	=> function ( $user_id ) {
						$args = array (
							'object_type' 	=> 'user',
							'object_id'		=> $user_id
						);
						$merge_tags = Notifier_Notification_Merge_Tags::get_merge_tags( array('Post') );
						self::send_trigger_request($args, $merge_tags);
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
		$dropdown_triggers = array('' => 'Select a trigger');
		foreach ($main_triggers as $key => $triggers) {
			$dropdown_triggers[$key] = wp_list_pluck($triggers, 'label', 'id');
		}
		return $dropdown_triggers;
	}

	/**
	 * Check whether current trigger is enabled
	 */
	public static function is_enabled_trigger($trigger) {
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
	public static function send_trigger_request ($context_args, $merge_tags) {
		if(empty($context_args) || empty($merge_tags)){
			return false;
		}

		$data = array();

		foreach($merge_tags as $group_name => $group_tags){
			foreach($group_tags as $tag){
				$data[$tag['id']] = Notifier_Notification_Merge_Tags::get_notification_merge_tag_value($tag['id'], $context_args);
			}
		}

		$params = array(
			'action'	=> 'fire_notification',
			'site_url'	=> site_url(),
			'source'	=> 'wp',
			'merge_tags'=> $data
    	);

		$response = Notifier::send_api_request( $params, 'POST' );

		if($response->error){
			error_log($response->message);
		}

	}

}
