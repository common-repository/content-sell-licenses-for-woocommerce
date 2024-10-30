<?php
/**
 * WooCommerce Price Based Country install
 *
 * @version 1.8.5
 * @package WCPBC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * csellwoo_Install Class
 */
class CSELLWOO_Install {

	/**
	 * Database update files.
	 *
	 * @var array
	 */
	private static $db_updates = array(
		'1.0.0'  => 'csellwoo_install_100'
	);

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'update_db' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 10 );
		add_action( 'in_plugin_update_message-content-sell-in-woocommerce/content-sell-in-woocommerce.php', array( __CLASS__, 'in_plugin_update_message' ) );
	}

	/**
	 * Update database to the last version.
	 */
	public static function update_db() {
		if ( empty( $_GET['update_csellwoo_nonce'] ) ) {
			return;
		}

		check_admin_referer( 'do_update_content_sell_in_woocommerce', 'update_content_sell_in_woocommerce_nonce' );

		include_once dirname( __FILE__ ) . '/csellwoo-update-functions.php';

		$current_version = self::get_install_version();
		foreach ( self::$db_updates as $version => $callback ) {
			if ( version_compare( $current_version, $version, '<' ) ) {
				if ( function_exists( $callback ) ) {
					call_user_func( $callback );
				}
			}
		}

		self::update_csellwoo_version();

		csellwoo_Admin_Notices::add_notice( 'updated' );
	}

	/**
	 * Check version and run the updater is required.
	 */
	public static function check_version() {
		if ( defined( 'IFRAME_REQUEST' ) ) {
			return;
		}

		$current_db_version = self::get_install_version();
		$needs_db_update    = false;

		if ( false !== $current_db_version && version_compare( $current_db_version, WCPBC()->version, '<' ) ) {
			$update_versions = array_keys( self::$db_updates );
			$needs_db_update = version_compare( $current_db_version, end( $update_versions ), '<' );

			if ( $needs_db_update ) {
				csellwoo_Admin_Notices::add_temp_notice( 'update_db' );
			}
		}

		if ( ! $needs_db_update && WCPBC()->version !== $current_db_version ) {

			self::update_csellwoo_version();

			if ( false === $current_db_version ) {

				// New install. Update GeoIP database for WC<3.9.
				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.4', '>=' ) && version_compare( WC_VERSION, '3.9', '<' ) ) {
					csellwoo_Update_GeoIP_DB::install();
				}

				do_action( 'wc_price_based_country_installed' );
			}
		}
	}

	/**
	 * Update WCPBC version to current.
	 */
	private static function update_csellwoo_version() {
		delete_option( 'wc_price_based_country_version' );
		add_option( 'wc_price_based_country_version', WCPBC()->version );
	}

	/**
	 * Get version installed.
	 */
	private static function get_install_version() {
		$install_version = get_option( 'wc_price_based_country_version', false );
		if ( false === $install_version && get_option( '_oga_wppbc_countries_groups' ) ) {
			$install_version = '1.3.1';
		}
		return $install_version;
	}

	/**
	 * Plugin activation. Check if the product version has changed.
	 */
	public static function plugin_activate() {
		$unistall_version = get_transient( 'csellwoo_unistall' );
		if ( $unistall_version && is_callable( array( 'WC_Cache_Helper', 'get_transient_version' ) ) ) {
			$product_version = WC_Cache_Helper::get_transient_version( 'product' );
			if ( $product_version && $product_version !== $unistall_version ) {
				// Sync all because products have changed.
				csellwoo_Product_Sync::sync_all();
			}
		}
		delete_transient( 'csellwoo_unistall' );
	}

	/**
	 * Plugin deactivation.
	 */
	public static function plugin_deactivate() {
		if ( is_callable( array( 'WC_Cache_Helper', 'get_transient_version' ) ) ) {
			$product_version = WC_Cache_Helper::get_transient_version( 'product' );
			set_transient( 'csellwoo_unistall', $product_version, DAY_IN_SECONDS * 30 );
		}
	}

	/**
	 * Run the last update db.
	 *
	 * @since 1.8.8
	 */
	public static function update_database() {
		include_once dirname( __FILE__ ) . '/csellwoo-update-functions.php';
		$callback = end( self::$db_updates );
		call_user_func( $callback );
		self::update_csellwoo_version();

		return __( 'Agreeme Licenses for Woocommerce database update complete. Thank you for updating to the latest version!', 'content-sell-in-woocommerce' );
	}

	/**
	 * Display plugin upgrade notice.
	 *
	 * @param array $args An array of plugin metadata.
	 */
	public static function in_plugin_update_message( $args ) {
return ""; //coming in later versions.

		$transient_name = 'csellwoo_upgrade_notice_' . $args['Version'];
		$upgrade_notice = get_transient( $transient_name );

		if ( false === $upgrade_notice ) {
			$response = ''; //need to get response from the hosted version and show the update text. to do it later.

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = self::parse_update_notice( $response['body'] );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Parse update notice from readme file.
	 *
	 * @param  string $content Readme content.
	 * @return string
	 */
	private static function parse_update_notice( $content ) {
		// Output Upgrade Notice.
		$matches        = null;
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( WCPBC()->version ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( WCPBC()->version, $version, '<' ) ) {

				$upgrade_notice .= '<span class="wc_plugin_upgrade_notice wc_pbc_upgrade_notice">';

				foreach ( $notices as $index => $line ) {
					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
				}

				$upgrade_notice .= '</span> ';
			}
		}

		return $upgrade_notice;
	}
}
