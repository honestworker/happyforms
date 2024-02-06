<?php 
$current_timestamp = current_time( 'timestamp', false ); 
$formatted_date = date('Y-m-d', $current_timestamp);

$min_year = $part['min_year'];
$max_year = $part['max_year'];

$current_value = ( happyforms_get_part_value( $part, $form, 'date' ) ) ? happyforms_get_part_value( $part, $form, 'date' ) : '';

if ($current_value == "") {
	if ('current' === $part['default_datetime']) {
		$current_value = $formatted_date;
	}
}
?>

<div class="happyforms-part-date__date-input happyforms-part--date__input-wrap happyforms-part-date-input--date" id="<?php happyforms_the_part_id( $part, $form ); ?>-part">
	<div class="happyforms-custom-date" data-searchable="true">
		<div class="happyforms-part__date-wrap">
			<div class="happyforms-input">
				<input type="date" min="<?php echo $min_year;?>-01-01" max="<?php echo $max_year;?>-12-31" value="<?php echo $current_value;?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[date]" data-serialize required class="happyforms-date-field" />
			</div>
		</div>
	</div>
</div>
