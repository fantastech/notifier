<?php
/**
 * Settings page class
 *
 * @package    Wa_Notifier
 */
class WA_Notifier_Settings {

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
		add_submenu_page( WA_NOTIFIER_NAME, 'WA FIlter Settings', 'Settings', 'manage_options', WA_NOTIFIER_NAME . '-settings', array( __CLASS__, 'output' ) );
	}

	/**
	 * Output
	 */
	public static function output() {
        include_once WA_NOTIFIER_PATH . '/views/admin-settings.php';
	}

	/**
	 * Settings fields
	 */
	private function settings_fields() {

		$settings = 
			array(
				array(
					'id' 			=> 'phone_number_id',
					'title'			=> 'WhastApp Phone Number ID',
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'business_account_id',
					'title'			=> 'WhatsApp Business Account ID',
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'permanent_access_token',
					'title'			=> 'Permanent Access Token',
					'description'	=> '',
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
			);

		$settings = apply_filters( 'wa_notifier_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Generate HTML for displaying individual fields
	 */
	public function display_field( $field ) {

		$html = '';

		$option_name = WA_NOTIFIER_PREFIX . $field['id'];
		$option = get_option( $option_name );

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}

		if( $field['type'] == 'title') {
			$html .= '<tr><th class="section-title" colspan="2"><h3>' . $field['title'] . '</h3>';
			$html .= '<p>' . $field['description'] . '</p></th></tr>';
		}

		$html .= '<tr>';
		$html .= '<th>'.$field['title'].'</th>';
		$html .= '<td>';

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $option_name ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>';
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $option_name ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>';
			break;

			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<label><input id="' . esc_attr( $option_name ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . $field['label'] . "</label>";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<p><label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $option_name . '_' . $k ) . '" /> ' . $v . '</label></p>';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<p><label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $option_name . '_' . $k ) . '" /> ' . $v . '</label></p>';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . 'Upload an image' . '" data-uploader_button_text="' . 'Use image' . '" class="image_upload_button button" value="'. 'Upload new image' . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. 'Remove image' . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?>
				<div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		if( $field['description'] != '') {
			$html .= '<p class="description">' . $field['description'] . '</p>';
		}

		$html .= '</td></tr>';

		echo $html;
	}

	/**
	 * Show the setting fields
	 */
	public function show_settings_fields () {
		$setting_fields = self::settings_fields();
		echo "<table class='wa-notifier-fields-table'>";
		foreach ($setting_fields as $field) {
			self::display_field( $field );
		}
		echo "</table>";
	}

	/**
	 * Save the settings.
	 * 
	 * TODO - check fields other than text and textarea 
	 */
	public function save_settings_fields() {
		if ( ! self::is_settings_page() ) {
			return;
		}

		if ( ! isset( $_POST['save'] ) ) {
			return;
		}

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], WA_NOTIFIER_NAME . '-settings' ) ) {
			return;	
		}

		$settings_fields = self::settings_fields();
		$data = $_POST;
		$update_options = array();

		// Loop options and get values to save.
		foreach ( $settings_fields as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) ) {
				continue;
			}
			
			$option_name  = WA_NOTIFIER_PREFIX . $option['id'];
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
			update_option( $name, $value, 'yes' );
		}

	}

	/**
	 * Check if on settings page
	 */
	public function is_settings_page() {
		$current_page = isset($_GET['page']) ? $_GET['page'] : '';
		return strpos($current_page, WA_NOTIFIER_NAME . '-settings') !== false;
	}

	
}
