<?php
/**
 * Module which enables exporting customer data
 *
 * @package InfobipOmnichannel\Modules
 * @since   1.0
 */

namespace InfobipOmnichannel\Modules;

use InfobipOmnichannel\Core\Module;
use InfobipOmnichannel\Utility\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Export class.
 */
final class Export extends Module {

	/**
	 * AJAX action ID.
	 *
	 * @var string
	 */
	public $export_action_id;

	/**
	 * User fields which will be exported for each entry
	 *
	 * @var array
	 */
	public $export_fields = [];

	/**
	 * Mandatory init method for initializing module admin page
	 *
	 * @return  void
	 */
	public function init() {
		$this->module_id   = 'export';
		$this->label       = __( 'Data Export', 'infobip-omnichannel' );
		$this->description = __( 'Effortlessly transfer customer data and interactions from WooCommerce store to Infobip People', 'infobip-omnichannel' );
		$this->switchable  = false;

		$this->nav = [
			'about'  => __( 'About', 'infobip-omnichannel' ),
			'export' => __( 'Export', 'infobip-omnichannel' ),
		];

		$this->export_fields = [
			'first_name'         => __( 'First Name', 'infobip-omnichannel' ),
			'last_name'          => __( 'Last Name', 'infobip-omnichannel' ),
			'phone'              => __( 'Phone', 'infobip-omnichannel' ),
			'user_email'         => __( 'Email', 'infobip-omnichannel' ),
			'billing_country'    => __( 'Billing Country', 'infobip-omnichannel' ),
			'billing_postcode'   => __( 'Billing Zip Code', 'infobip-omnichannel' ),
			'billing_city'       => __( 'Billing City', 'infobip-omnichannel' ),
			'billing_address_1'  => __( 'Billing Address Line', 'infobip-omnichannel' ),
			'shipping_country'   => __( 'Shipping Country', 'infobip-omnichannel' ),
			'shipping_postcode'  => __( 'Shipping Zip Code', 'infobip-omnichannel' ),
			'shipping_city'      => __( 'Shipping City', 'infobip-omnichannel' ),
			'shipping_address_1' => __( 'Shipping Address Line', 'infobip-omnichannel' ),
		];
	}

	/**
	 * Initialize module features
	 *
	 * @return  void
	 */
	public function init_module() {
		$this->export_action_id = Helper::get_plugin_id( 'export_users' );

		add_action( 'wp_ajax_' . $this->export_action_id, [ $this, $this->export_action_id ] );

		add_filter( 'infobip_omnichannel_script_data', [ $this, 'export_headings' ] );
	}

	/**
	 * Pass fields labels to JS
	 *
	 * @param   array $data  Data passed to JS.
	 *
	 * @return  array        Data including export fields
	 */
	public function export_headings( $data ) {
		$data['user_data'] = $this->export_fields;

		return $data;
	}

	/**
	 * Fetches and returns users from DB, batches works based on query pagination
	 *
	 * @return  void
	 */
	public function infobip_omnichannel_export_users() {
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'export_form', FILTER_SANITIZE_SPECIAL_CHARS ), $this->export_action_id ) ) {
			print 'Sorry, your nonce did not verify.';
			die();
		}

		$page = filter_input( INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT ) ?: 1;

		$role_query = new \WP_User_Query(
			[
				'number'   => 10,
				'offset'   => (int) ( $page - 1 ) * 10,
				'role__in' => [ 'customer' ],
			]
		);

		$user_data = [];

		$users = $role_query->get_results();

		if ( 1 === (int) $page && ! $users ) {
			wp_send_json_error( [ 'error' => __( 'We couldn\'t find any customers to export.', 'infobip-omnichannel' ) ], 400 );
		}

		foreach ( $users as $user ) {
			$user->phone = $user->billing_phone ?: $user->shipping_phone;

			$user_data[] = array_map( fn( $meta_key ) => $user->{$meta_key} ?: '', array_keys( $this->export_fields ) );
		}

		wp_send_json_success(
			[
				'users' => $user_data,
				'total' => $role_query->get_total(),
			]
		);
	}
}
