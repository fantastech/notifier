<?php
/**
 * Triggers CPT Meta Box
 *
 * @package WA_Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post_id;
?>
<div class="meta-fields">
	<div class="general-fields">
		 <div>
			<?php
			notifier_wp_select(
				array(
					'id'                => NOTIFIER_PREFIX . 'trigger',
					'value'             => get_post_meta( $post_id, NOTIFIER_PREFIX . 'trigger', true),
					'label'             => 'Select Trigger',
					'description'       => 'Select the trigger when you want to fire a Natification from WANotifier.com.',
					'options'           => Notifier_Notification_Triggers::get_notification_triggers_dropdown()
				)
			);
			?>
		</div>
		<?php
		$send_to_conditions = array (
				array (
					'field'		=> NOTIFIER_PREFIX . 'trigger',
					'operator'	=> '!=',
					'value'		=> ''
				)
			);
		?>
		<div class="form-field notifier-trigger-merge-tags" data-conditions="<?php echo esc_attr ( json_encode( $send_to_conditions ) ); ?>">

		</div>
	</div>
</div>
