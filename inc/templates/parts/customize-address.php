<script type="text/template" id="happyforms-customize-address-template">
	<?php include( happyforms_get_core_folder() . '/templates/customize-form-part-header.php' ); ?>
	<div class="label-field-group">
		<label for="<%= instance.id %>_title"><?php _e( 'Label', 'happyforms' ); ?></label>
		<div class="label-group">
			<input type="text" id="<%= instance.id %>_title" class="widefat title" value="<%- instance.label %>" data-bind="label" />
			<div class="happyforms-buttongroup">
				<label for="<%= instance.id %>-label_placement-show">
					<input type="radio" id="<%= instance.id %>-label_placement-show" value="show" name="<%= instance.id %>-label_placement" data-bind="label_placement" <%= ( instance.label_placement == 'show' ) ? 'checked' : '' %> />
					<span><?php _e( 'Show', 'happyforms' ); ?></span>
				</label>
				<label for="<%= instance.id %>-label_placement-hidden">
					<input type="radio" id="<%= instance.id %>-label_placement-hidden" value="hidden" name="<%= instance.id %>-label_placement" data-bind="label_placement" <%= ( instance.label_placement == 'hidden' ) ? 'checked' : '' %> />
					<span><?php _e( 'Hide', 'happyforms' ); ?></span>
				</label>
 			</div>
		</div>
	</div>
	<p class="happyforms-placeholder-option" style="display: <%= ( 'as_placeholder' !== instance.label_placement ) ? 'block' : 'none' %>">
		<label for="<%= instance.id %>_placeholder"><?php _e( 'Placeholder', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_placeholder" class="widefat title" value="<%- instance.placeholder %>" data-bind="placeholder" />
	</p>
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_address_before_options' ); ?>
	<p>
		<label for="<%= instance.id %>_mode"><?php _e( 'Mode', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_mode" name="mode" data-bind="mode" class="widefat">
			<option value="simple"<%= (instance.mode == 'simple') ? ' selected' : '' %>><?php _e( 'Full', 'happyforms' ); ?></option>
			<option value="country-city"<%= (instance.mode == 'country-city') ? ' selected' : '' %>><?php _e( 'Country and city', 'happyforms' ); ?></option>
			<option value="country"<%= (instance.mode == 'country') ? ' selected' : '' %>><?php _e( 'Country only', 'happyforms' ); ?></option>
		</select>
	</p>

	<?php do_action( 'happyforms_part_customize_address_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_address_before_advanced_options' ); ?>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php happyforms_customize_part_width_control(); ?>

	<?php do_action( 'happyforms_part_customize_address_after_advanced_options' ); ?>

	<p>
		<label for="<%= instance.id %>_css_class"><?php _e( 'Additional CSS class(es)', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%- instance.css_class %>" data-bind="css_class" />
	</p>

	<div class="happyforms-part-logic-wrap">
		<div class="happyforms-logic-view">
			<?php happyforms_customize_part_logic(); ?>
		</div>
	</div>

	<?php happyforms_customize_part_footer(); ?>
</script>
