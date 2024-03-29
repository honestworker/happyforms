<div class="happyforms-form__part happyforms-part happyforms-part--submit">
	<?php do_action( 'happyforms_form_submit_before', $form ); ?>
	<?php
	$current_break = happyforms_get_current_page_break( $form, true );
	$breaks = happyforms_get_page_breaks( $form );

	$form_controller = happyforms_get_form_controller();
	$breaks_parts = $form_controller->get_parts_by_type( $form, 'page_break' );
	$current_break_part = $breaks_parts[$current_break];
	$label = $current_break_part['continue_button_label'];

	if ( $current_break === count( $breaks ) - 1 ) {
		if ( ! happyforms_get_form_setup_upgrade()->requires_confirmation( $form ) ) {
			$label = esc_attr( happyforms_get_form_property( $form, 'submit_button_label' ) );
		} else {
			$label = esc_attr( happyforms_get_form_property( $form, 'review_button_label' ) );
		}
	}
	$submit_button_extra_class = '';
	if( happyforms_get_form_property( $form, 'add_submit_button_class' ) == 1 ) {
		$submit_button_extra_class = happyforms_get_form_property( $form, 'submit_button_html_class' );
	}
	?>
	<button type="submit" class="happyforms-submit happyforms-button--submit <?php echo $submit_button_extra_class; ?>"><?php echo $label; ?></button>
	<?php do_action( 'happyforms_form_submit_after', $form ); ?>
</div>
