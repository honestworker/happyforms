<script type="text/template" id="happyforms-customize-phone-template">
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
	<p class="happyforms-default-value-option">
		<label for="<%= instance.id %>_default_value"><?php _e( 'Prefill', 'happyforms' ); ?></label>
		<input type="number" id="<%= instance.id %>_default_value" class="widefat title default_value" value="<%- instance.default_value %>" data-bind="default_value" />
	</p>
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_phone_before_options' ); ?>

	<?php do_action( 'happyforms_part_customize_phone_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_phone_before_advanced_options' ); ?>

	<p>
		<label for="<%= instance.id %>_phone-format"><?php _e( 'Phone number format', 'happyforms' ); ?></label>
		<span class="happyforms-buttongroup">
			<label for="<%= instance.id %>-local">
				<input type="radio" id="<%= instance.id %>-local" value="0" name="<%= instance.id %>-masked" data-bind="masked" <%= ( instance.masked == 0 ) ? 'checked' : '' %> />
				<span><?php _e( 'Local', 'happyforms' ); ?></span>
			</label>
			<label for="<%= instance.id %>-international">
				<input type="radio" id="<%= instance.id %>-international" value="1" name="<%= instance.id %>-masked" data-bind="masked" <%= ( instance.masked == 1 ) ? 'checked' : '' %> />
				<span><?php _e( 'International', 'happyforms' ); ?></span>
			</label>
		</span>
		</p>
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

	<?php do_action( 'happyforms_part_customize_phone_after_advanced_options' ); ?>

	<div class="happyforms-part-logic-wrap">
		<div class="happyforms-logic-view">
			<?php happyforms_customize_part_logic(); ?>
		</div>
	</div>

	<?php happyforms_customize_part_footer(); ?>
</script>
