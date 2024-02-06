<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php $current_timestamp = current_time( 'timestamp', false ); ?>
		<?php if ( 'inside' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<?php happyforms_print_part_description( $part ); ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>
			<?php
			if ( 'date' === $part['date_type'] ) {
				require( 'frontend-date-field.php' );
			}

			if ('month' === $part['date_type'] ) {
				require( 'frontend-date-month.php' );
			}

			if ( 'month_year' === $part['date_type'] ) {
				require( 'frontend-date-monthyear.php' );
			}

			if ( 'time' !== $part['date_type'] && 'month' !== $part['date_type']  && 'month_year' !== $part['date_type'] && 'date' !== $part['date_type'] && 'datetime' !== $part['date_type'] ) {
				$year_value = ( happyforms_get_part_value( $part, $form, 'year' ) ) ? happyforms_get_part_value( $part, $form, 'year' ) : '';

				if ( '' === $year_value && 'current' === $part['default_datetime'] ) {
					$year_value = date( 'Y', $current_timestamp );
				}
			?>
				<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap happyforms-part-date-input--years">
					<div class="happyforms-custom-select">
						<div class="happyforms-part__select-wrap">
							<?php
							$placeholder_text = happyforms_get_datetime_placeholders( 'year' );
							$min_year = $part['min_year'];
							$max_year = $part['max_year'];
							$options = array();

							foreach( range( $min_year, $max_year ) as $year ) {
								$options[] = array(
									'label' => $year,
									'value' => $year,
									'is_default' => ( intval( $year_value ) === $year ),
								);
							}
							?>
							<select name="<?php happyforms_the_part_name( $part, $form ); ?>[year]" required data-serialize class="happyforms-select">
								<?php if ( ! empty( $placeholder_text ) ) : ?>
									<option disabled hidden <?php echo ( $year_value === '' ) ? ' selected' : ''; ?> value='' class="happyforms-placeholder-option"><?php echo $placeholder_text; ?></option>
								<?php endif; ?>
								<?php foreach ( $options as $index => $option ) : ?>
								<?php
									$option_value = isset( $option['value'] ) ? $option['value'] : $index;
									$submissions_left_label = isset( $option['submissions_left_label'] ) ? ' ' . $option['submissions_left_label'] : '';
									$selected = ( $year_value != '' && $year_value == $option_value ) ? ' selected' : '';
								?>
									<option value="<?php echo $option_value; ?>" <?php echo $selected; ?>><?php echo esc_attr( $option['label'] ); ?><?php echo $submissions_left_label; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php 
			
			if ( 'datetime' === $part['date_type'] ) {
				require( 'frontend-datetime-field.php' );
			}
			
			if ( 'time' === $part['date_type'] ) {
				require( 'frontend-time-field.php' );
			}

			?>
			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>
		</div>
		<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
	</div>
</div>
