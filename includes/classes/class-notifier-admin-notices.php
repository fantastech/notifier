<?php
/**
 * Admin notices class
 *
 * @package    Wa_Notifier
 */
class Notifier_Admin_Notices {

	public $notices = array();

	/**
	 * Notifier Constructor.
	 */
	public function __construct($notices) {
		$this->notices = $notices;
		add_action('admin_notices' , array( $this, 'show_notices'));
	}

	/**
	 * Show notices
	 */
	public function show_notices() {
		if(empty($this->notices)) {
			return;
		}

		foreach($this->notices as $notice) {
			?>
				<div class="notice notice-<?php echo esc_attr($notice['type']); ?> is-dismissible">
				    <p><?php echo esc_html($notice['message']); ?></p>
				</div>
			<?php
		}
	}

}
