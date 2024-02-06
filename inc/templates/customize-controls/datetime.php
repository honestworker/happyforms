<div class="customize-control" id="customize-control-<?php echo $control['field']; ?>" data-attribute="<?php echo $control['field']; ?>">
	<div class="happyforms-datetime-controls-label">
		<label for="<?php echo $control['field']; ?>" class="customize-control-title"><?php echo $control['label']; ?></label>
		<a href="#clear" style="display: <%= ( <?php echo $control['field']; ?>.date || <?php echo $control['field']; ?>.time ) ? 'block' : 'none' %>"><?php _e( 'Clear', 'happyforms' ); ?></a>
	</div>
	<div class="happyforms-datetime-controls" data-pointer-target>
		<div class="happyforms-datetime-component date">
			<input type="text" data-component="date" id="happyforms-datetime-date-<?php echo $control['field']; ?>" value="<%= <?php echo $control['field']; ?>.date %>" placeholder="mm/dd/yyyy" />
		</div>
		<div class="happyforms-datetime-component time">
			<input type="text" data-component="time" id="happyforms-datetime-time-<?php echo $control['field']; ?>" value="<%= <?php echo $control['field']; ?>.time %>" placeholder="hh:mm" />
		</div>
		<div class="happyforms-datetime-component period">
			<select data-component="period" id="happyforms-datetime-period-<?php echo $control['field']; ?>">
				<option value="AM"<% if ( 'AM' === <?php echo $control['field']; ?>.period ) { %>selected<% } %>><?php esc_html_e( 'AM' ); ?></option>
				<option value="PM"<% if ( 'PM' === <?php echo $control['field']; ?>.period ) { %>selected<% } %>><?php esc_html_e( 'PM' ); ?></option>
			</select>
		</div>
	</div>
</div>
