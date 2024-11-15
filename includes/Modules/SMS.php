<?php
/**
 * Module which enables sending SMS messages from within WooCommerce
 *
 * @package InfobipOmnichannel\Modules
 * @since   1.0
 */

namespace InfobipOmnichannel\Modules;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;
use InfobipOmnichannel\Core\Module;
use InfobipOmnichannel\Extras\OrderMessages;
use InfobipOmnichannel\Extras\OrderStatusChanges;
use InfobipOmnichannel\Utility\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * SMS class
 */
final class SMS extends Module {

	/**
	 * AJAX action ID
	 *
	 * @var string
	 */
	public $ajax_action_id;

	/**
	 * ID of opt in checkbox for sending SMS order notifications on checkout
	 *
	 * @var string
	 */
	public static $checkout_opt_id = 'infobipomnichannel/sms-notification-opt';

	/**
	 * Mandatory init method for initializing module admin page
	 *
	 * @return  void
	 */
	public function init() {
		$this->module_id    = 'sms';
		$this->label        = __( 'SMS Sending', 'infobip-omnichannel' );
		$this->description  = __( 'Enhance your communication with customers by delivering timely updates and notifications through SMS', 'infobip-omnichannel' );
		$this->switchable   = true;
		$this->has_settings = true;

		$this->nav = [
			'about'    => __( 'About', 'infobip-omnichannel' ),
			'testing'  => __( 'Testing', 'infobip-omnichannel' ),
			'settings' => __( 'Settings', 'infobip-omnichannel' ),
		];

		$this->ajax_action_id = Helper::get_plugin_id( 'sms_send' );

		add_action( 'wp_ajax_' . $this->ajax_action_id, [ $this, $this->ajax_action_id ] );
	}

	/**
	 * SMS sending method, works for testing form and manual sms form
	 *
	 * @return  void
	 */
	public function infobip_omnichannel_sms_send() {
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'sms_send_form', FILTER_SANITIZE_SPECIAL_CHARS ), $this->ajax_action_id ) ) {
			wp_send_json_error( [ 'error' => __( 'Nonce verification failed.', 'infobip-omnichannel' ) ], 401 );
		}

		if ( ! Helper::get_setting( 'phone_sender', 'sms' ) ) {
			wp_send_json_error(
				[
					'error' => sprintf(
						// translators: Warning message for missing SMS sender info.
						esc_html__( 'SMS sender number configuration missing, please go to %s and setup a validated sender number.', 'infobip-omnichannel' ),
						'<a href="' . esc_url( Helper::get_menu_page_url( 'sms', 'settings' ) ) . '">' . esc_html__( 'SMS settings', 'infobip-omnichannel' ) . '</a>'
					),
				],
				400
			);
		}

		if ( filter_input( INPUT_POST, 'test_form', FILTER_VALIDATE_BOOL ) ) {
			$args['recipient'] = Helper::format_phone( filter_input( INPUT_POST, 'sms_recipient', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
			$args['message']   = esc_html__( 'This is a test SMS message.', 'infobip-omnichannel' );
		} else {
			$args['recipient'] = filter_input( INPUT_POST, '_billing_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?: filter_input( INPUT_POST, '_shipping_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$args['message']   = filter_input( INPUT_POST, 'infobip_sms_message', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}

		$response = $this->send_sms( $args );

		if ( ! $response || is_wp_error( $response ) ) {
			wp_send_json_error(
				[
					'error' => sprintf(
						// translators: Error message if sending SMS failed.
						__( 'Something went wrong while sending test SMS, if logging enabled, please check the %s for more info.', 'infobip-omnichannel' ),
						'<a href="' . esc_url( Helper::get_log_url() ) . '">' . __( 'logs', 'infobip-omnichannel' ) . '</a>'
					),
				],
				$response ? $response->get_error_code() : 400
			);
		}

		wp_send_json_success(
			[
				'success' => sprintf(
					// translators: SMS sent to Infobip API success message.
					__( 'SMS message successfully sent for delivery, delivery reports can be checked in %s.', 'infobip-omnichannel' ),
					'<a href="https://portal.infobip.com/analyze/logs" target="_blank">' . esc_html__( 'Infobip Logs', 'infobip-omnichannel' ) . '</a>'
				),
			]
		);
	}

	/**
	 * Are there settings missing which are needed for module to work
	 * Mandatory settings - SMS phone sender
	 *
	 * @return  bool  Are all settings properly set
	 */
	public function is_setup_needed() {
		return ! (bool) $this->get_module_setting( 'phone_sender' );
	}

	/**
	 * Initialize module features
	 *
	 * @return  void
	 */
	public function init_module() {
		new OrderMessages( $this->ajax_action_id );
		new OrderStatusChanges();
	}

	/**
	 * Check if customer opted in for SMS notification on this order
	 *
	 * @param   object $order  Order object.
	 *
	 * @return  bool           Notifications enabled/disabled
	 */
	public static function notifications_enabled( $order ) {
		return $order->get_meta( 'iomni_order_notifications_opt' ) || Package::container()->get( CheckoutFields::class )->get_field_from_object( self::$checkout_opt_id, $order, 'order' );
	}
}
