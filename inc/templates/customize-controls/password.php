<?php
$form = happyforms_customize_get_current_form();
$password_hash = happyforms_get_meta( $form['ID'], $control['field'], true );
?>
<div class="customize-control" id="customize-control-<?php echo $control['field']; ?>">
	<?php if ( ! empty( $password_hash ) ) : ?>
		<a href="#" class="happyforms-reset-password"><?php _e( 'Reset password', 'happyforms' ); ?></a>
	<?php endif; ?>
	<div class="customize-password-field-wrap">
		<label for="<?php echo $control['field']; ?>" class="customize-control-title">
			<?php echo ( ! empty( $password_hash ) ) ? $control['label_filled'] : $control['label']; ?>
		</label>
		<input type="password" id="<?php echo $control['field']; ?>" data-attribute="<?php echo $control['field']; ?>" data-pointer-target />
	</div>
</div>
