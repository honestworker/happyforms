<?php 
$current_timestamp = current_time( 'timestamp', false ); 
$formatted_time = date('H:i', $current_timestamp);

$min_hour = str_pad($part['min_hour'], 2, '0', STR_PAD_LEFT) . ":00";
$max_hour = str_pad($part['max_hour'], 2, '0', STR_PAD_LEFT) . ":59";

$current_value = ( happyforms_get_part_value( $part, $form, 'time' ) ) ? happyforms_get_part_value( $part, $form, 'time' ) : '';

if ($current_value === ""){
	if ('current' === $part['default_datetime']) {
		$current_value = $formatted_time;
	}
}
?>

<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap happyforms-part-date-input--time_field" id="<?php happyforms_the_part_id( $part, $form ); ?>-part">
	<div class="happyforms-custom-date" data-searchable="true">
		<div class="happyforms-part__date-wrap">
			<div class="happyforms-input">
				<input type="time" value="<?php echo $current_value;?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[time]" data-serialize required class="happyforms-time-field" min="<?php echo $min_hour;?>" max="<?php echo $max_hour;?>" />
			</div>
		</div>
	</div>
</div>
