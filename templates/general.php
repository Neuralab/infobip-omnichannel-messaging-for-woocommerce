<?php
/**
 * General settings template
 *
 * Available variables:
 *
 *  $args['settings] - \InfobipOmnichannel\Core\Settings instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;

if ( filter_input( INPUT_GET, 'tutorial', FILTER_VALIDATE_BOOL ) ) {
	if ( ! get_option( 'infobip_omnichannel_tutorial_seen' ) ) {
		update_option( 'infobip_omnichannel_tutorial_seen', 1 );
	}

	Helper::load_template_part( 'settings-tutorial.php', [ 'settings' => $args['settings'] ] );
	return;
}
?>

<h1><?php esc_html_e( 'General settings', 'infobip-omnichannel' ); ?></h1>

<form id="infobip-omnichannel-settings" action="options.php" method="post">
	<?php settings_fields( Helper::get_plugin_slug( 'general' ) ); ?>

	<p class="mb-5">
		<?php
		printf(
			// translators: General settings instruction text.
			esc_html__( 'Access your API key and base URL from the %1$s to unlock all functionalities. You can always start %2$s for more details.', 'infobip-omnichannel' ),
			'<a href="' . esc_url( Helper::get_infobip_url() ) . '" target="_blank">' . esc_html__( 'Infobip portal', 'infobip-omnichannel' ) . '</a>',
			'<a href="' . esc_url( Helper::get_menu_page_url( 'general', null, [ 'tutorial' => true ] ) ) . '">' . esc_html__( 'First-time configuration', 'infobip-omnichannel' ) . '</a>'
		);
		?>
	</p>

	<div class="form-field">
		<label for="infobip-omnichannel-base-url" class="form-label"><?php esc_html_e( 'Base URL', 'infobip-omnichannel' ); ?></label>
		<input id="infobip-omnichannel-base-url" class="form-control" type="text" name="<?php echo esc_attr( Helper::get_plugin_id( 'general' ) . '[base_url]' ); ?>" value="<?php echo esc_attr( Helper::get_setting( 'base_url', 'general' ) ?? false ); ?>" placeholder="<?php esc_html_e( 'Base URL', 'infobip-omnichannel' ); ?>" />
	</div>

	<div class="form-field">
		<label for="infobip-omnichannel-api-key" class="form-label"><?php esc_html_e( 'API key', 'infobip-omnichannel' ); ?></label>
		<input id="infobip-omnichannel-api-key" class="form-control" type="text" name="<?php echo esc_attr( Helper::get_plugin_id( 'general' ) . '[api_key]' ); ?>" value="<?php echo esc_attr( Helper::mask_string( Helper::get_setting( 'api_key', 'general' ) ) ); ?>" placeholder="<?php esc_html_e( 'API key', 'infobip-omnichannel' ); ?>" />
	</div>

	<div>
		<h2 class="mb-2"><?php esc_html_e( 'Debug logs', 'infobip-omnichannel' ); ?></h2>
		<div class="mb-3 form-check">
			<label for="<?php echo esc_attr( Helper::get_plugin_slug( 'general' ) . '-logging-enabled' ); ?>"><?php esc_html_e( 'Enable error logging', 'infobip-omnichannel' ); ?></label>
			<?php
			printf(
				'<input id="%s" type="checkbox" name="%s" class="logging-checkbox" %s/>',
				esc_attr( Helper::get_plugin_slug( 'general' ) . '-logging-enabled' ),
				esc_attr( Helper::get_plugin_id( 'general' ) . '[logging_enabled]' ),
				checked( Helper::get_setting( 'logging_enabled', 'general' ) === 'on' ?? false, true, false )
			);
			?>
		</div>

		<p class="mb-3">
			<?php
			printf(
				// translators: General settings description of logger setting.
				esc_html__( 'Log errors and issues, if logger is enabled and there were issues you can find them in this file %s. New log file will be created for each day and only if there were issues logged otherwise file will not exist. You can find old logs in the logs list which can be also accessed from the link provided.', 'infobip-omnichannel' ),
				'<code>' . wp_kses_post( Helper::get_log_link() ) . '</code>'
			);
			?>
		</p>

		<p class="mb-3">
			<?php
			printf(
				// translators: General settings note about debug logs.
				esc_html__( '%s: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'infobip-omnichannel' ),
				'<b>' . esc_html__( 'Note', 'infobip-omnichannel' ) . '</b>'
			);
			?>
		</p>
	</div>

	<input type="submit" name="submit" class="btn btn--primary" value="<?php esc_attr_e( 'Save settings', 'infobip-omnichannel' ); ?>" />
</form>
<?php
