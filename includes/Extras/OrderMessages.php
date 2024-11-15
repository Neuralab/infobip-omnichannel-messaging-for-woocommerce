<?php
/**
 * Class to add custom SMS message metabox to admin order page, enables manually sending SMS messages
 *
 * @package InfobipOmnichannel\Extras
 * @since   1.0
 */

namespace InfobipOmnichannel\Extras;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use InfobipOmnichannel\Modules\SMS;

defined( 'ABSPATH' ) || exit;

use \InfobipOmnichannel\Utility\Helper;

/**
 * OrderMessages class
 */
class OrderMessages {
	use \InfobipOmnichannel\Core\Controller;

	/**
	 * AJAX ID for triggering SMS sending method
	 *
	 * @var string
	 */
	public $action_id;

	/**
	 * Class construct.
	 *
	 * @param   string $action  AJAX action ID.
	 *
	 * @return  void
	 */
	public function __construct( $action ) {
		$this->action_id = $action;

		add_action( 'add_meta_boxes_woocommerce_page_wc-orders', [ $this, 'admin_order_manual_sms_metabox' ] );
	}

	/**
	 * Adds metabox with manual SMS message form
	 *
	 * @param   object $order  Order object.
	 *
	 * @return  void
	 */
	public function admin_order_manual_sms_metabox( $order ) {
		if ( ! SMS::notifications_enabled( $order ) ) {
			return;
		}

		$screen = class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
		? wc_get_page_screen_id( 'shop-order' )
		: 'shop_order';

		add_meta_box(
			'custom',
			__( 'Send Customer SMS', 'infobip-omnichannel' ),
			[ $this, 'manual_sms_metabox' ],
			$screen,
			'side',
			'high'
		);
	}

	/**
	 * Metabox template callback
	 *
	 * @return  void
	 */
	public function manual_sms_metabox() {
		Helper::load_template_part( 'metabox-sms.php', [ 'action_id' => $this->action_id ] );
	}
}
