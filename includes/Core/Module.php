<?php
/**
 * Module class to extend and create specific modules
 *
 * @package InfobipOmnichannel\Core
 * @since   1.0
 */

namespace InfobipOmnichannel\Core;

defined( 'ABSPATH' ) || exit;

use \InfobipOmnichannel\Utility\Helper;

/**
 * Module class
 */
abstract class Module {
	use \InfobipOmnichannel\Core\Controller;

	/**
	 * Module ID
	 *
	 * @var string
	 */
	public $module_id;

	/**
	 * Module label
	 *
	 * @var string
	 */
	public $label;

	/**
	 * Short description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Can the module be switched off/on
	 *
	 * @var bool
	 */
	public $switchable;

	/**
	 * Module internal navigation
	 *
	 * @var array
	 */
	public $nav;

	/**
	 * Does the module need registered settings handle in options DB table
	 *
	 * @var bool
	 */
	public $has_settings;

	/**
	 * Class construct
	 *
	 * @return  void
	 */
	public function __construct() {
		$this->init();

		$this->register_settings();

		if ( ! $this->switchable || $this->is_enabled() ) {
			$this->init_module();
		}

		if ( method_exists( $this, 'settings_validate' ) ) {
			add_action(
				'pre_update_option',
				function( $value, $option, $old_value ) {
					if ( Helper::get_plugin_id( $this->module_id ) === $option ) {
						return $this->settings_validate( $value, $old_value );
					}

					return $value;
				},
				10,
				3
			);
		}
	}

	/**
	 * Checks if module is enabled
	 *
	 * @return bool  Enabled/Disabled
	 */
	public function is_enabled() {
		return (bool) Helper::get_setting( $this->module_id . '_enabled' );
	}

	/**
	 * Register admin settings pages for module
	 *
	 * @return  void
	 */
	protected function register_settings() {
		add_filter( 'omnichannel_woocommerce_modules', [ $this, 'add_menu_page' ] );
	}

	/**
	 * Admin page callback
	 *
	 * @param   array $menu_pages  All menu pages loaded.
	 *
	 * @return  array              All menu pages including current module
	 */
	public function add_menu_page( $menu_pages ) {
		$menu_pages[ $this->module_id ] = $this;

		return $menu_pages;
	}

	/**
	 * Get specific module setting from DB
	 *
	 * @param   string $setting  Setting name.
	 *
	 * @return  mixed            Setting value
	 */
	public function get_module_setting( $setting ) {
		return Helper::get_setting( $setting, $this->module_id ) ?? false;
	}

	/**
	 * Format input name to enable storing data in module settings
	 *
	 * @param   string $name  Input name.
	 *
	 * @return  string        Formatted input name
	 */
	public function module_input_name( $name ) {
		return Helper::get_plugin_id( $this->module_id ) . '[' . $name . ']';
	}

	/**
	 * Loads module template from templates/modules folder
	 *
	 * @return  void
	 */
	public function load_module_template() {
		Helper::load_template( 'modules/' . $this->module_id . '.php', [ 'module' => $this ] );
	}

	/**
	 * Setup module admin page and settings
	 *
	 * @return  void
	 */
	abstract protected function init();

	/**
	 * Setup module features when enabled
	 *
	 * @return  void
	 */
	abstract protected function init_module();
}
