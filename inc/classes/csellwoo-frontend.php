<?php
/**
 * Content Selling licenses for WooCommerce - Frontend Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Amin Y.
 */
use Automattic\WooCommerce\Utilities\OrderUtil; //HPOS
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!class_exists('csellwoo_Frontend')):
    class csellwoo_Frontend {
        /**
         * Constructor.
         *
         * @version 1.4.0
         * @since   1.0.0
         */
        public $looopc = 0;
        public $sdata = array();
        function __construct() {
            //check if enabled.. if not skip frontend...
            $enabled = get_option('csellwoo_enabled');
            if (!$enabled || $enabled == "no") return false;
   
			
			add_action('init',  array($this, 'start_csellsession'), 10);
			
            add_action('woocommerce_thankyou', array($this, 'order_completed'), PHP_INT_MAX);
         //   add_action('woocommerce_checkout_update_order_meta', array($this, 'update_csell_license_fields_order_meta'));
           
  add_action('woocommerce_order_status_completed', array($this, 'update_csell_license_fields_order_meta'));
         add_filter( 'woocommerce_email_classes', array($this,'csellwoo_custom_woocommerce_emails') );

		   add_action('wp_ajax_csellwoo_post', array($this, 'csellwoo_post'));
            add_action('admin_post_csellstatus_post', array($this, 'csellstatus_post'));
			            add_action('wp_ajax_csellstats_post', array($this, 'csellstats_post'));

            add_action('wp_ajax_csell_ajax_post_search', array($this, 'csellwoo_post_ajax'));
            add_action('wp_ajax_csell_ajax_page_search', array($this, 'csellwoo_page_ajax'));
            add_action('woocommerce_single_product_summary', array($this, 'csell_product_page_message'), PHP_INT_MAX);
            //admin end
            //add_action( 'edit_page_form', array($this, 'csellwoo_checkbox_callback_function'), PHP_INT_MAX  );
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'), PHP_INT_MAX);
            add_action('add_meta_boxes', array($this, 'add_csellwoo_checkbox_function'), PHP_INT_MAX);
            add_action('save_post', array($this, 'save_csellwoo_post'), PHP_INT_MAX);
            add_filter('the_content', array($this, 'check_content_sell_access'), PHP_INT_MAX);
			
	if(	$this->is_plugin_active('advanced-custom-fields/acf.php')	)
	{
		 add_filter('acf/load_value', array($this, 'check_acf_sell_access'), PHP_INT_MAX);
		 
	}
			//$postid = get_the_ID(); //check for the content sell access and if protected and no access do not show.
			
			 add_action('wp_footer', array($this, 'load_jsscripts'));
			//  add_action('admin_footer', array($this, 'load_jsscripts'));
           // $this->load_jsscripts();
            add_action('template_redirect', array($this, 'csell_password_check'), PHP_INT_MAX);
            add_action('template_redirect', array($this, 'csell_access_check'), PHP_INT_MAX);
            add_filter('body_class', array($this, 'my_plugin_body_class'), PHP_INT_MAX);
            add_filter('wp_default_editor', function () {
                return "html";
            });
			
			
			add_filter( 'csellaccess_the_content', array($this, 'check_cpt_content_sell_access'), PHP_INT_MAX );




        }
		
		
	function start_csellsession() {

}
	
	function csellwoo_custom_woocommerce_emails( $email_classes ) {
	//* Custom welcome email to customer when purchasing online training program
	$upload_dir = wp_upload_dir();
	
	require( 'content_sell_woo_email.php' );
	
	$email_classes['CSELLWOO_Access_Email'] = new CSELLWOO_Order_Email(); // add to the list of email classes that WooCommerce loads
	return $email_classes;
	
}	
		
public			function is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
	
        public function my_plugin_body_class($classes) {
            global $post;
			
			if(!$post) return $classes;
			
            $is_protected = get_post_meta($post->ID, 'is_protected', true);
            if ($is_protected) {
                // etc.
                if (get_option("csellwoo_rcd") == 'yes') $classes[] = 'csellprotected';
            }
            return $classes;
        }
        public function csellstatus_post() {
            //do it only by admin user.
            $return = array();
            if (!is_admin()) {
                return '';
            }
            $status = (int)$_REQUEST['cstatus'];
            $oid = (int)$_REQUEST['oid'];
            $lcode = sanitize_text_field($_REQUEST['lcode']);
			
			if($lcode){
            // we will pass post IDs and titles to this array
			
			
	$custom_field_value=array();		
           
				/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
		
			$order = wc_get_order( $oid );
		
	// HPOS usage is enabled.
//$custom_field_value = $order->get_meta('_csellwoo_lic',false);
$custom_field_valuearr = $order->get_meta('_csellwoo_lic',false);



foreach($custom_field_valuearr as $obj)
{
	$custom_field_value[$obj->key]=$obj->value;
	
	
}


	}else{
        $custom_field_value = get_post_meta($oid, '_csellwoo_lic');
			
	}
	
	

		
            foreach ($custom_field_value as $key => $val) {
                $keysarr = explode(" - ", $val);
			
				
				
                if ($lcode == $val) { //validate if old value is existing otherwise do not allow update.
				
				
				
                    $newvalue = $keysarr[0] . " - " . $keysarr[1] . " - " . $keysarr[2] . " - " . (int)$status;
				
					
						if (  class_exists('Automattic\WooCommerce\Utilities\OrderUtil') &&  OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.

		$order->update_meta_data( "_csellwoo_lic",$newvalue ); //why/how previous $val here.?
	$order->save();
	
	
	///
			}else{
                    update_post_meta($oid, "_csellwoo_lic", $newvalue, $val);
			}
					
					
					
                    $return['success'] = 1;
                }
            }
			
			
			
			
			
			
		
            wp_redirect(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=purchases&oid=' . $oid));
            exit;
			}
        }
		
		
		public function csellstats_post() {
            //do it only by admin user.
            $return = array();

			
            if (!is_admin()) {
                return '';
            }

            $lcode = sanitize_text_field($_REQUEST['lcode']);
			$licenses = CSELLWOO_CBX::get_licenses();
			$expdays=false;
			$foundlic=false;
			
			
	   foreach($licenses as $lic)
	   {
	

		if($lic->get_code()==$lcode)
		{
$expdays=(int)$lic->get_expiry_days();	

$foundlic=true;
break;
		}		
		   
	   }
if(!$foundlic) return false;

			
//get all orders for specific license code..
			if($lcode){


$orders = wc_get_orders( array(
    'limit'        => -1, // Query all orders
    'orderby'      => 'date',
    'order'        => 'DESC',
	  'meta_query' => array(
            array(
                'key' => '_csellwoo_'.$lcode,
            ),
			),
));



$notexpiredlic=0;
$time=time();


		  foreach( $orders as $order ){
		  //  $item_meta_data = $item->get_meta_data();
		  
		$cost=  $order->get_meta('_csellwoo_'.$lcode);
		$licd=  $order->get_meta('_csellwoo_lic');
		$cdate=strtotime($order->get_date_completed());
		$datediff = $time - $cdate;

$noofdays= round($datediff / (60 * 60 * 24));
		$tcost=$cost+$tcost;

if(!$expdays || $noofdays<$expdays)
	$notexpiredlic++;		
		
		
	// get the meta value..

// get the expiry status..

// store in json array and response.. for js display..	
	
		
		
		
			}
$dreturn['cost']=wc_price($tcost);
$dreturn['notexp']=$notexpiredlic;

		
echo json_encode($dreturn);
		
          
           // wp_redirect(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=purchases&oid=' . $oid));
            exit;
			}
        }
		
		
		
        public function admin_enqueue($hook) {
            // Only add to the edit.php admin page.
            // See WP docs.
			
            wp_enqueue_script('csellwoo-jsscript-backend', /* Handle/Name */
            CSELL_WC::plugin_url() . '/js/csellwoo-lic-backend.js', /* Path to the plugin/assets folder */
            //array('jquery', 'xml2json', 'json2xml'), /* Script Dependencies */
            array('jquery'), /* Script Dependencies */
            null, /* null is any version, but could be the specific version of jquery if required */
            true
            /* if true=add to footer, false=add to header */
            );
        }
        public function csell_product_page_message() {
            if (get_option('csellwoo_pnotice') != 'yes') return '';
            //get the licenses as array of lic objects
            $licenses = CSELLWOO_CBX::get_licenses();
            //loop through all...
            foreach ($licenses as $id => $lic) {
                //get location of each...and proceed further if matches
                //  if (in_array(1, $location_arr)) //product page addtocart button
                //  {
                //get the product id match if not return...
                global $product;
                $pid = $product->get_id();
                $l_product = $lic->get_license_product();
                if ($pid == $l_product) {
                    ob_start(); ?>
		<div class="woocommerce-message">
		
		
			<?php echo wp_kses_post(get_option('csellwoo_pnoticetext')); ?>
		</div>
	<?php
                    echo ob_get_clean();
                    break;
                }
                ///}
                
            }
        }
        public function csellwoo_post_ajax() {
            // we will pass post IDs and titles to this array
            $return = array();
            $cptype = get_option("csellwoo_ptype");
            if (!$cptype) $cptype = "post";
            // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
            $search_results = new WP_Query(array('s' => sanitize_text_field($_GET['q']), // the search query
            'post_type' => $cptype));
            if ($search_results->have_posts()):
                while ($search_results->have_posts()):
                    $search_results->the_post();
                    // shorten the title a little
                    $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                    $return[] = array($search_results->post->ID, $title); // array( Post ID, Post Title )
                    
                endwhile;
            endif;
            echo json_encode($return);
            die;
        }
        public function csellwoo_page_ajax() {
            // we will pass post IDs and titles to this array
            $return = array();
            // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
            $search_results = new WP_Query(array('s' => sanitize_text_field($_GET['q']), // the search query
            'post_type' => 'page'));
            if ($search_results->have_posts()):
                while ($search_results->have_posts()):
                    $search_results->the_post();
                    // shorten the title a little
                    $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                    $return[] = array($search_results->post->ID, $title); // array( Post ID, Post Title )
                    
                endwhile;
            endif;
            echo json_encode($return);
            die;
        }
        public function csell_password_check() {
            if (!isset($_POST['acc_lic_email']) && !isset($_POST['csellform'])) {
                //wc_add_notice( 'Invalid entries', 'error' );
                return;
            }
		
			
            if (!wp_verify_nonce(sanitize_key($_POST['csellform']), 'csellaccess')) {
                //wc_add_notice( 'Invalid entries', 'error' );
                return;
            }
	
            if (isset($_POST['acc_lic_email'])) {
				
				$cmail=sanitize_email($_POST['acc_lic_email']);
				
				
                $order_id = 0;
                if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    /////if(woocommerce installed) {
                   // $the_user = get_user_by('email', sanitize_email($_POST['acc_lic_email'])); //need clean up...
				
					
					if($cmail)
					{
                  
						
                   
						
								$purchases     = array();
$args = array(
'limit'=>1, 'page'=>1,
	'posts_per_page' => 5,
		'paginate'       => true, 'customer' => $cmail,
  'order'        => 'DESC',
  'meta_query' => array(
            array(
                'key' => '_csellwoo_lic',
            ),
			),
 'status'=> array( 'wc-completed'), 'return'        => 'objects'

);

//$purchases = get_posts(array('post_type' => 'shop_order','post_status' => 'wc_completed',));

$lorders = wc_get_orders($args);


/*

*/



// NOT empty
if ( ! empty ( $lorders ) ) {  



    foreach ( $lorders->orders as $id=>$order ) {
		
	
	$ordermatched=0;
	$page_id=(int)$_POST['postid'];
	
if($page_id)
{

$order_id = $order->get_id();


    $custom_field_value = get_post_meta((int)$order_id, '_csellwoo_lic');
	
	
			
                foreach ($custom_field_value as $key => $val) {
                    $keysarr = explode(" - ", $val);
					
					
                    if ($keysarr[0]) {
                        $lic = CSELLWOO_CBX::get_lic($keysarr[0]);
                        if (!$lic) continue;
						
						
                        $pages = $lic->get_license_pages();
						
						$posts= $lic->get_license_posts();
						
						      if (in_array( $page_id,$pages) || in_array( $page_id,$posts))  {
  
	$ordermatched=1;
	break;
      }
	  
					}
					
				}



}
                       // $order = wc_get_order($order_id);
                           // $order->update_status('pending');
						   
						   if(	$ordermatched==1)
						   {


if(!$order_id) return false;
                            $wc_emails = WC()->mailer()->get_emails();
                            if (empty($wc_emails)) return;
                            add_filter('woocommerce_new_order_email_allows_resend', '__return_true');
							
							
						//$mailer = WC()->mailer();
							///print_r($mailer->emails);
							
							$email_options = get_option('woocommerce_csellwoo_order_email_settings');
							if( ($email_options && $email_options['enabled'] == 'yes' ) ){ //|| $mailer->emails['CSELLWOO_Order_Email']->is_enabled()
								$eid="csellwoo_order_email";
							}else
								$eid="csellwoo_order_email"; //"customer_completed_order";
								
						
							
                            foreach ($wc_emails as $wc_mail) {
								
								
                                if ($wc_mail->id == $eid) {
							
                                    $wc_mail->trigger($order_id);
                                }
                            }
                            //$order->update_status('completed');
							
							
                            wc_add_notice( __('Check your email inbox for order details with access links.', 'content-sell-in-woocommerce'), 'success');
							
							return;
						   }

    }
}

                        
						
                            
                    
                   
             
					
					}else
						 wc_add_notice( __('Invalid Email, Use your Account/Billing Email, while placing the order', 'content-sell-in-woocommerce'), 'success');
                }
            }
            wc_add_notice('Invalid entries', 'error');
			return;
			
        }
        public function csell_handle_mp4() {
           //future versions
        }
        public function csell_access_check() {
            $post = get_post();
            
			
			
			    if ( ! session_id() ) {
        session_start();
    }
			
			
			
			if (!isset($_GET['csellaccess'])) return '';
			if (!$_GET['csellaccess']) return '';
            //
            //if(!(int)$_GET['oid']) return;
            $req_arr = explode("-", sanitize_text_field($_GET['csellaccess']));
			
			if(count($req_arr)<2){  return ''; }
			
			
			
            $license_code = $req_arr[0];
            $oid = (int)$req_arr[1];
			
			
			
            if (!$this->has_csellaccess($post)) { //if not already session set.
                //check validate get....
				
				
				
					/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// HPOS usage is enabled.
		$order = wc_get_order( $oid );
//$custom_field_value = $order->get_meta('_csellwoo_lic',false);
$custom_field_valuearr = $order->get_meta('_csellwoo_lic',false);

$custom_field_value=array();

foreach($custom_field_valuearr as $obj)
{
	$custom_field_value[$obj->key]=$obj->value;
	
	
}


	}else{
				
                $custom_field_value = get_post_meta((int)$oid, '_csellwoo_lic');
				
	}
		
				
                foreach ($custom_field_value as $key => $val) {
                    $keysarr = explode(" - ", $val);
					
					
                    if ($keysarr[0]) {
                        $lic = CSELLWOO_CBX::get_lic($keysarr[0]);
                        if (!$lic) continue;
						
						
                        $pages = $lic->get_license_pages();
						
						$posts= $lic->get_license_posts();
						
						$pages=array_unique(array_merge($pages,$posts));
						
                        $ndays = (int)ceil($lic->get_expiry_days());
                   
                        $lcode = $keysarr[1]; //$lic->get_code();
						
						
						
						
                        foreach ($pages as $pid) {
                            //get the days after order...and the lic days..
                            if (($pid == $post->ID) && ($lcode == $license_code)) {
								
								
								
                                $diff_in_days = 0;
                                if ($ndays) {
                                    //$order = new WC_Order((int)$_GET['oid']);
                                    $order = wc_get_order((int)$oid);
                                    $dcompl = $order->get_date_completed();
                                    $date_completed_ts = $dcompl->getTimestamp();
                                    $diff_in_days = (int)((time() - $date_completed_ts) / (60 * 60 * 24)) + 1;
                                }
								
							
                                if ($diff_in_days <= $ndays) {
                                    $_SESSION['csellaccess'][(int)$oid] = implode(",", $pages);
                                   // return true;
                                } else {
                                }
                            }
                        }
                    }
                }
            }
        }
		
		
		        public function check_acf_sell_access($val) {
$post = get_post();
      
			
            if (!$this->has_csellaccess($post)) {
                // etc.
				
return '';
            }
            return $val;
        }
		
		
        public function check_content_sell_access($content) {
            $post = get_post();
            $this->looopc++;
			
            if ($this->looopc > 5) return $content; //safety limit;
			
	
            if (in_the_loop() && !$this->has_csellaccess($post)) {
                // etc.
                remove_filter('the_content', array($this, 'check_content_sell_access'), PHP_INT_MAX);
                $xx = $this->protected_post_content($post);
                add_filter('the_content', array($this, 'check_content_sell_access'), PHP_INT_MAX);
                return $xx;
            }
            return $content;
        }
		
		        public function check_cpt_content_sell_access($content) {
            $post = get_post();
            $this->looopc++;
			
            if ($this->looopc > 5) return $content; //safety limit;
			
			
            if (in_the_loop() && !$this->has_csellaccess($post)) {
                // etc.
                remove_filter('csellaccess_the_content', array($this, 'check_cpt_content_sell_access'), PHP_INT_MAX);
                $xx = $this->protected_post_content($post);
                add_filter('csellaccess_the_content', array($this, 'check_cpt_content_sell_access'), PHP_INT_MAX);
                return $xx;
            }
            return $content;
        }
		
		
        function protected_post_content($posty) {
            $settings = array();
            $settings['csellwoo_ptitle'] = get_option('csellwoo_ptitle');
            $settings['csellwoo_pdesc'] = get_option('csellwoo_pdesc');
            $settings['csellwoo_showproducts'] = get_option('csellwoo_showproducts');
			$settings['csellwoo_spf'] = get_option('csellwoo_spf');
			$settings['csellwoo_loginlink'] = get_option('csellwoo_loginlink');
			$settings['csellwoo_pid'] = $posty->ID;
            //= csellwoo_settings_lic::get_settings();
            $data = new stdClass();
            $data->post = $posty;
            $data->settings = $settings;
            $dpc = get_post_meta($posty->ID, 'ptext1', true);;
            $data->post_content = $dpc; //apply_filters('the_content',$dpc);
			

			
         
            //echo the protected content message..
            $output = CSELL_WC::getView('view-csell-access-form.php', $data);
			
			   if ($settings['csellwoo_showproducts']=='yes') $data->settings['related_products'] = $this->get_related_products($posty->ID);else
			$data->settings['related_products'] ='';
			
			  $output=str_replace("{RELATED_PRODUCTS}",$data->settings['related_products'],$output);
			  
            return $output;
        }
        public function has_csellaccess($posty) {
            if (!$posty->ID) return true;
			
			    if ( ! session_id() ) {
        session_start();
    }
	
			if( current_user_can('administrator') ) return true;
			
			
			
            $is_protected = get_post_meta($posty->ID, 'is_protected', true);
			
	
            //get if password protected_post_content
            if (!$is_protected) return true;
			
		
	
		
            if (!isset($_SESSION['csellaccess'])) return false;
            if (!count($_SESSION['csellaccess'])) return false;
			
			
		
			
            foreach ($_SESSION['csellaccess'] as $oid => $postids) {
                $pidarr = explode(",", $postids);
                if (in_array($posty->ID, $pidarr)) return true;
                //get the products
                
            }
            if ($is_protected) return false; //anyway not allow if protected.
            else return true;
        }
        public function add_csellwoo_checkbox_function() {
            $ptypes = array('post', 'page');
            $cptype = get_option('csellwoo_ptype');
            if ($cptype) array_push($ptypes, $cptype);
            add_meta_box('csellwoo_checkbox_id', 'Content Sell Protection ', array($this, 'csellwoo_checkbox_callback_function'), $ptypes, 'side', 'high');
        }
        function csellwoo_checkbox_callback_function($post) {
            $isProtected = get_post_meta($post->ID, 'is_protected', true);
            $ptext = get_post_meta($post->ID, 'ptext1', true);
?>
 <div><label>Is Protected?: </label>
   <input type="checkbox" name="is_protected" value="1" <?php echo (($isProtected == '1') ? 'checked="checked"' : ''); ?>/> YES
   </div>
   <div>
   
    <label>Protection Message:</label>
	<div>
<?php
            $settings = array('media_buttons' => true, 'textarea_rows' => 10, 'textarea_name' => 'ptext1');
            wp_editor($ptext, 'ptext1', $settings);
            echo "</div></div>";
           
        }
        function save_csellwoo_post($post_id) {
			

			if(isset($_POST['is_protected']) || isset($_POST['ptext1']))
			{
				if(!isset($_POST['is_protected'])) $is_protected=0;
				else  $is_protected=(int)$_POST['is_protected'];
				
            $arr = array('a' => array('href' => array(), 'title' => array()), 'img' => array('alt' => array(), 'class' => array(), 'height' => array(), 'src' => array(), 'width' => array(),), 'br' => array(), 'em' => array(), 'strong' => array(),);
            update_post_meta($post_id, 'ptext1', wp_kses($_POST['ptext1'], $arr));
            update_post_meta($post_id, 'is_protected', $is_protected);
			
			}
			
		}
        /**
         * Submit license data in cart page, Proceed to Checkout button link click : ajax request.
         *
         * @todo: may need validations for required licenses
         */
        public function order_completed($order_id) {
            if(isset($_SESSION['csellwc'])){$_SESSION['csellwc'] = '';
            unset($_SESSION['csellwc']);
			}
        }
        public function csellwoo_post() {
            if (isset($_POST['action']) == 'csellwoo_post') {
                $licenses = CSELLWOO_CBX::get_licenses();
                //loop through all...
                foreach ($licenses as $id => $lic) {
                    if (isset($_POST[$id])) unset($_SESSION['csellwc'][$id]);
                    if ($_POST[$id]) {
                        $_SESSION['csellwc'][$id] = (int)$_POST[$id]; //license if checked should be 1
                        
                    }
                }
                echo json_encode(array("success" => "1"));
                die();
            }
        }
        public function load_jsscripts() {
            //$screen = get_current_screen();
            //if ( in_array( $screen->id, array( 'wc-settings') ) )
	
                $settings = array();
                //get the general options
                wp_enqueue_script('csellwoo-jsscript', /* Handle/Name */
                CSELL_WC::plugin_url() . '/js/csellwoo-lic.js', /* Path to the plugin/assets folder */
                //array('jquery', 'xml2json', 'json2xml'), /* Script Dependencies */
                array('jquery'), /* Script Dependencies */
                null, /* null is any version, but could be the specific version of jquery if required */
                true
                /* if true=add to footer, false=add to header */
                );
                $settings = $this->sdata;
                //get these options
                $settings['ajURL'] = admin_url('admin-ajax.php');
                wp_localize_script('csellwoo-jsscript', 'CSELLWOO_VARS', $settings);
           
            wp_enqueue_style('csellwoo-style', CSELL_WC::plugin_url() . '/css/csellaccess.css', array(), CSELL_WC_VERSION);
        }
        /**
         * add_fees.
         *
         * @version 1.1.0
         * For future versions
         *
         */
        public function add_fees($cart) {
            //coming soon...
            $fees_to_add = array();
            //get the licenses as array of lic objects
            $licenses = CSELLWOO_CBX::get_licenses();
            //loop through all...
            foreach ($licenses as $id => $lic) {
                //get location of each...and proceed further if matches
                $location_arr = $lic->get_locations();
                $fee_value = $lic->get_add_fee();
                if ((in_array(3, $location_arr) || in_array(1, $location_arr) || in_array(2, $location_arr)) && $fee_value && isset($_SESSION['csellwc'][$id]) && $_SESSION['csellwc'][$id]) //add fee only works from checkout page..
                {
                    $fee_title = $lic->get_fee_text();
                    // Adding fee
                    $fees_to_add[] = array('name' => $fee_title, 'amount' => $fee_value, 'taxable' => (isset($taxable) ? ('yes' === $taxable) : true), 'tax_class' => 'standard',);
                }
            }
            // Add fees
            if (!empty($fees_to_add)) {
                foreach ($fees_to_add as $fee_to_add) {
                    $cart->add_fee($fee_to_add['name'], $fee_to_add['amount'], $fee_to_add['taxable'], $fee_to_add['tax_class']);
                }
            }
        }
        /**
         * update_custom_checkout_fields_order_meta.
         *
         * @version 1.0.0
         * @since   1.0.0
         * @todo
         */
        function update_csell_license_fields_order_meta($order_id) {
            $pid_arr = array();
            if (!$order_id) return;
            $order = wc_get_order($order_id);
      
            if ($order->get_status() == 'completed' || $order->get_status() == 'processing') {
                $status = 1;
            } else $status = 0;
			
			
           // foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
			     foreach ( $order->get_items() as $item_id => $lineItem ) {
                $pid_arr[] =  $lineItem['product_id'];//$values['product_id'];
				
				$cost_arr[$lineItem['product_id']]= $lineItem['total']; //$lineitem->get_total(); // Get the item line total discounted

            }
			

			
			
            $fields_data = array();
            $val_arr[0] = CSELLWOO_NO;
            $val_arr[1] = CSELLWOO_YES;
            //get the licenses as array of lic objects
            $licenses = CSELLWOO_CBX::get_licenses();
            $type = 'license';
            delete_post_meta($order_id, '_csellwoo_lic');
			           
            //loop through all...
            $ix = 0;
            foreach ($licenses as $id => $lic) {
                $pid = $lic->get_license_product();
				
				 delete_post_meta($order_id,  '_csellwoo_'.$lic->get_code());
                $ix++;
                if (in_array($pid, $pid_arr)) {
					
					$pc=$cost_arr[$pid];
						/////AMIN WC3 HPOS	 
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {			
$order->update_meta_data(  '_csellwoo_lic',  $id . " - " . $lic->get_code() . " - " . $pid . " - " . $status ); // Add the metadata
$order->update_meta_data( '_csellwoo_'.$lic->get_code(),  $pc ); // Add the metadata
$order->save(); // Save it to the database
	}else{


                    add_post_meta($order_id, '_csellwoo_lic', $id . " - " . $lic->get_code() . " - " . $pid . " - " . $status);
                    add_post_meta($order_id, '_csellwoo_'.$lic->get_code(), $pc);
					
	}
                }
            }
        }
        public function get_related_products($postid) {
            //get all licenses/product id related to the related product...
            $licenses = CSELLWOO_CBX::get_licenses();
            $pidarr = array();
            $plist = '';
            foreach ($licenses as $id => $lic) {
                $p1s = $lic->get_license_pages();
                $p2s = $lic->get_license_posts();
                if (in_array($postid, $p1s) || in_array($postid, $p2s)) {
                    $pidarr[] = $lic->get_license_product();
                }
            }
            if (count($pidarr)) {
				
				 $plist.= "<h4>Buy product(s) below, to gain access to this page content.</h4>";
				 
                $args = array('post_type' => 'product', 'posts_per_page' => 10, 'post__in' => $pidarr);
                wp_enqueue_style('woocommerce_stylesheet', WP_PLUGIN_URL . '/woocommerce/assets/css/woocommerce.css', false, '1.0', "all");
                $loop = new WP_Query($args);
                $plist.= '<ul class="products columns-4">';
                while ($loop->have_posts()):
                    $loop->the_post();
                    $product = wc_get_product(get_the_ID()); //set the global product object
                    $plist.= '<li class="product"> <div class="image_holder"><a href="' . get_permalink() . '">' . woocommerce_get_product_thumbnail() . '</a></div><div class="title_area_holder"><h4>' . get_the_title() . '</h4><br>' . $product->get_price_html() . '</div>';
                    $plist.= "<a href='" . $product->add_to_cart_url() . "'>" . esc_html($product->add_to_cart_text()) . '</a></li>';
                endwhile;
                $plist.= "</ul>";
                wp_reset_query();
            }
            return $plist;
        }
    }
endif;
return new csellwoo_Frontend();
