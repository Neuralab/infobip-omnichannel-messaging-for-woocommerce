<?php
/**
 * Class which handles automatic SMS messages when handling orders, changing status or creating new order
 *
 * @package InfobipOmnichannel\Extras
 * @since   1.0
 */

namespace InfobipOmnichannel\Extras;

use InfobipOmnichannel\Modules\SMS;

defined( 'ABSPATH' ) || exit;

/**
 * OrderStatusChanges class
 */
class OrderStatusChanges {
	use \InfobipOmnichannel\Core\Controller;

	/**
	 * CLass construct
	 */
	public function __construct() {
		add_filter( 'woocommerce_order_status_changed', [ $this, 'order_status_changed_notification' ], 10, 4 );
		add_action( 'woocommerce_review_order_before_submit', [ $this, 'checkout_order_notifications_opt_checkbox' ] );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'checkout_order_notifications_opt_checkbox_save' ], 10, 1 );
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'checkout_order_notifications_opt_validate' ], 10, 2 );
		add_action( 'woocommerce_init', [ $this, 'block_checkout_order_notifications_opt_checkbox' ] );
		add_action( 'woocommerce_blocks_validate_location_address_fields', [ $this, 'validate_phone_field' ], 10, 3 );
	}

	/**
	 * Validation for SMS notifications opt checkbox
	 *
	 * @param   array  $data    Data passed for validation.
	 * @param   object $errors  Validation errors.
	 *
	 * @return  void
	 */
	public function checkout_order_notifications_opt_validate( $data, $errors ) {
		$order_notification_opt = filter_input( INPUT_POST, 'iomni_order_notifications_opt', FILTER_VALIDATE_BOOL );

		if ( $order_notification_opt ) {
			$phone = $data['shipping_phone'] ?? $data['billing_phone'] ?? null;

			if ( ! $phone ) {
				$errors->add( 'order_notifications_opt_checkbox', __( 'Please enter a phone number in address fields if you wish to receive SMS notifications.', 'infobip-omnichannel' ) );
			}
		}
	}

	/**
	 * Save opt checkbox value to order meta
	 *
	 * @param   int $order_id  Order ID.
	 *
	 * @return  void
	 */
	public function checkout_order_notifications_opt_checkbox_save( $order_id ) {
		$order_notification_opt = filter_input( INPUT_POST, 'iomni_order_notifications_opt', FILTER_VALIDATE_BOOL );

		if ( $order_notification_opt ) {
			$order = wc_get_order( $order_id );
			$order->add_meta_data( 'iomni_order_notifications_opt', (bool) $order_notification_opt, true );
			$order->save();
		}
	}

	/**
	 * Render opt checkbox
	 *
	 * @return  void
	 */
	public function checkout_order_notifications_opt_checkbox() {
		woocommerce_form_field(
			'iomni_order_notifications_opt',
			[
				'type'  => 'checkbox',
				'class' => [ 'input-checkbox' ],
				'label' => __( 'Receive notifications about this order via SMS.', 'infobip-omnichannel' ),
			],
			WC()->checkout->get_value( 'iomni_order_notifications_opt' )
		);
	}

	/**
	 * Render opt checkbox for block checkout
	 *
	 * @return  void
	 */
	public function block_checkout_order_notifications_opt_checkbox() {
		woocommerce_register_additional_checkout_field(
			[
				'id'                => SMS::$checkout_opt_id,
				'label'             => __( 'Receive notifications about this order via SMS.', 'infobip-omnichannel' ),
				'location'          => 'order',
				'type'              => 'checkbox',
				'validate_callback' => function ( $field_value ) {
					if ( $field_value && ! WC()->session->get( 'phone' ) ) {
						return new \WP_Error( SMS::$checkout_opt_id, __( 'Please enter a phone number in address fields if you wish to receive SMS notifications.', 'infobip-omnichannel' ) );
					}
				},
			]
		);
	}

	/**
	 * Store phone from checkout fields so we can make the field required if users want SMS notifications
	 *
	 * @param   \WP_Error $errors  Errors in fields.
	 * @param   array     $fields  List of field data.
	 * @param   string    $group   Field group name.
	 *
	 * @return  void
	 */
	public function validate_phone_field( $errors, $fields, $group ) {
		WC()->session->set( 'phone', $fields['phone'] );
	}

	/**
	 * Sends order status change SMS message to the customer which created the order
	 *
	 * @param   int    $order_id    Order ID.
	 * @param   string $old_status  Old order status which was changed.
	 * @param   string $new_status  New order status to which it was changed.
	 * @param   object $order       WC_Order object.
	 *
	 * @return  void
	 */
	public function order_status_changed_notification( $order_id, $old_status, $new_status, $order ) {
		if ( ! SMS::notifications_enabled( $order ) ) {
			return;
		}

		$recipient = $order->get_billing_phone() ?: $order->get_shipping_phone() ?: null;

		if ( ! $recipient ) {
			return;
		}

		$args = [
			'recipient' => $recipient,
			// translators: Status changes SMS text.
			'message'   => sprintf( __( 'The status of your order (%1$s) has been updated to: %2$s. Click here to view the details %3$s', 'infobip-omnichannel' ), $order_id, wc_get_order_status_name( $new_status ), get_site_url() ),
		];

		$this->send_sms( $args );
	}

}
