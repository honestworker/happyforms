<script type="text/template" id="customize-happyforms-logic-group">
	<fieldset class="happyforms-conditional__group">
		<% if ( options && 'part' == options.type ) { %>
			<select class="widefat happyforms-conditional__action" data-show-prefix="<?php _e( 'Show', 'happyforms' ); ?>" data-hide-prefix="<?php _e( 'Hide', 'happyforms' ); ?>">
				<option value="" selected disabled><?php _e( 'This field will…', 'happyforms' ); ?></option>
				<option value="show"><?php _e( 'Show', 'happyforms' ); ?></option>
				<option value="hide"><?php _e( 'Hide', 'happyforms' ); ?></option>
			</select>
		<% } else if ( options && 'option' == options.type ) { %>
			<select class="widefat happyforms-conditional__action" data-show_option-prefix="<?php _e( 'Show', 'happyforms' ); ?>" data-hide_option-prefix="<?php _e( 'Hide', 'happyforms' ); ?>">
				<% if ( 'heading' == options.subtype ) { %>
				<option value="" selected disabled><?php _e( 'This heading will…', 'happyforms' ); ?></option>
				<% } else { %>
				<option value="" selected disabled><?php _e( 'This choice will…', 'happyforms' ); ?></option>
				<% } %>
				<option value="show_option"><?php _e( 'Show', 'happyforms' ); ?></option>
				<option value="hide_option"><?php _e( 'Hide', 'happyforms' ); ?></option>
			</select>
		<% } %>

		<div class="happyforms-conditional__static">
			<% if ( options && 'set' == options.type ) { %>
			<input type="text" placeholder="<%= options.thenText %>" data-then-value>
			<% } else if ( options && 'select' == options.type ) { %>
			<select data-then-value>
				<option value="" selected disabled><%= options.thenText %></option>
				<% _( options.options ).each( function( option, index ) { %>
					<% if ( 'undefined' !== typeof option.options ) { %>
						<% if ( '' !== option.title ) { %>
						<optgroup label="<%= option.title %>">
						<% } %>
							<% _( option.options ).each( function( suboption, subindex ) { %>
								<option value="<%= suboption.value %>"><%= suboption.label %></option>
							<% } ); %>
						<% if ( '' !== option.title ) { %>
						</optgroup>
						<% } %>
					<% } else { %>
					<option value="<%= index %>"><%= option %></option>
					<% } %>
				<% } ); %>
			</select>
			<% } else if ( options && 'template' == options.type ) { %>
				<% print( options.template( obj ) ); %>
			<% } %>

			<div class="happyforms-conditional__tools">
				<a href="#" class="happyforms-conditional__delete"><?php _e( 'Delete', 'happyforms' ); ?></a> | <a class="happyforms-conditional__add" href="#"><?php _e( 'Add condition', 'happyforms' ); ?></a>
			</div>
		</div>
	</fieldset>
</script>
