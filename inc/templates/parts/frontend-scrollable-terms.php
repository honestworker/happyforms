<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php
		if ( ! empty( $part['label'] ) || happyforms_is_preview() ) {
			happyforms_the_part_label( $part, $form );
		}
		?>

		<?php happyforms_print_part_description( $part ); ?>

		<?php if ( 1 ==  $part['required'] ) : ?>
		<?php $has_scrolled = happyforms_get_part_value( $part, $form ); ?>
			<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>" value="<?php echo $has_scrolled; ?>" data-serialize />
		<?php endif; ?>
		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php
			$terms_text = $part['terms_text'];
			$terms_text = html_entity_decode( $terms_text );
			$terms_text = wp_unslash( $terms_text );
			$terms_text = do_shortcode( $terms_text );
			?>
			<div class="scrollbox" tabindex="0">
				<div class="content">
					<?php echo $terms_text; ?>
				</div>
			</div>
			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
