<?php
/**
 * Notifier Helper Functions
 *
 * @package    Wa_Notifier
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Create new post type
 *
 * @return null
 */
function notifier_register_post_type($cpt_slug, $cpt_name, $cpt_name_plural, $args = []) {
    $labels = array (
        'name'                => $cpt_name_plural,
        'singular_name'       => $cpt_name,
        'menu_name'           => $cpt_name_plural,
        'parent_item_colon'   => "Parent {$cpt_name}",
        'all_items'           => "All {$cpt_name_plural}",
        'view_item'           => "View {$cpt_name}",
        'add_new_item'        => "Add New {$cpt_name}",
        'add_new'             => 'Add New',
        'edit_item'           => "Edit {$cpt_name}",
        'update_item'         => "Update {$cpt_name}",
        'search_items'        => "Search {$cpt_name}",
        'not_found'           => "No {$cpt_name_plural} found",
        'not_found_in_trash'  => "No {$cpt_name_plural} found in Trash"
    );
    $args = wp_parse_args($args, array (
        'label'               => $cpt_slug,
        'description'         => "{$cpt_name_plural} Post Type",
        'labels'              => $labels,
        'supports'            => array( 'title'),
        'hierarchical'        => false,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => false,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
    ));
    register_post_type($cpt_slug, $args);
}

/**
 * Create new taxonomy for a post type
 *
 * @return null
 */
function notifier_register_taxonomy($taxonomy_slug, $taxonomy_name, $taxonomy_name_plural, $cpt_slug, $args = array()) {
    $labels = array (
        'name' => $taxonomy_name_plural,
        'singular_name' => $taxonomy_name,
        'search_items' =>  "Search {$taxonomy_name_plural}",
        'all_items' => "All {$taxonomy_name_plural}",
        'parent_item' => "Parent {$taxonomy_name}",
        'parent_item_colon' => "Parent {$taxonomy_name}:",
        'edit_item' => "Edit {$taxonomy_name}",
        'update_item' => "Update {$taxonomy_name}",
        'add_new_item' => "Add New {$taxonomy_name}",
        'new_item_name' => "New {$taxonomy_name} Name",
        'menu_name' => $taxonomy_name_plural,
    );

    $args =  wp_parse_args( $args, array (
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'show_in_rest' => true,
        'rewrite' => array( 'slug' => $taxonomy_slug )
    ));

    register_taxonomy($taxonomy_slug, [ $cpt_slug ], $args);
}

/**
 * Sanitize array
 *
 * @return Array
 */
function notifier_sanitize_array ( $array ) {
	foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $value = notifier_sanitize_array($value);
        } else {
            $value = sanitize_text_field( $value );
        }
    }
    return $array;
}

/**
 * Upload image from URL programmatically
 */
function notifier_upload_file_by_url( $image_url ) {

	// it allows us to use download_url() and wp_handle_sideload() functions
	require_once ABSPATH . 'wp-admin/includes/file.php';

	// download to temp dir
	$temp_file = download_url( $image_url );

	if ( is_wp_error( $temp_file ) ) {
		return false;
	}

	// move the temp file into the uploads directory
	$file = array(
		'name'     => basename( parse_url($image_url, PHP_URL_PATH) ),
		'type'     => mime_content_type( $temp_file ),
		'tmp_name' => $temp_file,
		'size'     => filesize( $temp_file ),
	);

	$sideload = wp_handle_sideload(
		$file,
		array(
			'test_form'   => false // no needs to check 'action' parameter
		)
	);

	if ( ! empty( $sideload[ 'error' ] ) ) {
		// you may return error message if you want
		return false;
	}

	// it is time to add our uploaded image into WordPress media library
	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => $sideload[ 'url' ],
			'post_mime_type' => $sideload[ 'type' ],
			'post_title'     => basename( $sideload[ 'file' ] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$sideload[ 'file' ]
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return false;
	}

	// update medatata, regenerate image sizes
	require_once ABSPATH . 'wp-admin/includes/image.php';

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $sideload[ 'file' ] )
	);

	return $attachment_id;

}

/**
 * Sanitize phone number
 */
function notifier_sanitize_phone_number( $phone_number ) {
	return preg_replace( '/[^\d+]/', '', $phone_number );
}

/**
 * Validate phone number
 */
function notifier_validate_phone_number( $phone_number ) {
    return preg_match( '/^\+[1-9]\d{8,15}$/', $phone_number );
}
