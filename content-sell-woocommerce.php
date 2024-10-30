<?php
/*
Plugin Name: Protected Content Packages For WooCommerce
Plugin URI: https:///content-sell-in-woocommerce/
Description: Protect & Sell Post/Page contents using WooCommerce.
Version: 1.0.8
Author: Amin Y
Author URI: https://qcompsolutions.com
Text Domain: content-sell-in-woocommerce
Domain Path: /languages
Copyright: © 2024 Qcompsolutions.com
WC tested up to: 6.6.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Constants
if ( ! defined( 'CSELL_WC_VERSION' ) ) {
	define( 'CSELL_WC_VERSION', '1.0.0' );
}
if ( ! defined( 'CSELL_WC_ID' ) ) {
	define( 'CSELL_WC_ID',      'csellwoo-lic' );
}
if ( ! defined( 'CSELL_WC_KEY' ) ) {
	define( 'CSELL_WC_KEY',     'csell_wc_license' );
}
// Define CSELLWOO_PLUGIN_FILE.
if ( ! defined( 'CSELLWOO_PLUGIN_FILE' ) ) {
	define( 'CSELLWOO_PLUGIN_FILE', __FILE__ );
}


define( 'CSELLWOO_YES',       __( 'Yes', 'content-sell-in-woocommerce' ) );
define( 'CSELLWOO_NO',       __( 'No', 'content-sell-in-woocommerce' ) );

//HPOS COMPATIBLE CODE
 add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

if ( ! function_exists( 'csell_wc_get_option' ) ) {
	/**
	 * csell_wc_get_option.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function csellwoo_get_option( $option, $default = false ) {
		return get_option( CSELL_WC_ID . '_' . $option, $default );
	}
}










if ( ! class_exists( 'CSELL_WC' ) ) :

 function csellwoo_activate() {
	////////////// check for options............and add basic options

	   if(get_option('csellwoo_enabled', false)){

    }
    else {
	
	

	add_option('csellwoo_enabled', 'yes');
	  
	
	}
}

 function csellwoo_deactivate() {
	//////////////
	////////////// remove options
	
	
	
	}
	
	



	/**
	 * csellwoo_uninstall.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
function csellwoo_uninstall() {
	//////////////
	////////////// remove options
	
	delete_option("csellwoo_licenses_data");
	
	}
	
	
register_activation_hook(__FILE__,  'csellwoo_activate'  );
register_deactivation_hook(__FILE__, 'csellwoo_deactivate'  );
register_uninstall_hook(__FILE__, 'csellwoo_uninstall');
/**
 * Main CSELL_WC Class
 *
 * @class   CSELL_WC
 * @version 1.0.0
 */
final class CSELL_WC {

	/**
	 * @var   CSELL_WC The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main CSELL_WC Instance
	 *
	 * Ensures only one instance of CSELL_WC is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  CSELL_WC - Main instance
	 */
	static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * CSELL_WC Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @access  public
	 *
	 */
	function __construct() {
		

		
		// Check for active plugins
		if (
			! $this->is_plugin_active( 'woocommerce/woocommerce.php' )
			
		) {
			return;
		}
       

		// Set up localisation
		load_plugin_textdomain( 'content-sell-in-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	
	

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * is_plugin_active.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function includes() {
		// Functions
	
		require_once( 'inc/csellwoo-functions.php' );

	
		
			
		// Main
		require_once( 'inc/classes/csellwoo-main.php' );
	}
	
	
	

	
/**
	 
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public static function csellwoo_activate() {
	////////////// check for options............and add basic options



	   if(get_option('csellwoo_enabled', false)){

    }
    else {
	add_option('csellwoo_enabled', 'yes');
	add_option('csellwoo_buttonclasses', 'yes');
	add_option('csellwoo_formclasses', 'yes');
	add_option('csellwoo_alertmsg', 'yes');
	}
}



	/**
	 * alg_wc_ccf_get_default_date_format.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public static  function csellwoo_deactivate() {
	//////////////
	////////////// remove options
	
	
	
	}
	
	



	/**
	 * alg_wc_ccf_get_default_date_format.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public static function csellwoo_uninstall() {
	//////////////
	////////////// remove options
	
	delete_option('csellwoo_enabled');
	delete_option('csellwoo_buttonclasses');
	delete_option('csellwoo_formclasses');
	delete_option('csellwoo_alertmsg');
	
	}
	
	
	/**
	 * admin.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( csellwoo_get_option( 'version', '' ) !== CSELL_WC_VERSION ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . CSELL_WC_ID ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';

		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Custom Checkout Fields settings tab to WooCommerce settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		
	
		$settings[] = require_once( 'inc/options/csellwoo-settings-lic.php' );
		
		
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function version_updated() {
		update_option( CSELL_WC_ID . '_' . 'version', CSELL_WC_VERSION );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	public static function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	
	
	public	static function getView($view, $data = null)
	{
		ob_start();
		self::outputView($view, $data);
		return ob_get_clean();
	}
	
		static function outputView($view, $data = null)
	{
		
		
		if (!($data instanceof stdClass))
		{
			$data = new stdClass();
		}

		$file = self::getPluginPath() . DIRECTORY_SEPARATOR .'inc' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view;


		if (file_exists($file))
		{
			include $file;
		}
	}
	
		static function getPluginPath()
	{
		return dirname(__FILE__);
	}
	
	
	
	
	
	

}

endif;




if ( ! function_exists( 'csellwoo_license_fields' ) ) {
	/**
	 * Returns the main instance of CSELL_WC.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  CSELL_WC
	 */
	 
	function csellwoo_license_fields() {
		return CSELL_WC::instance();
		


	
	
}



	
return csellwoo_license_fields();

	
   
	
	
		
		
	}





