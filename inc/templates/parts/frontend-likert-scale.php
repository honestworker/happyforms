<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php
			$start = intval( $part['min_value'] );
			$end   = intval( $part['max_value'] );
			$value = happyforms_get_part_value( $part, $form );
			?>

			<?php if ( $start < $end ) : ?>
				<div class="happyforms-likert-scale-label happyforms-likert-scale-label--small happyforms-likert-scale-label--min"><?php echo $part['min_label']; ?></div>

				<div class="happyforms-likert-scale">
					<?php for ( $i = $start; $i <= $end; $i++ ) : ?>
						<label>
							<input type="radio" class="happyforms-visuallyhidden" name="<?php happyforms_the_part_name( $part, $form ); ?>" value="<?php echo $i; ?>" <?php checked( $value, $i, true ); ?>>
							<span class="happyforms-likert-scale__label"><?php echo $i; ?></span>
						</label>
					<?php endfor; ?>
				</div>

				<div class="happyforms-likert-scale-label happyforms-likert-scale-label--small happyforms-likert-scale-label--max"><?php echo $part['max_label']; ?></div>

				<div class="happyforms-likert-scale-labels">
					<span class="happyforms-likert-scale-label happyforms-likert-scale-label--min happyforms-likert-scale-labels__label--min"><?php echo $part['min_label']; ?></span>
					<span class="happyforms-likert-scale-label happyforms-likert-scale-label--max happyforms-likert-scale-labels__label--max"><?php echo $part['max_label']; ?></span>
				</div>
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
