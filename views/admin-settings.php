<?php
/**
 * Admin View: Settings
 *
 * @package WA_Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap wa-notifier">
    <h1>Settings</h1>

    <div class="wa-notifier-wrapper">

        <form method="POST" id="wa_notifier_settings_form" action="" enctype="multipart/form-data">
            <?php WA_Notifier_Settings::show_settings_fields(); ?>
            <p class="submit">
                <button name="save" class="button-primary wa-notifier-save-button" type="submit" value="">Save changes</button>
                <?php wp_nonce_field( WA_NOTIFIER_NAME . '-settings' ); ?>
            </p>
        </form>
    </div>
</div>