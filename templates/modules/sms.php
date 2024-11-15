<?php
/**
 * Module template - sms
 *
 * Available variables:
 *
 *  $args['module'] - \InfobipOmnichannel\Modules\SMS instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<h4><?php esc_html_e( 'SMS Sending', 'infobip-omnichannel' ); ?></h4>

<div class="tabs">
	<section id="about" class="active">
		<div class="d-flex ai-center jc-between mb-3">
			<h1 class="mb-0"><?php esc_html_e( 'About', 'infobip-omnichannel' ); ?></h1>
			<form id="infobip-omnichannel-settings-enablers" action="options.php" method="post">
				<?php settings_fields( Helper::get_plugin_slug() ); ?>
				<?php Helper::load_template_part( 'switch-checkbox.php', [ 'module' => $args['module'] ] ); ?>
				<?php Helper::load_template_part( 'modal-sms.php', [ 'module' => $args['module'] ] ); ?>
				<input type="hidden" name="single_module_enabler" value="<?php echo esc_attr( $args['module']->module_id ); ?>">
			</form>
		</div>

		<p class="mb-2">
			<?php esc_html_e( 'Experience seamless communication with your customers through automated SMS notifications. Instant updates can be received on order status changes to keep customers informed.', 'infobip-omnichannel' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Additionally, take control with manual SMS messaging, allowing personalized communication directly from your WP Admin panel or towards specific customer during order edits.', 'infobip-omnichannel' ); ?>
		</p>
	</section>

	<section id="testing">
		<h1 class="mb-3"><?php esc_html_e( 'Testing', 'infobip-omnichannel' ); ?></h1>
		<p class="mb-5"><?php esc_html_e( 'Easily test sending SMS messages for validation or demonstration purposes.', 'infobip-omnichannel' ); ?></p>
		<form id="ajax-form">
			<?php wp_nonce_field( $args['module']->ajax_action_id, 'sms_send_form' ); ?>
			<input type="hidden" name="action" value="<?php echo esc_attr( $args['module']->ajax_action_id ); ?>">
			<input type="hidden" name="test_form" value="1">

			<div class="form-field">
				<label for="sms-recipient" class="form-label" ><?php esc_html_e( 'Recipient phone number', 'infobip-omnichannel' ); ?></label>
				<input id="sms-recipient" class="form-control" type="tel" name="sms_recipient" placeholder="<?php esc_html_e( 'Recipient phone number', 'infobip-omnichannel' ); ?>" data-required="true"/>
			</div>

			<div class="form-field">
				<label for="sms-message" class="form-label"><?php esc_html_e( 'SMS message', 'infobip-omnichannel' ); ?></label>
				<textarea id="sms-message" class="form-control" name="infobip_sms_message" rows="5" cols="40" readonly><?php esc_html_e( 'This is a test SMS message.', 'infobip-omnichannel' ); ?></textarea>
			</div>

			<input type="submit" name="test_sms" class="btn btn--primary" value="<?php esc_attr_e( 'Send', 'infobip-omnichannel' ); ?>" />
		</form>
	</section>

	<section id="settings">
		<h1 class="mb-3"><?php esc_html_e( 'Settings', 'infobip-omnichannel' ); ?></h1>
		<form id="infobip-omnichannel-settings" action="options.php" method="post">
			<?php settings_fields( Helper::get_plugin_slug( $args['module']->module_id ) ); ?>

			<p class="mb-3">
				<?php esc_html_e( 'Configure basic preferences for SMS sending.', 'infobip-omnichannel' ); ?>
			</p>

			<p class="mb-3">
				<?php
				printf(
					// translators: Instructions on testing SMS and checking delivery in Infobip.
					esc_html__( 'To ensure you have entered a correct sender, please use the test form and check the delivery logs in the %s.', 'infobip-omnichannel' ),
					'<a href="' . esc_url( 'https://portal.infobip.com/analyze/logs' ) . '" target="_blank">' . esc_html__( 'Infobip Logs', 'infobip-omnichannel' ) . '</a>'
				);
				?>
			</p>

			<p class="mb-5">
				<?php esc_html_e( 'Below you can input your SMS sender number.', 'infobip-omnichannel' ); ?>
			</p>

			<div class="form-field">
				<label for="infobip-omnichannel-sender" class="form-label"><?php esc_html_e( 'Sender phone number', 'infobip-omnichannel' ); ?></label>
				<input id="infobip-omnichannel-sender" class="form-control" type="tel" name="<?php echo esc_attr( $args['module']->module_input_name( 'phone_sender' ) ); ?>" value="<?php echo esc_attr( $args['module']->get_module_setting( 'phone_sender' ) ); ?>" placeholder="<?php esc_html_e( 'Sender phone number', 'infobip-omnichannel' ); ?>" data-required="true"/>
			</div>

			<input type="submit" name="submit" class="btn btn--primary" value="<?php esc_attr_e( 'Save settings', 'infobip-omnichannel' ); ?>" />

		</form>
	</section>
</div>
