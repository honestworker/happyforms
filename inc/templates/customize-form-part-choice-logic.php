<?php
$only_part_text = __( 'Add another Radio, Checkbox or Dropdown field.', 'happyforms' );
$default_text = __( 'No Radio, Checkbox or Dropdown field added yet.', 'happyforms' );
?>
<div class="no-parts" data-only-part-text="<?php echo $only_part_text; ?>" data-default-text="<?php echo $default_text; ?>">
	<h3><?php _e( 'Logic', 'happyforms' ); ?></h3>

	<p class="description"><?php echo $default_text; ?></p>
</div>

<button class="button button-secondary happyforms-conditional__add-group"><?php _e( 'Add Logic Group', 'happyforms' ); ?></button>
