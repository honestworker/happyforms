<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>
			<?php
			$toggletip_text = $part['details'];
			$toggletip_text = html_entity_decode( $toggletip_text );
			$toggletip_text = wp_unslash( $toggletip_text );
			$toggletip_text = do_shortcode( $toggletip_text );
			?>
			<details class="happyforms-toggletip-details">
				<summary class="happyforms-toggletip-summary"><u><?php echo esc_html( $part['label'] ); ?></u></summary>
				<div class="happyforms-toggletip-text"><?php echo $toggletip_text; ?></div>
			</details>
			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
		</div>
	</div>
</div>
