<?php
/**
 * Module which intercepts all outgoing emails and passes them to Infobip API for further processing
 *
 * @package InfobipOmnichannel\Modules
 * @since   1.0
 */

namespace InfobipOmnichannel\Modules;

use InfobipOmnichannel\Core\Module;
use InfobipOmnichannel\Core\Mailer;
use InfobipOmnichannel\Utility\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Email class
 */
class Email extends Module {

	/**
	 * AJAX action ID
	 *
	 * @var string
	 */
	public $test_action_id;

	/**
	 * Mandatory init method for initializing module admin page
	 *
	 * @return  void
	 */
	public function init() {
		$this->module_id    = 'email';
		$this->label        = __( 'Email Sending', 'infobip-omnichannel' );
		$this->description  = __( 'Improve your communication with customers by delivering timely updates and notifications through email', 'infobip-omnichannel' );
		$this->switchable   = true;
		$this->has_settings = true;

		$this->nav = [
			'about'    => __( 'About', 'infobip-omnichannel' ),
			'testing'  => __( 'Testing', 'infobip-omnichannel' ),
			'settings' => __( 'Settings', 'infobip-omnichannel' ),
		];

		$this->test_action_id = Helper::get_plugin_id( 'email_test' );

		add_action( 'wp_ajax_' . $this->test_action_id, [ $this, $this->test_action_id ] );
	}

