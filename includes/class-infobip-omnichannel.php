<?php
/**
 * Main class to initialize plugin functionality
 *
 * @package InfobipOmnichannel
 * @since   1.0
 */

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Utilities\FeaturesUtil;

use \InfobipOmnichannel\Modules\Email;
use \InfobipOmnichannel\Modules\SMS;
use \InfobipOmnichannel\Modules\Export;

use \InfobipOmnichannel\Core\Settings;
use \InfobipOmnichannel\Utility\Helper;

/**
 * Infobip_Omnichannel class.
 */
final class Infobip_Omnichannel {
	/**
	 * Instance of the current class, null before first usage.
	 *
	 * @var Infobip_Omnichannel
	 */
	protected static $instance = null;

	/**
	 * Return class instance.
	 *
	 * @return Infobip_Omnichannel
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		return wp_die( 'Cloning is forbidden!' );
	}

	/**
	 * Deserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		return wp_die( 'Deserializing instances is forbidden!' );
	}

	/**
	 * Class construct.
	 */
	private function __construct() {
		$this->define_constants();
		$this->include();
		$this->init_hooks();
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Define plugin constants.
	 */
	private function define_constants() {
		$this->define( 'IOMNI_DIR_PATH', plugin_dir_path( IOMNI_ROOT_FILE ) );
		$this->define( 'IOMNI_DIR_URL', plugin_dir_url( IOMNI_ROOT_FILE ) );
		$this->define( 'IOMNI_PLUGIN_ID', 'infobip_omnichannel' );
		$this->define( 'IOMNI_PLUGIN_SLUG', 'infobip-omnichannel' );
	}

	/**
	 * Includes dependencies.
	 */
	public function include() {
		// Include the autoloader.
		if ( file_exists( IOMNI_DIR_PATH . 'vendor/autoload.php' ) ) {
			require IOMNI_DIR_PATH . 'vendor/autoload.php';
		}

		// Order of creating Module instances also directs the order Modules appear in admin.
		new SMS();     // 1
		new Email();   // 2
		new Export();  // 3

		new Settings();
	}

	/**
	 * Init plugin hooks.
	 */
	private function init_hooks() {
		// Activation and deactivation hooks.
		register_activation_hook( IOMNI_ROOT_FILE, [ __CLASS__, 'activate' ] );
		register_deactivation_hook( IOMNI_ROOT_FILE, [ __CLASS__, 'deactivate' ] );
		register_uninstall_hook( IOMNI_ROOT_FILE, [ __CLASS__, 'uninstall' ] );

		// Load text domain.
		add_action( 'init', [ $this, 'load_textdomain' ], 0 );

		add_action( 'before_woocommerce_init', [ $this, 'declare_hpos_compatibility' ] );

		add_filter( 'plugin_action_links', [ $this, 'add_settings_link' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_script' ] );
		add_action( 'in_admin_header', [ $this, 'remove_admin_notices' ] );
	}

	/**
	 * Register plugin's admin JS script.
	 */
	public function register_admin_script() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( false !== strpos( $screen_id, IOMNI_PLUGIN_SLUG ) || 'woocommerce_page_wc-orders' === $screen_id ) {
			wp_enqueue_script( 'omnichannel-script', IOMNI_DIR_URL . '/assets/dist/js/omnichannel.js', [ 'wp-i18n' ], IOMNI_PLUGIN_VER, true );
			wp_enqueue_style( 'omnichannel-style', IOMNI_DIR_URL . '/assets/dist/css/omnichannel.css', [], IOMNI_PLUGIN_VER );

			$default_data = [
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'plugin_id' => IOMNI_PLUGIN_ID,
			];

			// Add extra data for script.
			$main_data = apply_filters( 'infobip_omnichannel_script_data', $default_data );

			wp_localize_script( 'omnichannel-script', 'main_data', $main_data );
		}
	}

	/**
	 * Set plugin as compatible with HPOS.
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', IOMNI_ROOT_FILE, true );
		}
	}

	/**
	 * Adds the link to the settings page on the plugins WP page.
	 *
	 * @param   array  $links  List of links for plugin list page.
	 * @param   string $file   Plugin filename loaded.
	 *
	 * @return  array          List of link including settings one.
	 */
	public function add_settings_link( $links, $file ) {
		if ( strpos( $file, 'infobip-omnichannel-messaging-for-woocommerce' ) !== false ) {
			array_unshift( $links, '<a href="' . get_admin_url( null, 'admin.php?page=' . IOMNI_PLUGIN_SLUG ) . '">' . __( 'Settings', 'infobip-omnichannel' ) . '</a>' );
		}

		return $links;
	}

	/**
	 * Remove global notices from Infobip omnichannel screen
	 *
	 * @return  void
	 */
	public function remove_admin_notices() {
		$current_screen = get_current_screen();

		if ( false !== str_contains( $current_screen->id, Helper::get_plugin_slug() ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Activate plugin.
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}
	}

	/**
	 * Deactivate plugin.
	 */
	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}
	}

	/**
	 * Uninstall plugin.
	 */
	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}

		// Clear plugin data.
		foreach ( Settings::$menu_pages as $key => $value ) {
			delete_option( Helper::get_plugin_id( 'dashboard' === $key ? '' : $key ) );
		}
		delete_option( 'infobip_omnichannel_tutorial_seen' );
	}

	/**
	 * Load text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'infobip-omnichannel', false, dirname( plugin_basename( IOMNI_ROOT_FILE ) ) . 'i18n/languages/' );
	}
}
