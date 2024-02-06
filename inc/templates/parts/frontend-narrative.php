<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<p><?php
			$tokens = happyforms_get_narrative_tokens( $part['format'], true );
			$format = happyforms_get_narrative_format( $part['format'] );
			$inputs = array();

			foreach ( $tokens as $t => $placeholder ) {
				ob_start(); ?>
				<input id="<?php happyforms_the_part_id( $part, $form ); ?>" type="text" name="<?php happyforms_the_part_name( $part, $form ); ?>[]" <?php if ( ! empty( $placeholder ) ) : ?>placeholder="<?php echo esc_html( $placeholder ); ?>" <?php endif; ?> value="<?php happyforms_the_part_value( $part, $form, $t ); ?>" <?php happyforms_the_part_attributes( $part, $form, $t ); ?> /><?php
				$input = ob_get_clean();
				$inputs[$t] = $input;
			}

			$part_content = do_shortcode( vsprintf( html_entity_decode( stripslashes( $format ) ), $inputs ) );

			echo $part_content;
			?></p>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
