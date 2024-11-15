<?php
/**
 * Utility helper functions to help with formatting or fetching data
 *
 * @package InfobipOmnichannel\Utility
 * @since   1.0
 */

namespace InfobipOmnichannel\Utility;

defined( 'ABSPATH' ) || exit;

/**
 * Helper class
 */
class Helper {

	/**
	 * Directory containing built assets
	 *
	 * @var string
	 */
	private static $asset_dir = 'assets/dist';

	/**
	 * Link to Infobip portal
	 *
	 * @var string
	 */
	private static $infobip_portal = 'https://portal.infobip.com/homepage';

	/**
	 * Format email address
	 *
	 * @param   string $name     Email name.
	 * @param   string $address  Email address.
	 *
	 * @return  string           Formatted address with name
	 */
	public static function format_email_name( $name, $address ) {
		return $name . ' <' . $address . '>';
	}

	/**
	 * Removes all the characters from numeric phone numbers
	 *
	 * @param   string $number  Phone number.
	 *
	 * @return  string          Cleaned up phone number
	 */
	public static function format_phone( $number ) {
		return preg_replace( '/[^0-9]/', '', $number );
	}

	/**
	 * Return plugin slug or concat it with suffix
	 *
	 * @param   string|null $suffix  Suffix to add onto slug.
	 *
	 * @return  string               Plugin slug
	 */
	public static function get_plugin_slug( $suffix = null ) {
		return IOMNI_PLUGIN_SLUG . ( $suffix ? '-' . $suffix : '' );
	}

	/**
	 * Return plugin id or concat it with suffix
	 *
	 * @param   string|null $suffix  Suffix to add onto id.
	 *
	 * @return  string               Plugin id
	 */
	public static function get_plugin_id( $suffix = null ) {
		return IOMNI_PLUGIN_ID . ( $suffix ? '_' . $suffix : '' );
	}

	/**
	 * Returns infobip portal url
	 *
	 * @return  string  Infobip portal url
	 */
	public static function get_infobip_url() {
		return self::$infobip_portal;
	}

	/**
	 * Get link for menu page with or without section id
	 *
	 * @param   string $menu_page   Menu page ID for which to fetch URL.
	 * @param   array  $section     Section name.
	 * @param   array  $query_args  Data to add to url as query arguments.
	 *
	 * @return  string              Menu page URL
	 */
	public static function get_menu_page_url( $menu_page, $section = null, $query_args = array() ) {
		$url = admin_url( 'admin.php?page=' . self::get_plugin_slug( $menu_page ) );

		if ( $section ) {
			$url = add_query_arg( 'section', $section, $url );
		}

		if ( ! empty( $query_args ) ) {
			$url = add_query_arg( $query_args, $url );
		}

		return $url;
	}

	/**
	 * Filter hooks passed through this function have no effect on code
	 * Settings they are filtering are being automatically set on the API side of integration and cannot be altered
	 *
	 * @param   string $hook  Hook name.
	 *
	 * @return  string        Message about hook not being used
	 */
	public static function apply_filters_obsolete_message( $hook ) {
		// translators: Message for obsolete hooks.
		return sprintf( __( 'Hook "%s" is currently not being used, these settings are being automatically handled by Infobip mailer.', 'infobip-omnichannel' ), $hook );
	}

	/**
	 * Fetch plugin settings
	 *
	 * @param   string $source  Which settings slug to get settings for.
	 *
	 * @return  array           Plugin settings from options DB table
	 */
	public static function get_settings( $source = null ) {
		return get_option( self::get_plugin_id( $source ), null );
	}

	/**
	 * Fetch specific plugin setting
	 *
	 * @param   string $setting  Setting name.
	 * @param   string $source   Which settings slug to get setting for.
	 *
	 * @return  array            Plugin setting from options DB table
	 */
	public static function get_setting( $setting, $source = null ) {
		$settings = self::get_settings( $source );

		return $settings[ $setting ] ?? null;
	}

	/**
	 * Get Email sender address from WC settings
	 *
	 * @return  string          Email address
	 */
	public static function get_wc_sender_address() {
		return sanitize_email( get_option( 'woocommerce_email_from_address' ) );
	}

	/**
	 * Replace most of the string with dot character to mask it
	 *
	 * @param   string $string  String to replace characters for.
	 *
	 * @return  string          String with replaced characters
	 */
	public static function mask_string( $string ) {
		return $string ? str_repeat( '•', 17 ) . substr( $string, -4 ) : false;
	}

