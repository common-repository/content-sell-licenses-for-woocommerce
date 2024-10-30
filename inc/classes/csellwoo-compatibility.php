<?php
/**
 * Agreeme License for WooCommerce - Thirdparty Class
 * Checks and add code for plugin to work with other plugins.
 * @version 1.0.0
 * @since   1.0.0
 * @author  Amin Yasser.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'CSELLWOO_Thirdparty' ) ) :

class CSELLWOO_Thirdparty {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @todo    [dev] (test) "WooCommerce – Store Exporter" - test if it's still working
	 */
	function __construct() {
		// "WooCommerce – Store Exporter" plugin - https://wordpress.org/plugins/woocommerce-exporter/
			}


}

endif;

return new CSELLWOO_Thirdparty();
