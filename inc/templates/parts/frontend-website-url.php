<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php
		$input_group = false;
		$has_prefix = ( '' !== $part['prefix'] );
		$has_suffix = ( '' !== $part['suffix'] );
		$early_label = true;

		if ( $has_prefix || $has_suffix ) {
			$input_group = true;
		}

		if ( 'as_placeholder' === $part['label_placement'] ) {
			$early_label = false;
		}

		if ( $has_prefix && 'inside' === $part['label_placement'] ) {
			$early_label = false;
		}
		?>

		<?php if ( $early_label ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( $input_group ) : ?>
				<div class="happyforms-input-group<?php echo ( $has_prefix ) ? ' with-prefix' : ''; ?><?php echo ( $has_suffix ) ? ' with-suffix' : ''; ?>">
					<div class="happyforms-input-group__prefix">
						<span><?php echo $part['prefix']; ?></span>
					</div>
			<?php endif; ?>

			<div class="happyforms-input">
				<?php if ( ! $early_label && 'as_placeholder' !== $part['label_placement'] ) : ?>
					<?php happyforms_the_part_label( $part, $form ); ?>
				<?php endif; ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>" type="text" value="<?php happyforms_the_part_value( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php happyforms_the_part_attributes( $part, $form ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />
				<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
					<?php happyforms_the_part_label( $part, $form ); ?>
				<?php endif; ?>
			</div>

			<?php if ( $input_group ) : ?>
				<?php if ( $has_suffix ) : ?>
					<div class="happyforms-input-group__suffix">
						<span><?php echo $part['suffix']; ?></span>
					</div>
				<?php endif; ?>
				</div><!-- /.happyforms-input-group -->
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
