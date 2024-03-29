<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php happyforms_print_part_description( $part ); ?>

		<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

		<div class="happyforms-part__el">
			<?php
			$options = happyforms_get_part_options( $part['options'], $part, $form );
			$value = happyforms_get_part_value( $part, $form );

			$placeholder_text = $part['placeholder'];
			$no_result_option = '<li class="happyforms-custom-select-dropdown__not-found">' . $form['no_results_label'] . '</li>';
			$options_size = count( $options );

			$is_searchable = $options_size > 5;
			$is_searchable = apply_filters( 'happyforms_is_dropdown_searchable', $is_searchable, $part, $form );

			foreach( $options as $o => $option ) :
				$temp_value = array();
				$rank_value = '';
				$clear_button_hidden = 'hidden';

				if( ! empty( $value ) ) {
					$temp_value = $value;
					unset( $temp_value[$option['id']] );

					if( $value[$option['id']] != 0 ) {
						$rank_value = $value[$option['id']];
						$clear_button_hidden = '';
					}
				}
			?>
			<div class="happyforms-part__option happyforms-part-option" id="<?php echo esc_attr( $option['id'] ); ?>">
				<div class="happyforms-custom-select" data-searchable="true">
					<div class="happyforms-part__select-wrap">
						<select name="<?php happyforms_the_part_name( $part, $form ); ?>[<?php echo $option['id']; ?>]" data-serialize class="happyforms-select" data-prev-value="<?php echo $rank_value;?>" required >
							<option disabled hidden <?php echo ( $rank_value === '' ) ? ' selected' : ''; ?> value='' class="happyforms-placeholder-option"><?php echo $placeholder_text; ?></option>
							<?php for ( $index = 0; $index < $options_size; $index++ ) :
								$option_label = $index + 1;
								$disabled = in_array( $option_label, $temp_value ) ? ' disabled' : '';
								$selected = ( $rank_value != '' && $rank_value == $option_label ) ? ' selected' : '';
							?>
								<option value="<?php echo $option_label; ?>" <?php echo $selected; ?><?php echo $disabled; ?>><?php echo $option_label; ?></option>
							<?php endfor; ?>
							<option value="clear" class="happyforms-rank-clear-button" <?php echo $clear_button_hidden; ?>><?php echo __( 'Clear', 'happyforms' ); ?></option>
						</select>
					</div>
				</div>
				<label class="option-label">
					<span class="label"><?php echo esc_attr( $option['label'] ); ?></span>
				</label>
			</div>
			<?php endforeach; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
