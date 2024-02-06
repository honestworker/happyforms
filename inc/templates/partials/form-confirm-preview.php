<div class="happyforms-form__part happyforms-part happyforms-part--submit">
	<button class="submit happyforms-submit happyforms-button--submit happyforms-button--edit"><?php echo $form['edit_button_label']; ?></button>
	<button type="submit" class="happyforms-submit happyforms-button--submit" data-step="<?php echo happyforms_get_last_step( $form, true ); ?>"><?php echo esc_attr( happyforms_get_form_property( $form, 'submit_button_label' ) ); ?></button>
</div>
