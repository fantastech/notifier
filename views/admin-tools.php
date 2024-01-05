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
            <table>
                <tbody class="tools">
                    <tr class="export-customers">
                        <th>
                            <strong class="name">Export WooCommerce Customers</strong>
                            <p class="description">Export all your WooCommerce customers in CSV format to import them in WANotifier.</p>
                        </th>
                        <td class="export-customer-tool">
							<input name="export_customer" type="submit" class="button button-large" value="Export">
                            <?php wp_nonce_field( NOTIFIER_NAME . '-tools-export-customers' ); ?>
				        </td>                        
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
