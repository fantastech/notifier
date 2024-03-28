<?php
/**
 * Admin View: Tools
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap notifier">
    <div class="notifier-wrapper">
    	<?php
    		$current_tab = isset($_GET['tab']) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'export_woo_customer';
            $tool_tabs = Notifier_Tools::get_tools_tabs();
		 	echo '<h2 class="nav-tab-wrapper">';
			foreach ( $tool_tabs as $tab_key => $name ) {
				$class = ( $tab_key == $current_tab ) ? ' nav-tab-active' : '';
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=notifier-tools&tab=' . esc_attr( $tab_key ) . '">' . esc_html($name) . '</a>';
			}
		    echo '</h2>';
    	?>

    	<?php do_action('notifier_before_tools_fields_form', $current_tab); ?>
            <?php Notifier_Tools::tools_tab_output($current_tab); ?>
        <?php do_action('notifier_after_tools_fields_form', $current_tab); ?>

    </div>
</div>
