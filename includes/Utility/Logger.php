<?php
/**
 * Custom logger class built on top of WooCommerce logger for errors and issues.
 *
 * @package InfobipOmnichannel\Utility
 * @since   1.0
 */

namespace InfobipOmnichannel\Utility;

defined( 'ABSPATH' ) || exit;

use \InfobipOmnichannel\Utility\Helper;

/**
 * Logger class
 */
trait Logger {
	/**
	 * Logs given message for given level and return true if successful, false otherwise
	 *
	 * @param  string $message  Message to write into log.
	 * @param  string $level    Log level.
	 *
	 * @return bool
	 */
	public static function log( $message, $level ) {
		if ( ! Helper::get_setting( 'logging_enabled', 'general' ) ) {
			return false;
		}

		// Check if WooCommerce logger function exists.
		if ( function_exists( 'wc_get_logger' ) ) {
			$logger = wc_get_logger();
		} else {
			return false;
		}

		// Prevent logger to ignore logs via threshold setting in case invalid level is sent.
		if ( ! \WC_Log_Levels::is_valid_level( $level ) ) {
			self::log( 'Invalid log level provided: ' . $level, \WC_Log_Levels::DEBUG );
			$level = \WC_Log_Levels::NOTICE;
		}

		$context = [
			'source' => Helper::get_plugin_slug(),
		];

		$logger->log( $level, 'Infobip Omnichannel v' . IOMNI_PLUGIN_VER . ' - ' . $message, $context );

		return true;
	}

	/**
	 * Write a error message to log
	 *
	 * @param   string $message  Message to write into log.
	 *
	 * @return  void
	 */
	public static function log_error( string $message ) {
		self::log( $message, 'error' );
	}
}
