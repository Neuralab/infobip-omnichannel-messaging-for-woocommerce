<?php
/**
 * Handles all aspects of communication with Infobip API
 *
 * @package InfobipOmnichannel\Core
 * @since   1.0
 */

namespace InfobipOmnichannel\Core;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use InfobipOmnichannel\Utility\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Request controller
 */
trait Controller {
	use \InfobipOmnichannel\Utility\Logger;

	/**
	 * Guzzle client request wrapper method
	 *
	 * @param   array $args  Arguments for creating a request.
	 *
	 * @return  object       Response object or WP_Error object in case of failed request
	 */
	private function request( $args ) {
		$args = wp_parse_args(
			array_filter( $args ),
			[
				'base_url' => Helper::get_setting( 'base_url', 'general' ),
				'api_key'  => Helper::get_setting( 'api_key', 'general' ),
				'method'   => 'POST',
				'endpoint' => '/',
				'payload'  => [],
			]
		);

		$args = apply_filters( 'omnichannel_woocommerce_request_args', $args, $this );

		try {
			$client = new Client(
				[
					'base_uri' => Helper::schemeify( $args['base_url'] ),
					'headers'  => [
						'Authorization' => 'App ' . $args['api_key'],
					],
				]
			);

			// Add transaction integrator data.
			$endpoint = add_query_arg(
				[
					'piIntegrator' => '6j3eq5',
					'piPlatform'   => 'jd4k',
				],
				$args['endpoint']
			);

			$response = $client->request( $args['method'], $endpoint, $args['payload'] );

			return json_decode( $response->getBody()->getContents() );
		} catch ( \Throwable $e ) {
			$message = Helper::get_exception_message( $e );

			self::log_error( 'Request error on endpoint: "' . $args['endpoint'] . '". Description: ' . Helper::get_exception_description( $e ) . '". Error message: ' . $message );
			return new \WP_Error( $e->getCode(), $message ?: wp_json_encode( $e ) );
		}
	}

	/**
	 * Email API sending method with formdata/multipart payload build
	 *
	 * @param   array $body  Email request body.
	 *
	 * @return  object        Response object or WP_Error object in case of failed request
	 */
	public function send_email( array $body ) {

		$payload = [];

		foreach ( $body as $name => $contents ) {
			if ( is_array( $contents ) ) {
				foreach ( $contents as $content ) {
					$part = [
						'name' => $name,
					];

					if ( 'attachment' === $name ) {
						$part['contents'] = Psr7\Utils::tryFopen( $content['attachment'], 'r' );
						$part['filename'] = $content['filename'];
					} else {
						$part['contents'] = $content;
					}

					$payload[] = $part;
				}
			} else {
				$payload[] = [
					'name'     => $name,
					'contents' => $contents,
				];
			}
		}

		$args = [
			'endpoint' => '/email/3/send',
			'payload'  => [
				'multipart' => $payload,
			],
		];

		return $this->request( $args );
	}

	/**
	 * Messages API sending method
	 *
	 * @param   array $body  Messages request body.
	 *
	 * @return  object       Response object or WP_Error object in case of failed request
	 */
	public function send_message( $body ) {
		$args = [
			'endpoint' => '/messages-api/1/messages',
			'payload'  => [
				'json' => $body,
			],
		];

		return $this->request( $args );
	}

	/**
	 * SMS sending method
	 *
	 * @param   array $args  Message data.
	 *
	 * @return  object       Response object or WP_Error object in case of failed request
	 */
	public function send_sms( $args ) {
		return $this->send_message(
			[
				'messages' => [
					[
						'channel'      => 'SMS',
						'sender'       => Helper::get_setting( 'phone_sender', 'sms' ),
						'destinations' => [
							[
								'to' => $args['recipient'],
							],
						],
						'content'      => [
							'body' => [
								'text' => $args['message'],
								'type' => 'TEXT',
							],
						],
					],
				],
			]
		);
	}
}
