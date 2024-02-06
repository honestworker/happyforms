<div class="happyforms-form <?php happyforms_the_form_class( $form ); ?>" id="<?php happyforms_the_form_container_id( $form ); ?>">
	<?php do_action( 'happyforms_form_before', $form ); ?>

	<form id="<?php happyforms_the_form_id( $form ); ?>" <?php happyforms_the_form_attributes( $form ); ?>>
		<?php do_action( 'happyforms_form_open', $form ); ?>

		<?php happyforms_message_notices( $form['ID'] ); ?>

		<?php do_action( 'happyforms_form_close', $form ); ?>
	</form>

	<?php do_action( 'happyforms_form_after', $form ); ?>
</div>
