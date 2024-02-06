<script type="text/template" id="customize-happyforms-logic-email-part-lists-then-value">
	<select data-then-value>
		<option value="" selected disabled><%= options.thenText %></option>
        <%
        var fields = happyForms.form.get( 'parts' ).where( { type: 'email' } );

        fields.forEach( function( field ) { %>
        <option value="<%= field.get( 'id' ) %>">"<%= ( '' !== field.get( 'label' ) ? field.get( 'label' ) : _happyFormsSettings.unlabeledFieldLabel ) %>" <?php _e( 'field', 'happyforms' ); ?></option>
        <% } ); %>

        <option value="all"><?php _e( 'All Email fields', 'happyforms' ); ?></option>
    </select>
</script>
