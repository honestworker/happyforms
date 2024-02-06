<% if ( instance.has_geolocation ) { %>
<p>
	<label>
		<input type="checkbox" name="has_geolocation" class="checkbox" value="1" <% if ( instance.has_geolocation ) { %>checked="checked"<% } %> data-bind="has_geolocation" /> <?php _e( 'Let respondent fetch address based on their location data', 'happyforms' ); ?>
	</label>
</p>
<% } %>