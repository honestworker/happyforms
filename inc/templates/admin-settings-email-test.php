<?php
$email_tester = happyforms_get_email_tester();

?>
<div class="widget-content">
	<div class="happyforms-email-test-notices"></div>
	<p>Hapyforms uses your WordPress' mail system. This will let you test if your WordPress installation can send out emails.</p>
	<form class="" id="hf-test-email">
		<input type="hidden" name="action" value="<?php echo $email_tester->send_action; ?>">
	 	<?php wp_nonce_field( $email_tester->send_action ); ?>
		<label><?php echo _e( 'Recepient Email Address', 'happyforms' ); ?></label>
		<div>
			<input type="email" name="email-recepient" class="widefat" value="" required />
		</div>
		<div class="send-email-wrap">
			<span class="spinner"></span>
			<input type="submit" class="connected button button-primary widget-control-save" value="<?php _e( 'Send Test Email', 'happyforms' ); ?>">
		</div>
	</form>
	<p>Can't find the test email? Please check your spam folder. If it's still missing, your WordPress setup may not be able to send emails. In that case, please reach out to your hosting provider.</p>
</div>