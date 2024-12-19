<?php
/**
 * Plugin Name: Infobip Omnichannel Messaging for WooCommerce
 * Plugin URI:  https://github.com/Neuralab/infobip-omnichannel-messaging-for-woocommerce
 * Description: Enhance your customer experience by integrating omnichannel communication into your WooCommerce store.
 * Version:     1.1
 * Author:      Neuralab
 * Author URI:  https://neuralab.net
 * Text Domain: infobip-omnichannel
 * Domain Path: i18n/languages
 *
 * License:     GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 *
 * WC requires at least: 8.2
 * WC tested up to:      9.4
 *
 * @package InfobipOmnichannel
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Define plugin file path.
if ( ! defined( 'IOMNI_ROOT_FILE' ) ) {
	define( 'IOMNI_ROOT_FILE', __FILE__ );
}
// Define plugin version.
if ( ! defined( 'IOMNI_PLUGIN_VER' ) ) {
	define( 'IOMNI_PLUGIN_VER', '1.1' );
}

if ( ! class_exists( 'Infobip_Omnichannel' ) ) {
	include_once __DIR__ . '/includes/class-infobip-omnichannel.php';
}

/**
 * Init the plugin.
 *
 * @return object Instance of Infobip_Omnichannel class.
 */
function infobip_omnichannel() {
	return Infobip_Omnichannel::get_instance();
}

infobip_omnichannel();
