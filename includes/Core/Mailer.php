<?php
/**
 * Class that replaces PHPMailer and gathers all mail data to be sent to Infobip API
 *
 * @package InfobipOmnichannel\Core
 * @since   1.0
 */

namespace InfobipOmnichannel\Core;

defined( 'ABSPATH' ) || exit;

use \InfobipOmnichannel\Utility\Helper;

/**
 * Mail formatting class
 */
class Mailer {
	use \InfobipOmnichannel\Core\Controller;

	/**
	 * From address
	 *
	 * @var string
	 */
	public $from;

	/**
	 * To addresses
	 *
	 * @var array
	 */
	public $to = [];

	/**
	 * CC addresses
	 *
	 * @var array
	 */
	public $cc = [];

	/**
	 * BCC addresses
	 *
	 * @var array
	 */
	public $bcc = [];

	/**
	 * Reply To address
	 *
	 * @var string
	 */
	public $replyTo;

	/**
	 * Email subject
	 *
	 * @var string
	 */
	public $subject;

	/**
	 * Email html body
	 *
	 * @var array
	 */
	public $html;

	/**
	 * Email attachments
	 *
	 * @var array
	 */
	public $attachments = [];

	/**
	 * Send Email with set data
	 *
	 * @return  object  Response object
	 */
	public function send() {
		return $this->send_email( array_filter( get_object_vars( $this ) ) );
	}

	/**
	 * Validates and formats sender email
	 *
	 * @param   string $name           Email sender name.
	 * @param   string $email_address  Email sender address.
	 *
	 * @return  void
	 *
	 * @throws  \Exception  Invalid email format provided.
	 */
	public function set_from( $name, $email_address ) {
		if ( ! is_email( $email_address ) ) {
			throw new \Exception( __( 'Email address is not valid.', 'infobip-omnichannel' ), 406 );
		}

		$this->from = Helper::format_email_name( $name, $email_address );
	}

	/**
	 * Set email subject
	 *
	 * @param   string $subject  Email subject.
	 *
	 * @return  void
	 */
	public function set_subject( $subject ) {
		$this->subject = $subject;
	}

	/**
	 * Set email body
	 *
	 * @param   string $body  Email body.
	 *
	 * @return  void
	 */
	public function set_body( $body ) {
		$this->html = $body;
	}

	/**
	 * Adds email recipients addresses
	 *
	 * @param   string $name           Email sender name.
	 * @param   string $email_address  Email sender address.
	 *
	 * @return  void
	 */
	public function set_to( $name, $email_address ) {
		$this->add_recipient( 'to', $name, $email_address );
	}

	/**
	 * Adds email CC addresses
	 *
	 * @param   string $name           CC email name.
	 * @param   string $email_address  CC email address.
	 *
	 * @return  void
	 */
	public function set_cc( $name, $email_address ) {
		$this->add_recipient( 'cc', $name, $email_address );
	}

	/**
	 * Adds email BCC addresses
	 *
	 * @param   string $name           BCC email name.
	 * @param   string $email_address  BCC email address.
	 *
	 * @return  void
	 */
	public function set_bcc( $name, $email_address ) {
		$this->add_recipient( 'bcc', $name, $email_address );
	}

	/**
	 * Sets ReplyTo address
	 *
	 * @param   string $name           ReplyTo email name.
	 * @param   string $email_address  ReplyTo email address.
	 *
	 * @return  void
	 */
	public function set_reply_to( $name, $email_address ) {
		$this->replyTo = Helper::format_email_name( $name, $email_address );
	}

	/**
	 * Set attachments to send
	 *
	 * @param   string $attachment  Attachment name.
	 * @param   string $filename    Filename.
	 *
	 * @return  void
	 */
	public function set_attachments( $attachment, $filename ) {
		$this->attachments[] = [
			'attachment' => $attachment,
			'filename'   => $filename,
		];
	}

	/**
	 * Adds emails to already saved ones
	 *
	 * @param   string $field          Field to add data to.
	 * @param   string $name           Email name.
	 * @param   string $email_address  Email address.
	 *
	 * @return  void
	 */
	public function add_recipient( $field, $name, $email_address ) {
		if ( is_email( $email_address ) ) {
			if ( $name ) {
				$this->{ $field }[] = [
					'to'          => $email_address,
					'placeholder' => [
						'name' => $name,
					],
				];
			} else {
				$this->{ $field }[] = $email_address;
			}
		}
	}
}
