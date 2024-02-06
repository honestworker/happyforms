<?php
$current_timestamp = current_time( 'timestamp', false );

$day_value = ( happyforms_get_part_value( $part, $form, 'day' ) ) ? happyforms_get_part_value( $part, $form, 'day' ) : '';

if ( '' === $day_value && 'current' === $part['default_datetime'] ) {
	$day_value = date( 'j', $current_timestamp );
}
?>
<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap happyforms-part-date-input--days">
	<div class="happyforms-custom-select" data-searchable="true">
		<div class="happyforms-part__select-wrap">
			<?php
			$placeholder_text = happyforms_get_datetime_placeholders( 'day' );
			$options = array();
			$days = happyforms_get_days();

			foreach( $days as $i ) {
				$options[] = array(
					'label' => $i,
					'value' => $i,
					'is_default' => ( intval( $day_value ) === $i )
				);
			}
			$is_searchable = count( $options ) > 5;
			$is_searchable = apply_filters( 'happyforms_is_dropdown_searchable', $is_searchable, $part, $form );
			?>

			<select name="<?php happyforms_the_part_name( $part, $form ); ?>[day]" data-serialize required class="happyforms-select">
				<?php if ( ! empty( $placeholder_text ) ) : ?>
					<option disabled hidden <?php echo ( $day_value === '' ) ? ' selected' : ''; ?> value='' class="happyforms-placeholder-option"><?php echo $placeholder_text; ?></option>
				<?php endif; ?>
				<?php foreach ( $options as $index => $option ) : ?>
				<?php
					$option_value = isset( $option['value'] ) ? $option['value'] : $index;
					$submissions_left_label = isset( $option['submissions_left_label'] ) ? ' ' . $option['submissions_left_label'] : '';
					$selected = ( $day_value != '' && $day_value == $option_value ) ? ' selected' : '';
				?>
					<option value="<?php echo $option_value; ?>" <?php echo $selected; ?>><?php echo esc_attr( $option['label'] ); ?><?php echo $submissions_left_label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
</div>
