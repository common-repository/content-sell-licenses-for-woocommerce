<?php
/**
 * WooCommerce Agreeme Licenses Listing table.
 *
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package CSELLWOO
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * CSELLWOO_Cbx_List_Table Class
 */
class CSELLWOO_Purchase_List_Table extends WP_List_Table {
    /**
     * Base currency
     *
     * @var string
     */
    protected $base_currency;
    protected $ix;
    /**
     * Initialize the regions table list
     */
    public function __construct() {
        parent::__construct(array('singular' => __('Order', 'content-sell-in-woocommerce'), 'plural' => __('Orders', 'content-sell-in-woocommerce'), 'ajax' => false,));
    }
    /**
     * Get a list of CSS classes for the WP_List_Table table tag.
     *
     * @return array List of CSS classes for the table tag.
     */
    protected function get_table_classes() {
        return array('widefat', 'fixed', $this->_args['plural']);
    }
    /**
     * Get list columns
     *
     * @return array
     */
    public function get_columns() {
        return apply_filters('content-sell-in-woocommerce_columns', array('cb' => '', 'order_id' => __('Order ID', 'content-sell-in-woocommerce'), 'order_date' => __('Order Date', 'content-sell-in-woocommerce'), 'customer' => __('Customer Details', 'content-sell-in-woocommerce'), 'license_name' => __('License Name', 'content-sell-in-woocommerce'),));
    }
    /**
     * Default column handler.
     *
     * @param CSELLWOO_CBX $item        Item being shown.
     * @param string     $column_name Name of column being shown.
     * @return string Default column output.
     */
    public function column_default($item, $column_name) {
        return apply_filters('content-sell-in-woocommerce_column_' . $column_name, $item);
    }
    /**
     * Column cb.
     *
     * @param CSELLWOO_CBX $lic  lic instance.
     * @return string
     */
    public function column_cb($lic) {
        if ($lic->get_id()) {
            return '<span></span>';
        } else {
            return '<span class="lic-worldwide-icon"></span>';
        }
    }
    public function column_hash($lic) {
        if ($lic->get_id()) {
            return '<span>' . $lic->get_id() . '</span>';
        } else {
            return '<span class="lic-worldwide-icon"></span>';
        }
    }
    /**
     * Return name column.
     *
     * @param CSELLWOO_CBX $lic  lic instance.
     * @return string
     */
    public function column_order_id($lic) {
        $output = '<span>' . $lic->get_id() . '</span><div class="row-actions">&nbsp;</div>';
        return $output;
    }
    /**
     * Return currency column
     *
     * @param CSELLWOO_CBX $lic lic instance.
     * @return string
     */
    public function column_order_date($lic) {
        $datex = $lic->get_date_completed();
        if ($datex) return $datex->date("F j, Y, g:i:s A T");
        else return 'N/C';
    }
    public function column_customer($lic) {
        $output = $lic->get_billing_last_name();
        $output.= "<br>" . $lic->get_billing_email();
        $output.= "<br>" . $lic->get_billing_phone();
        return $output;
    }
    public function column_license_name($lic) {
        $output = $lic->get_meta('_csellwoo_lic');
        if (!is_array($output)) {
            $output = array($output);
        }
        $links = '';
        foreach ($output as $val) {
            $keyvalarr = explode(" - ", $val);
            //add the enable disable links..
            if ((int)$keyvalarr[3]) {
                $ed_url = admin_url('admin-post.php?page=wc-settings&tab=csellwoo-lic&section=purchases&oid=' . $lic->get_id() . '&action=csellstatus_post&cstatus=0&lcode=' . $val);
                $linkname = "Disable";
            } else {
                $ed_url = admin_url('admin-post.php?page=wc-settings&tab=csellwoo-lic&section=purchases&oid=' . $lic->get_id() . '&action=csellstatus_post&cstatus=1&lcode=' . $val);
                $linkname = "Enable";
            }
            $links.= $keyvalarr[0] . ' <a href="' . $ed_url . '">' . $linkname . '</a>';
        }
        return $links;
    }
    /**
     * Prepare table list items.
     */
    public function prepare_items() {
        //$default_lic = CSELLWOO_CBX::create();
        // $per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
        $current_page = $this->get_pagenum();
        //$default_lic->set_name( __( 'Countries not covered by your other licenses', 'content-sell-in-woocommerce' ) );
        $purchases = CSELLWOO_CBX::get_purchases(5, $current_page);
        //$licenses[] = $default_lic;
        $totalItems = $purchases->total;
        $this->set_pagination_args(array('total_items' => $totalItems, 'per_page' => 5));
        $this->_column_headers = array($this->get_columns(), array(), array());
        $this->items = $purchases->orders;
    }
    /**
     * Generate the table navigation above or below the table. No need the tablenav section.
     *
     * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
     */
    /**
     * Generates content for a single row of the table.
     *
     * @param CSELLWOO_CBX $lic  lic instance.
     */
    public function single_row($lic) {
        if ($lic->get_id()) {
            parent::single_row($lic);
        } else {
            echo '<tr class="lic-worldwide">';
            $this->single_row_columns($lic);
            echo '</tr>';
        }
    }
}
