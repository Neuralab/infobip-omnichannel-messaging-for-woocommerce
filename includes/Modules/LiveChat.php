<?php
/**
 * Module which enables Live Chat widget.
 *
 * @package InfobipOmnichannel\Modules
 * @since   1.1
 */

namespace InfobipOmnichannel\Modules;

use InfobipOmnichannel\Core\Module;

defined( 'ABSPATH' ) || exit;

/**
 * LiveChat class.
 */
final class LiveChat extends Module {
	/**
	 * Mandatory init method for initializing module admin page
	 *
	 * @return  void
	 */
	public function init() {
		$this->module_id    = 'live-chat';
		$this->label        = __( 'Live Chat', 'infobip-omnichannel' );
		$this->description  = __( 'Provide real-time support and build stronger customer relationships with Live Chat directly on your WooCommerce store', 'infobip-omnichannel' );
		$this->switchable   = true;
		$this->has_settings = true;

		$this->nav = [
			'about'    => __( 'About', 'infobip-omnichannel' ),
			'settings' => __( 'Settings', 'infobip-omnichannel' ),
		];
	}

	/**
	 * Are there settings missing which are needed for module to work
	 * Mandatory settings - Live Chat code snippet
	 *
	 * @return  bool  Are all settings properly set
	 */
	public function is_setup_needed() {
		return ! (bool) $this->get_module_setting( 'live_chat_code' );
	}

	/**
	 * Initialize module features
	 *
	 * @return  void
	 */
	public function init_module() {
		add_action( 'wp_footer', [ $this, 'render_live_chat_code' ] );
	}

	/**
	 * Injects Live Chat code snippet to page footer.
	 *
	 * @return  void
	 */
	public function render_live_chat_code() {
		echo $this->get_module_setting( 'live_chat_code' ) ?: '';
	}
}
