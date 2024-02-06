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
				$attachment = get_posts( array(
					'post_type' => 'attachment',
					'p'         => $part['attachment'],
				) );

				if ( ! empty( $attachment ) ) {
					$attachment = $attachment[0];
					$src        = wp_get_attachment_url( $attachment->ID );
					$html       = '';

					if ( wp_attachment_is( 'video', $attachment ) || wp_attachment_is( 'audio', $attachment ) ) {
						global $wp_embed;

						$html = do_shortcode( $wp_embed->run_shortcode( "[embed]{$src}[/embed]" ) );
					} else {
						$html = sprintf(
							'<img src="%s" alt="%s">',
							$src,
							$attachment->post_content
						);
					}

					echo $html;
				}
			}
			?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

		</div>
	</div>
</div>
