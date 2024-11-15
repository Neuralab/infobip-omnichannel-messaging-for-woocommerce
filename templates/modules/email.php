<?php
/**
 * Module template - Email
 *
 * Available variables:
 *
 *  $args['module'] - \InfobipOmnichannel\Modules\Email instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<h4><?php esc_html_e( 'Email Sending', 'infobip-omnichannel' ); ?></h4>

<div class="tabs">
	<section id="about" class="active">
		<div class="d-flex ai-center jc-between mb-2">
			<h1 class="mb-0"><?php esc_html_e( 'About', 'infobip-omnichannel' ); ?></h1>
			<form id="infobip-omnichannel-settings-enablers" action="options.php" method="post">
				<?php settings_fields( Helper::get_plugin_slug() ); ?>
				<?php Helper::load_template_part( 'switch-checkbox.php', [ 'module' => $args['module'] ] ); ?>
				<?php Helper::load_template_part( 'modal-email.php', [ 'module' => $args['module'] ] ); ?>
				<input type="hidden" name="single_module_enabler" value="<?php echo esc_attr( $args['module']->module_id ); ?>">
			</form>
		</div>

		<p>
			<?php esc_html_e( 'Experience seamless communication with your customers through automated Email notifications. Instant updates can be received on order status changes to keep customers informed.', 'infobip-omnichannel' ); ?>
		</p>
	</section>

	<section id="testing">
		<h1 class="mb-3"><?php esc_html_e( 'Testing', 'infobip-omnichannel' ); ?></h1>
		<p class="mb-5">
			<?php esc_html_e( 'Easily test sending email messages for validation or demonstration purposes.', 'infobip-omnichannel' ); ?>
		</p>
		<form id="ajax-form">
			<?php wp_nonce_field( $args['module']->test_action_id, 'email_test_form' ); ?>
			<input type="hidden" name="action" value="<?php echo esc_attr( $args['module']->test_action_id ); ?>">

			<div class="form-field">
				<label for="email-recipient" class="form-label"><?php esc_html_e( 'Recipient email address', 'infobip-omnichannel' ); ?></label>
				<input id="email-recipient" class="form-control" type="email" name="email_recipient" placeholder="<?php esc_html_e( 'Recipient email address', 'infobip-omnichannel' ); ?>" data-required="true"/>
			</div>

			<div class="form-field">
				<label for="email-message" class="form-label"><?php esc_html_e( 'Email message', 'infobip-omnichannel' ); ?></label>
				<textarea id="email-message" class="form-control" name="email_message" rows="5" cols="40" readonly><?php esc_html_e( 'This is a test message email.', 'infobip-omnichannel' ); ?></textarea>
			</div>

			<input type="submit" name="test_email" class="btn btn--primary" value="<?php esc_attr_e( 'Send test message', 'infobip-omnichannel' ); ?>" />
		</form>
	</section>

	<section id="settings">
		<h1 class="mb-3"><?php esc_html_e( 'Settings', 'infobip-omnichannel' ); ?></h1>
		<form id="infobip-omnichannel-settings" action="options.php" method="post">
			<?php settings_fields( Helper::get_plugin_slug( $args['module']->module_id ) ); ?>

			<p class="mb-3">
				<?php esc_html_e( 'Configure basic preferences for Email sending.', 'infobip-omnichannel' ); ?>
			</p>

			<p class="mb-3">
				<?php
				printf(
					// translators: Description about setting up email senders in Infobip.
					esc_html__( 'Before using email sending, you should follow the %s to setup your email sender address.', 'infobip-omnichannel' ),
					'<a href="' . esc_url( Helper::get_menu_page_url( 'general', null, [ 'tutorial' => true ] ) ) . '" target="_blank">' . esc_html__( 'First-time configuration', 'infobip-omnichannel' ) . '</a>'
				);
				?>
			</p>

			<p class="mb-3">
				<?php
				printf(
					// translators: Instructions on testing emails and checking delivery in Infobip.
					esc_html__( 'To ensure you have entered a correct sender, please use the test form and check the delivery logs in the %s', 'infobip-omnichannel' ),
					'<a href="' . esc_url( 'https://portal.infobip.com/analyze/logs' ) . '" target="_blank">' . esc_html__( 'Infobip Logs', 'infobip-omnichannel' ) . '</a>'
				);
				?>
			</p>

			<p class="mb-3">
				<?php esc_html_e( 'Below you can input your email address with verified domain and custom "from" email name.', 'infobip-omnichannel' ); ?>
			</p>


			<div class="form-field">
				<label for="infobip-omnichannel-sender-name" class="form-label"><?php esc_html_e( '"From" email name', 'infobip-omnichannel' ); ?></label>
				<input id="infobip-omnichannel-sender-name" class="form-control" type="text" name="<?php echo esc_attr( $args['module']->module_input_name( 'email_sender_name' ) ); ?>" value="<?php echo esc_attr( get_option( 'woocommerce_email_from_name' ) ); ?>" placeholder="<?php esc_html_e( '"From" email name', 'infobip-omnichannel' ); ?>" />
			</div>

			<div class="form-field">
				<label for="infobip-omnichannel-sender-address" class="form-label"><?php esc_html_e( '"From" email address', 'infobip-omnichannel' ); ?></label>
				<input id="infobip-omnichannel-sender-address" class="form-control" type="email" name="<?php echo esc_attr( $args['module']->module_input_name( 'email_sender_address' ) ); ?>" value="<?php echo esc_attr( get_option( 'woocommerce_email_from_address' ) ); ?>" placeholder="<?php esc_html_e( '"From" email address', 'infobip-omnichannel' ); ?>" data-required="true"/>
			</div>

			<input type="submit" name="submit" class="btn btn--primary" value="<?php esc_attr_e( 'Save settings', 'infobip-omnichannel' ); ?>" />

		</form>

	</section>

</div>
