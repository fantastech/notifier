<?php
/**
 * Settings page class
 *
 * @package    Wa_Notifier
 */
class Notifier_Settings {

	/**
	 * Init
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__ , 'setup_admin_page') );
        add_action( 'admin_init', array( __CLASS__ , 'save_settings_fields' ) );
	}

	/**
	 * Add settings page to men
	 */
	public static function setup_admin_page () {
		add_submenu_page( NOTIFIER_NAME, 'Settings', 'Settings', 'manage_options', NOTIFIER_NAME . '-settings', array( __CLASS__, 'output' ) );
	}

	/**
	 * Output
	 */
	public static function output() {
        include_once NOTIFIER_PATH . '/views/admin-settings.php';
	}

	/**
	 * Get settings tabs
	 */
	private static function get_settings_tabs() {
		$tabs = array(
			'general'		=> 'General'
		);
		return $tabs;
	}

	/**
	 * Check if on settings page
	 */
	public static function is_settings_page() {
		$current_page = isset($_GET['page']) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		return strpos($current_page, NOTIFIER_NAME . '-settings') !== false;
	}

	/**
	 * Settings fields
	 */
	private static function settings_fields($tab) {
		$activated = get_option(NOTIFIER_PREFIX . 'api_activated');
		if('yes' == $activated) {
			$description = 'You are successfully connected to WANotifier.com.';
		}
		else{
			$description = 'You are not connected to WANotifier.com yet. Please enter valid API key and save the settings.';
		}
		$settings = array();
		switch ($tab) {
			case 'general':
				$settings = array(
					array(
						'title'			=> 'General',
						'description'	=> '',
						'type'			=> 'title',
					),
					array(
						'id' 			=> 'api_key',
						'title'			=> 'WANotifier.com API key',
						'type'			=> 'text',
						'placeholder'	=> 'Enter your WANotifier.com API key here',
						'default'		=> '',
						'description'	=> $description
					),
				);
				break;
		}
		$settings = apply_filters( 'notifier_$tab_settings_fields', $settings );
		return $settings;
	}

