<?php
$current_timestamp = current_time( 'timestamp', false );
$month_value = ( happyforms_get_part_value( $part, $form, 'month' ) ) ? happyforms_get_part_value( $part, $form, 'month' ) : '';

if ( '' === $month_value && 'current' === $part['default_datetime'] ) {
	$month_value = date( 'n', $current_timestamp );
}
?>
<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap happyforms-part-date-input--months">
	<div class="happyforms-custom-select" data-searchable="true">
		<div class="happyforms-part__select-wrap">
			<?php
			$months = happyforms_get_months( $form );
			$placeholder_text = happyforms_get_datetime_placeholders( 'month' );
			$options = array();

			foreach ( $months as $i => $month ) {
				$options[] = array(
					'label' => $month,
					'value' => $i,
					'is_default' => ( intval( $month_value ) === $i )
				);
			}
			$is_searchable = count( $options ) > 5;
			$is_searchable = apply_filters( 'happyforms_is_dropdown_searchable', $is_searchable, $part, $form );
			?>
			<select name="<?php happyforms_the_part_name( $part, $form ); ?>[month]" data-serialize required class="happyforms-select">
				<?php if ( ! empty( $placeholder_text ) ) : ?>
					<option disabled hidden <?php echo ( $month_value === '' ) ? ' selected' : ''; ?> value='' class="happyforms-placeholder-option"><?php echo $placeholder_text; ?></option>
				<?php endif; ?>
				<?php foreach ( $options as $index => $option ) : ?>
				<?php
					$option_value = isset( $option['value'] ) ? $option['value'] : $index;
					$submissions_left_label = isset( $option['submissions_left_label'] ) ? ' ' . $option['submissions_left_label'] : '';
					$selected = ( $month_value != '' && $month_value == $option_value ) ? ' selected' : '';
				?>
					<option value="<?php echo $option_value; ?>" <?php echo $selected; ?>><?php echo esc_attr( $option['label'] ); ?><?php echo $submissions_left_label; ?></option>
				<?php endforeach; ?>
			</select>

		</div>
	</div>
</div>
