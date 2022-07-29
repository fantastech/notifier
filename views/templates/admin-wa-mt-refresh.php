<?php
/**
 * Admin side template to load Notification refresh button
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$refresh_url = '?' . http_build_query(array_merge($_GET, array('refresh_status'=>'1')));

?>

<a href="<?php echo esc_attr($refresh_url); ?>" class="refresh-status page-title-action">
	Refresh Status
</a>
