<?php
$service = happyforms_get_integrations()->get_service( 'google-places' );
$credentials = $service->get_credentials();
$action = happyforms_get_integrations()->integrations_action;
?>
<form class="happyforms-service hf-ajax-submit">
	<div class="happyforms-integrations-notices"><?php do_action( 'happyforms_integrations_print_notices' ); ?></div>
	<div class="widget-content">
		<?php wp_nonce_field( $action ); ?>
		<input type="hidden" name="action" value="<?php echo $action; ?>">
		<input type="hidden" name="service" value="<?php echo $service->id; ?>">

		<div id="happyforms-service-<?php echo $service->id; ?>" class="happyforms-service-integration">
			<div class="widget-content <?php if ( $service->is_connected() ) : ?>authenticated<?php endif; ?>">
				<?php
				happyforms_credentials_input(
					$service->id,
					'key',
					__( 'API key', 'happyforms' ),
					$credentials['key']
				);
				?>
			</div>
		</div>
		<div class="widget-control-actions">
			<div class="alignleft">
				<span class="spinner"></span>
				<input type="submit" class="connected button button-primary widget-control-save right" value="<?php _e( 'Save Changes', 'happyforms' ); ?>">
			</div>
			<br class="clear" />
		</div>
	</div>
</form>
