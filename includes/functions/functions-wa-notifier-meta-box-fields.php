<?php
/**
 * WA Notifier Meta Box Functions
 *
 * @package     WA_Notifier
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function wa_notifier_wp_text_input( $field ) {
	global $thepostid, $post;

	$field = apply_filters ('wa_notifier_wp_text_input_args', $field, $post);

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : '';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
	$field['limit']         = isset( $field['limit'] ) ? $field['limit'] : 0;
	$field['conditional_logic']	= isset( $field['conditional_logic'] ) ? json_encode( $field['conditional_logic'] ) : '';
	$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

	switch ( $data_type ) {
		case 'url':
			$field['class'] .= ' wa_notifier_input_url';
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

	if( $field['required'] ) {
		$custom_attributes[] = 'required="required"';
	}

	do_action('wa_notifier_before_meta_field_wrapper', $field, $post);

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_attr( $field['conditional_logic'] ).'">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . $show_limit_text . '</label>';

	do_action('wa_notifier_before_meta_field', $field, $post);

	echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	do_action('wa_notifier_after_meta_field', $field, $post);

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('wa_notifier_after_meta_field_description', $field, $post);

	echo '</p>';

	do_action('wa_notifier_after_meta_field_wrapper', $field, $post);
}

/**
 * Output a hidden input box.
 *
 * @param array $field
 */
function wa_notifier_wp_hidden_input( $field ) {
	global $thepostid, $post;

	$field = apply_filters ('wa_notifier_wp_hidden_input', $field, $post);

	$thepostid      = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

	do_action('wa_notifier_before_meta_field', $field, $post);

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';

	do_action('wa_notifier_after_meta_field', $field, $post);
}

/**
 * Output a textarea input box.
 *
 * @param array $field
 */
function wa_notifier_wp_textarea_input( $field ) {
	global $thepostid, $post;

	$field = apply_filters ('wa_notifier_wp_textarea_input', $field, $post);

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : '';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 2;
	$field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;
	$field['limit']         = isset( $field['limit'] ) ? $field['limit'] : 0;
	$field['conditional_logic']	= isset( $field['conditional_logic'] ) ? json_encode( $field['conditional_logic'] ) : '';

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

	if( $field['required'] ) {
		$custom_attributes[] = 'required="required"';
	}

	do_action('wa_notifier_before_meta_field_wrapper', $field, $post);

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_attr( $field['conditional_logic'] ).'">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . $show_limit_text . '</label>';

	do_action('wa_notifier_before_meta_field', $field, $post);

	echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . ' >' . esc_textarea( $field['value'] ) . '</textarea> ';

	do_action('wa_notifier_after_meta_field', $field, $post);

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('wa_notifier_after_meta_field_description', $field, $post);

	echo '</p>';

	do_action('wa_notifier_after_meta_field_wrapper', $field, $post);
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function wa_notifier_wp_checkbox( $field ) {
	global $thepostid, $post;

	$field = apply_filters ('wa_notifier_wp_checkbox', $field, $post);

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['conditional_logic']	= isset( $field['conditional_logic'] ) ? json_encode( $field['conditional_logic'] ) : '';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	do_action('wa_notifier_before_meta_field_wrapper', $field, $post);

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_attr( $field['conditional_logic'] ).'">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	do_action('wa_notifier_before_meta_field', $field, $post);

	echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

	do_action('wa_notifier_after_meta_field', $field, $post);

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('wa_notifier_after_meta_field_description', $field, $post);

	echo '</p>';

	do_action('wa_notifier_after_meta_field_wrapper', $field, $post);
}

/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function wa_notifier_wp_select( $field ) {
	global $thepostid, $post;

	$field = apply_filters ('wa_notifier_wp_select', $field, $post);

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
	$field     = wp_parse_args(
		$field, array(
			'class'             => '',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => get_post_meta( $thepostid, $field['id'], true ),
			'name'              => $field['id'],
			'custom_attributes' => array()
		)
	);

	$field['conditional_logic']	= isset( $field['conditional_logic'] ) ? json_encode( $field['conditional_logic'] ) : '';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	$description = ! empty( $field['description'] ) ? $field['description'] : '';
	
	do_action('wa_notifier_before_meta_field_wrapper', $field, $post);

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_attr( $field['conditional_logic'] ).'">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	do_action('wa_notifier_before_meta_field', $field, $post);

	echo '<select class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '"' . implode( ' ', $custom_attributes ) . '/> ';

	foreach ( $field['options'] as $key => $value ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $field['value'], false) . '>' . esc_html( $value ) . '</option>';
	}

	echo '</select>';

	do_action('wa_notifier_after_meta_field', $field, $post);

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('wa_notifier_after_meta_field_description', $field, $post);

	echo '</p>';

	do_action('wa_notifier_after_meta_field_wrapper', $field, $post);

}

/**
 * Output a radio input box.
 *
 * @param array $field
 */
function wa_notifier_wp_radio( $field ) {
	global $thepostid, $post;

	$field = apply_filters ('wa_notifier_wp_radio', $field, $post);

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : '';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['conditional_logic']	= isset( $field['conditional_logic'] ) ? json_encode( $field['conditional_logic'] ) : '';

	do_action('wa_notifier_before_meta_field_wrapper', $field, $post);

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '" data-conditions="'.esc_attr($field['conditional_logic']).'"><legend>' . wp_kses_post( $field['label'] ) . '</legend>';

	do_action('wa_notifier_before_meta_field', $field, $post);

	echo '<ul class="radio-buttons">';

	foreach ( $field['options'] as $key => $value ) {

		echo '<li><label><input
				name="' . esc_attr( $field['name'] ) . '"
				value="' . esc_attr( $key ) . '"
				type="radio"
				class="' . esc_attr( $field['class'] ) . '"
				style="' . esc_attr( $field['style'] ) . '"
				' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
				/> ' . esc_html( $value ) . '</label>
		</li>';
	}
	echo '</ul>';

	do_action('wa_notifier_after_meta_field', $field, $post);

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	do_action('wa_notifier_after_meta_field_description', $field, $post);

	echo '</fieldset>';

	do_action('wa_notifier_after_meta_field_wrapper', $field, $post);
}
