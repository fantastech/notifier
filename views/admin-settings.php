<?php
/**
 * Admin View: Settings
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
    		$current_tab = isset($_GET['tab']) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
    		$settings_tabs = Notifier_Settings::get_settings_tabs();
		 	echo '<h2 class="nav-tab-wrapper">';
		foreach ( $settings_tabs as $tab_key => $name ) {
			$class = ( $tab_key == $current_tab ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=notifier-settings&tab=' . esc_attr( $tab_key ) . '">' . esc_html($name) . '</a>';
		}
		    echo '</h2>';
    	?>

    	<?php do_action('notifier_before_settings_fields_form', $current_tab); ?>

        <form method="POST" id="notifier_settings_form" class="notifier-settings-form notifier-settings-form-<?php echo esc_attr( $current_tab ); ?>" action="" enctype="multipart/form-data">

        	<?php do_action('notifier_before_settings_fields', $current_tab); ?>

            <?php Notifier_Settings::show_settings_fields($current_tab); ?>

            <p class="submit">
                <button name="save" class="button-primary notifier-save-button" type="submit" value="">Save changes</button>
                <?php wp_nonce_field( NOTIFIER_NAME . '-settings' ); ?>
            </p>

            <?php do_action('notifier_after_settings_fields', $current_tab); ?>

        </form>

        <?php do_action('notifier_after_settings_fields_form', $current_tab); ?>

    </div>
</div>