	/**
	 * Generate HTML for displaying individual fields
	 */
	public static function display_field( $field ) {
		$html = '';

		if (isset($field['id'])) {
			$option_name = NOTIFIER_PREFIX . $field['id'];
			$option = get_option( $option_name );
		}

		$data = '';
		if ( isset( $field['default'] ) ) {
			$data = $field['default'];
			if ( isset( $option ) && '' != $option) {
				$data = $option;
			}
		}

		if ( 'title' === $field['type']) {
			$html .= '<tr><th class="section-title" colspan="2"><h3>' . $field['title'] . '</h3>';
			$html .= '<p>' . $field['description'] . '</p></th></tr>';
		} else {
			$html .= '<tr>';
			$html .= '<th>' . $field['title'] . '</th>';
			$html .= '<td>';

			switch ( $field['type'] ) {

				case 'text':
				case 'password':
				case 'number':
					$html .= '<input id="' . esc_attr( $option_name ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>';
				    break;

				case 'textarea':
					$html .= '<textarea id="' . esc_attr( $option_name ) . '" rows="5" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>';
				    break;

				case 'checkbox':
					$checked = '';
					if ( $option && 'on' == $option ) {
						$checked = 'checked="checked"';
					}
					$html .= '<label><input id="' . esc_attr( $option_name ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . $field['label'] . '</label>';
				    break;

				case 'checkbox_multi':
					foreach ( $field['options'] as $k => $v ) {
						$checked = false;
						if ( in_array( $k, $data ) ) {
							$checked = true;
						}
						$html .= '<p><label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $option_name . '_' . $k ) . '" /> ' . $v . '</label></p>';
					}
				    break;

				case 'radio':
					foreach ( $field['options'] as $k => $v ) {
						$checked = false;
						if ( $k == $data ) {
							$checked = true;
						}
						$html .= '<p><label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $option_name . '_' . $k ) . '" /> ' . $v . '</label></p>';
					}
				    break;

				case 'select':
					$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
					foreach ( $field['options'] as $k => $v ) {
						$selected = false;
						if ( $k == $data ) {
							$selected = true;
						}
						$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
					}
					$html .= '</select> ';
				    break;

				case 'select_multi':
					$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
					foreach ( $field['options'] as $k => $v ) {
						$selected = false;
						if ( in_array( $k, $data ) ) {
							$selected = true;
						}
						$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
					}
					$html .= '</select> ';
				    break;

				case 'file':
					$uploader_title = isset($field['uploader_title']) ? $field['uploader_title'] : 'Upload media';
					$uploader_button_text = isset($field['uploader_button_text']) ? $field['uploader_button_text'] : 'Select';
					$file_types = isset($field['uploader_supported_file_types']) ? $field['uploader_supported_file_types'] : array();

					$image_thumb = '';
					$video_url = '';
					$show_image = 'hide';
					$show_video = 'hide';

					$html .= '<span class="notifier-media-preview">';
					if ( '' != $data ) {
						$file_type = $field['uploader_supported_file_types'];
						switch ($file_type) {
						    case 'image':
						    case 'image/jpeg':
						    case 'image/png':
						    case 'application/pdf':
						    	$image_thumb = wp_get_attachment_thumb_url( $data );
						    	$show_image = '';
						    	$show_video = 'hide';
						    	break;

						    case 'video/mp4':
						    	$video_url = wp_get_attachment_url( $data );
								$show_image = 'hide';
								$show_video = '';
								break;

							default:
								$show_image = 'hide';
								$show_video = 'hide';
					  	}
					}
					$html .= '<img id="' . esc_attr( $option_name ) . '_preview_image" class="notifier-media-preview-item '. esc_attr($show_image) .'" src="' . esc_url($image_thumb) . '" />';
				  	$html .= '<video id="' . esc_attr( $option_name ) . '_preview_video" class="notifier-media-preview-item '. esc_attr($show_video) .'"  width="300" height="169" controls muted><source src="' . esc_url($video_url) . '"  type="video/mp4"></video><br/>';
					$html .= '</span>';

					$html .= '<input id="' . esc_attr( $option_name ) . '_button" type="button" data-uploader_title="' . esc_attr($uploader_title) . '" data-uploader_button_text="' . esc_attr($uploader_button_text) . '" data-uploader_supported_file_types="' . esc_attr($file_types) . '" class="notifier-media-upload-button button" value="' . 'Upload' . '" /> ';
					$html .= '<input id="' . esc_attr( $option_name ) . '_delete" type="button" class="notifier-media-delete-button button" value="' . 'Remove' . '" />';
					$html .= '<input id="' . esc_attr( $option_name ) . '" class="notifier-media-attachment-id" type="hidden" name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $data ) . '"/><br/>';
				    break;

				case 'color':
					?>
					<div class="color-picker" style="position:relative;">
				        <input type="text" name="<?php echo esc_attr( $option_name ); ?>" class="color" value="<?php echo esc_attr( $data ); ?>" />
				        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
				    </div>
				    <?php
				    break;

			}

			if ( '' != $field['description'] ) {
				$html .= '<p class="description">' . esc_html($field['description']) . '</p>';
			}
		}

		//phpcs:ignore
		echo $html;
	}

	/**
	 * Show the setting fields
	 */
	public static function show_settings_fields ($tab) {
		$setting_fields = self::settings_fields($tab);
		echo "<table class='notifier-fields-table'>";
		foreach ($setting_fields as $field) {
			self::display_field( $field );
		}
		echo '</table>';
	}

	/**
	 * Save the settings.
	 *
	 * TODO - check fields other than text and textarea
	 */
	public static function save_settings_fields() {
		if ( ! self::is_settings_page() ) {
			return;
		}

		if ( ! isset( $_POST['save'] ) ) {
			return;
		}

		//phpcs:ignore
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-settings' ) ) {
			return;
		}

		$tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';

		$settings_fields = self::settings_fields($tab);
		$data = $_POST;
		$update_options = array();

		// Loop options and get values to save.
		foreach ( $settings_fields as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) ) {
				continue;
			}

			$option_name  = NOTIFIER_PREFIX . $option['id'];
			$setting_name = '';
			$raw_value    = isset( $data[ $option_name ] ) ? wp_unslash( $data[ $option_name ] ) : null;

			// Format the value based on option type.
			switch ( $option['type'] ) {
				case 'checkbox':
					$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
					break;
				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				case 'select':
					$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
					if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
						$value = null;
						break;
					}
					$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
					$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
					break;
				default:
					$value = sanitize_text_field( $raw_value );
					break;
			}

			// Check if option is an array and handle that differently to single values.
			if ( $option_name && $setting_name ) {
				if ( ! isset( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = get_option( $option_name, array() );
				}
				if ( ! is_array( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = array();
				}
				$update_options[ $option_name ][ $setting_name ] = $value;
			} else {
				$update_options[ $option_name ] = $value;
			}
		}

		// Save all options in our array.
		foreach ( $update_options as $name => $value ) {
			if('notifier_api_key' == $name){
				$current_api_key = get_option($name);
				update_option('notifier_api_key', $value);
				delete_option('notifier_enabled_triggers');
				delete_option('notifier_api_activated');

				$params = array(
					'action'    => 'verify_api',
					'site_url'	=> site_url(),
					'source'	=> 'wp'
		    	);

				$response = Notifier::send_api_request( $params, 'POST' );

				if($response->error){
					$notices[] = array(
						'message' => 'There was an error validating API key. Error: ' . $response->message,
						'type' => 'error'
					);
				}
				else{
					update_option('notifier_api_activated', 'yes');
					$notices[] = array(
						'message' => 'API key validated and saved successfully. Your triggers have been reset. Please enable and save your triggers again from the <a href="'.admin_url('admin.php?page=notifier').'">WA Notifier</a> page.',
						'type' => 'success'
					);
				}
			}
			else{
				update_option( $name, $value, 'yes' );
			}
		}

		if(empty($notices)){
			$notices[] = array(
				'message' => 'Settings updated successfully.',
				'type' => 'success'
			);
		}

		new Notifier_Admin_Notices($notices);
	}

}
