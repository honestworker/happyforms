<% if ( instance.has_autocomplete ) { %>
<p class="happyforms-customize-part-google_autocomplete">
	<label>
		<input type="checkbox" name="has_autocomplete" class="checkbox" value="1" <% if ( instance.has_autocomplete ) { %>checked="checked"<% } %> data-bind="has_autocomplete" /> <?php _e( 'Suggest address based on what the respondent types', 'happyforms' ); ?>
	</label>
</p>
<% } %>