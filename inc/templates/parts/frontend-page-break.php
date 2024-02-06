<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( happyforms_is_preview() ) : ?>
			<div class="happyforms-page-break">
				<?php	echo happyforms_the_part_label( $part, $form ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
