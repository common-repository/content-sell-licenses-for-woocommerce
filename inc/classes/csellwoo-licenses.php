<?php
/**
 * Handles storage and retrieval of Licenses
 *
 * @version 1.0.0
 * @since   1.0.0
 * @package CSELLWOO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AGR Licenses class.
 */
class CSELLWOO_CBX {

	/**
	 * Return the pricing lic class.
	 *
	 * @return string
	 */
	private static function get_lic_class_name() {
		$classname = 'CSELLWOO_CB';
		

		return $classname;
	}

	/**
	 * Return a empty pricing lic object.
	 *
	 * @return CSELLWOO_CB
	 */
	public static function create() {
		$classname = self::get_lic_class_name();
		return new $classname();
	}

	/**
	 * Save a lic.
	 *
	 * @since 1.8.0
	 * @param CSELLWOO_CB $lic License instance.
	 * @return string
	 */
	public static function save( $lic ) {
		$licenses = (array) get_option( 'csellwoo_licenses_data', array() );

		if ( ! $lic->get_lic_id() ) {
			$lic_id = self::get_unique_slug( sanitize_key( sanitize_title( $lic->get_name() ) ), array_keys( $licenses ) );
			$lic->set_lic_id( $lic_id );
		} else {
			$lic_id = $lic->get_lic_id();
		}
		$lic_data = $lic->get_data();
		unset( $lic_data['lic_id'] );

		$licenses[ $lic_id ] = $lic_data;
	
		update_option( 'csellwoo_licenses_data', $licenses );

		return $lic_id;
	}

	/**
	 * Save a group licenses.
	 *
	 * @since 1.8.0
	 * @param array $licenses Array of  licenses.
	 */
	public static function bulk_save( $licenses ) {
		$alicenses = (array) get_option( 'csellwoo_licenses_data', array() );

		foreach ( $licenses as $lic ) {

			if ( ! $lic->get_lic_id() ) {
				$lic_id = self::get_unique_slug( sanitize_key( sanitize_title( $lic->get_name() ) ), array_keys( $licenses ) );
				$lic->set_lic_id( $lic_id );
			} else {
				$lic_id = $lic->get_lic_id();
			}

			$lic_data = $lic->get_data();
			unset( $lic_data['lic_id'] );

			$alicenses[ $lic_id ] = $lic_data;
		}
		update_option( 'csellwoo_licenses_data', $alicenses );
	}

	/**
	 * Get a unique slug that indentify a lic
	 *
	 * @since 1.8.0
	 * @param string $new_slug New slug.
	 * @param array  $slugs All IDs of the licenses.
	 * @return array
	 */
	private static function get_unique_slug( $new_slug, $slugs ) {
		$seqs = array();

		foreach ( $slugs as $slug ) {
			$slug_parts = explode( '-', $slug, 2 );
			if ( $slug_parts[0] === $new_slug && ( count( $slug_parts ) === 1 || is_numeric( $slug_parts[1] ) ) ) {
				$seqs[] = isset( $slug_parts[1] ) ? $slug_parts[1] : 0;
			}
		}

		if ( $seqs ) {
			rsort( $seqs );
			$new_slug = $new_slug . '-' . ( $seqs[0] + 1 );
		}

		return $new_slug;
	}

	/**
	 * Delete a lic.
	 *
	 * @since 1.8.0
	 * @param CSELLWOO  $lic instance.
	 */
	public static function delete( $lic ) {
		global $wpdb;

		$licenses = (array) get_option( 'csellwoo_licenses_data', array() );

		if ( isset( $licenses[ $lic->get_lic_id() ] ) ) {
			unset( $licenses[ $lic->get_lic_id() ] );
			update_option( 'csellwoo_licenses_data', $licenses );
		}
	}

	/**
	 * Get pricing licenses.
	 *
	 * @param array $lic_ids Array of IDs of Pricing licenses to filter the result. Optional. False return all.
	 * @return array Array of CSELLWOO_CB instances.
	 */
	public static function get_licenses( $lic_ids = false ) {
		$classname = self::get_lic_class_name();
		$licenses     = array();

		foreach ( (array) get_option( 'csellwoo_licenses_data', array() ) as $id => $data ) {
			if ( ! empty( $lic_ids ) && is_array( $lic_ids ) && ! in_array( $id, $lic_ids, true ) ) {
				continue;
			}
			$licenses[ $id ] = new $classname( array_merge( $data, array( 'lic_id' => $id ) ) );
		}

		return $licenses;
	}



	public static function get_purchases($perpage,$current_page ) {
		$classname = self::get_lic_class_name();
$purchases     = array();
$args = array(
'limit'=>$perpage, 'page'=>$current_page,
	'posts_per_page' => 5,
		'paginate'       => true,
  'order'        => 'DESC',
  'meta_query' => array(
            array(
                'key' => '_csellwoo_lic',
            ),
			),
 'status'=> array( 'wc-completed'),
);

//$purchases = get_posts(array('post_type' => 'shop_order','post_status' => 'wc_completed',));

$purchases = wc_get_orders($args);

return $purchases;
	}
	
	
	
	/**
	 * Get a  lic.
	 *
	 * @param mixed $the_lic CSELLWOO_CB|array|string|bool lic instance, array of  lic properties,  lic ID, or false to return the current  lic.
	 * @return CSELLWOO_CB
	 */
	public static function get_lic( $the_lic = false ) {
		$lic      = false;
		$classname = self::get_lic_class_name();

		if ( is_object( $the_lic ) && in_array( get_class( $the_lic ), array( 'CSELLWOO_CB', 'CSELLWOO_CB_Pro' ), true ) ) {
			$lic = $the_lic;
		} elseif ( is_array( $the_lic ) ) {
			$lic = new $classname( $the_lic );
		} elseif ( ! $the_lic ) {
			$lic = false;
		} else {
			$lic = self::get_lic_by_id( $the_lic );
		}

		return $lic;
	}

	/**
	 * Get lic by an ID.
	 *
	 * @param string $id  lic ID.
	 * @return CSELLWOO_CB
	 */
	public static function get_lic_by_id( $id ) {
		$lic      = null;
		$licenses     = (array) get_option( 'csellwoo_licenses_data', array() );
		$classname = self::get_lic_class_name();

		if ( ! empty( $licenses[ $id ] ) ) {
			$lic = new $classname( array_merge( $licenses[ $id ], array( 'lic_id' => $id ) ) );
		}

		return $lic;
	}





	/**
	 * There is  licenses.
	 *
	 * @return bool
	 */
	public static function has_licenses() {
		$licenses = (array) get_option( 'csellwoo_licenses_data', array() );
		return count( $licenses );
	}
}
