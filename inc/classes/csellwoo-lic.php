<?php
/**
 * Represents a single license class
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package CSELLWOO
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * CSELLWOO_CB, The license class
 */
class CSELLWOO_CB {
    /**
     * License data.
     *
     * @var array
     */
    public $data = array();
    protected $elocationnamearr = array();
    /**
     * Constructor for License.
     *
     * @param array $data CBX attributes as array.
     */
    public function __construct($data = null) {
        $this->elocationnamearr[1] = __('Products Page', 'content-sell-in-woocommerce');
        $this->elocationnamearr[2] = __('Cart Page', 'content-sell-in-woocommerce');
        $this->elocationnamearr[3] = __('Checkout Page', 'content-sell-in-woocommerce');
        $this->data = wp_parse_args($data, array('lic_id' => '', 'name' => '',   'elocations' => array(), 'license_pages' => array(),'license_posts' => array(), 'license_product' =>'',  'code' => '', 'expiry_days' => '30'));
    }
    /**
     * Get license data.
     *
     * @return array
     */
    public function get_data() {
        return $this->data;
    }
    /**
     * Gets a prop for a getter method.
     *
     * @since 1.7.9
     * @param  string $prop Name of prop to get.
     * @return mixed
     */
    protected function get_prop($prop) {
        return isset($this->data[$prop]) ? $this->data[$prop] : false;
    }
    /**
     * Sets a prop for a setter method.
     *
     * @since 1.8.0
     * @param string $prop Name of prop to set.
     * @param mixed  $value Value to set.
     */
    protected function set_prop($prop, $value) {
        if (isset($this->data[$prop])) {
            $this->data[$prop] = $value;
        }
    }
    /**
     * Set license id.
     *
     * @param string $id License ID.
     */
    public function set_id($id) {
        $this->set_prop('lic_id', $id);
    }
    /**
     * Get license id.
     *
     * @return string
     */
    public function get_id() {
        return $this->get_prop('lic_id');
    }
    /**
     * Set license id.
     *
     * @param string $id License ID.
     */
    public function set_lic_id($id) {
        $this->set_id($id);
    }
    /**
     * Get license id.
     *
     * @return string
     */
    public function get_lic_id() {
        return $this->get_id();
    }
    /**
     * Get license name.
     *
     * @return string
     */
    public function get_name() {
        return $this->get_prop('name');
    }
    /**
     * Set the license name.
     *
     * @param string $name License name.
     */
    public function set_name($name) {
        $this->set_prop('name', $name);
    }
    public function get_code() {
		
		
	$lcode=$this->get_prop('code')	;
	if($lcode) return $lcode;
	
	if($this->get_prop('name'))	
        return md5( $this->get_prop('name')).rand(99,999);
	else
		return '';
    }
    /**
     * Set the license name.
     *
     * @param string $name License name.
     */
    public function set_code($label) {
		if($label=='') $label=md5( $this->get_prop('name')).rand(99,999);
        $this->set_prop('code', $label);
    }
   
      public function set_expiry_days($days) {
		
        $this->set_prop('expiry_days', $days);
    } 

  public function get_expiry_days() {
        return $this->get_prop('expiry_days');
    }
    /**
     * Get locations.
     *
     * @return array
     */
    public function get_elocations() {
        return $this->get_prop('elocations');
    }
    public function get_elocationnames() {
        $elocation_name = '';
        foreach ($this->get_prop('elocations') as $key) {
            $elocation_name.= $this->elocationnamearr[trim($key) ] . ", ";
        }
        return trim($elocation_name, ", ");
    }
    /**
     * Get locations.
     *
     * @return array
     */
    public function set_elocations($locations) {
        if (!$locations) $locations = array();
        return $this->set_prop('elocations', $locations);
    }

    /**
     * Get license limit products.
     *
     * @return string
     */
    public function get_license_product() {
        return $this->get_prop('license_product');
    }
	
	 public function set_license_product($products) {
	
        $this->set_prop('license_product', $products);
    }
	
	
	   public function get_license_productname() {
        $pid= $this->get_prop('license_product');
		$pname='';
		if($pid)
		{
			$product = wc_get_product( $pid );

			$pname= $product->get_title();

		}
		return $pname;
    }
	
	
	
		
	   public function get_license_pagenames() {
   
		 $pidarr= array_merge($this->get_prop('license_pages'), $this->get_prop('license_posts')); 
		$ptitle='';
		$cc='';
		if(count($pidarr)>2){$pidarr=array_slice($pidarr, 0, 2);$cc="...";}
		foreach($pidarr as $pid)
		{
			if(trim($pid))
			$ptitle .= ", ". get_the_title(  $pid );
			

		}
		return ltrim($ptitle,', ').$cc;
    }
	
	
	
    /**
     * Set the license limit categories.
     *
     * @param string $categories.
     */
    public function set_license_pages($pages) {
        $this->set_prop('license_pages', $pages);
    }
    public function get_license_pages() {
        return $this->get_prop('license_pages');
    }
    public function set_license_posts($posts) {
        $this->set_prop('license_posts',  $posts);
    }
    public function get_license_posts() {
        return $this->get_prop('license_posts');
    }

    /**
     * Set the license limit to products.
     *
     * @param array $products.
     */
   
   
}
