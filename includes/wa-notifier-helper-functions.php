<?php
/**
 * WA Notifier Helper Functions
 *
 * @package     WA_Notifier
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Create new post type
 *
 * @return null
 */
function wa_notifier_register_post_type($cpt_slug, $cpt_name, $cpt_name_plural, $args = []) {
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
