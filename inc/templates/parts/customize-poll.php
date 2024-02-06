<script type="text/template" id="customize-happyforms-poll-template">
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

	<?php do_action( 'happyforms_part_customize_poll_before_options' ); ?>

	<div class="options">
		<label><?php _e( 'List', 'happyforms' ); ?>:</label>
		<ul class="option-list"></ul>
		<p class="no-options description customize-control-description"><?php _e( 'It doesn\'t look like your field has any choices yet. Want to add one? Click the "Add Choice" button to start.', 'happyforms' ); ?></p>
	</div>
	<div class="options-import">
		<h3><?php _e( 'Choices', 'happyforms' ); ?></h3>
		<textarea class="option-import-area" cols="30" rows="10" placeholder="<?php _e( 'Type or paste your choices here, adding each on a new line.' ); ?>"></textarea>
	</div>
	<p class="links mode-manual">
		<a href="#" class="button add-option"><?php _e( 'Add choice', 'happyforms' ); ?></a>
	</p>

	<?php do_action( 'happyforms_part_customize_poll_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_poll_before_advanced_options' ); ?>

	<% if ( instance.other_option ) { %>
		<p>
			<label>
				<input type="checkbox" class="checkbox" value="1" data-bind="other_option" checked /> <?php _e( 'Add \'other\' choice', 'happyforms' ); ?>
			</label>
		</p>
		<div class="happyforms-nested-settings" data-trigger="other_option" style="display: <%= ( instance.other_option ) ? 'block' : 'none' %>">
			<p>
				<label for="<%= instance.id %>_other_option_label"><?php _e( '\'Other\' label', 'happyforms' ); ?></label>
				<input type="text" id="<%= instance.id %>_other_option_label" maxlength="30" class="widefat title" value="<%- instance.other_option_label %>" data-bind="other_option_label" />
			</p>
			<p>
				<label for="<%= instance.id %>_other_option_placeholder"><?php _e( '\'Other\' placeholder', 'happyforms' ); ?></label>
				<input type="text" id="<%= instance.id %>_other_option_placeholder" maxlength="50" class="widefat title" value="<%- instance.other_option_placeholder %>" data-bind="other_option_placeholder" />
			</p>
		</div>
	<% } %>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.allow_multiple ) { %>checked="checked"<% } %> data-bind="allow_multiple" /> <?php _e( 'Allow multiple choices', 'happyforms' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.shuffle_options ) { %>checked="checked"<% } %> data-bind="shuffle_options" /> <?php _e( 'Shuffle order of choices', 'happyforms' ); ?>
		</label>
	</p>
	<p class="happyforms-poll-limit-choices-wrap" style="display: <%= ( instance.allow_multiple ) ? 'block' : 'none' %>">
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.limit_choices ) { %>checked="checked"<% } %> data-bind="limit_choices" /> <?php _e( 'Limit choices', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-nested-settings" data-trigger="limit_choices" style="display: <%= ( instance.limit_choices ) ? 'block' : 'none' %>">
		<p>
			<label for="<%= instance.id %>_limit_choices_min"><?php _e( 'Min choices', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_limit_choices_min" class="widefat title happyforms-poll-limit-min" min="1" value="<%= instance.limit_choices_min %>" data-trigger="limit_choices_min" data-bind="limit_choices_min" />
		</p>
		<p>
			<label for="<%= instance.id %>_limit_choices_max"><?php _e( 'Max choices', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_limit_choices_max" class="widefat title happyforms-poll-limit-max" min="1" value="<%= instance.limit_choices_max %>" data-trigger="limit_choices_max" data-bind="limit_choices_max" />
		</p>
	</div>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.show_results_before_voting ) { %>checked="checked"<% } %> data-bind="show_results_before_voting" /> <?php _e( 'Allow previewing results', 'happyforms' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php happyforms_customize_part_width_control(); ?>

	<?php do_action( 'happyforms_part_customize_poll_after_advanced_options' ); ?>

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
<script type="text/template" id="customize-happyforms-poll-item-template">
	<li data-option-id="<%= id %>" class="happyforms-choice-item-widget">
		<div class="happyforms-part-item-handle">
			<div class="happyforms-part-item-advanced-option">
				<button type="button" class="happyforms-advanced-option-action">
					<span class="toggle-indicator"></span>
				</button>
			</div>
			<div class="happyforms-item-choice-widget-title">
				<h3><?php _e( 'Choice', 'happyforms' ); ?><span class="choice-in-widget-title">: <span><%= label %></span></span></h3>
			</div>
		</div>
		<div class="happyforms-part-item-body">
			<div class="happyforms-part-item-advanced">
				<p>
					<label>
						<?php _e( 'Label', 'happyforms' ); ?>:
						<input type="text" class="widefat" name="label" value="<%- label %>" data-option-attribute="label">
					</label>
				</p>
				<div class="option-actions">
					<a href="#" class="happyforms-delete-item"><?php _e( 'Delete', 'happyforms' ); ?></a> |
					<a href="#" class="happyforms-duplicate-item"><?php _e( 'Duplicate', 'happyforms' ); ?></a>
				</div>
			</div>
		</div>
	</li>
</script>
