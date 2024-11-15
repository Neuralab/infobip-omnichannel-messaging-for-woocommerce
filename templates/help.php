<?php
/**
 * Information and helpful links
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<h1 class="mb-3"><?php esc_html_e( 'Help & Support', 'infobip-omnichannel' ); ?></h1>
<p class="mb-5"><?php esc_html_e( 'Explore our Help & Support section for easy access to resources and assistance.', 'infobip-omnichannel' ); ?></p>

<h2 class="mb-2"><?php esc_html_e( 'Contact us', 'infobip-omnichannel' ); ?></h2>
<p class="mb-3"><?php esc_html_e( 'Reach out to us via email for any inquiries or assistance you may need.', 'infobip-omnichannel' ); ?></p>
<a class="d-inline-block mb-5" href="mailto:info@infobip.com"><?php echo esc_html( 'support@infobip.com' ); ?></a>

<h2 class="mb-2"><?php esc_html_e( 'First-time configuration', 'infobip-omnichannel' ); ?></h2>
<p class="mb-3"><?php esc_html_e( 'If needed, you can start first time configuration again here.', 'infobip-omnichannel' ); ?></p>

<a href="<?php echo esc_url( Helper::get_menu_page_url( 'general', null, [ 'tutorial' => true ] ) ); ?>">
	<?php esc_html_e( 'Start First-time Configuration', 'infobip-omnichannel' ); ?>
</a>
