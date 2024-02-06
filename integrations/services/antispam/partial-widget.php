<?php

$integrations = happyforms_get_integrations();
$service = $integrations->get_service( 'spam' );
$action = $integrations->action_update;
$services = $integrations->get_services();
$active_service = $service->get_active_service();
$active_service = $active_service ? $active_service->id : $active_service;
$groups = array();

foreach( $services as $sub_service ) {
	$groups[$sub_service->group][] = $sub_service;
}
?>
<div class="widget-content has-service-selection <?php if ( $service->is_connected() ) : ?>authenticated<?php endif; ?>" data-active-service="<?php echo $active_service; ?>">
	<?php wp_nonce_field( $action ); ?>
	<input type="hidden" name="action" value="<?php echo $action; ?>">
	<input type="hidden" name="group" value="spam">

	<p>
		<label for="happyforms_integrations_spam_service"><?php _e( 'Service:', 'happyforms' ); ?></label>
		<select id="happyforms_integrations_spam_service" name="services[]" class="widefat">
			<option value="">— <?php _e( 'Select', 'happyforms' ); ?> —</option>

			<?php foreach( $groups['spam'] as $sub_service ) : ?>
				<option value="<?php echo $sub_service->id; ?>" <?php selected( $sub_service->id, $active_service ); ?>><?php echo $sub_service->label; ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<?php
	foreach ( $groups['spam'] as $sub_service ) {
		$sub_service->admin_widget();
	}
	?>

	<div class="widget-control-actions">
		<input type="submit" class="connected button button-primary widget-control-save right" value="<?php _e( 'Save Changes', 'happyforms' ); ?>" disabled>
		<span class="spinner"></span>
	</div>
</div>
