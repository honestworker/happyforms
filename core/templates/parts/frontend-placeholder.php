<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php
		if ( ! empty( $part['label'] ) ) {
			happyforms_the_part_label( $part, $form );
		}
		?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php
			$placeholder_text = $part['placeholder_text'];
			$placeholder_text = html_entity_decode( $placeholder_text );
			$placeholder_text = wp_unslash( $placeholder_text );
			$placeholder_text = do_shortcode( $placeholder_text );

			echo $placeholder_text;
			?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
		</div>
	</div>
</div>
