<li class="happyforms-attachment-item" data-attachment-id="<?php echo $attachment_id; ?>">
	<div class="happyforms-attachment-item__col happyforms-attachment-item__col--main">
		<span class="happyforms-attachment-item__name"><?php echo $attachment_name; ?></span>
		<span class="happyforms-attachment-item__size"><?php echo size_format( $attachment_size ); ?></span>
	</div>
	<div class="happyforms-attachment-item__col">
		<button type="button" class="happyforms-text-button happyforms-attachment-link happyforms-delete-attachment"><?php echo $form['file_upload_delete_label']; ?></button>
	</div>
	<div>
		<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[<?php echo $i; ?>][id]" value="<?php echo $attachment_id; ?>" class="happyforms-attachment-input__id" />
		<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[<?php echo $i; ?>][name]" value="<?php echo $attachment_name; ?>" class="happyforms-attachment-input__name" />
		<input type="hidden" name="<?php happyforms_the_part_name( $part, $form ); ?>[<?php echo $i; ?>][size]" value="<?php echo $attachment_size; ?>" class="happyforms-attachment-input__size" />
	</div>
</li>