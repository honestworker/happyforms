<script type="text/template" id="happyforms-customize-placeholder-template">
	<?php include( happyforms_get_core_folder() . '/templates/customize-form-part-header.php' ); ?>

	<% if ( instance.label ) { %>
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
	<% } %>

	<% if ( instance.description ) { %>
		<p>
			<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
			<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
		</p>
	<% } %>

	<?php do_action( 'happyforms_part_customize_placeholder_before_options' ); ?>
	<p>
		<label for="<%= instance.id %>_placeholder_text"><?php _e( 'Text', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_placeholder_text" class="wp-editor-area" name="placeholder_text" data-bind="placeholder_text"><%= instance.placeholder_text %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_placeholder_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_placeholder_before_advanced_options' ); ?>

	<?php happyforms_customize_part_width_control(); ?>

	<?php do_action( 'happyforms_part_customize_placeholder_after_advanced_options' ); ?>

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