	/**
	 * Test form email sending
	 *
	 * @return  void
	 */
	public function infobip_omnichannel_email_test() {
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'email_test_form', FILTER_SANITIZE_SPECIAL_CHARS ), $this->test_action_id ) ) {
			wp_send_json_error( [ 'error' => __( 'Nonce verification failed.', 'infobip-omnichannel' ) ], 401 );
		}

		$email_sender = Helper::get_wc_sender_address();
		if ( ! $email_sender ) {
			wp_send_json_error(
				[
					'error' => sprintf(
						// translators: Warning message for missing sender email address.
						esc_html__( 'Email sender address configuration missing please visit %s and setup an email address with validated domain.', 'infobip-omnichannel' ),
						'<a href="' . esc_url( Helper::get_menu_page_url( 'email', 'settings' ) ) . '">' . esc_html__( 'Email settings', 'infobip-omnichannel' ) . '</a>'
					),
				],
				400
			);
		}

		$args = [
			'from'    => $email_sender,
			'to'      => filter_input( INPUT_POST, 'email_recipient', FILTER_SANITIZE_EMAIL ),
			// translators: Test email subject.
			'subject' => sprintf( __( 'Test email from %s', 'infobip-omnichannel' ), get_bloginfo( 'name' ) ),
			'text'    => esc_html__( 'This is a test message email.', 'infobip-omnichannel' ),
		];

		$response = $this->send_email( $args );

		if ( ! $response || is_wp_error( $response ) ) {
			wp_send_json_error(
				[
					'error' => sprintf(
						// translators: Error message if sending SMS failed.
						__( 'Something went wrong while sending test email, if logging enabled, please check the %s for more info.', 'infobip-omnichannel' ),
						'<a href="' . esc_url( Helper::get_log_url() ) . '">' . __( 'logs', 'infobip-omnichannel' ) . '</a>'
					),
				],
				$response ? $response->get_error_code() : 400
			);
		}

		wp_send_json_success(
			[
				'success' => sprintf(
					// translators: Email sent to Infobip API success message.
					__( 'Email successfully sent for delivery, delivery reports can be checked in %s.', 'infobip-omnichannel' ),
					'<a href="https://portal.infobip.com/analyze/logs" target="_blank">' . esc_html__( 'Infobip Logs', 'infobip-omnichannel' ) . '</a>'
				),
			]
		);
	}

	/**
	 * Are there settings missing which are needed for module to work
	 * Mandatory settings - Email sender
	 *
	 * @return  bool  Are all settings properly set
	 */
	public function is_setup_needed() {
		return ! (bool) Helper::get_wc_sender_address();
	}

	/**
	 * Validate module specific settings being stored
	 *
	 * @param   array $new_values  New values being stored.
	 * @param   array $old_values  Currently stored settings.
	 *
	 * @return  void
	 */
	public function settings_validate( $new_values, $old_values ) {
		update_option( 'woocommerce_email_from_name', $new_values['email_sender_name'] );
		update_option( 'woocommerce_email_from_address', $new_values['email_sender_address'] );
	}

	/**
	 * Initialize module features
	 *
	 * @return  void
	 */
	public function init_module() {
		add_filter( 'pre_wp_mail', [ $this, 'pre_wp_mail' ], 10, 2 );
	}

	/**
	 * Override of WP_Mail function but modified with custom Mailer instead of PHPMailer
	 * Most of the functionality is preserved with exception to:
	 *     - wp_mail_content_type
	 *     - wp_mail_charset
	 *
	 * @param   void  $_null       Null value.
	 * @param   array $attributes  Email data.
	 *
	 * @return  bool               Email sending status
	 */
	public function pre_wp_mail( $_null, $attributes ) {
		$mailer = new Mailer();

		if ( isset( $attributes['to'] ) ) {
			$to = $attributes['to'];
		}

		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
		}

		if ( isset( $attributes['subject'] ) ) {
			$subject = $attributes['subject'];
		}

		if ( isset( $attributes['message'] ) ) {
			$message = $attributes['message'];
		}

		if ( isset( $attributes['headers'] ) ) {
			$headers = $attributes['headers'];
		}

		if ( isset( $attributes['attachments'] ) ) {
			$attachments = $attributes['attachments'];
		}

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}

		// Headers.
		$cc       = array();
		$bcc      = array();
		$reply_to = array();

		if ( empty( $headers ) ) {
			$headers = array();
		} else {
			if ( ! is_array( $headers ) ) {
				/*
				 * Explode the headers out, so this function can take
				 * both string headers and an array of headers.
				 */
				$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			} else {
				$tempheaders = $headers;
			}
			$headers = array();

			// If it's actually got contents.
			if ( ! empty( $tempheaders ) ) {
				// Iterate through the raw headers.
				foreach ( (array) $tempheaders as $header ) {
					if ( ! str_contains( $header, ':' ) ) {
						continue;
					}
					// Explode them out.
					list( $name, $content ) = explode( ':', trim( $header ), 2 );

					// Cleanup crew.
					$name    = trim( $name );
					$content = trim( $content );

					switch ( strtolower( $name ) ) {
						// Mainly for legacy -- process a "From:" header if it's there.
						case 'from':
							$bracket_pos = strpos( $content, '<' );
							if ( false !== $bracket_pos ) {
								// Text before the bracketed email is the "From" name.
								if ( $bracket_pos > 0 ) {
									$from_name = substr( $content, 0, $bracket_pos );
									$from_name = str_replace( '"', '', $from_name );
									$from_name = trim( $from_name );
								}

								$from_email = substr( $content, $bracket_pos + 1 );
								$from_email = str_replace( '>', '', $from_email );
								$from_email = trim( $from_email );

								// Avoid setting an empty $from_email.
							} elseif ( '' !== trim( $content ) ) {
								$from_email = trim( $content );
							}
							break;
						case 'cc':
							$cc = array_merge( (array) $cc, explode( ',', $content ) );
							break;
						case 'bcc':
							$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
							break;
						case 'reply-to':
							$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
							break;
						default:
							// Add it to our grand headers array.
							$headers[ trim( $name ) ] = trim( $content );
							break;
					}
				}
			}
		}

		// Set "From" name and email.

		// If we don't have a name from the input headers.
		if ( ! isset( $from_name ) ) {
			$from_name = 'WordPress';
		}

		/*
		 * If we don't have an email from the input headers, default to wordpress@$sitename
		 * Some hosts will block outgoing mail from this address if it doesn't exist,
		 * but there's no easy alternative. Defaulting to admin_email might appear to be
		 * another option, but some hosts may refuse to relay mail from an unknown domain.
		 * See https://core.trac.wordpress.org/ticket/5007.
		 */
		if ( ! isset( $from_email ) ) {
			// Get the site domain and get rid of www.
			$sitename   = wp_parse_url( network_home_url(), PHP_URL_HOST );
			$from_email = 'wordpress@';

			if ( null !== $sitename ) {
				if ( str_starts_with( $sitename, 'www.' ) ) {
					$sitename = substr( $sitename, 4 );
				}

				$from_email .= $sitename;
			}
		}

		/**
		 * Filters the email address to send from.
		 *
		 * @since 2.2.0
		 *
		 * @param string $from_email Email address to send from.
		 */
		$from_email = apply_filters( 'wp_mail_from', $from_email );

		/**
		 * Filters the name to associate with the "from" email address.
		 *
		 * @since 2.3.0
		 *
		 * @param string $from_name Name associated with the "from" email address.
		 */
		$from_name = apply_filters( 'wp_mail_from_name', $from_name );

		try {
			$mailer->set_from( $from_name, $from_email );
		} catch ( \Exception $e ) {
			$mail_error_data                   = compact( 'to', 'subject', 'message', 'attachments' );
			$mail_error_data['exception_code'] = $e->getCode();

			self::log_error( 'Error setting from email', [ 'message' => $e->getMessage() ] );

			/** This filter is documented in wp-includes/pluggable.php */
			do_action( 'wp_mail_failed', new \WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );

			return false;
		}

		// Set mail's subject and body.
		$mailer->set_subject( $subject );
		$mailer->set_body( $message );

		// Set destination addresses, using appropriate methods for handling addresses.
		$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

		foreach ( $address_headers as $address_header => $addresses ) {
			if ( empty( $addresses ) ) {
				continue;
			}

			foreach ( (array) $addresses as $address ) {
				try {
					// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
					$recipient_name = '';

					if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
						if ( count( $matches ) === 3 ) {
							$recipient_name = $matches[1];
							$address        = $matches[2];
						}
					}

					switch ( $address_header ) {
						case 'to':
							$mailer->set_to( $recipient_name, $address );
							break;
						case 'cc':
							$mailer->set_cc( $recipient_name, $address );
							break;
						case 'bcc':
							$mailer->set_bcc( $recipient_name, $address );
							break;
						case 'reply_to':
							$mailer->set_reply_to( $recipient_name, $address );
							break;
					}
				} catch ( \Exception $e ) {
					continue;
				}
			}
		}

		/**
		 * Filters the wp_mail() content type.
		 *
		 * @since 2.3.0
		 *
		 * @param string $content_type Default wp_mail() content type.
		 *
		 * OBSOLETE - not being used for API communication.
		 */
		apply_filters( 'wp_mail_content_type', Helper::apply_filters_obsolete_message( 'wp_mail_content_type' ) );

		/**
		 * Filters the default wp_mail() charset.
		 *
		 * @since 2.3.0
		 *
		 * @param string $charset Default email charset.
		 *
		 * OBSOLETE - not being used for API communication.
		 */
		apply_filters( 'wp_mail_charset', Helper::apply_filters_obsolete_message( 'wp_mail_charset' ) );

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $filename => $attachment ) {
				$filename = is_string( $filename ) ? $filename : '';

				try {
					$mailer->set_attachments( $attachment, $filename );
				} catch ( \Exception $e ) {
					continue;
				}
			}
		}

		$mail_data = compact( 'to', 'subject', 'message', 'attachments' );

		$mailer_response = $mailer->send();

		if ( is_wp_error( $mailer_response ) ) {
			$mail_data['exception_code'] = $mailer_response->get_error_code();

			/**
			 * Fires in case Mailer responded with WP_Error.
			 *
			 * @since 4.4.0
			 *
			 * @param WP_Error $error A WP_Error object with the Mailer Exception message, and an array
			 *                        containing the mail recipient, subject, message, and attachments.
			 */
			do_action( 'wp_mail_failed', new \WP_Error( 'wp_mail_failed', $mailer_response->get_error_message(), $mail_data ) );

			return false;
		}

		/**
		 * Fires after Mailer has successfully sent an email.
		 *
		 * The firing of this action does not necessarily mean that the recipient(s) received the
		 * email successfully. It only means that the `send` method above was able to
		 * process the request without any errors.
		 *
		 * @since 5.9.0
		 *
		 * @param array $mail_data {
		 *     An array containing the email recipient(s), subject, message, and attachments.
		 *
		 *     @type string[] $to          Email addresses to send message.
		 *     @type string   $subject     Email subject.
		 *     @type string   $message     Message contents.
		 *     @type string[] $attachments Paths to files to attach.
		 * }
		 */
		do_action( 'wp_mail_succeeded', $mail_data );

		return true;
	}
}
