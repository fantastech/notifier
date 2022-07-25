<?php
/**
 * WA Notifier Meta Box Functions
 *
 * @package     Notifier
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function notifier_wp_text_input( $field ) {
	global $post;

	$field = apply_filters ('notifier_wp_text_input_args', $field, $post);

	$field = wp_parse_args(
		$field , array(
			'label'				=> '',
			'placeholder'		=> '',
			'class'             => '',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => '',
			'name'              => $field['id'],
			'description'		=> '',
			'type'				=> 'text',
			'limit'				=> 0,
			'conditional_logic'	=> '',
			'show_wrapper'		=> true,
			'custom_attributes' => array(),
			'data_type'			=> '',
			'required'			=> ''
		)
	);

	$field['conditional_logic'] = ('' != $field['conditional_logic']) ? json_encode($field['conditional_logic']) : '';

	switch ( $field['data_type'] ) {
		case 'url':
			$field['class'] .= ' notifier_input_url';
			$field['value']  = esc_url( $field['value'] );
			break;

		default:
			break;
	}

	$show_limit_text = '';
	if($field['limit'] != 0) {
		$show_limit_text = '<span class="limit-text"><span class="limit-used">0</span> / <span>'.$field['limit'].'</span></span>';
		$field['custom_attributes']['data-limit'] = $field['limit'];
		$field['class'] = $field['class'] . ' force-text-limit';
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if( '' != $field['required'] ) {
		$custom_attributes[] = 'required="required"';
	}

	if($field['show_wrapper']){
		do_action('notifier_before_meta_field_wrapper', $field, $post);
		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_js( $field['conditional_logic'] ).'">';
	}

	if ( '' != $field['label'] ) {
		echo '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . $show_limit_text . '</label>';
	}

	do_action('notifier_before_meta_field', $field, $post);

	echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	do_action('notifier_after_meta_field', $field, $post);

	if ( '' != $field['description'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('notifier_after_meta_field_description', $field, $post);

	if($field['show_wrapper']){
		echo '</p>';
		do_action('notifier_after_meta_field_wrapper', $field, $post);
	}
}

/**
 * Output a hidden input box.
 *
 * @param array $field
 */
function notifier_wp_hidden_input( $field ) {
	global $post;

	$field = apply_filters ('notifier_wp_hidden_input', $field, $post);

	$field = wp_parse_args(
		$field , array(
			'class'             => '',
			'value'             => ''
		)
	);

	do_action('notifier_before_meta_field', $field, $post);

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';

	do_action('notifier_after_meta_field', $field, $post);
}

/**
 * Output a textarea input box.
 *
 * @param array $field
 */
