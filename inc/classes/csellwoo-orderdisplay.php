<?php
/**
 * Agreeme Licenses for WooCommerce - Order Display Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Amin Yasser.
 */
use Automattic\WooCommerce\Utilities\OrderUtil; //HPOS
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'csellwoo_Orderdisplay' ) ) :

class csellwoo_Orderdisplay {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'add_csellwoo_meta_admin_order' ), PHP_INT_MAX );
		add_action( 'woocommerce_after_order_details', array( $this, 'add_csellwoo_meta_admin_order' ), PHP_INT_MAX );
		
		add_action( 'woocommerce_email_after_order_table',                 array( $this, 'add_csellwoo_meta_to_emails' ), PHP_INT_MAX, 2 );
		
		
		if ( 'yes' === csellwoo_get_option( 'add_to_order_received', 'yes' ) ) {
			///add_action( 'woocommerce_order_details_after_order_table',     array( $this, 'add_custom_fields_to_view_order_and_thankyou_pages' ), PHP_INT_MAX );
		}
	}



function add_csellwoo_meta_admin_order( $order ){


		//loop through all...
		
	$oid=$order->get_id();
	
        /*
         * get all the meta data values we need
         */ 
 
				 	/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.
$custom_field_valuearr = $order->get_meta('_csellwoo_lic',false);

$custom_field_value=array();

foreach($custom_field_valuearr as $obj)
{
	$custom_field_value[$obj->key]=$obj->value;
	
	
}


	}else{ 
        $custom_field_value = get_post_meta( $oid ,'_csellwoo_lic');
		
	}


	
echo "<div>";

		foreach($custom_field_value as $key=>$val)
{

	
	
		$keysarr=explode(" - ",$val);
	
		
		if($keysarr[0])
		{
		$lic=CSELLWOO_CBX::get_lic($keysarr[0]);
		
		if(!$lic) continue;
		
		$enabled=" ";$disbled=" ";

		 echo '<ul style="list-style-type:none;"><li><b>Content Sell Access: '.esc_html($keysarr[0]).' </b></li>';
		  
if($order->get_status()=='completed' || $order->get_status()=='processing')
			{
		  
		    echo '<li>Access given to these Page(s) </li>';
		$pages=array_merge($lic->get_license_pages(),$lic->get_license_posts());
	$lcode=csellwoo_encode($keysarr[1],$oid); //$lic->get_code();
		foreach($pages as $pid)
		{
			
			//get the title and id.. form the access url and the links here...
        $ptitle = get_the_title(  $pid);
		$plink=add_query_arg(array( 'csellaccess'=> $lcode),get_permalink(  $pid));

		   echo '<li><a href="'.esc_url( $plink).'">-> '.esc_html($ptitle).'</a></li>';



		
		
		}
		
			}else
				
				{
					
						    echo '<li>Order Not Completed, Access will be given to these Page(s) on completion </li>';
		$pages=array_merge($lic->get_license_pages(),$lic->get_license_posts());
		$lcode=csellwoo_encode($keysarr[1],$oid); //$lic->get_code();
		foreach($pages as $pid)
		{
			
			//get the title and id.. form the access url and the links here...
        $ptitle = get_the_title(  $pid);
		
		   echo '<li>-> '.esc_html($ptitle).'</li>';



			
					
					
				}
		echo '</ul>';
		
		
		}
	
}


echo "</div>";
  
}

}
function add_csellwoo_meta_to_emails( $order  ){


$status=$order->get_status();
if($status!='completed' && $status!='processing') return '';
	$oid=$order->get_id();
	

$op ='';

        /*
         * get all the meta data values we need
     */ 
		 
		 	/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.
//$custom_field_value = $order->get_meta('_csellwoo_lic',false);
$custom_field_valuearr = $order->get_meta('_csellwoo_lic',false);

$custom_field_value=array();

foreach($custom_field_valuearr as $obj)
{
	$custom_field_value[$obj->key]=$obj->value;
	
	
}


	}else{
        $custom_field_value = get_post_meta( $oid ,'_csellwoo_lic');
		
	}
		
		
		
if(is_array($custom_field_value) && (count($custom_field_value)>0))
echo '<div>'.__( 'Use  below links to gain access:', 'content-sell-in-woocommerce' ).' </div><div>';
	
		foreach($custom_field_value as $key=>$val)
{

		
		$keysarr=explode(" - ",$val);
	
		if($keysarr[0])
		{
		$lic=CSELLWOO_CBX::get_lic($keysarr[0]);
		if(!$lic) continue;
		echo '<div>';
	echo '<b>'.esc_html($lic->get_name()).'</b>';
	
		
		   echo '<ul>';
		   $lcodeo=$lic->get_code(); 
		   
		$pages=array_merge($lic->get_license_pages(),$lic->get_license_posts());
		$lcode=csellwoo_encode($lcodeo,$oid); //$lic->get_code();
		foreach($pages as $pid)
		{
			
			//get the title and id.. form the access url and the links here...
        $ptitle = get_the_title(  $pid);
		$plink=add_query_arg(array( 'csellaccess'=> $lcode),get_permalink(  $pid));
  echo '<li><a href="'.esc_url($plink).'">'.esc_html($ptitle).'</a></li>';



		
		
		}
		
		echo '</ul>';
		echo '</div>';
		
		}
			
	
}
		echo '</div>';



}




	/**
	 * get_order_id.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_order_id( $_order ) {
		if ( ! $_order || ! is_object( $_order ) ) {
			return 0;
		}
		if ( ! isset( $this->is_wc_version_below_3_0_0 ) ) {
			$this->is_wc_version_below_3_0_0 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		}
		return ( $this->is_wc_version_below_3_0_0 ? $_order->id : $_order->get_id() );
	}



}



return new csellwoo_Orderdisplay();
endif;