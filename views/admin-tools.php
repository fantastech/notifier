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
        <h3>Tools</h3>
        <form method="POST" id="notifier_tools_form" class="notifier-tools-form" action="" enctype="multipart/form-data">
            <div class="notifier-tool-wrap">
                <div class="notifier-tool-details">
                    <h3 class="notifier-tool-name">Export WooCommerce Customers</h3>
                    <p class="notifier-tool-description">Export all your WooCommerce customers in CSV format to import them in WANotifier.</p>
                </div>
                <div class="notifier-tool-action">
					<input name="export_customer" type="submit" class="button button-large" value="Export">
                    <?php wp_nonce_field( NOTIFIER_NAME . '-tools-export-customers' ); ?>
		        </div>
            </div>
        </form>
    </div>
</div>
