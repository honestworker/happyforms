<script type="text/template" id="customize-happyforms-attachment-template">
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
	<p class="happyforms-placeholder-option">
		<label for="<%= instance.id %>_placeholder"><?php _e( 'Placeholder', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_placeholder" class="widefat title" value="<%- instance.placeholder %>" data-bind="placeholder" />
	</p>
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_attachment_before_options' ); ?>

	<?php do_action( 'happyforms_part_customize_attachment_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_attachment_before_advanced_options' ); ?>

	<p>
		<label for="<%= instance.id %>_min_file_count"><?php _e( 'Min number of files', 'happyforms' ); ?></label>
		<input type="number" min="0" id="<%= instance.id %>_min_file_count" class="widefat title" value="<%= ( 0 < instance.min_file_count ) ? instance.min_file_count : '' %>" data-bind="min_file_count" />
	</p>

	<p>
		<label for="<%= instance.id %>_max_file_count"><?php _e( 'Max number of files', 'happyforms' ); ?></label>
		<input type="number" min="0" id="<%= instance.id %>_max_file_count" class="widefat title" value="<%= ( 0 < instance.max_file_count ) ? instance.max_file_count : '' %>" data-bind="max_file_count" />
	</p>

	<p>
		<label for="<%= instance.id %>_max_file_size"><?php _e( 'Max size per file (MB)', 'happyforms' ); ?></label>
		<input type="number" min="1" max="<?php echo happyforms_get_max_upload_size(); ?>" id="<%= instance.id %>_max_file_size" class="widefat title" value="<%= instance.max_file_size %>" data-bind="max_file_size" />
	</p>

	<p>
		<label for="<%= instance.id %>_allowed_file_extensions"><?php _e( 'Allowed file types', 'happyforms' ); ?></label>
		<input type="hidden" id="<%= instance.id %>_allowed_file_extensions" data-bind="allowed_file_extensions" value="<%= instance.allowed_file_extensions %>">
		<?php $file_extensions = happyforms_allowed_file_extensions(); ?>
		<% var allowed_types = instance.allowed_file_extensions.replace(/\s/g, '').split( ',' ); %>
		<span class="happyforms-file-types-wrap">
		<?php foreach ( $file_extensions as $extension ): ?>
			<label class="happyforms-file-type-checkbox-label" for="<%= instance.id %>_allowed_file_extensions_<?php echo $extension; ?>">
				<input type="checkbox" class="happyforms-file-type-checkbox" value="<?php echo $extension; ?>" <% if ( allowed_types.indexOf( '<?php echo $extension; ?>' ) >= 0 ) { %>checked="checked"<% } %> id="<%= instance.id %>_allowed_file_extensions_<?php echo $extension; ?>" />
				<?php echo $extension; ?>
			</label>
		<?php endforeach; ?>
		</span>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php happyforms_customize_part_width_control(); ?>

	<?php do_action( 'happyforms_part_customize_attachment_after_advanced_options' ); ?>

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
