<?php
/**
 * Agreeme Licenses for WooCommerce - Functions
 *
 * @version 1.1.0
 * @since   1.0.0
 * @author  Amin Yasser.
 */
use Automattic\WooCommerce\Utilities\OrderUtil; //HPOS
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'csellwoo_update_order_fields_data' ) ) {
	/*
	 * csellwoo_update_order_fields_data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 */
	function csellwoo_update_orderfields_data( $order_id, $fields_data ) {
		
				//HPOS
			if (  class_exists('Automattic\WooCommerce\Utilities\OrderUtil') &&  OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.

	$order = wc_get_order( $order_id );
		$order->update_meta_data( '_' . CSELL_WC_ID . '_data', $fields_data ); 
	$order->save();
	///
			}else{
		
		update_post_meta( $order_id, '_' . CSELL_WC_ID . '_data', $fields_data );
		
			}
		
	}
}
if ( ! function_exists( 'csellwoo_encode' ) ) {
	
		function csellwoo_encode( $lcode,$oid) {
			
			return $lcode."-".$oid."-".md5($oid);
		}
	
}



if ( ! function_exists( 'csellwoo_get_product_terms' ) ) {
	/**
	 * csellwoo_get_product_terms.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function csellwoo_get_product_terms( $taxonomy = 'product_cat' ) {
		$product_terms = array();
		$_product_terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
		if ( ! empty( $_product_terms ) && ! is_wp_error( $_product_terms ) ){
			foreach ( $_product_terms as $_product_term ) {
				$product_terms[ $_product_term->term_id ] = $_product_term->name;
			}
		}
		return $product_terms;
	}
}

if ( ! function_exists( 'csellwoo_get_products' ) ) {
	/**
	 * csellwoo_get_products.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function csellwoo_get_products( $products = array(), $post_status = 'any' ) {
		$offset     = 0;
		$block_size = 1024;
		while( true ) {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => $post_status,
				'posts_per_page' => $block_size,
				'offset'         => $offset,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( ! $loop->have_posts() ) {
				break;
			}
			foreach ( $loop->posts as $post_id ) {
				$products[ $post_id ] = get_the_title( $post_id ) . ' [ID:' . $post_id . ']';
			}
			$offset += $block_size;
		}
		return $products;
	}
}




if ( ! function_exists( 'csellwoo_get_purchases' ) ) {
	/**
	 * csellwoo_get_products.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function csellwoo_get_purchases( ) {
		
	//get logged in user id..
	$uid=get_current_user_id();
	
	if(!$uid) return '';
	
	

$purchases     = array();
$args = array(
'limit'=>20, 'page'=>1,
	'posts_per_page' => 5,
		'paginate'       => true,
		
		'customer_id' => $uid,
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


echo '<div><h2>Your Recent Purchases</h2></div>';
 
foreach($purchases->orders as $k => $order)
{
	



   /*
         * get all the meta data values we need
         */ 
		 
		 
		 	
	/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.
$custom_field_value = $order->get_meta('_csellwoo_lic',false);


	}else{
        $custom_field_value = get_post_meta( $order->get_id() ,'_csellwoo_lic');

	}
		foreach($custom_field_value as $key=>$val)
{

	
	
		$keysarr=explode(" - ",$val);
	
		
		if($keysarr[0])
		{
		$lic=CSELLWOO_CBX::get_lic($keysarr[0]);
		
		if(!$lic) continue;
		
		$enabled=" ";$disbled=" ";

		  
if($order->get_status()=='completed')
			{
		   echo '<ul style="list-style-type:none;font-size:11px;"><li><b> '.esc_html($lic->get_name()).' </b></li>';
		  
		    echo '<li>Access given to these Page(s) </li>';
		$pages=array_merge($lic->get_license_pages(),$lic->get_license_posts());
	$lcode=csellwoo_encode($keysarr[1],$order->get_id()); //$lic->get_code();
		foreach($pages as $pid)
		{
			
			//get the title and id.. form the access url and the links here...
        $ptitle = get_the_title(  $pid);
		$plink= add_query_arg(array( 'csellaccess'=> $lcode),get_permalink(  $pid));

		   echo '<li><a href="'.esc_url($plink).'">-> '.esc_html($ptitle).'</a></li>';



		
		
		}
		
			}elseif($order->get_status()!='trash')
				
				{
					 echo '<ul style="list-style-type:none;font-size:11px;"><li><b> '.esc_html($lic->get_name()).' </b></li>';
		  
					
						    echo '<li>Order Not Completed, Access will be given to these Page(s) on completion </li>';
		$pages=array_merge($lic->get_license_pages(),$lic->get_license_posts());
		$lcode=csellwoo_encode($keysarr[1],$order->get_id()); //$lic->get_code();
		foreach($pages as $pid)
		{
			
			//get the title and id.. form the access url and the links here...
        $ptitle = get_the_title(  $pid);
		
		   echo '<li>-> '.esc_html($ptitle).'</li>';



			
					
					
				}
	
		
		
		}
		
			echo '</ul>';
	
}



  
}

















	
	
}



	}
}





if ( ! function_exists( 'csellwoo_get_user_roles' ) ) {
	/**
	 * csellwoo_get_user_roles.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function csellwoo_get_user_roles() {
		global $wp_roles;
		$all_roles = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles->roles : array();
		$all_roles = apply_filters( 'editable_roles', $all_roles );
		$all_roles = array_merge( array(
			'guest' => array(
				'name'         => __( 'Guest', 'content-sell-in-woocommerce' ),
				'capabilities' => array(),
			) ), $all_roles );
		$all_roles_options = array();
		foreach ( $all_roles as $_role_key => $_role ) {
			$all_roles_options[ $_role_key ] = $_role['name'];
		}
		return $all_roles_options;
	}
}

if ( ! function_exists( 'csellwoo_is_user_role' ) ) {
	/**
	 * csellwoo_is_user_role.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  bool
	 */
	function csellwoo_is_user_role( $user_roles, $user_id = 0 ) {
		$_user = ( 0 == $user_id ? wp_get_current_user() : get_user_by( 'id', $user_id ) );
		if ( ! isset( $_user->roles ) || empty( $_user->roles ) ) {
			$_user->roles = array( 'guest' );
		}
		if ( ! is_array( $_user->roles ) ) {
			return false;
		}
		if ( is_array( $user_roles ) ) {
			if ( in_array( 'administrator', $user_roles ) ) {
				$user_roles[] = 'super_admin';
			}
			$_intersect = array_intersect( $user_roles, $_user->roles );
			return ( ! empty( $_intersect ) );
		} else {
			return ( 'administrator' == $user_roles ?
				( in_array( 'administrator', $_user->roles ) || in_array( 'super_admin', $_user->roles ) ) :
				( in_array( $user_roles, $_user->roles ) )
			);
		}
	}
}

