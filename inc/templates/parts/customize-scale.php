<script type="text/template" id="happyforms-customize-scale-template">
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
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_scale_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_scale_before_advanced_options' ); ?>

	<?php do_action( 'happyforms_part_customize_scale_before_options' ); ?>

	<div class="min-max-wrapper">
		<p>
			<label for="<%= instance.id %>_max_value"><?php _e( 'Min number', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_max_value" class="widefat title" value="<%= instance.min_value %>" data-bind="min_value" />
		</p>
		<p>
			<label for="<%= instance.id %>_max_value"><?php _e( 'Max number', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_max_value" class="widefat title" value="<%= instance.max_value %>" data-bind="max_value" />
		</p>
	</div>

	<p>
		<label for="<%= instance.id %>_multiple"><?php _e( 'Type', 'happyforms' ); ?></label>
		<span class="happyforms-buttongroup">
			<label for="<%= instance.id %>-multiple-value">
				<input type="radio" id="<%= instance.id %>-multiple-value" value="0" name="<%= instance.id %>-multiple" data-bind="multiple" <%= ( instance.multiple == '0' ) ? 'checked' : '' %> />
				<span><?php _e( 'Value', 'happyforms' ); ?></span>
			</label>
			<label for="<%= instance.id %>-multiple-range">
				<input type="radio" id="<%= instance.id %>-multiple-range" value="1" name="<%= instance.id %>-multiple" data-bind="multiple" <%= ( instance.multiple == '1' ) ? 'checked' : '' %> />
				<span><?php _e( 'Range', 'happyforms' ); ?></span>
			</label>
		</span>
	</p>

	<div class="happyforms-nested-settings" data-trigger="multiple" style="display: <%= ( instance.multiple ) ? 'block' : 'none' %>">
		<p>
			<label for="<%= instance.id %>_default_range_from"><?php _e( 'Default min number', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_default_range_from" class="widefat title" value="<%= instance.default_range_from %>" data-bind="default_range_from" />
		</p>
		<p>
			<label for="<%= instance.id %>_default_range_to"><?php _e( 'Default max number', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_default_range_to" class="widefat title" value="<%= instance.default_range_to %>" data-bind="default_range_to" />
		</p>
	</div>

	<p class="scale-single-options" style="display: <%= ( instance.multiple ) ? 'none' : 'block' %>">
		<label for="<%= instance.id %>_default_value"><?php _e( 'Default value', 'happyforms' ); ?></label>
		<input type="number" id="<%= instance.id %>_default_value" class="widefat title" value="<%= instance.default_value %>" data-bind="default_value" />
	</p>
	<p>
		<label for="<%= instance.id %>_prefix"><?php _e( 'Prefix', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_prefix" class="widefat title" value="<%- instance.prefix %>" data-bind="prefix" />
	</p>
	<p>
		<label for="<%= instance.id %>_suffix"><?php _e( 'Suffix', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_suffix" class="widefat title" value="<%- instance.suffix %>" data-bind="suffix" />
	</p>
	<p>
		<label for="<%= instance.id %>_step"><?php _e( 'Step Interval', 'happyforms' ); ?></label>
		<input type="number" id="<%= instance.id %>_step" class="widefat title" value="<%= instance.step %>" data-bind="step" />
	</p>

	<?php do_action( 'happyforms_part_customize_scale_after_advanced_options' ); ?>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php happyforms_customize_part_width_control(); ?>

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
