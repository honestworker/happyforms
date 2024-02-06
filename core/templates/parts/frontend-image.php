<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php
		if ( ! empty( $part['label'] ) ) {
			happyforms_the_part_label( $part, $form );
		}
		?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php
			if ( 0 != $part['attachment'] ) {
				$attachment_url = wp_get_attachment_url( $part['attachment'] );
			?>
				<img src="<?php echo $attachment_url; ?>" />
			<?php
			}
			?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

		</div>
	</div>
</div>
