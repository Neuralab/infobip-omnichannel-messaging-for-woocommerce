<?php
/**
 * Single order view metabox for sending manual SMS template
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */
?>

<div id="infobip-message" class="infobip-message-metabox">
	<?php wp_nonce_field( $args['action_id'], 'sms_send_form' ); ?>

	<div>
		<label for="infobip-message-text">
			<?php esc_html_e( 'Write a message', 'infobip-omnichannel' ); ?>
			<?php echo wp_kses_post( wc_help_tip( __( 'Send user a custom SMS message. If you wish to know if the SMS was delivered to the customer please log into your Infobip account dashboard and check the logs.', 'infobip-omnichannel' ) ) ); ?>
		</label>
		<textarea id="infobip-message-text" name="infobip_sms_message" class="input-text" rows="5" cols="35"></textarea>
	</div>

	<button id="infobip-message-button" type="button" class="button">
		<?php esc_attr_e( 'Send', 'infobip-omnichannel' ); ?>
	</button>
</div>
