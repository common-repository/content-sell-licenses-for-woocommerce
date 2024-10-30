<?php
/**
 * Protected Content Packages admin add/edit view
 *
 * @package agrwc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



?>

<div class="settings-panel csellwoo-lic-settings">

	<h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=csellwoo-lic&section=licenses' ) ); ?>"><?php esc_html_e( 'Protected Content Packages', 'content-sell-in-woocommerce' ); ?></a> &gt;
		<span class="csellwoo-lic-name"><?php echo esc_html( $lic->get_name() ? $lic->get_name() : __( 'New Package', 'content-sell-in-woocommerce' ) ); ?></span>
	</h2>

	<table class="form-table">

		<!-- Name -->
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Package Name', 'content-sell-in-woocommerce' ); ?>*</label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is the name of the Package for your reference.', 'content-sell-in-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="name" id="name" type="text" value="<?php echo esc_attr( $lic->get_name() ); ?>"/>
				</td>
		</tr>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="label"><?php esc_html_e( 'Package Code', 'content-sell-in-woocommerce' ); ?>*</label>
				<?php echo wp_kses_post( wc_help_tip( __( 'This is the access code of the package, auto generated.', 'content-sell-in-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				<input name="code" id="code" type="hidden"  value="<?php echo esc_attr( $lic->get_code() ); ?>"/>
			
					<input name="codeV" id="license_codeV" type="text" disabled value="<?php echo esc_attr( $lic->get_code() ); ?>"/>
				</td>
		</tr>
		
		

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Expiry in Days', 'content-sell-in-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'The expiry in number of days from the day of purchase, keep it empty if expiry not required.', 'content-sell-in-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					<input name="expiry_days" id="expiry_days" type="text" value="<?php echo (int)$lic->data['expiry_days']; ?>" size=4 />
				
				</td>
		</tr>
		
	



		
<?php 
    $product_id = $lic->data['license_product'];

  
		?>
				<tr valign="top">
			<th scope="row" class="titledesc">
                <label for="license_product"><?php _e( 'Product', 'content-sell-in-woocommerce' ); ?></label>
				</th>
				<td class="forminp forminp-text">
                <select class="wc-product-search"  style="width: 50%;" id="license_product" name="license_product" data-placeholder="<?php esc_attr_e( 'product to purchase...', 'woocommerce' ); ?>" data-list="products" data-field="autocomplete" data-action="woocommerce_json_search_products">
                    <?php
                      //  foreach ( $product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                            }
//}
                    ?>
                </select> <?php echo wc_help_tip( __( 'The product customer orders, to get access.', 'content-sell-in-woocommerce' ) ); ?>
            </td> 
			
			</tr>

		<tr valign="top">
			<th colspan="2" scope="row" class="titledesc" style="border-radius: 4px; background-color:#efefef; border-top: solid 1px #333;  padding: 10px;">
                <label for="license_product"><?php _e( '< ---- ---- Select Protected Contents that are given access when the above product is purchased. ---- ---- >', 'content-sell-in-woocommerce' ); ?></label>
				</th>
				</tr>


<?php 
    $post_ids = $lic->data['license_pages'];

	
    if( empty($post_ids) )
        $post_ids = array();
  
		?>

		
				<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Pages', 'content-sell-in-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'Select the list of protected pages for this license, edit the page to make it  Protected.', 'content-sell-in-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
					
						<select id='csell_pages'  name="license_pages[]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Protected Pages', 'content-sell-in-woocommerce' ); ?>" data-allow_clear="true" data-hide_empty="false">
					<?php
				
				      foreach ( $post_ids as $post_id ) {
                            $ptitle = get_the_title(  $post_id);
				?>
			
					<option value="<?php echo esc_attr( $post_id ); ?>" selected="selected"><?php echo esc_html( htmlspecialchars( wp_kses_post( $ptitle) ) ); ?></option>
<?php

					  }
					  ?>
			</select>
	<?php echo wp_kses_post( wc_help_tip( __( 'The Access links for these pages will be sent in the order completion email' ) ) ); ?>
						
				</td>
		</tr>

		
		<?php 
    $post_ids = $lic->data['license_posts'];
	
	
    if( empty($post_ids) )
        $post_ids = array();
  
		?>

		
				<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Other Contents(Custom PostType)', 'content-sell-in-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'Select the list of protected custom post type content for this license, edit post to make it as Protected.', 'content-sell-in-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				<?php /* ?>	<input name="limit_categories" id="limit_categories" type="text" value="<?php echo esc_attr( $lic->data['limit_categories'] ); ?>"/> <?php */ ?>
					
						<select id='csell_posts' name="license_posts[]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Protected Posts', 'content-sell-in-woocommerce' ); ?>" data-allow_clear="true" data-hide_empty="false">
						
						
				<?php
				
				      foreach ( $post_ids as $post_id ) {
                            $ptitle = get_the_title(  $post_id );
				?>
			
					<option value="<?php echo esc_attr( $post_id ); ?>" selected="selected"><?php echo esc_html( htmlspecialchars( wp_kses_post( $ptitle) ) ); ?></option>
<?php

					  }
					  ?>
			</select>
	<?php echo wp_kses_post( wc_help_tip( __( 'The Access links for these posts will be sent in the order completion email' ) ) ); ?>
						
				</td>
		</tr>
		
		
		
				<tr valign="top">
			<th colspan="2" scope="row" class="titledesc" style="border-radius: 4px; background-color:#efefef; border-top: solid 1px #333;  padding: 10px;">
                	</th>
				</tr>	
		

		
		
			<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="name"><?php esc_html_e( 'Emails', 'content-sell-in-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( __( 'Emails to add the protected content access links and instructions', 'content-sell-in-woocommerce' ) ) ); ?>
			</th>
				<td class="forminp forminp-text">
				
				<input type="hidden" name="elocations[]" value="999">
					<input name="elocations[]" id="elocation1" type="checkbox" value="1" <?php if(in_array('1',$lic->data['elocations']))echo "checked"; ?> /><?php echo _e( 'Order Completed', 'content-sell-in-woocommerce' );?>
					<input name="elocations[]" id="elocation2" type="checkbox" value="2" <?php if(in_array('2',$lic->data['elocations']))echo "checked"; ?> /><?php echo _e( 'Customer Invoice', 'content-sell-in-woocommerce' );?>
			
				</td>
		</tr>

	
		


	</table>


	<input type="hidden" name="page" value="wc-settings" />
	<input type="hidden" name="tab" value="csellwoo-lic" />
	<input type="hidden" name="section" value="licenses" />

	<p class="submit">
		<?php submit_button( __( 'Save Changes', 'content-sell-in-woocommerce' ), 'primary', 'save', false ); ?>
		<?php if ( $lic->get_lic_id() ) : ?>
		<a class="csellwoo-delete-lic" id="csellwoo-delete-lic"  style="color: #a00; text-decoration: none; margin-left: 10px;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'delete_lic' => $lic->get_lic_id() ), admin_url( 'admin.php?page=wc-settings&tab=csellwoo_lic&section=licenses' ) ), 'content-sell-in-woocommerce' ) ); ?>"><?php esc_html_e( 'Delete license', 'content-sell-in-woocommerce' ); ?></a> || <a class="csellwoo-lic-stats" id="csellwoo-lic-stats"  style="color: #a00; text-decoration: none; margin-left: 10px;" lcode='<?php echo esc_attr( $lic->get_code() ); ?>' href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'lic-stats' => $lic->get_lic_id() ), admin_url( 'admin.php?page=wc-settings&tab=csellwoo_lic&section=licenses' ) ), 'content-sell-in-woocommerce' ) ); ?>"><?php esc_html_e( 'Get Stats', 'content-sell-in-woocommerce' ); ?></a>

		<?php endif; ?>
	</p>
<div id="csell_lic_stats"></div>
</div>