function notifier_wp_textarea_input( $field ) {
	global $post;

	$field = apply_filters ('notifier_wp_textarea_input', $field, $post);

	$field = wp_parse_args(
		$field , array(
			'label'				=> '',
			'placeholder'		=> '',
			'class'             => '',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => '',
			'name'              => $field['id'],
			'description'		=> '',
			'limit'				=> 0,
			'conditional_logic'	=> '',
			'show_wrapper'		=> true,
			'custom_attributes' => array(),
			'rows'				=> 2,
			'cols'				=> 20,
			'required'			=> ''
		)
	);

	$field['conditional_logic'] = ('' != $field['conditional_logic']) ? json_encode($field['conditional_logic']) : '';

	$show_limit_text = '';
	if($field['limit'] != 0) {
		$show_limit_text = '<span class="limit-text"><span class="limit-used">0</span> / <span>'.$field['limit'].'</span></span>';
		$field['custom_attributes']['data-limit'] = $field['limit'];
		$field['class'] = $field['class'] . ' force-text-limit';
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if( '' != $field['required'] ) {
		$custom_attributes[] = 'required="required"';
	}

	if($field['show_wrapper']){
		do_action('notifier_before_meta_field_wrapper', $field, $post);
		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_js( $field['conditional_logic'] ).'">';
	}

	if ( '' != $field['label'] ) {
		echo '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . $show_limit_text . '</label>';
	}

	do_action('notifier_before_meta_field', $field, $post);

	echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . ' >' . esc_textarea( $field['value'] ) . '</textarea> ';

	do_action('notifier_after_meta_field', $field, $post);

	if ( '' != $field['description'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('notifier_after_meta_field_description', $field, $post);

	if($field['show_wrapper']){
		echo '</p>';
		do_action('notifier_after_meta_field_wrapper', $field, $post);
	}
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function notifier_wp_checkbox( $field ) {
	global $post;

	$field = apply_filters ('notifier_wp_checkbox', $field, $post);

	$field = wp_parse_args(
		$field , array(
			'label'				=> '',
			'class'             => '',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => '',
			'cbvalue'			=> 'yes',
			'name'              => $field['id'],
			'description'		=> '',
			'conditional_logic'	=> '',
			'show_wrapper'		=> true,
			'custom_attributes' => array(),
			'required'			=> ''
		)
	);

	$field['conditional_logic'] = ('' != $field['conditional_logic']) ? json_encode($field['conditional_logic']) : '';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if($field['show_wrapper']){
		do_action('notifier_before_meta_field_wrapper', $field, $post);
		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_js( $field['conditional_logic'] ).'">';
	}

	if ( '' != $field['label'] ) {
		echo '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
	}

	do_action('notifier_before_meta_field', $field, $post);

	echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

	do_action('notifier_after_meta_field', $field, $post);

	if ( '' != $field['description'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('notifier_after_meta_field_description', $field, $post);

	if($field['show_wrapper']){
		echo '</p>';
		do_action('notifier_after_meta_field_wrapper', $field, $post);
	}
}

/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function notifier_wp_select( $field ) {
	global $post;

	$field = apply_filters ('notifier_wp_select', $field, $post);

	$field = wp_parse_args(
		$field , array(
			'label'				=> '',
			'class'             => '',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => '',
			'name'              => $field['id'],
			'description'		=> '',
			'conditional_logic'	=> '',
			'show_wrapper'		=> true,
			'custom_attributes' => array()
		)
	);

	$field['conditional_logic'] = ('' != $field['conditional_logic']) ? json_encode($field['conditional_logic']) : '';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	$description = ! empty( $field['description'] ) ? $field['description'] : '';
	
	if($field['show_wrapper']){
		do_action('notifier_before_meta_field_wrapper', $field, $post);
		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_js( $field['conditional_logic'] ).'">';
	}

	if ( '' != $field['label'] ) {
		echo '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
	}

	do_action('notifier_before_meta_field', $field, $post);

	echo '<select class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '"' . implode( ' ', $custom_attributes ) . '/> ';

	foreach ( $field['options'] as $key => $value ) {
		if(is_array($value)){
			$opt_group_options = $value;
			echo '<optgroup label="'.$key.'">';
			foreach($opt_group_options as $opt_key => $opt_val) {
				echo '<option value="' . esc_attr( $opt_key ) . '"' . selected( $opt_key, $field['value'], false) . '>' . esc_html( $opt_val ) . '</option>';
			}
			echo '</optgroup>';
		}
		else {
			echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $field['value'], false) . '>' . esc_html( $value ) . '</option>';
		}
	}

	echo '</select>';

	do_action('notifier_after_meta_field', $field, $post);

	if ( '' != $field['description'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('notifier_after_meta_field_description', $field, $post);

	if($field['show_wrapper']){
		echo '</p>';
		do_action('notifier_after_meta_field_wrapper', $field, $post);
	}
}

/**
 * Output a radio input box.
 *
 * @param array $field
 */
function notifier_wp_radio( $field ) {
	global $post;

	$field = apply_filters ('notifier_wp_radio', $field, $post);

	$field = wp_parse_args(
		$field , array(
			'label'				=> '',
			'class'             => '',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => '',
			'name'              => $field['id'],
			'description'		=> '',
			'conditional_logic'	=> '',
			'show_wrapper'		=> true,
			'custom_attributes' => array()
		)
	);

	$field['conditional_logic'] = ('' != $field['conditional_logic']) ? json_encode($field['conditional_logic']) : '';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if($field['show_wrapper']){
		do_action('notifier_before_meta_field_wrapper', $field, $post);
		echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_js($field['conditional_logic']).'">';
	}

	if ( '' != $field['label'] ) {
		echo '<legend>' . wp_kses_post( $field['label'] ) . '</legend>';
	}

	do_action('notifier_before_meta_field', $field, $post);

	echo '<ul class="radio-buttons">';

	foreach ( $field['options'] as $key => $value ) {
		echo '<li><label><input
				name="' . esc_attr( $field['name'] ) . '"
				value="' . esc_attr( $key ) . '"
				type="radio"
				class="' . esc_attr( $field['class'] ) . '"
				style="' . esc_attr( $field['style'] ) . '"
				' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
				' . implode( ' ', $custom_attributes ) . '
				/> ' . esc_html( $value ) . '</label>
		</li>';
	}
	echo '</ul>';

	do_action('notifier_after_meta_field', $field, $post);

	if ( '' != $field['description'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('notifier_after_meta_field_description', $field, $post);

	if($field['show_wrapper']){
		echo '</fieldset>';
		do_action('notifier_after_meta_field_wrapper', $field, $post);
	}

}
