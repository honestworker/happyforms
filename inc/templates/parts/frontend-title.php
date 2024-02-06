<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php
			$options = happyforms_get_part_options( $part['options'], $part, $form );
			$value = happyforms_get_part_value( $part, $form );
			$default_label = ( '' !== $value ) ? $options[$value]['label'] : '';
			$placeholder_text = $part['placeholder'];
		?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>
			<div class="happyforms-custom-select">
				<div class="happyforms-part__select-wrap">
					<select name="<?php happyforms_the_part_name( $part, $form ); ?>" data-serialize class="happyforms-select" required>
							<option disabled hidden <?php echo ( $value === '' ) ? ' selected' : ''; ?> value='' class="happyforms-placeholder-option"><?php echo $placeholder_text; ?></option>
						<?php foreach ( $options as $index => $option ) : ?>
						<?php
							$option_value = isset( $option['value'] ) ? $option['value'] : $index;
							$submissions_left_label = isset( $option['submissions_left_label'] ) ? ' ' . $option['submissions_left_label'] : '';
							$selected = ( $value != '' && $value == $option_value ) ? ' selected' : '';
						?>
							<option value="<?php echo $option_value; ?>" <?php echo $selected; ?>><?php echo esc_attr( $option['label'] ); ?><?php echo $submissions_left_label; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_print_part_description( $part ); ?>
			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
