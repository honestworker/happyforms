<?php
	$form_controller = happyforms_get_form_controller();
	$forms_ = $form_controller->get();
	$forms = array_filter( $forms_, function ( $form ) {
	    return ( $form['post_status'] != 'trash');
	});

	$submission_counter = happyforms_submission_counter();
?>
<div class="wrap">
	<h1><?php _e( 'Export', 'happyforms' ); ?></h1>
	<p><?php _e( 'When you click the button below WordPress will create an XML or CSV file for you to save to your computer.', 'happyforms' ); ?>&nbsp;<?php _e( 'The file will contain your forms or submissions.', 'happyforms' ); ?></p>
	<p><?php _e( "Once you've saved the download file, you can use the Import function in another WordPress installation to import the content from this site.", 'happyforms' ); ?></p>
	<h2><?php _e( 'Choose what to export', 'happyforms' ); ?></h2>
	<form method="get" id="happyforms-export-form" action="<?php echo admin_url( 'admin.php' ); ?>" method="post">
		<input type="hidden" name="action" value="happyforms_export_import">
		<?php wp_nonce_field( 'happyforms_export_import', 'happyforms_export_nonce' ); ?>
		<fieldset>
			<p><label><input type="radio" name="action_type" value="export_responses">Submissions to spreadsheet (.csv)</label></p>
			<ul id="submission-filters" class="export-filters" style="display: none;">
				<li>
					<label><span class="label-responsive"><?php _e( 'Form', 'happyforms' ); ?>:</span>
					<select name="export_responses-form_id">
						<option value="" data-has-responses selected><?php _e( '— Select —', 'happyforms' ); ?>
						<?php
						foreach ( $forms as $form ) {
							$submissions_total = happyforms_get_meta( $form['ID'], $submission_counter->key_count_submission_total, true );

							if ( ! empty( $submissions_total ) ) {
							?>
							<option value="<?php echo esc_attr( $form['ID'] ); ?>"><?php echo happyforms_get_form_property( $form, 'post_title' ); ?></option>
							<?php
							}
						}
						?>
					</select>
					</label>
				</li>
			</ul>
			<p><label><input type="radio" name="action_type" value="export_form">Forms</label></p>
			<ul id="form-filters" class="export-filters" style="display: none;">
				<li>
					<label><span class="label-responsive"><?php _e( 'Form', 'happyforms' ); ?>:</span>
					<select name="form_id">
						<option value="" data-has-responses selected><?php _e( '— Select —', 'happyforms' ); ?>
						<?php
						foreach ( $forms as $form ) {
							?>
							<option value="<?php echo esc_attr( $form['ID'] ); ?>"><?php echo happyforms_get_form_property( $form, 'post_title' ); ?></option>
							<?php
						}
						?>
					</select>
					</label>
				</li>
				<li>
					<label><input type="checkbox" name="export_form_responses" value="1" /> <span class="label-responsive"><?php _e( 'Include submissions of selected form in export file', 'happyforms' ); ?></span></label>
				</li>
			</ul>
		</fieldset>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Download Export File"></p>
	</form>
</div>