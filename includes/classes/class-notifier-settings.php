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
        add_action( 'admin_enqueue_scripts', array( __CLASS__ , 'admin_scripts') );
        // add_action( 'notifier_before_settings_fields', array( __CLASS__ , 'show_wa_profile_screenshot_start') );
        // add_action( 'notifier_after_settings_fields', array( __CLASS__ , 'show_wa_profile_screenshot_end') );
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
			'profile' 		=> 'WhatsApp Profile',
			'general'		=> 'General',
			'api' 			=> 'API Configuration',
		);
		return $tabs;
	}

	/**
	 * Add JS to settings page
	 */
	public static function admin_scripts () {
		if(!self::is_settings_page()){
			return;
		}
	    wp_enqueue_media();
	}

	/**
	 * Check if on settings page
	 */
	public static function is_settings_page() {
		$current_page = isset($_GET['page']) ? $_GET['page'] : '';
		return strpos($current_page, NOTIFIER_NAME . '-settings') !== false;
	}

	/**
	 * Settings fields
	 */
	private static function settings_fields($tab) {
		$settings = array();
		switch($tab) {
			case 'profile':
				$settings = array(
					array(
						'title'			=> 'WhatsApp Profile',
						'description'	=> 'Update how your profile will look to your customers on WhatsApp.',
						'type'			=> 'title',
					),
					// array(
					// 	'id' 			=> 'wa_profile_picture',
					// 	'title'			=> 'Profile Picture',
					// 	'description'	=> 'Recommended profile image size 640px x 640px.',
					// 	'type'			=> 'image',
					// 	'default'		=> '',
					// 	'placeholder'	=> ''
					// ),
					array(
						'id' 			=> 'wa_profile_description',
						'title'			=> 'Description',
						'description'	=> '',
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> ''
					),
					array(
						'id' 			=> 'wa_profile_address',
						'title'			=> 'Address',
						'description'	=> '',
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> ''
					),
					array(
						'id' 			=> 'wa_profile_category',
						'title'			=> 'Category',
						'description'	=> '',
						'type'			=> 'select',
						'options'		=> array(
							'UNDEFINED'	=> 'Select your business industry',
							'NOT_A_BIZ'	=> 'Not a business',
							'AUTO'		=> 'Automotive',
							'BEAUTY'	=> 'Beauty, Spa & Salon',
							'APPAREL'	=> 'Clothing & Apparel',
							'EDU'		=> 'Education',
							'ENTERTAIN'	=> 'Entertainment',
							'EVENT_PLAN'=> 'Event Planning & Service',
							'FINANCE'	=> 'Finance & Banking',
							'GROCERY'	=> 'Grocery & Supermarket',
							'GOVT'		=> 'Public & Government Service',
							'HOTEL'		=> 'Hotel & Lodging',
							'HEALTH'	=> 'Medical & Health',
							'NONPROFIT'	=> 'Non-profit',
							'PROF_SERVICES'	=> 'Professional Services',
							'RETAIL'	=> 'Shopping & Retail',
							'TRAVEL'	=> 'Travel & Transportation',
							'RESTAURANT'=> 'Restaurant',
							'OTHER'		=> 'Other'
						),
						'default'		=> '',
						'placeholder'	=> ''
					),
					array(
						'id' 			=> 'wa_profile_email',
						'title'			=> 'Email (optional)',
						'description'	=> '',
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> ''
					),
					array(
						'id' 			=> 'wa_profile_website_1',
						'title'			=> 'Website URL 1',
						'description'	=> '',
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> 'https://'
					),
					array(
						'id' 			=> 'wa_profile_website_2',
						'title'			=> 'Website URL 2',
						'description'	=> '',
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> 'https://'
					)
				);
				break;

			case 'general':
				$settings = array(
					array(
						'title'			=> 'General',
						'description'	=> '',
						'type'			=> 'title',
					),
					array(
						'id' 			=> 'bulk_message_batch_limit',
						'title'			=> 'Bulk message batch limit',
						'description'	=> 'Enter the number of messages you want to send in each batch when sending bulk messages. Default value is <code>50</code>. If you want send more number of messages in each batch and have good server resources you can increase the limit here.',
						'type'			=> 'number',
						'default'		=> 50,
						'placeholder'	=> ''
					),
				);
				break;

			case 'api':
				$settings = array(
					array(
						'title'			=> 'WhastApp API Configuration',
						'description'	=> 'Enter the API details below to setup WhatsApp.',
						'type'			=> 'title',
					),
					array(
						'id' 			=> 'phone_number_id',
						'title'			=> 'Phone Number ID',
						'description'	=> '',
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> ''
					),
					array(
						'id' 			=> 'business_account_id',
						'title'			=> 'Business Account ID',
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

		if(isset($field['id'])){
			$option_name = NOTIFIER_PREFIX . $field['id'];
			$option = get_option( $option_name );
		}

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( isset( $option ) && '' != $option) {
				$data = $option;
			}
		}

		if( $field['type'] == 'title') {
			$html .= '<tr><th class="section-title" colspan="2"><h3>' . $field['title'] . '</h3>';
			$html .= '<p>' . $field['description'] . '</p></th></tr>';
		}
		else{
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
					$html .= '<textarea id="' . esc_attr( $option_name ) . '" rows="5" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>';
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
				        <input type="text" name="<?php echo esc_attr( $option_name ); ?>" class="color" value="<?php echo esc_attr( $data ); ?>" />
				        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
				    </div>
				    <?php
				break;

			}

			if( $field['description'] != '') {
				$html .= '<p class="description">' . $field['description'] . '</p>';
			}
		}

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
		echo "</table>";
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

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], NOTIFIER_NAME . '-settings' ) ) {
			return;	
		}

		$tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

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

		if('api' == $tab) {
			$phone_number_id = isset($update_options[ NOTIFIER_PREFIX . 'phone_number_id' ]) ? $update_options[ NOTIFIER_PREFIX . 'phone_number_id' ] : '';
			$business_account_id = isset($update_options[ NOTIFIER_PREFIX . 'business_account_id' ]) ? $update_options[ NOTIFIER_PREFIX . 'business_account_id' ] : '';
			$permanent_access_token = isset($update_options[ NOTIFIER_PREFIX . 'permanent_access_token' ]) ? $update_options[ NOTIFIER_PREFIX . 'permanent_access_token' ] : '';

			if('' == $phone_number_id || '' == $business_account_id || '' == $permanent_access_token){
				$notices[] = array(
					'message' => 'Phone number ID, Business Account ID and Permanent Access Token are mandatory fields.',
					'type' => 'error'
				);
				new Notifier_Admin_Notices($notices);
				return;
			}
		}

		// Save all options in our array.
		foreach ( $update_options as $name => $value ) {
			update_option( $name, $value, 'yes' );
		}

		// Do other things after updating settings

		switch($tab) {
			case 'profile':
				$notices = Notifier_Settings::save_whatsapp_profile_details($update_options);
				break;

			case 'api':
				$notices = Notifier_Settings::fetch_and_save_whatsapp_details($phone_number_id);
				break;

			default:
				$notices[] = array(
					'message' => 'Settings saved.',
					'type' => 'success'
				);
		}

		new Notifier_Admin_Notices($notices);
	}

	/**
	 * Save WhatsApp profile details
	 */
	public static function save_whatsapp_profile_details($profile_fields) {
		$args = array (
			'messaging_product'	=> 'whatsapp',
			'description' =>  isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_description']) ? $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_description' ] : '',
			'address'	=> isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_address']) ? $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_address' ] : '',
			'vertical' 	=> isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_category']) ? $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_category' ] : '',
			'email' 	=> isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_email']) ? $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_email' ] : '',
		);

		if(isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_website_1' ]) && '' != $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_website_1' ]) {
			$args['websites'][] = $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_website_1'];
		}

		if(isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_website_2' ]) && '' != $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_website_2' ]) {
			$args['websites'][] = $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_website_2' ];
		}

		$profile_picture_id = isset($profile_fields[ NOTIFIER_PREFIX . 'wa_profile_picture']) ? $profile_fields[ NOTIFIER_PREFIX . 'wa_profile_picture' ] : '';
		// $pic_url = wp_get_attachment_image_url($profile_picture_id);

		//if($pic_url){
			// $args['profile_picture_handle'] = $pic_url;
		//}

		$response = Notifier::wa_cloud_api_request( 'whatsapp_business_profile', $args );

		if(isset($response->error)) {
			$notices[] = array(
				'message' => 'API request can not be validated. Error Code ' . $response->error->code . ': ' . $response->error->message ,
				'type' => 'error'
			);
			return $notices;
		}
		else {
			$notices[] = array(
				'message' => 'Profile updated successfully.',
				'type' => 'success'
			);
		}
		return $notices;
	}

	/**
	 * Fetch and save WhatsApp details
	 */
	public static function fetch_and_save_whatsapp_details($phone_number_id) {
		$response = Notifier::wa_cloud_api_request('', array(), 'GET');
		if(isset($response->error)) {
			$notices[] = array(
				'message' => 'API request can not be validated. Error Code ' . $response->error->code . ': ' . $response->error->message ,
				'type' => 'error'
			);
			return $notices;
		}
		else {
			$phone_number_details[$phone_number_id] = array (
				'display_num'		=> $response->display_phone_number,
				'display_name'		=> $response->verified_name,
				'phone_num_status'	=> $response->code_verification_status,
				'quality_rating'	=> $response->quality_rating
			);
			update_option( NOTIFIER_PREFIX . 'phone_number_details', $phone_number_details );
		}

		$response_profile = Notifier::wa_cloud_api_request('whatsapp_business_profile', array(
			'fields' => 'about,address,description,email,profile_picture_url,websites,vertical'
		), 'GET');

		if(isset($response_profile->error)) {
			$notices[] = array(
				'message' => 'WhatsApp Error Code: ' . $response_profile->error->code . ': ' . $response_profile->error->message ,
				'type' => 'error'
			);
			return $notices;
		}
		else {
			if(isset($response_profile->data)){
				$data = $response_profile->data[0];
				update_option( NOTIFIER_PREFIX . 'wa_profile_address', isset($data->address) ? $data->address : '' );
				update_option( NOTIFIER_PREFIX . 'wa_profile_description', isset($data->description) ? $data->description : '');
				update_option( NOTIFIER_PREFIX . 'wa_profile_email', isset($data->email) ? $data->email : '');
				update_option( NOTIFIER_PREFIX . 'wa_profile_category', isset($data->vertical) ? $data->vertical : '');
				if(isset($data->websites)) {
					update_option( NOTIFIER_PREFIX . 'wa_profile_website_1', isset($data->websites[0]) ? $data->websites[0] : '');
					update_option( NOTIFIER_PREFIX . 'wa_profile_website_2', isset($data->websites[1]) ? $data->websites[1] : '');
				}
			}
			$notices[] = array(
				'message' => 'Settings saved.',
				'type' => 'success'
			);
			return $notices;
		}
	}

	/**
	 * Show WA profile screenshot on Profile tab
	 */
	public static function show_wa_profile_screenshot_start($tab) {
		if('profile' != $tab){
			return;
		}
		echo '<div class="notifier-profile-fields-left">';
	}

	/**
	 * Show WA profile screenshot on Profile tab
	 */
	public static function show_wa_profile_screenshot_end($tab) {
		if('profile' != $tab){
			return;
		}
		echo '</div><div class="notifier-profile-fields-right"><img src="' . NOTIFIER_URL . 'assets/images/wa-profile.jpg" /></div>';
	}
}
