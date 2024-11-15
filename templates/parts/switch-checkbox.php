<?php
/**
 * Switch to enable/disable modules
 *
 * Available variables:
 *
 *  $args['module'] - \InfobipOmnichannel\Core\Module children instance
 *
 * @package InfobipOmnichannel\Template
 * @version 1.0
 */

use \InfobipOmnichannel\Utility\Helper;
?>

<label class="switch">
	<input
	type="checkbox"
	id="<?php echo esc_attr( Helper::get_plugin_slug() . '-' . $args['module']->module_id . '-enabled' ); ?>"
	name="<?php echo esc_attr( Helper::get_plugin_id() . '[' . $args['module']->module_id . '_enabled]' ); ?>"
	class="iomni-modal-switch-open enable-checkbox"
	data-modal="<?php echo esc_attr( 'modal-' . $args['module']->module_id ); ?>"
	<?php checked( 'on' === Helper::get_setting( $args['module']->module_id . '_enabled' ) ?? false, true ); ?>
	>

	<span class="switch__slider"></span>
</label>

