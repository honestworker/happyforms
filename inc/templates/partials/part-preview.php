<?php $visible = apply_filters( 'happyforms_message_part_visible', true, $part ); ?>
<div class="happyforms-form__part happyforms-part-preview" <?php if ( ! $visible ) : ?>style="display: none;"<?php endif; ?>>
	<label class="happyforms-part__label">
		<span class="label"><?php echo esc_html( $part['label'] ); ?></span>
	</label>
	<div class="happyforms-part__el-preview happyforms-part__el-preview__<?php echo $part['type']; ?>"><?php happyforms_the_part_preview_value( $part, $form ); ?></div>
	<div class="happyforms-hide">