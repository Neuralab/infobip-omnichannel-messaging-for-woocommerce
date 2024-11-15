<?php
/**
 * Dashboard template
 *
 * Available variables:
 *
 *  $args['settings] - \InfobipOmnichannel\Core\Settings instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<h1 class="mb-3"><?php esc_html_e( 'Dashboard', 'infobip-omnichannel' ); ?></h1>

<?php if ( ! get_option( 'infobip_omnichannel_tutorial_seen' ) ) { ?>
	<?php Helper::load_template_part( 'banner-tutorial.php', [ 'settings' => $args['settings'] ] ); ?>
<?php } ?>
<h2><?php esc_html_e( 'Features', 'infobip-omnichannel' ); ?></h2>

<form id="infobip-omnichannel-settings-enablers" action="options.php" method="post">
	<?php settings_fields( Helper::get_plugin_slug() ); ?>
	<div class="grid-dashboard mb-5">
		<?php foreach ( $args['settings']->modules as $module ) { ?>
			<div class="module">
				<div>
					<div class="mb-2"><?php Helper::render_svg( 'module-' . $module->module_id ); ?></div>
					<h3 class="module__title"><?php echo esc_html( $module->label ); ?></h3>
					<p class="module__description"><?php echo esc_html( $module->description ); ?></p>
				</div>

				<div>
					<?php if ( $module->switchable && $module->is_enabled() && $module->is_setup_needed() ) { ?>
						<div class="alert alert--info my-3">
							<?php esc_html_e( 'Configuration needed.', 'infobip-omnichannel' ); ?>
							<a href="<?php echo esc_url( Helper::get_menu_page_url( $module->module_id, 'settings' ) ); ?>">
								<?php esc_html_e( 'Go to settings', 'infobip-omnichannel' ); ?>
							</a>
						</div>
					<?php } ?>
					<div class="module__cta d-flex jc-between ai-center">
						<div>
							<a href="<?php echo esc_url( Helper::get_menu_page_url( $module->module_id ) ); ?>"><?php esc_html_e( 'View Details', 'infobip-omnichannel' ); ?></a>
						</div>
						<div>
							<?php if ( $module->switchable ) { ?>
								<?php Helper::load_template_part( 'switch-checkbox.php', [ 'module' => $module ] ); ?>
							<?php } ?>
						</div>
					</div>

					<?php if ( $module->switchable ) { ?>
						<?php Helper::load_template_part( 'modal-' . $module->module_id . '.php', [ 'module' => $module ] ); ?>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
</form>

<h2><?php esc_html_e( 'Useful links', 'infobip-omnichannel' ); ?></h2>
<div class="grid-dashboard">
	<div class="module">
		<div class="mb-3">
			<h3 class="module__title"><?php esc_html_e( 'General settings', 'infobip-omnichannel' ); ?></h3>
			<p class="module__description"><?php esc_html_e( 'Set up the Base URL and API key for seamless integration and functionality', 'infobip-omnichannel' ); ?></p>
		</div>

		<a href="<?php menu_page_url( Helper::get_plugin_slug( 'general' ) ); ?>">
			<?php esc_html_e( 'Go to General settings', 'infobip-omnichannel' ); ?>
		</a>
	</div>
	<div class="module">
		<div class="mb-3">
			<h3 class="module__title"><?php esc_html_e( 'Help', 'infobip-omnichannel' ); ?></h3>
			<p class="module__description"><?php esc_html_e( 'Access help and support for any questions or concerns', 'infobip-omnichannel' ); ?></p>
		</div>

		<a href="<?php menu_page_url( Helper::get_plugin_slug( 'help' ) ); ?>">
			<?php esc_html_e( 'Go to Help & Support', 'infobip-omnichannel' ); ?>
		</a>
	</div>
</div>
