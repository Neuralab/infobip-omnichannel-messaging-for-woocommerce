<?php
/**
 * Banner to start first time configuration
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<div class="banner">
	<h3 class="banner__title"><?php esc_html_e( 'First-time Configuration', 'infobip-omnichannel' ); ?></h3>
	<p class="mb-3"><?php esc_html_e( 'To get started with the plugin, you need to complete the initial setup. These steps are essential for establishing a connection and ensuring the plugin functions correctly.', 'infobip-omnichannel' ); ?></p>
	<a class="btn btn--primary" href="<?php echo esc_url( Helper::get_menu_page_url( 'general', null, [ 'tutorial' => true ] ) ); ?>"><?php esc_html_e( 'Start', 'infobip-omnichannel' ); ?></a>
</div>
