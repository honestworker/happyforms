<script type="text/template" id="happyforms-customize-likert-scale-template">
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

	<?php do_action( 'happyforms_part_customize_scale_before_options' ); ?>

	<div class="min-max-wrapper">
		<p>
			<label for="<%= instance.id %>_max_value"><?php _e( 'Min number', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_max_value" min="-10" max="1" class="widefat title" value="<%= instance.min_value %>" data-bind="min_value" />
		</p>
		<p>
			<label for="<%= instance.id %>_max_value"><?php _e( 'Max number', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_max_value" min="0" max="10" class="widefat title" value="<%= instance.max_value %>" data-bind="max_value" />
		</p>
	</div>

	<?php do_action( 'happyforms_part_customize_scale_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_scale_before_advanced_options' ); ?>

	<p>
		<label for="<%= instance.id %>_min_label"><?php _e( 'Min number label', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_min_label" class="widefat title" value="<%= instance.min_label %>" data-bind="min_label" />
	</p>
	<p>
		<label for="<%= instance.id %>_max_label"><?php _e( 'Max number label', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_max_label" class="widefat title" value="<%= instance.max_label %>" data-bind="max_label" />
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php do_action( 'happyforms_part_customize_scale_after_advanced_options' ); ?>

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
