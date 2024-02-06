<tr class="happyforms-message-edit-field-<?php echo $part['type']; ?>">
	<td class="first">
		<label for="<?php echo $part['id']; ?>"><?php echo esc_html( happyforms_get_part_label( $part ) ); ?></label>
	</td>
	<td>
		<div class="scrollbox">
			<?php happyforms_the_message_part_value( $value, $part, 'admin-edit' ); ?>
		</div>
	</td>
</tr>
