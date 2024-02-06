<script type="text/template" id="customize-happyforms-page-break-template">
	<div class="happyforms-widget happyforms-part-widget" data-part-type="<%= instance.type %>" data-part-id="<%= instance.id %>">
		<div class="happyforms-widget-top<%= ( ! instance.is_first ) ? ' happyforms-part-widget-top' : '' %>">
			<div class="happyforms-part-widget-title-action">
				<button type="button" class="happyforms-widget-action">
					<span class="toggle-indicator"></span>
				</button>
			</div>
			<div class="happyforms-widget-title">
				<h3><%= settings.label %><span class="in-widget-title"<% if (!instance.label) { %> style="display: none"<% } %>>: <span><%= (instance.label) ? instance.label : '' %></span></span></h3>
			</div>
		</div>
		<div class="happyforms-widget-content">
			<div class="happyforms-widget-form">
				<p>
					<label for="<%= instance.id %>_title"><?php _e( 'Name', 'happyforms' ); ?></label>
					<input type="text" id="<%= instance.id %>_title" class="widefat title" value="<%- instance.label %>" data-bind="label" />
				</p>
				<p class="happyforms-goto_next_page">
					<label for="<%= instance.id %>_continue_button_label"><?php _e( '’Continue’ label', 'happyforms' ); ?></label>
					<input type="text" id="<%= instance.id %>_continue_button_label" class="widefat title" value="<%- instance.continue_button_label %>" data-bind="continue_button_label" />
				</p>
				<p>
					<label for="<%= instance.id %>_css_class"><?php _e( 'Additional CSS class(es)', 'happyforms' ); ?></label>
					<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%- instance.css_class %>" data-bind="css_class" />
				</p>
				</p>
			</div>

			<div class="happyforms-widget-actions">
				<% if ( ! instance.is_first ) { %>
				<a href="#" class="happyforms-form-part-remove"><?php _e( 'Delete', 'happyforms' ); ?></a> |
				<a href="#" class="happyforms-form-part-duplicate"><?php _e( 'Duplicate', 'happyforms' ); ?></a>
				<% } %>
			</div>
		</div>
	</div>
</div>
</script>
