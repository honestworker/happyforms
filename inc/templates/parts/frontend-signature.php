<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>
		
		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( '' !== $part['intent_text'] ) : ?>
			<label class="option-label">
				<input type="checkbox" class="happyforms-visuallyhidden" id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[intent]" value="yes" <?php checked( happyforms_get_part_value( $part, $form, 'intent' ), 'yes' ); ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
				<span class="checkmark"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="currentColor" d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/></svg></span>
				<span class="label"><?php echo html_entity_decode( $part['intent_text'] ); ?></span>
			</label>
			<?php endif; ?>

			<?php if ( 'type' === $part['signature_type'] ) : ?>

				<input id="<?php happyforms_the_part_id( $part, $form ); ?>_signature" type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>[signature]" value="<?php happyforms_the_part_value( $part, $form, 'signature' ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php happyforms_the_part_attributes( $part, $form ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />

			<?php elseif ( 'draw' === $part['signature_type'] ) : ?>

				<?php $signature_path_data = happyforms_get_part_value( $part, $form, 'signature_path_data' ); ?>

				<input id="<?php happyforms_the_part_id( $part, $form ); ?>_signature_path_data" type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[signature_path_data]" value="<?php happyforms_the_part_value( $part, $form, 'signature_path_data' ); ?>" data-happyforms-path-data />
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>_signature_raster_data" type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[signature_raster_data]" value="<?php happyforms_the_part_value( $part, $form, 'signature_raster_data' ); ?>" data-happyforms-raster-data />

				<div class="happyforms--signature-area--container <?php echo $signature_path_data ? 'drawn' : ''; ?>">
					<div class="happyforms--signature-area">
						<svg viewBox="<?php happyforms_the_part_value( $part, $form, 'signature_viewbox' ); ?>" data-happyforms-name="<?php happyforms_the_part_name( $part, $form ); ?>[signature_viewbox]" preserveAspectRatio="xMidYMid meet">
							<path d="<?php echo $signature_path_data; ?>" />
						</svg>
						<img />
					</div>
					<button class="happyforms-button happyforms--signature-area--start-drawing"><?php echo happyforms_get_validation_message( 'field_signature_start_drawing_button_label' ); ?></button>
					<div class="happyforms--signature-area--toolbar">
						<button class="happyforms-button happyforms--signature-area--clear-drawing"><?php echo happyforms_get_validation_message( 'field_signature_clear_button_label' ); ?></button>
						<button class="happyforms-button happyforms--signature-area--done-drawing"><?php echo happyforms_get_validation_message( 'field_signature_done_button_label' ); ?></button>
						<button class="happyforms-button happyforms--signature-area--edit-drawing"><?php echo happyforms_get_validation_message( 'field_signature_start_over_button_label' ); ?></button>
					</div>
				</div>

			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