	/**
	 * If needed add the scheme the the url
	 *
	 * @param   string $url  URL to add the scheme to.
	 *
	 * @return  string       URL with scheme
	 */
	public static function schemeify( $url ) {
		$url_parsed = wp_parse_url( $url );

		if ( $url_parsed && ! isset( $url_parsed['scheme'] ) ) {
			$url = 'https://' . $url;
		}

		return $url;
	}

	/**
	 * Extract the domain from the email address
	 *
	 * @param   string $email  Email address.
	 *
	 * @return  string         Email address domain
	 */
	public static function get_email_domain( $email ) {
		return substr( $email, strpos( $email, '@' ) + 1 );
	}

	/**
	 * Get image path in the assets folder
	 *
	 * @param   string $img  Image name.
	 *
	 * @return  string       Image path
	 */
	public static function get_img_path( $img ) {
		$img_path = IOMNI_DIR_PATH . self::$asset_dir . '/img/' . $img;

		if ( ! file_exists( $img_path ) ) {
			return;
		}

		return $img_path;
	}

	/**
	 * Get image URL from the assets folder
	 *
	 * @param   string $img  Image name.
	 *
	 * @return  string       Image URL
	 */
	public static function get_img_url( $img ) {
		return IOMNI_DIR_URL . self::$asset_dir . '/img/' . $img;
	}

	/**
	 * Fetch a SVG from assets
	 *
	 * @param   string $svg_name  SVG name.
	 *
	 * @return  string|null       SVG
	 */
	public static function get_svg( $svg_name ) {
		$svg = self::get_img_path( $svg_name . '.svg' );

		return $svg ? file_get_contents( $svg ) : null;
	}

	/**
	 * Display SVG
	 *
	 * @param   string $svg_name  SVG name.
	 *
	 * @return  void
	 */
	public static function render_svg( $svg_name ) {
		echo self::get_svg( $svg_name ); // @codingStandardsIgnoreLine - SVG already escaped and optimized and ready for display.
	}

	/**
	 * Encodes icon as base64 data URI
	 *
	 * @param   string $icon  Icon name.
	 *
	 * @return  string        Encoded icon
	 */
	public static function get_base64_icon( $icon ) {
		return 'data:image/svg+xml;base64,' . base64_encode( self::get_svg( 'icon-' . $icon ) ); //@codingStandardsIgnoreLine - base64_encode used safely
	}

	/**
	 * Extract exception message from object
	 *
	 * @param   object $exception  Exception thrown.
	 *
	 * @return  string             Exception message
	 */
	public static function get_exception_message( $exception ) {
		try {
			return (string) $exception->getResponse()->getBody()->getContents();
		} catch ( \Throwable $th ) {
			return (string) $exception->getMessage();
		}
	}

	/**
	 * Create a readable description for possible exception issues
	 *
	 * @param   object $exception  Exception thrown.
	 *
	 * @return  string             Exception description
	 */
	public static function get_exception_description( $exception ) {
		// CURL exception won't return code, only if connecting to server is successful will the server return exception code.
		if ( $exception->getCode() ) {
			return __( 'Issues with data sent to Infobip servers, check if all the Module settings are properly set or data you are sending is of correct format and try again. If the issue persists please contact plugin support.', 'infobip-omnichannel' );
		} else {
			return __( 'Issues connecting to Infobip servers, check if Base URL and API key are properly set and try again. If the issue persists please contact plugin support.', 'infobip-omnichannel' );
		}
	}

	/**
	 * Creates a url for todays error log
	 *
	 * @return  string  URL to view the log in WC status
	 */
	public static function get_log_url() {
		return get_admin_url( null, 'admin.php?page=wc-status&tab=logs&view=single_file&file_id=' . self::get_plugin_slug( gmdate( 'Y-m-d', time() ) ) );
	}

	/**
	 * Creates a link for todays error log
	 *
	 * @return  html  Anchor to view the log in WC status
	 */
	public static function get_log_link() {
		return '<a href="' . esc_url( self::get_log_url() ) . '">' . esc_html( self::get_plugin_slug( gmdate( 'Y-m-d', time() ) ) ) . '</a>';
	}

	/**
	 * Loads templates from plugin "templates" folder
	 *
	 * @param   string $template_file  Template name.
	 * @param   array  $args           Template arguments.
	 *
	 * @return  void
	 */
	public static function load_template( string $template_file, array $args = array() ) {
		load_template( IOMNI_DIR_PATH . 'templates/' . $template_file, false, $args );
	}

	/**
	 * Wrapper for load template which only loads from "parts" subfolder in "templates" folder
	 *
	 * @param   string $template_file  Template name.
	 * @param   array  $args           Template arguments.
	 *
	 * @return  void
	 */
	public static function load_template_part( string $template_file, array $args = array() ) {
		self::load_template( 'parts/' . $template_file, $args );
	}
}
