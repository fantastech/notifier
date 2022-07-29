<?php
/**
 * Admin side template to load Notification Send To fields row
 *
 * @package Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo Notifier_Notifications::get_notification_send_to_fields_row('row_num');
