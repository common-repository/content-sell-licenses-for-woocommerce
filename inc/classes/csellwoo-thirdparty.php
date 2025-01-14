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
		add_filter( 'woo_ce_order_fields', array( $this, 'add_custom_fields_to_store_exporter' ) );
		add_filter( 'woo_ce_order',        array( $this, 'add_custom_fields_to_store_exporter_order' ), PHP_INT_MAX, 2 );
	}

	/**
	 * add_custom_fields_to_store_exporter_order.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_custom_fields_to_store_exporter_order( $order, $order_id ) {
		if ( ! ( $fields_data = alg_wc_ccf_get_order_fields_data( $order_id ) ) ) {
			return $order;
		}
		foreach ( $fields_data as $field_data ) {
			$order->{$field_data['_value_meta_key']} = $field_data['_value'];
		}
		return $order;
	}

	/**
	 * add_custom_fields_to_store_exporter.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function add_custom_fields_to_store_exporter( $fields ) {
		for ( $i = 1; $i <= apply_filters( 'alg_wc_ccf_total_fields', 1 ); $i++ ) {
			if ( 'yes' === alg_wc_ccf_get_field_option( 'enabled', $i, 'no' ) ) {
				$section = alg_wc_ccf_get_field_option( 'section', $i, 'billing' );
				$fields[] = array(
					'name'  => $section . '_' . ALG_WC_CCF_KEY . '_' . $i,
					'label' => alg_wc_ccf_get_field_option( 'label', $i, '' ),
				);
			}
		}
	    return $fields;
	}
}

endif;

return new CSELLWOO_Thirdparty();
