<?php foreach( $form['parts'] as $part ) : ?>

	<?php if ( happyforms_email_is_part_visible( $part, $form, $message ) ) : ?>

	<?php $label = happyforms_get_email_part_label( $message, $part, $form ); ?>

	<?php if ( '' !== $label ) : ?>
		<b><?php echo $label; ?></b><br>
	<?php endif; ?>

	<?php echo happyforms_get_email_part_value( $message, $part, $form, 'admin-email' ); ?>
	<br><br>

	<?php endif; ?>

<?php endforeach; ?>

<b><?php _e( 'IPv4/IPv6', 'happyforms' ); ?></b><br>
<?php echo happyforms_get_meta( $message['ID'], 'client_ip', true ); ?>