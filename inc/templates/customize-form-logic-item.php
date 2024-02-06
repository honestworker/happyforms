<script type="text/template" id="customize-happyforms-logic-item">
	<?php
	$condition_constants = HappyForms_Condition::get_constants();
	$and = $condition_constants['AND'];
	?>
	<div class="happyforms-condition">
		<select class="widefat happyforms-conditional__operator" style="display: none" disabled>
			<option value="<?php echo $and; ?>"><?php _e( 'And', 'happyforms' ); ?></option>
		</select>
		<select class="widefat happyforms-conditional__part"  data-prefix="<?php _e( 'If', 'happyforms' ); ?> " disabled>
			<option value="" selected disabled><?php _e( 'If field is…', 'happyforms' ); ?></option>
		</select>
		<select class="widefat happyforms-conditional__option" data-prefix="<?php _e( 'Is', 'happyforms' ); ?> " disabled>
			<option value="" selected disabled><?php _e( 'And choice is…', 'happyforms' ); ?></option>
		</select>
	</div>
</script>
<script type="text/template" id="customize-happyforms-logic-part-dropdown-template">
	<option value="<%= data.id %>" data-label="<%= ( '' !== data.label ? data.label : _happyFormsSettings.unlabeledFieldLabel ) %>"><%= ( '' !== data.label ? data.label : _happyFormsSettings.unlabeledFieldLabel ) %></option>
</script>
<script type="text/template" id="customize-happyforms-logic-value-dropdown-template">
	<option value="<%= data.index %>" data-label="<%= data.option.label %>" data-option-id="<%= data.option.id %>"><%= data.option.label %></option>
</script>
