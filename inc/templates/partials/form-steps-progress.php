<?php if ( ! happyforms_is_preview() ) : ?>
	<div class="happyforms-form__progress happyforms-form-progress">
		<?php
		  $form_controller = happyforms_get_form_controller();
		  $steps = $form_controller->get_parts_by_type( $form, 'page_break' );
		  $total_steps = count( $steps );
		  $step_index = happyforms_get_current_page_break( $form, true );
		  $current_part_step = $steps[ $step_index ];
			$multistep_back_label = $form['multi_step_back_label'];
			$multi_step_current_page_label = $form['multi_step_current_page_label'];

			$submitted_forms = $step_index;
		?>

		<div class="happyforms-flex happyforms-step_information_wrapper">
			<div class="happyforms-message-notice happyforms-step-wrapper-notice">
				<?php if ( $step_index + 1 > 1  ) : ?>
					<button type="button" data-step="-<?php echo ( $step_index - 1 ); ?>" class="submit happyforms-submit happyforms-button--submit happyforms-back-step"><?php echo $multistep_back_label; ?></button>
				<?php endif; ?>
				<span class="happyforms-form-progress__step-index happyforms-form-progress__step-title"><?php echo sprintf( __( '%s %s/%s: %s', 'happyforms' ), $multi_step_current_page_label, ( $step_index + 1 ),  $total_steps, $current_part_step['label'] ); ?></span>
			</div>
		</div>

	</div>
<?php endif; ?>
