<?php
$form = happyforms_customize_get_current_form();
$attachment_id = happyforms_get_meta( $form['ID'], $control['field'], true );
$attachment_url = wp_get_attachment_url( $attachment_id );
?>
<div class="customize-control happyforms-media-upload" id="customize-control-<?php echo $control['field']; ?>" data-overlay-title="<?php echo $control['settings']['overlay_title']; ?>" data-overlay-button-text="<?php echo $control['settings']['overlay_button']; ?>">
	<label for="<?php echo $control['field']; ?>" class="customize-control-title"><?php echo $control['label']; ?></label>

	<div class="attachment-media-view">
		<img id="<?php echo $control['field']; ?>-image-preview" src="<?php echo $attachment_url; ?>" data-preview-target="<?php echo $control['field']; ?>" class="happyforms-upload-preview<?php echo ( ! empty( $attachment_url ) ) ? ' show' : ''; ?>">

		<button id="<?php echo $control['field']; ?>_button" class="button upload-button button-add-media happyforms-upload-button <%= ( 0 == <?php echo $control['field']; ?> ) ? ' show' : '' %>" data-upload-target="<?php echo $control['field']; ?>" data-pointer-target><?php _e( 'Select logo', 'happyforms' ); ?></button>
		<input type="hidden" data-attribute="<?php echo $control['field']; ?>" value="<%= <?php echo $control['field']; ?> %>">

		<div class="actions happyforms-upload-actions">
			<button type="button" class="button happyforms-remove-button remove-button<%= ( 0 != <?php echo $control['field']; ?> ) ? ' show' : '' %>"><?php _e( 'Remove', 'happyforms' ); ?></button>
			<button type="button" class="button happyforms-change-button upload-button<%= ( 0 != <?php echo $control['field']; ?> ) ? ' show' : '' %>"><?php _e( 'Change logo', 'happyforms' ); ?></button>
		</div>
	</div>
</div>
