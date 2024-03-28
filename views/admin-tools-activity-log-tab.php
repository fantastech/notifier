<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$selected_date = '';
if (!empty($_POST) && check_admin_referer(NOTIFIER_NAME . '-tools-activity-log')) {
    $selected_date = isset($_POST['activity_date']) ? sanitize_text_field($_POST['activity_date']) : '';
    $logs = Notifier_Tools::fetch_user_activity_logs_by_date();
}

$dates = Notifier_Tools::get_activity_log_dates_for_current_user();
?>

<?php if ('yes' === get_option('notifier_enable_activity_log')): ?>
    <?php if (!empty($dates)): ?>
        <form method="POST" id="notifier_activity_log_form" class="notifier-tools-form" action="" enctype="multipart/form-data">
            <div class="notifier-tool-wrap">
                <div class="notifier-tool-details">
                    <h3 class="notifier-tool-name">Activity Log</h3>
                </div>
                <div class="notifier-tool-action">
                    <select name="activity_date">
                        <option value="">Select date</option>
                        <?php foreach ($dates as $date): ?>
                            <option value="<?php echo esc_attr($date->log_date); ?>" <?php selected($selected_date, $date->log_date); ?>><?php echo esc_html($date->log_date); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input name="get_activity_logs" type="submit" class="button button-large" value="View Log">
                    <?php wp_nonce_field(NOTIFIER_NAME . '-tools-activity-log'); ?>
                </div>
            </div>
        </form>

        <?php if (!empty($logs)): ?>
            <div class="activity-logs">
                <?php foreach ($logs as $log): ?>
                    <strong><?php echo esc_html(date('Y-m-d H:i:s', strtotime($log->timestamp))); ?></strong>: 
                    <?php echo esc_html($log->message); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>        
    <?php else: ?>
        <p><b>No activity records found.</b></p>
    <?php endif; ?>
<?php else: ?>
    <div class="notice notice-error is-dismissible">
        <p>It appears that the activity log option is not enabled. To enable it, please go to the <b>Settings page</b>, navigate to the <b>Advanced tab</b>, and turn on the <b>Enable activity log</b> option. You can also directly access this setting by <a href="/wp-admin/admin.php?page=notifier-settings&tab=advanced">clicking here</a>.</p>
    </div>
<?php endif; ?>

