<?php
/**
 * Notification Triggers class
 *
 * @package    Wa_Notifier
 */
class Notifier_Notification_Triggers extends Notifier_Notifications {

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
				add_action($hook, $callback, $priority, $args_num);
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
				'description'	=> 'Send notification when a new post is published.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('WordPress', 'Post') ),
				'action'		=> array (
					'hook'		=> 'transition_post_status',
					'args_num'	=> 3,
					'callback' 	=> function ( $new_status, $old_status, $post ) {
						$notif_ids = self::trigger_has_active_notification('new_post');
						if (empty($notif_ids)) {
							return;
						}

						if ('post' !== get_post_type($post)) {
							return;
						}

						if ( 'publish' === $new_status && 'publish' !== $old_status ) {
							foreach ($notif_ids as $nid) {
								$args = array (
									'object_type' 	=> 'post',
									'object_id'		=> $post->ID
								);
								Notifier_Notifications::send_triggered_notification($nid, $args);
							}
						}
					}
				)

			),
			array(
			 	'id'			=> 'new_comment',
				'label' 		=> 'New comment is added',
				'description'	=> 'Send notification when a new comment is added.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('WordPress', 'Comment') ),
				'action'		=> array (
					'hook'		=> 'comment_post',
					'args_num'	=> 3,
					'callback' 	=> function ( $comment_id, $comment_approved, $commentdata ) {
						$notif_ids = self::trigger_has_active_notification('new_comment');
						if (empty($notif_ids)) {
							return;
						}
						if ( 'spam' != $comment_approved) {
							foreach ($notif_ids as $nid) {
								$args = array (
									'object_type' 	=> 'comment',
									'object_id'		=> $comment_id
								);
								Notifier_Notifications::send_triggered_notification($nid, $args);
							}
						}

					}
				)
			),
			array(
			 	'id'			=> 'new_user',
				'label' 		=> 'New user is registered',
				'description'	=> 'Send notification when a new user is created.',
				'merge_tags' 	=> Notifier_Notification_Merge_Tags::get_merge_tags( array('WordPress', 'User') ),
				'action'		=> array (
					'hook'		=> 'user_register',
					'callback' 	=> function ( $user_id ) {
						$notif_ids = self::trigger_has_active_notification('new_user');
						if (empty($notif_ids)) {
							return;
						}

						foreach ($notif_ids as $nid) {
							$args = array (
								'object_type' 	=> 'user',
								'object_id'		=> $user_id
							);
							Notifier_Notifications::send_triggered_notification($nid, $args);
						}
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
	 * Check whether current trigger has active notification
	 */
	public static function trigger_has_active_notification($trigger) {
		$active_triggers = get_option('notifier_active_triggers');

		$has_active_notification = false;

		if ( !empty($active_triggers) && !empty($active_triggers[$trigger])) {
			$has_active_notification = $active_triggers[$trigger];
		}

		return $has_active_notification;
	}
}
