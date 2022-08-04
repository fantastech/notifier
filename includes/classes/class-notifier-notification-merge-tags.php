<?php
/**
 * Notification Merge Tags class
 *
 * @package    Wa_Notifier
 */
class Notifier_Notification_Merge_Tags extends Notifier_Notifications {

	public $merge_tags = array();

	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'notifier_notification_merge_tags', array(__CLASS__, 'post_merge_tags') );
		add_filter( 'notifier_notification_merge_tags', array(__CLASS__, 'comment_merge_tags') );
		add_filter( 'notifier_notification_merge_tags', array(__CLASS__, 'user_merge_tags') );
	}

	/**
	 * Get WordPress merge tags
	 */
	public static function get_merge_tags($types = array()) {
		$merge_tags = array();
		$merge_tags['WordPress'] = array(
			array(
				'id' 			=> 'site_title',
				'label' 		=> 'Site title',
				'preview_value' => get_bloginfo('name'),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return get_bloginfo('name');
				}
			),
			array(
				'id'			=> 'site_tagline',
				'label' 		=> 'Site tagline',
				'preview_value' => get_bloginfo('description'),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return get_bloginfo('description');
				}
			),
			 array(
			 	'id'			=> 'site_url',
				'label'		 	=> 'Site URL',
				'preview_value' => get_bloginfo('url'),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return get_bloginfo('url');
				}
			),
			array(
				'id'			=> 'admin_email',
				'label' 		=> 'Admin email',
				'preview_value' => get_bloginfo('admin_email'),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return get_bloginfo('admin_email');
				}
			),
			array(
				'id'			=> 'current_datetime',
				'label' 		=> 'Current datetime',
				'preview_value' => current_datetime()->format(get_option('date_format')) . ' ' . current_datetime()->format(get_option('time_format')),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return current_datetime()->format(get_option('date_format')) . ' ' . current_datetime()->format(get_option('time_format'));
				}
			),
			array(
				'id'			=> 'current_date',
				'label' 		=> 'Current date',
				'preview_value' => current_datetime()->format(get_option('date_format')),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return current_datetime()->format(get_option('date_format'));
				}
			),
			array(
				'id'			=> 'current_time',
				'label' 		=> 'Current time',
				'preview_value' => current_datetime()->format(get_option('time_format')),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return current_datetime()->format(get_option('time_format'));
				}
			),
			array(
				'id' 			=> 'site_logo_image',
				'label' 		=> 'Site logo image',
				'preview_value' => get_custom_logo(),
				'return_type'	=> 'media',
				'value'			=> function ($args) {
				 	$custom_logo_id = get_theme_mod( 'custom_logo' );
				 	return wp_get_attachment_image_url( $custom_logo_id, 'full' );
				}
			),
		);

		$merge_tags = apply_filters('notifier_notification_merge_tags', $merge_tags);

		$final_merge_tags = array();

		if (empty($types)) {
			$final_merge_tags = $merge_tags;
		} else {
			foreach ($types as $type) {
				$final_merge_tags[$type] = $merge_tags[$type];
			}
		}

		return $final_merge_tags;
	}

	/**
	 * Post merge tags
	 */
	public static function post_merge_tags($merge_tags) {
		$merge_tags['Post'] = array(
			array(
				'id' 			=> 'post_ID',
				'label' 		=> 'Post ID',
				'preview_value' => '123',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return $args['object_id'];
				}
			),
			array(
				'id' 			=> 'post_title',
				'label' 		=> 'Post title',
				'preview_value' => 'Hello World!',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return $post->post_title;
				}
			),
			array(
				'id' 			=> 'post_permalink',
				'label' 		=> 'Post permalink',
				'preview_value' => site_url() . 'hello-world/',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return get_permalink( $post->ID );
				}
			),
			array(
				'id' 			=> 'post_author',
				'label' 		=> 'Post author',
				'preview_value' => 'John Doe',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return $post->post_author;
				}
			),
			array(
				'id' 			=> 'post_content',
				'label' 		=> 'Post content',
				'preview_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return sanitize_text_field($post->post_content);
				}
			),
			array(
				'id' 			=> 'post_excerpt',
				'label' 		=> 'Post excerpt',
				'preview_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return sanitize_text_field($post->post_excerpt);
				}
			),
			array(
				'id' 			=> 'post_status',
				'label' 		=> 'Post status',
				'preview_value' => 'publish',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return $post->post_status;
				}
			),
			array(
				'id' 			=> 'post_publish_date',
				'label' 		=> 'Post publish date',
				'preview_value' => date(get_option('date_format')),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return $post->post_date;
				}
			),
			array(
				'id' 			=> 'post_modified_date',
				'label' 		=> 'Post modified date',
				'preview_value' => date(get_option('date_format')),
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$post = get_post($args['object_id']);
					return $post->post_modified;
				}
			)
		);
		return $merge_tags;
	}

	/**
	 * Comment merge tags
	 */
	public static function comment_merge_tags($merge_tags) {
		$merge_tags['Comment'] = array(
			array(
				'id' 			=> 'comment_ID',
				'label' 		=> 'Comment ID',
				'preview_value' => '123',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return $args['object_id'];
				}
			),
			array(
				'id' 			=> 'comment_author',
				'label' 		=> 'Comment author',
				'preview_value' => 'John Doe',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return $comment->comment_author;
				}
			),
			array(
				'id' 			=> 'comment_author_email',
				'label' 		=> 'Comment author email',
				'preview_value' => 'john@example.com',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return $comment->comment_author_email;
				}
			),
			array(
				'id' 			=> 'comment_author_url',
				'label' 		=> 'Comment author URL',
				'preview_value' => 'https://example.com',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return $comment->comment_author_url;
				}
			),
			array(
				'id' 			=> 'comment_content',
				'label' 		=> 'Comment content',
				'preview_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return sanitize_text_field($comment->comment_content);
				}
			),
			array(
				'id' 			=> 'comment_post_ID',
				'label' 		=> 'Comment post ID',
				'preview_value' => '124',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return $comment->comment_post_ID;
				}
			),
			array(
				'id' 			=> 'comment_post_title',
				'label' 		=> 'Comment post title',
				'preview_value' => 'Hello World!',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return get_the_title($comment->comment_post_ID);
				}
			),
			array(
				'id' 			=> 'comment_post_url',
				'label' 		=> 'Comment post URL',
				'preview_value' => 'https://example.com/hello-world/',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$comment = get_comment($args['object_id']);
					return get_the_permalink($comment->comment_post_ID);
				}
			),
		);
		return $merge_tags;
	}

	/**
	 * User merge tags
	 */
	public static function user_merge_tags($merge_tags) {
		$merge_tags['User'] = array(
			array(
				'id' 			=> 'user_ID',
				'label' 		=> 'User ID',
				'preview_value' => '123',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					return $args['object_id'];
				}
			),
			array(
				'id' 			=> 'user_login',
				'label' 		=> 'Username',
				'preview_value' => 'username',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->user_login;
				}
			),
			array(
				'id' 			=> 'user_email',
				'label' 		=> 'User email',
				'return_type'	=> 'text',
				'preview_value' => 'john@example.com',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->user_email;
				}
			),
			array(
				'id' 			=> 'user_first_name',
				'label' 		=> 'User first name',
				'preview_value' => 'John',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->first_name;
				}
			),
			array(
				'id' 			=> 'user_last_name',
				'label' 		=> 'User last name',
				'preview_value' => 'Doe',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->last_name;
				}
			),
			array(
				'id' 			=> 'user_display_name',
				'label' 		=> 'User display name',
				'preview_value' => 'John Doe',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->display_name;
				}
			),
			array(
				'id' 			=> 'user_url',
				'label' 		=> 'User website',
				'preview_value' => 'https://example.com',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->user_url;
				}
			),
			array(
				'id' 			=> 'user_role',
				'label' 		=> 'User role',
				'preview_value' => 'Subscriber',
				'return_type'	=> 'text',
				'value'			=> function ($args) {
					$user = get_userdata($args['object_id']);
					return $user->role;
				}
			),
		);
		return $merge_tags;
	}

	/**
	 * Return notification merge tags for supplied trigger
	 */
	public static function get_notification_merge_tags($trigger, $merge_tag_type = 'text') {
		$main_triggers = Notifier_Notification_Triggers::get_notification_triggers();

		foreach ($main_triggers as $key => $triggers) {
			foreach ($triggers as $t) {
				if ( $trigger == $t['id'] ) {
					$tags = $t['merge_tags'];
					break 2;
				}
			}
		}

		$merge_tags = array();

		if (!empty($tags)) {
			foreach ($tags as $tag_key => $merge_tags_list) {
				foreach($merge_tags_list as $tag) {
					if($merge_tag_type != $tag['return_type']){
						continue;
					}
					$merge_tags[$tag_key][$tag['id']] = $tag['label'];
				}
			}
		}

		$default_tags = self::get_merge_tags(array('WordPress'));
		foreach($default_tags['WordPress'] as $wp_tag){
			if($merge_tag_type != $tag['return_type']){
				continue;
			}
			$merge_tags['WordPress'][$wp_tag['id']] = $wp_tag['label'];
		}

		return $merge_tags;
	}

	/**
	 * Return notification merge tag value for supplied trigger and object
	 */
	public static function get_notification_merge_tag_value($tag_id, $context_args) {
		$merge_tags = self::get_merge_tags();
		foreach ($merge_tags as $tags) {
			foreach ($tags as $tag) {
				if ($tag['id'] == $tag_id) {
					$the_tag = $tag;
					break 2;
				}
			}
		}
		$value = trim($the_tag['value']($context_args));
		if ('' == $value) {
			$value = ' ';
		}
		return $value;
	}

}
