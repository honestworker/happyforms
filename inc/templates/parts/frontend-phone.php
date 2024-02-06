<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<div class="happyforms-part-phone-wrap">

				<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

				<?php if ( 1 === intval( $part['masked'] ) ) : ?>
					<div class="happyforms-input-country-code">
						<div class="happyforms-phone-international-wrap"><label class="happyforms-phone-international-labels"><?php echo $form['phone_label_country_code']; ?></label></div>
						<div class="happyforms-phone-country-group">
							<div class="happyforms-input-group with-prefix">
								<div class="happyforms-input-group__prefix"><span>+</span></div>
								<div class="happyforms-input">
									<input type="tel" name="<?php happyforms_the_part_name( $part, $form ); ?>[code]" class="happyforms-phone-code" value="<?php happyforms_the_part_value( $part, $form, 'code' ); ?>" <?php happyforms_the_part_attributes( $part, $form, 'code' ); ?> size="6"/>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="happyforms-input">
					<?php if ( 1 === intval( $part['masked'] ) ) : ?>
						<div class="happyforms-phone-international-wrap"><label class="happyforms-phone-international-labels"><?php echo $form['phone_label_number']; ?></label></div>
					<?php endif; ?>
					<input id="<?php happyforms_the_part_id( $part, $form ); ?>" type="tel" value="<?php happyforms_the_part_value( $part, $form, 'number' ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[number]" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php happyforms_the_part_attributes( $part, $form, 'number' ); ?> />
					<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
						<?php happyforms_the_part_label( $part, $form ); ?>
					<?php endif; ?>
				</div>

				<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
			</div>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
