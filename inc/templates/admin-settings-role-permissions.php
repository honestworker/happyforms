<?php
$role_permissions = happyforms_get_role_permissions();
$action = $role_permissions->save_action;
$nonce = $role_permissions->save_nonce;
$roles = $role_permissions->get_roles();
$permissions = $role_permissions->read();
?>
<div>
	<div class="happyforms-settings-notices"></div>

	<form class="hf-ajax-submit">
		<?php wp_nonce_field( $action, $nonce ); ?>
		<input type="hidden" name="action" value="<?php echo $action; ?>">

		<p><?php _e( 'Manage users\' access to forms, submissions and settings per role.', 'happyforms' ); ?></p>

		<div class="controls">
			<?php foreach( $roles as $role_id => $role ) : ?>
			<div class="control">
				<div class="control__line">
					<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow]" id="<?php echo "{$role_id}_allow"; ?>" value="1" <?php checked( $permissions[$role_id]['allow'], 1 ); ?>>
					<label for="<?php echo "{$role_id}_allow"; ?>"><?php printf( __( '%s role', 'happyforms' ), translate_user_role( $role['name'] ) ); ?></label>
					<div class="nested-input">
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_forms]" value="1" id="<?php echo $role_id; ?>_allow_forms" <?php checked( $permissions[$role_id]['allow_forms'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_forms"><?php _e( 'Allow access to forms', 'happyforms' ); ?></label>
							</div>
						</div>
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_activity]" value="1" id="<?php echo $role_id; ?>_allow_activity" <?php checked( $permissions[$role_id]['allow_activity'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_activity"><?php _e( 'Allow access to submissions', 'happyforms' ); ?></label>
							</div>
						</div>
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_coupons]" value="1" id="<?php echo $role_id; ?>_allow_coupons" <?php checked( $permissions[$role_id]['allow_coupons'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_coupons"><?php _e( 'Allow access to coupons', 'happyforms' ); ?></label>
							</div>
						</div>
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_integrations]" value="1" id="<?php echo $role_id; ?>_allow_integrations" <?php checked( $permissions[$role_id]['allow_integrations'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_integrations"><?php _e( 'Allow access to integrations', 'happyforms' ); ?></label>
							</div>
						</div>
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_import]" value="1" id="<?php echo $role_id; ?>_allow_import" <?php checked( $permissions[$role_id]['allow_import'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_import"><?php _e( 'Allow access to import', 'happyforms' ); ?></label>
							</div>
						</div>
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_export]" value="1" id="<?php echo $role_id; ?>_allow_export" <?php checked( $permissions[$role_id]['allow_export'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_export"><?php _e( 'Allow access to export', 'happyforms' ); ?></label>
							</div>
						</div>
						<div class="control">
							<div class="control__line">
								<input type="checkbox" name="happyforms_role_permissions[<?php echo $role_id; ?>][allow_settings]" value="1" id="<?php echo $role_id; ?>_allow_settings" <?php checked( $permissions[$role_id]['allow_settings'], true ); ?>>
								<label for="<?php echo $role_id; ?>_allow_settings"><?php _e( 'Allow access to settings (excludes Role Capabilities)', 'happyforms' ); ?></label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="alignleft">
			<span class="spinner"></span>
			<input type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'happyforms' ); ?>">
		</div>
		<br class="clear">
	</form>
</div>
