<?php
/**
 *
 * Welcome email content template
 *
 * The file is prone to modifications after plugin upgrade or alike; customizations are advised via hooks/filters
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Thank you for your request. Your last order detail request with protected page access details', 'woocommerce' ); ?></p>
<p>You can  Login to access your account and view part order details.  <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account',''); ?>"><?php _e('My Account',''); ?></p>
<p><?php _e( 'Below are the last order details for your reference.' ) ?></p>

<?php
/**
* @hooked WC_Emails::order_details() Shows the order details table.
* @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
* @since 2.5.0
*/
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
/**
* @hooked WC_Emails::order_meta() Shows order meta data.
*/
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );