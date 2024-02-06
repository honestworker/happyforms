<div class="happyforms-form happyforms-form--hide-progress-bar <?php happyforms_the_form_class( $form ); ?>" id="<?php happyforms_the_form_container_id( $form ); ?>">
	<?php do_action( 'happyforms_form_before', $form ); ?>
	
	<form class="happyforms-form--password-protect" action="<?php happyforms_form_action( $form['ID'] ); ?>" id="<?php happyforms_the_form_id( $form ); ?>" method="post" <?php happyforms_the_form_attributes( $form ); ?>>
		<?php do_action( 'happyforms_form_open', $form ); ?>

		<?php happyforms_action_field(); ?>
		<?php happyforms_form_field( $form['ID'] ); ?>
		<?php happyforms_step_field( $form ); ?>

		<div class="happyforms-flex">
			<?php happyforms_message_notices( $form['ID'] ); ?>
			<?php happyforms_honeypot( $form ); ?>
			<div class="happyforms-form__part happyforms-part happyforms-part--form-password happyforms-part--width-auto" data-happyforms-type="password">
				<div class="happyforms-part-wrap">
					<div class="happyforms-part__el">
						<input type="password" name="happyforms_password" id="happyforms-<?php echo esc_attr( $form['ID'] ); ?>_password" placeholder="<?php echo $form['password_input_placeholder']; ?>">
					</div>
				</div>
			</div>
			<?php happyforms_password_submit( $form ); ?>
		</div>

		<?php do_action( 'happyforms_form_close', $form ); ?>
	</form>

	<?php do_action( 'happyforms_form_after', $form ); ?>
</div>