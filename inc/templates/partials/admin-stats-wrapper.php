<div class="wrap happyforms-stats__wrap">
	<div class="tablenav happyforms-stats__nav">
		<div class="alignleft actions">
			<form action="" id="happyform-stats">
				<?php wp_nonce_field( $action_filter ); ?>
				<input type="hidden" name="action" value="<?php echo $action_filter; ?>" />
				<select name="form_id">
					<option value=""><?php _e( 'From all forms', 'happyforms' ); ?></option>
					<?php
					$forms = happyforms_get_form_controller()->get();
					foreach( $forms as $form ) : ?>
					<option value="<?php echo $form['ID']; ?>"><?php _e( 'From', 'happyforms' ); ?> "<?php echo happyforms_get_form_title( $form ); ?>"</option>
					<?php endforeach; ?>
				</select>
				<select name="span">
					<option value="daily"><?php _e( 'Daily data', 'happyforms' ); ?></option>
					<option value="weekly" selected><?php _e( 'Weekly data', 'happyforms' ); ?></option>
					<option value="monthly"><?php _e( 'Monthly data', 'happyforms' ); ?></option>
				</select>
				<input type="submit" name="filter_action" class="button" value="<?php _e( 'Filter', 'happyforms' ); ?>" />
			</form>
		</div>
		<div class="alignright actions">
			<a href="<?php echo wp_nonce_url( add_query_arg( 'action', $action_reset, admin_url( 'admin-ajax.php' ) ), $action_reset ); ?>" id="happyforms-stats__reset"><?php _e( 'Reset all', 'happyforms' ); ?></a>
		</div>
	</div>
	<?php require_once( happyforms_get_include_folder() . '/templates/partials/admin-stats.php' ); ?>
</div>
