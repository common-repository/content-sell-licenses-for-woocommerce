<?php if ($data instanceof stdClass) : ?>
<div>

</div>
<form action="#" method="POST" class="csellaccess-form">
	<?php wp_nonce_field( 'csellaccess', 'csellform' );

wc_print_notices();

	?>
	
	<h2><?php echo esc_html($data->settings['csellwoo_ptitle']);?></h2>
<p><?php echo esc_html($data->settings['csellwoo_pdesc']); ?></p>

<?php if($data->settings['csellwoo_spf']=='yes'){?>
	<div><input type="hidden" name="postid" value="<?php echo (int)($data->settings['csellwoo_pid']); ?>">
		<label for="acc_lic_email"><?php _e( 'Enter your last order email address' ); ?></label>
		<div><input id="acc_lic" type="text" name="acc_lic_email" value="" placeholder=''/></div><div><input id="submit" type="submit" name="acc_submit" id="submit" class="submit" value="<?php esc_attr_e( 'Submit', 'msk' ); ?>" /></div>
	
	</div>
<?php } ?>
	
	
	
</form>

<?php if($data->settings['csellwoo_loginlink']){?>
<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account',''); ?>"> <?php echo esc_html($data->settings['csellwoo_loginlink']);?></a>
<?php } ?>

<div class="woocommerce csellaccess">


{RELATED_PRODUCTS}


</div>

	

<?php endif; ?>