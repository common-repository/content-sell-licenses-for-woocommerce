<?php
/**
 * WooCommerce Content Sell Licenses settings page
 *
 * @version 1.0.5
 * @package CSELLWOO
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
    
}
if (!class_exists('csellwoo_settings_lic')):
    /**
     * agrwc settings_lic Class
     */
    class csellwoo_settings_lic extends WC_Settings_Page {
        /**
         * License ID.
         *
         * @var string
         */
        protected $lic_id;
        /**
         * Constructor.
         */
        public function __construct() {
            $this->id = 'csellwoo-lic';
            $this->label = __('Protected Content Packages', 'content-sell-in-woocommerce');
            $this->lic_id = empty($_GET['lic_id']) ? false : wc_clean(wp_unslash($_GET['lic_id'])); // phpcs:ignore WordPress.Security.NonceVerification
            $this->init_hooks();
            $this->delete_lic();
        }
        /**
         * Init action and filters
         */
        protected function init_hooks() {
            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_sections_' . $this->id, array($this, 'update_lic_notice'), 5);
            add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }
		
	protected	function allowed_html() {

	$allowed_tags = array(
		'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'abbr' => array(
			'title' => array(),
		),
		'b' => array(),
		'blockquote' => array(
			'cite'  => array(),
		),
		'cite' => array(
			'title' => array(),
		),
		'code' => array(),
		'del' => array(
			'datetime' => array(),
			'title' => array(),
		),
		'dd' => array(),
		'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'dl' => array(),
		'input' => array(	'class' => array(),
			'name'  => array(),
			'id'   => array(),
			'placeholder' => array(),),
		'select' => array('class' => array(),
			'name'  => array(),
			'id'   => array(),
			'placeholder' => array(),),
		'label' => array(),
		'dt' => array(),
		'em' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'i' => array(),
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li' => array(
			'class' => array(),
		),
		'ol' => array(
			'class' => array(),
		),
		'p' => array(
			'class' => array(),
		),
		'q' => array(
			'cite' => array(),
			'title' => array(),
		),
		'span' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'ul' => array(
			'class' => array(),
		),
	);
	
	return $allowed_tags;
}



        /**
         * Delete a lic
         */
        protected function delete_lic() {
            if (!empty($_GET['delete_lic']) && isset($_GET['tab']) && 'csellwoo-lic' === $_GET['tab'] && isset($_GET['section']) && 'licenses' === $_GET['section']) { // WPCS: CSRF ok.
                if (empty($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'content-sell-in-woocommerce-delete-lic')) { // WPCS: input var ok, sanitization ok.
                    wp_die(esc_html__('Action failed. Please refresh the page and retry.', 'content-sell-in-woocommerce'));
                }
                $lic = CSELLWOO_CBX::get_lic_by_id(wc_clean(wp_unslash($_GET['delete_lic'])));
                if (!$lic) {
                    wp_die(esc_html__('License does not exist!', 'content-sell-in-woocommerce'));
                }
                CSELLWOO_CBX::delete($lic);
                WC_Admin_Settings::add_message(__('License have been deleted.', 'content-sell-in-woocommerce'));
            }
        }
        /**
         * Checks the current section
         *
         * @param string $section String to check.
         * @return bool
         */
        protected function is_section($section) {
            global $current_section;
            return $section === $current_section;
        }
        /**
         * Get sections
         *
         * @return array
         */
        public function get_sections() {
		      $xx= (array) get_option( 'csellwoo_licenses_data', array() );
			  
			  
            $sections = array('licenses' => __('Protected Content Packages('.count($xx).')', 'content-sell-in-woocommerce'),'general' => __('General options', 'content-sell-in-woocommerce'), 'purchases' => __('Purchases', 'content-sell-in-woocommerce'),);
            return $sections;
        }
		
		
		public function get_posttypes(){
		$ptypes=array(''=>'Select Post Type');
		$args=array(
    'public'                => true
); 
$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'
		
								$post_types = get_post_types($args,$output,$operator); 

							foreach ($post_types as $post_type) {
								
					$ptypes[$post_type]=$post_type;
					
								}
						
			return $ptypes;
			
		}
        /**
         * Get settings array
         *
         * @return array
         */
        public function get_settings() {
            $settings = apply_filters('content_sell_in_woocommerce_settings_general', array(array('title' => __('General Options', 'content-sell-in-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options',), array('title' => __('Enable', 'content-sell-in-woocommerce'), 'desc' => __('Enable Switch On Off all CSP(Content Sell Protection)', 'content-sell-in-woocommerce'), 'id' => 'csellwoo_enabled', 'default' => 'yes', 'type' => 'checkbox',
            // translators: HTML tags.
            'desc_tip' => __('Enable Plugin to protect content, If not enabled, The protected page contents will be visible to all.', 'content-sell-in-woocommerce')),
			 array('title' => __('Show  Products to purchase in the bottom of the protected pages', 'content-sell-in-woocommerce'), 'desc' => __('Show related products to purchase License in the protected page(s)', 'content-sell-in-woocommerce'), 'id' => 'csellwoo_showproducts', 'default' => 'yes', 'type' => 'checkbox',
            // translators: HTML tags.
            'desc_tip' => __('The product will  be listed when showing the protected message notice', 'content-sell-in-woocommerce')),
			array('title' => __('Show Email Field in protected pages to request access details in email', 'content-sell-in-woocommerce'), 'desc' => __('Show Email Field in protected pages to request access details in email', 'content-sell-in-woocommerce'), 'id' => 'csellwoo_spf', 'default' => 'yes', 'type' => 'checkbox',
            // translators: HTML tags.
            'desc_tip' => __('Purchased customers will only be sent the access links.', 'content-sell-in-woocommerce')),
			
			
			 array('title' => __('Show Notice in  Product\'s Page', 'content-sell-in-woocommerce'), 'desc' => __('Show notice in licensed product page ', 'content-sell-in-woocommerce'), 'id' => 'csellwoo_pnotice', 'default' => 'Yes', 'type' => 'checkbox',
            // translators: HTML tags.
            'desc_tip' => __('Below notice will displayed in the  product page', 'content-sell-in-woocommerce')),
			
			array('title' => __('Product Page Notice Text'),'id' => 'csellwoo_pnoticetext', 'type' => 'textarea', 'label' => 'bbbbbbbbbbbb', 'default' => 'This item on purchase will give you access to protected content.'),
					 
					 
						 array('title' => __('Extra Post Type apart from Pages', 'content-sell-in-woocommerce'), 'desc' => __('The additional post type for protected content', 'content-sell-in-woocommerce'), 'id' => 'csellwoo_ptype', 'default' => '', 'type' => 'select','options' => $this->get_posttypes(),
            // translators: HTML tags.
            'desc_tip' => __('Select additional post type that can be selected as protected contents', 'content-sell-in-woocommerce')),
			
			 array('title' => __('Right click disable in protected pages', 'content-sell-in-woocommerce'), 'desc' => __('Disable right click in protected pages', 'content-sell-in-woocommerce'), 'id' => 'csellwoo_rcd', 'default' => 'yes', 'type' => 'checkbox',
            // translators: HTML tags.
            'desc_tip' => __('Right clicking disabled in the protected content pages', 'content-sell-in-woocommerce')),
			
			
		 array('title' => __('Protected Content Title'), 'id' => 'csellwoo_ptitle', 'type' => 'text', 'default' => 'Protected Content',),  array('title' => __('Password Protection Message'),'id' => 'csellwoo_pdesc', 'type' => 'textarea', 'label' => 'bbbbbbbbbbbb', 'default' => 'This page content is protected and you need a valid License/Purchase access, if you have already purchased, enter your email address  and we will send you access links to this page.'),array('title' => __('Login link text in Protected Page'),'id' => 'csellwoo_loginlink', 'type' => 'text', 'label' => 'Login link',  'desc_tip' => __('Leave empty if not required', 'content-sell-in-woocommerce'), 'default' => 'Please login to access your account and protected content'),array('type' => 'sectionend', 'id' => 'general_options'),));
            return $settings;
        }
        /**
         * Output the settings
         */
        public function output() {
          //  ob_start();
            if ($this->is_section('licenses') || $this->is_section('') ) {
                $this->output_lic_screen();
            } elseif ($this->is_section('purchases')) {
                $this->output_purchase_screen();
            }elseif ($this->is_section('general')) {
                $settings = $this->get_settings();
                WC_Admin_Settings::output_fields($settings);
            }
			
			
           // $output = ob_get_clean();
			
			//$allowed_html = wp_kses_allowed_html('post'); 
			
			//$allowed_html=array_merge($allowed_html,$this->allowed_html());
           // echo  wp_kses($output,$allowed_html);
        }
        /**
         * Save settings
         */
        public function save() {
            if ($this->is_section('licenses') && $this->lic_id) {
                $this->save_lic();
            } elseif ($this->is_section('license') && class_exists('csellwoo_License_Settings')) {
				//coming in next version
                WCPBC_License_Settings::save_fields();
            } elseif (!$this->is_section('licenses')) {
                // Save General settings.
                $settings = $this->get_settings();
                //this will update the woo options..
                WC_Admin_Settings::save_fields($settings);
            }
        }
		
		
		/**
         * Handles output of the License page in admin.
         */
        protected function output_purchase_screen() {
            global $hide_save_button;
            $hide_save_button = true; // @codingStandardsIgnoreLine
     
                // License list table.
                include_once CSELL_WC::plugin_path() . '/inc/classes/csellwoo-purchase-list-table.php';
                echo '<h3>' . esc_html__('PCP Purchases', 'content-sell-in-woocommerce') . ' <a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses&lic_id=new')) . '" class="add-new-h2">' . esc_html__('Add New', 'content-sell-in-woocommerce') . '</a></h3>';
                echo '<p>' . esc_html__('These are orders which customers bought access to protected content(s)', 'content-sell-in-woocommerce') . '</p>';
                $table_list = new CSELLWOO_Purchase_List_Table();
                $table_list->prepare_items();
                $table_list->views();
                $table_list->display();
            
        }
		
		
        /**
         * Handles output of the License page in admin.
         */
        protected function output_lic_screen() {
            global $hide_save_button;
            $hide_save_button = true; // @codingStandardsIgnoreLine
            if ($this->lic_id) {
                // Single lic screen.
                if ('new' === $this->lic_id) {
                    $lic = CSELLWOO_CBX::create();
                } else {
                    $lic = CSELLWOO_CBX::get_lic_by_id($this->lic_id);
                }
                if (!$lic) {
                    wp_die(esc_html__('License does not exist!', 'content-sell-in-woocommerce'));
                }
                include dirname(__FILE__) . '/view/admin-page-lic.php';
            } else {
                // License list table.
                include_once CSELL_WC::plugin_path() . '/inc/classes/csellwoo-lic-list-table.php';
                echo '<h3>' . esc_html__('Protected Content Packages', 'content-sell-in-woocommerce') . ' <a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses&lic_id=new')) . '" class="add-new-h2">' . esc_html__('Add New', 'content-sell-in-woocommerce') . '</a></h3>';
                echo '<p>' . esc_html__('List of protected content packages(PCPs)', 'content-sell-in-woocommerce') . '</p>';
				//   echo '<p>' . esc_html__('You need a woocommerce product and protected content posts/pages  adding a license', 'content-sell-in-woocommerce') . '</p>';
                $table_list = new CSELLWOO_Cbx_List_Table();
                $table_list->prepare_items();
                $table_list->views();
                $table_list->display();
            }
        }
        /**
         * Save a License from the $_POST array.
         */
        protected function save_lic() {
            do_action('csellwoo_before_save_lic');
            $postdata = wc_clean(wp_unslash($_POST)); // WPCS: CSRF ok.
			
            $allowed_html = array('a' => array('href' => array(),), 'br' => array(),);
            //clean the post...with only required values...

            if ('new' === $this->lic_id) {
                $lic = CSELLWOO_CBX::create();
            } else {
                $lic = CSELLWOO_CBX::get_lic_by_id($this->lic_id);
            }
            if (!$lic) {
                wp_die(esc_html__('Entry does not exist!', 'content-sell-in-woocommerce'));
            }
            foreach ($postdata as $field => $value) {
                if (!isset($lic->data[$field])) unset($postdata[$field]);
            }
            // Fields validation.
            $pass = false;
            if (empty($postdata['name'])) {
                WC_Admin_Settings::add_error(__('Name is required.', 'content-sell-in-woocommerce'));
            }  else {
                $pass = true;
            }
			
		
			
            if ($pass) {
                foreach ($postdata as $field => $value) {
                    if (is_callable(array($lic, 'set_' . $field))) {
                        $lic->{'set_' . $field}($value);
                    }
                }
                $id = CSELLWOO_CBX::save($lic);
                do_action('csellwoo_after_save_lic', $id);
                wp_safe_redirect(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses&lic_id=' . $id . '&updated=1'));
            }
        }
        /**
         * Output the lic update notice
         */
        public function update_lic_notice() {
            if ($this->is_section('licenses') && !empty($_GET['updated'])) { // WPCS: CSRF ok.
                
?>
			<div id="message" class="updated inline">
				<p><strong><?php esc_html_e('Entry updated successfully.', 'content-sell-in-woocommerce'); ?></strong></p>
				<p>
					<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses')); ?>">&larr; <?php esc_html_e('Back to Listings', 'content-sell-in-woocommerce'); ?></a>
					<a style="margin-left:15px;" href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses&lic_id=new')); ?>"><?php esc_html_e('Add a new license', 'content-sell-in-woocommerce'); ?></a>
				</p>
			</div>
				<?php
            }
        }
    }
endif;
return new csellwoo_settings_lic();
