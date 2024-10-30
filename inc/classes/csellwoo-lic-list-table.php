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
class CSELLWOO_Cbx_List_Table extends WP_List_Table {
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
        parent::__construct(array('singular' => __('PCP', 'content-sell-in-woocommerce'), 'plural' => __('PCPs', 'content-sell-in-woocommerce'), 'ajax' => false,));
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
        return apply_filters('content-sell-in-woocommerce_columns', array('cb' => '', 'name' => __('Name', 'content-sell-in-woocommerce'), 'product' => __('Product', 'content-sell-in-woocommerce'), 'pages' => __('Content(s)', 'content-sell-in-woocommerce'), 'expiry_days' => __('Expiry Days', 'content-sell-in-woocommerce'),));
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
        if ($lic->get_lic_id()) {
            return '<span></span>';
        } else {
            return '<span class="lic-worldwide-icon"></span>';
        }
    }
    public function column_hash($lic) {
        if ($lic->get_lic_id()) {
            return '<span>' . $lic->get_lic_id() . '</span>';
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
    public function column_name($lic) {
        if ($lic->get_lic_id()) {
            $edit_url = admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses&lic_id=' . $lic->get_lic_id());
            $actions = array('id' => sprintf('Slug: %s', $lic->get_lic_id()), 'edit' => '<a href="' . esc_url($edit_url) . '">' . __('Edit', 'content-sell-in-woocommerce') . '</a>', 'trash' => '<a class="submitdelete wcpbc-delete-lic" title="' . esc_attr__('Delete', 'content-sell-in-woocommerce') . '" href="' . esc_url(wp_nonce_url(add_query_arg(array('delete_lic' => $lic->get_lic_id()), admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses')), 'content-sell-in-woocommerce-delete-lic')) . '">' . __('Delete', 'content-sell-in-woocommerce') . '</a>',);
            $row_actions = array();
            foreach ($actions as $action => $link) {
                $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
            }
            $output = sprintf('<a href="%1$s">%2$s</a>', esc_url($edit_url), $lic->get_name());
            $output.= '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
        } else {
            $output = '<span>' . $lic->get_name() . '</span><div class="row-actions">&nbsp;</div>';
        }
        return $output;
    }
    /**
     * Return countries column.
     *
     * @param CSELLWOO_CBX $lic lic instance.
     * @return string
     */
    public function column_productx($lic) {
        if ($lic->get_lic_id()) {
            $edit_url = admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses&lic_id=' . $lic->get_lic_id());
            $actions = array('id' => sprintf('Slug: %s', $lic->get_lic_id()), 'edit' => '<a href="' . esc_url($edit_url) . '">' . __('Edit', 'content-sell-in-woocommerce') . '</a>', 'trash' => '<a class="submitdelete wcpbc-delete-lic" title="' . esc_attr__('Delete', 'content-sell-in-woocommerce') . '" href="' . esc_url(wp_nonce_url(add_query_arg(array('delete_lic' => $lic->get_lic_id()), admin_url('admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses')), 'wc-csellwoo-lic-delete-lic')) . '">' . __('Delete', 'content-sell-in-woocommerce') . '</a>',);
            $row_actions = array();
            foreach ($actions as $action => $link) {
                $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
            }
            $output = sprintf('<a href="%1$s">%2$s</a>', esc_url($edit_url), $lic->get_name());
            $output.= '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
        } else {
            $output = '<span>' . $lic->get_name() . '</span><div class="row-actions">&nbsp;</div>';
        }
        return $output;
    }
    /**
     * Return currency column
     *
     * @param CSELLWOO_CBX $lic lic instance.
     * @return string
     */
    public function column_product($lic) {
        $output = $lic->get_license_productname();
        return $output;
    }
    public function column_pages($lic) {
        $output = $lic->get_license_pagenames();
        return $output;
    }
    public function column_expiry_days($lic) {
        $output = $lic->data['expiry_days'];
        return $output;
    }
    /**
     * Prepare table list items.
     */
    public function prepare_items() {
        //$default_lic = CSELLWOO_CBX::create();
        //$default_lic->set_name( __( 'Countries not covered by your other licenses', 'content-sell-in-woocommerce' ) );
        $licenses = CSELLWOO_CBX::get_licenses();
        //$licenses[] = $default_lic;
        $this->_column_headers = array($this->get_columns(), array(), array());
        $this->items = $licenses;
    }
    /**
     * Generate the table navigation above or below the table. No need the tablenav section.
     *
     * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
     */
    protected function display_tablenav($which) {
    }
    /**
     * Generates content for a single row of the table.
     *
     * @param CSELLWOO_CBX $lic  lic instance.
     */
    public function single_row($lic) {
        if ($lic->get_lic_id()) {
            parent::single_row($lic);
        } else {
            echo '<tr class="lic-worldwide">';
            $this->single_row_columns($lic);
            echo '</tr>';
        }
    }
}
