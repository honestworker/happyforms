<?php

$integrations = happyforms_get_integrations();
$service = $integrations->get_service( 'recaptcha' );
$credentials = $service->get_credentials();
?>
<div id="happyforms-service-<?php echo $service->id; ?>" class="happyforms-service-integration">
	<div class="widget-content <?php if ( $service->is_connected() ) : ?>authenticated<?php endif; ?>">
		<?php
		happyforms_credentials_input(
			$service->id,
			'site',
			__( 'Site key', 'happyforms' ),
			$credentials['site']
		);
		?>
		<?php
		happyforms_credentials_input(
			$service->id,
			'secret',
			__( 'Secret key', 'happyforms' ),
			$credentials['secret']
		);
		?>
	</div>
</div>