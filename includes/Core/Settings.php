<?php
/**
 * Setup all plugin settings pages and options
 *
 * @package InfobipOmnichannel\Core
 * @since   1.0
 */

namespace InfobipOmnichannel\Core;

defined( 'ABSPATH' ) || exit;

use \InfobipOmnichannel\Utility\Helper;

/**
 * Settings class
 */
class Settings {
	use \InfobipOmnichannel\Core\Controller;

	/**
	 * Capability needed to edit plugin settings
	 *
	 * @var string
	 */
	private $capability = 'manage_woocommerce';

	/**
	 * Plugin settings
	 *
	 * @var string
	 */
	private $settings;

	/**
	 * List of plugin menu pages in WP Admin
	 *
	 * @var array
	 */
	public static $menu_pages = [];

	/**
	 * List of plugin modules
	 *
	 * @var array
	 */
	public $modules = [];

	/**
	 * Class construct
	 *
	 * @return  void
	 */
	public function __construct() {
		$this->modules = apply_filters( 'omnichannel_woocommerce_modules', $this->modules );

		$this->set_menu_pages();
		$this->set_settings();
		$this->set_saving_capability();

		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
		add_action( 'admin_init', [ $this, 'settings_register' ] );
		add_action( 'updated_option', [ $this, 'settings_updated' ], 10, 3 );
	}

	/**
	 * If a method is called that matches page slug wrap it with settings template before calling the specific render
	 *
	 * @param   string $method  Method called.
	 * @param   array  $args    Method arguments passed.
	 *
	 * @return  void
	 */
	public function __call( $method, $args ) {
		if ( in_array( $method, array_keys( self::$menu_pages ), true ) ) {
			$this->page_render( $method, $args );
		}
	}

	/**
	 * Fetch settings from DB
	 *
	 * @return  void
	 */
	private function set_settings() {
		$this->settings = Helper::get_settings();
	}

	/**
	 * Set all the admin pages from plugin settings
	 *
	 * @return  void
	 */
	private function set_menu_pages() {
		self::$menu_pages['dashboard'] = __( 'Dashboard', 'infobip-omnichannel' );
		self::$menu_pages              = array_merge( self::$menu_pages, $this->modules );
		self::$menu_pages['general']   = __( 'General Settings', 'infobip-omnichannel' );
		self::$menu_pages['help']      = __( 'Help & Support', 'infobip-omnichannel' );
	}

	/**
	 * Define capability needed for saving plugin settings
	 *
	 * @return  void
	 */
	private function set_saving_capability() {
		foreach ( self::$menu_pages as $key => $value ) {
			add_filter( 'option_page_capability_' . Helper::get_plugin_slug( 'dashboard' === $key ? '' : $key ), fn() => $this->capability );
		}
	}

	/**
	 * Wrap admin pages and add navigation
	 *
	 * @param   string $menu_page  Menu page to render.
	 * @param   array  $args       Extra arguments.
	 *
	 * @return  void
	 */
	private function page_render( $menu_page, $args ) {
		Helper::load_template(
			'wrapper.php',
			[
				'settings'      => $this,
				'module'        => array_key_exists( $menu_page, $this->modules ) ? $this->modules[ $menu_page ] : null,
				'menu_page'     => $menu_page,
				'template_args' => $args,
			]
		);
	}

	/**
	 * Validate non module specific settings
	 *
	 * @param   array $values    The sanitized option value.
	 *
	 * @return  array|WP_Error   Pass on the values or trigger error
	 */
	public function settings_validate( $values ) {
		// Check if API access credentials are valid.
		if ( isset( $values['api_key'] ) || isset( $values['base_url'] ) ) {
			$api_key = Helper::get_setting( 'api_key', 'general' );

			// Prevent saving masked data.
			if ( false !== strpos( $values['api_key'], '•' ) ) {
				$values['api_key'] = $api_key;
			}
		}

		// Handle enabling module from inside module settings and prevent overriding other module enablers.
		$single_enabler = filter_input( INPUT_POST, 'single_module_enabler', FILTER_SANITIZE_SPECIAL_CHARS );
		if ( $single_enabler ) {
			$settings = Helper::get_settings();
			if ( $values ) {
				$settings = array_merge( (array) $settings, (array) $values );
			} else {
				unset( $settings[ $single_enabler . '_enabled' ] );
			}

			$values = $settings;
		}

		if ( $values ) {
			foreach ( $values as $key => $value ) {
				if ( str_contains( $key, 'code' ) ) {
					continue;
				}

				$values[ $key ] = sanitize_text_field( $value );
			}
		}

		return $values;
	}

	/**
	 * Add menu pages to WP
	 *
	 * @return  void
	 */
	public function register_settings_page() {
		add_menu_page(
			__( 'Omnichannel', 'infobip-omnichannel' ),
			__( 'Omnichannel', 'infobip-omnichannel' ),
			$this->capability,
			Helper::get_plugin_slug(),
			'',
			Helper::get_base64_icon( 'infobip' ),
		);

		foreach ( self::$menu_pages as $page_slug => $page ) {
			$page_label = is_object( $page ) ? $page->label : $page;

			add_submenu_page(
				Helper::get_plugin_slug(),
				$page_label,
				$page_label,
				$this->capability,
				Helper::get_plugin_slug( 'dashboard' === $page_slug ? '' : $page_slug ),
				[ $this, $page_slug ]
			);
		}
	}

	/**
	 * Register settings for storing in options DB table
	 *
	 * @return  void
	 */
	public function settings_register() {
		register_setting(
			Helper::get_plugin_slug(),
			Helper::get_plugin_id(),
			[ $this, 'settings_validate' ]
		);

		register_setting(
			Helper::get_plugin_slug( 'general' ),
			Helper::get_plugin_id( 'general' ),
			[ $this, 'settings_validate' ]
		);

		foreach ( $this->modules as $module ) {
			if ( $module->has_settings ) {
				register_setting(
					Helper::get_plugin_slug( $module->module_id ),
					Helper::get_plugin_id( $module->module_id ),
					[ $this, 'settings_validate' ]
				);
			}
		}
	}

	/**
	 * Returns array of helpful link for Infobip related integrations.
	 *
	 * @return  array  Helpful link with descriptions and documentation links.
	 */
	public static function get_helpful_links() {
		return [
			'zapier'  => [
				'title'       => __( 'Zapier Integration', 'infobip-omnichannel' ),
				'description' => __( 'Automate your workflows by integrating Infobip messaging channels with Zapier', 'infobip-omnichannel' ),
				'anchor_text' => __( 'Learn more about Zapier messaging integration', 'infobip-omnichannel' ),
				'anchor_url'  => 'https://www.infobip.com/docs/integrations/zapier-for-infobip#integration',
			],
			'slack'   => [
				'title'       => __( 'Slack Integration', 'infobip-omnichannel' ),
				'description' => __( 'Streamline team collaboration by integrating Infobip Conversations with Slack', 'infobip-omnichannel' ),
				'anchor_text' => __( 'Learn more about Slack messaging integration', 'infobip-omnichannel' ),
				'anchor_url'  => 'https://www.infobip.com/docs/integrations/slack-for-conversations#integration-guide',
			],
			'postman' => [
				'title'       => __( 'Postman Integration', 'infobip-omnichannel' ),
				'description' => __( 'Explore Infobip’s public API collections on Postman', 'infobip-omnichannel' ),
				'anchor_text' => __( 'Discover Infobip on Postman', 'infobip-omnichannel' ),
				'anchor_url'  => 'https://www.postman.com/infobip',
			],
		];
	}
}
