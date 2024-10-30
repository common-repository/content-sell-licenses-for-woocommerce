<?php
/**
 * Agreeme Licenses for WooCommerce - Core Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Amin Yasser.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'csellwoo_main' ) ) :

class csellwoo_main {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'csellwoo_enabled', 'yes' ) ) {
				require_once( 'content_sell_woo_email.php' );
				require_once( 'csellwoo-lic.php' );
			require_once( 'csellwoo-licenses.php' );
			//if ( is_front_page() ){
			require_once( 'csellwoo-frontend.php' );
			//}
		//	require_once( 'csellwoo-scripts.php' );
			require_once( 'csellwoo-orderdisplay.php' );
		
		}
	}
	
	


}

endif;

return new csellwoo_main();
