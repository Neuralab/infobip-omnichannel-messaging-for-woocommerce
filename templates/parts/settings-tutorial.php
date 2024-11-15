<?php
/**
 * General settings tutorial template
 *
 * Available variables:
 *
 *  $args['settings] - \InfobipOmnichannel\Core\Settings instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use InfobipOmnichannel\Utility\Helper;
?>

<h1><?php esc_html_e( 'First-time Configuration', 'infobip-omnichannel' ); ?></h1>

<p class="mb-5">
	<?php esc_html_e( 'During the initial setup, you\'ll need to obtain your API key and base URL to integrate seamlessly with our services. Here\'s how to get started.', 'infobip-omnichannel' ); ?>
</p>

<section class="mb-5">
	<h1 class="d-flex ai-center mb-2">
		<span class="bullet">1</span>
		<?php esc_html_e( 'Create an Infobip account', 'infobip-omnichannel' ); ?>
	</h1>

	<p>
		<?php
		printf(
			// translators: Instructions on creating Infobip account.
			esc_html__( 'Infobip account is needed to setup the plugin so first you will need to %s.', 'infobip-omnichannel' ),
			'<a href="https://www.infobip.com/signup" target="_blank">' . esc_html__( 'create an account', 'infobip-omnichannel' ) . '</a>'
		);
		?>
	</p>
</section>

<section class="mb-5">
	<h1 class="d-flex ai-center mb-2">
		<span class="bullet">2</span>
		<?php esc_html_e( 'Connect your Infobip account', 'infobip-omnichannel' ); ?>
	</h1>

	<p class="mb-5">
		<?php
		printf(
			// translators: Instructions on getting Infobip API Base URL.
			esc_html__( 'Your API Base URL and key can be found on the home page of your %s.', 'infobip-omnichannel' ),
			'<a href="https://portal.infobip.com/homepage" target="_blank">' . esc_html__( 'Infobip account', 'infobip-omnichannel' ) . '</a>'
		);
		?>
	</p>

	<p class="mb-5">
		<?php esc_html_e( 'Copy these values and save them below.', 'infobip-omnichannel' ); ?>
	</p>

	<form id="infobip-omnichannel-settings" action="options.php" method="post">
		<?php settings_fields( Helper::get_plugin_slug( 'general' ) ); ?>

		<div class="form-field">
			<label for="infobip-omnichannel-base-url" class="form-label"><?php esc_html_e( 'Base URL', 'infobip-omnichannel' ); ?></label>
			<input id="infobip-omnichannel-base-url" class="form-control" type="text" name="<?php echo esc_attr( Helper::get_plugin_id( 'general' ) . '[base_url]' ); ?>" value="<?php echo esc_attr( Helper::get_setting( 'base_url', 'general' ) ?? false ); ?>" data-required="true"/>
		</div>

		<div class="form-field">
			<label for="infobip-omnichannel-api-key" class="form-label"><?php esc_html_e( 'API key', 'infobip-omnichannel' ); ?></label>
			<input id="infobip-omnichannel-api-key" class="form-control" type="text" name="<?php echo esc_attr( Helper::get_plugin_id( 'general' ) . '[api_key]' ); ?>" value="<?php echo esc_attr( Helper::mask_string( Helper::get_setting( 'api_key', 'general' ) ) ); ?>" data-required="true"/>
		</div>

		<input type="submit" name="submit" class="btn btn--primary" value="<?php esc_attr_e( 'Save', 'infobip-omnichannel' ); ?>" />
	</form>
</section>

<section class="mb-5">
	<h1 class="d-flex ai-center mb-2">
		<span class="bullet">3</span>
		<?php esc_html_e( 'Setup your senders and register your domain', 'infobip-omnichannel' ); ?>
	</h1>

	<h3><?php esc_html_e( 'SMS', 'infobip-omnichannel' ); ?></h3>
	<p class="mb-5">
		<?php esc_html_e( 'To start sending SMS messages you will need a sender number, you will be assigned a shared number after signup but it is recommended that you purchase your own sender number.', 'infobip-omnichannel' ); ?>
	</p>
	<p class="mb-5">
		<?php
		printf(
			// translators: Copy about getting verified numbers from Infobip.
			esc_html__( 'You can find more info about your numbers %1$s and after you decide on a number copy it into %2$s to start using the SMS module.', 'infobip-omnichannel' ),
			'<a href="https://portal.infobip.com/channels-and-numbers/channels/sms/overview" target="_blank">' . esc_html__( 'here', 'infobip-omnichannel' ) . '</a>',
			'<a href="' . esc_url( Helper::get_menu_page_url( 'sms', 'settings' ) ) . '">' . esc_html__( 'SMS settings', 'infobip-omnichannel' ) . '</a>'
		);
		?>
	</p>

	<h3><?php esc_html_e( 'Email', 'infobip-omnichannel' ); ?></h3>
	<p class="mb-5">
		<?php
		printf(
			// translators: Copy about registering email domain in Infobip.
			esc_html__( 'To use email module, you will need to use a single sender or register your email domain in Infobip portal, you can follow the %1$s to get the sender email.', 'infobip-omnichannel' ),
			'<a href="https://www.infobip.com/docs/email/get-started" target="_blank">' . esc_html__( 'Email get-started documentation', 'infobip-omnichannel' ) . '</a>',
		);
		?>
	</p>
	<p class="mb-5">
		<?php
		printf(
			// translators: Copy about registering email domain in Infobip.
			esc_html__( 'You can find all your registered domains %1$s and after you decide on the sender email address copy it into %2$s to start using the Email module.', 'infobip-omnichannel' ),
			'<a href="https://portal.infobip.com/apps/email-setup/marketing-transactional" target="_blank">' . esc_html__( 'here', 'infobip-omnichannel' ) . '</a>',
			'<a href="' . esc_url( Helper::get_menu_page_url( 'email', 'settings' ) ) . '">' . esc_html__( 'Email settings', 'infobip-omnichannel' ) . '</a>'
		);
		?>
	</p>
</section>
