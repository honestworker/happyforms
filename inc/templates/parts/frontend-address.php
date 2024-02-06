<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( 'simple' === $part['mode'] ) : ?>
				<div class="happyforms-part-el-wrap">
					<?php if ( 1 == $part['has_autocomplete'] ) : ?>
					<div class="happyforms-part__dummy-input">
						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[full]" value="<?php happyforms_the_part_value( $part, $form, 'full' ); ?>" data-serialize />
					<?php endif; ?>
						<?php if ( 1 == $part['has_geolocation'] ) : ?>
							<div class="happyforms-input-group with-suffix">
						<?php endif; ?>
						<?php if ( 1 == $part['has_autocomplete'] ) : ?>
						<div class="happyforms-input">
							<input id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_id( $part, $form ); ?>_full_dummy_<?php echo time(); ?>" class="happyforms-part--address__autocomplete address-full" type="text" value="<?php happyforms_the_part_value( $part, $form, 'full' ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" autocomplete="none" <?php happyforms_the_part_attributes( $part, $form, 'full' ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />
						</div>
						<?php else: ?>
						<div class="happyforms-input">
							<input id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[full]" class="address-full" type="text" value="<?php happyforms_the_part_value( $part, $form, 'full' ); ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php happyforms_the_part_attributes( $part, $form, 'full' ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />
						</div>
						<?php endif; ?>


						<?php if ( 1 == $part['has_geolocation'] ) : ?>
							<div class="happyforms-input-group__suffix happyforms-input-group__suffix--button">
								<?php happyforms_geolocation_button( $part ); ?>
							</div>

						</div><!-- /.happyforms-input-group -->
						<?php endif; ?>

						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>

						<?php happyforms_select( array(), $part, $form ); ?>
					<?php if ( 1 == $part['has_autocomplete'] ) : ?>
					</div>
					<?php endif; ?>
				</div>
			<?php elseif ( 'country' === $part['mode'] ) : ?>
				<div class="happyforms-part-el-wrap">
					<div class="happyforms-part__dummy-input">
						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[country]" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" data-serialize />

						<?php if ( 1 == $part['has_geolocation'] ) : ?>
							<div class="happyforms-input-group with-suffix">
						<?php endif; ?>

						<div class="happyforms-input">
							<input id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_id( $part, $form ); ?>_country_dummy_<?php echo time(); ?>" class="happyforms-part--address__autocomplete address-country" type="text" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" placeholder="<?php _e( 'Country', 'happyforms' ); ?>" autocomplete="off" <?php happyforms_the_part_attributes( $part, $form, 'country' ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />
						</div>

						<?php if ( 1 == $part['has_geolocation'] ) : ?>
							<div class="happyforms-input-group__suffix happyforms-input-group__suffix--button">
								<?php happyforms_geolocation_button( $part ); ?>
							</div>

						</div><!-- /.happyforms-input-group -->
						<?php endif; ?>

						<?php happyforms_select( array(), $part, $form ); ?>

						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="happyforms-part-el-wrap">
					<div class="happyforms-part__dummy-input">
						<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[country]" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" data-serialize />

						<?php if ( 1 == $part['has_geolocation'] ) : ?>
							<div class="happyforms-input-group with-suffix">
						<?php endif; ?>

						<div class="happyforms-input">
							<input id ="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>_country_dummy_<?php echo time(); ?>" class="happyforms-part--address__autocomplete address-country" type="text" value="<?php happyforms_the_part_value( $part, $form, 'country' ); ?>" placeholder="<?php _e( 'Country', 'happyforms' ); ?>" autocomplete="off" <?php happyforms_the_part_attributes( $part, $form, 'country' ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />
						</div>

						<?php if ( 1 == $part['has_geolocation'] ) : ?>
							<div class="happyforms-input-group__suffix happyforms-input-group__suffix--button">
								<?php happyforms_geolocation_button( $part ); ?>
							</div>

						</div><!-- /.happyforms-input-group -->
						<?php endif; ?>

						<?php happyforms_select( array(), $part, $form ); ?>

						<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
							<?php happyforms_the_part_label( $part, $form ); ?>
						<?php endif; ?>
					</div>

					<input name="<?php happyforms_the_part_name( $part, $form ); ?>[city]" class="address-city" type="text" value="<?php happyforms_the_part_value( $part, $form, 'city' ); ?>" placeholder="<?php _e( 'City', 'happyforms' ); ?>" <?php happyforms_the_part_attributes( $part, $form, 'city' ); ?> <?php happyforms_parts_autocorrect_attribute( $part ); ?> />
				</div>
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
