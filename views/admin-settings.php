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
    		$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
    		$tabs = Notifier_Settings::get_settings_tabs();
		 	echo '<h2 class="nav-tab-wrapper">';
		    foreach( $tabs as $tab => $name ){
		        $class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
		        echo "<a class='nav-tab$class' href='?page=notifier-settings&tab=$tab'>$name</a>";
		    }
		    echo '</h2>';
    	?>

    	<?php do_action('notifier_before_settings_fields_form', $current_tab); ?>

        <form method="POST" id="notifier_settings_form" class="notifier-settings-form notifier-settings-form-<?php echo $current_tab; ?>" action="" enctype="multipart/form-data">

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
