<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php
		$part_name = happyforms_get_part_name( $part, $form );
		$part_value = happyforms_get_part_value( $part, $form );
		$part_value_1 = $part_value_2 = $part_value;

		$has_prefix = ( '' !== $part['prefix'] );
		$has_suffix = ( '' !== $part['suffix'] );

		if ( 1 === intval( $part['multiple'] ) ) {
			$part_name = $part_name . '[]';
			$part_value_1 = $part_value[0];
			$part_value_2 = $part_value[1];
		}

		?>
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php happyforms_print_part_description( $part ); ?>

		<?php if ( $has_prefix ) : ?><span class="happyforms-part--scale__prefix"><?php echo $part['prefix']; ?></span><?php endif; ?><output for="<?php happyforms_the_part_id( $part, $form ); ?>"><?php echo $part_value_1; ?></output><?php if ( $has_suffix ) : ?><span class="happyforms-part--scale__suffix"><?php echo $part['suffix']; ?></span><?php endif; ?>

		<?php if ( 1 === intval( $part['multiple'] ) ) : ?><span class="happyforms-part--scale__emdash">—</span>
			<?php if ( $has_prefix ) : ?><span class="happyforms-part--scale__prefix"><?php echo $part['prefix']; ?></span><?php endif; ?><output for="<?php happyforms_the_part_id( $part, $form ); ?>_clone"><?php echo $part_value_2; ?></output><?php if ( $has_suffix ) : ?><span class="happyforms-part--scale__suffix"><?php echo $part['suffix']; ?></span><?php endif; ?>
		<?php endif; ?>


		<div class="happyforms-part__el">
			<div class="happyforms-part--scale__inputwrap">
				<div class="happyforms-part--scale__wrap">
					<input id="<?php happyforms_the_part_id( $part, $form ); ?>"<?php if ( 1 === intval( $part['multiple'] ) ) : ?> multiple<?php endif; ?> type="range" name="<?php echo $part_name; ?>" step="<?php echo esc_attr( $part['step'] ); ?>" min="<?php echo esc_attr( $part['min_value'] ); ?>" max="<?php echo esc_attr( $part['max_value'] ); ?>" value="<?php happyforms_the_part_value( $part, $form ); ?>" <?php happyforms_the_part_attributes( $part, $form ); ?> />
				</div>
			</div>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
