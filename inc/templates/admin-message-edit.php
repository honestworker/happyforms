<?php
global $message, $form;

if ( ! $form ) {
	return;
}
?>

<?php do_action( 'happyforms_message_edit_screen_before' ); ?>

<div class="inside">
	<div id="comment-link-box">
		<strong><?php _e( 'Referral:', 'happyforms' ); ?></strong>
		<span id="sample-permalink">
			<?php
			$link = happyforms_get_meta( $post->ID, 'client_referer', true );
			$text = urldecode( $link );
			echo sprintf( '<a href="%s">%s</a>', $link, $text );
			?>
		</span>
	</div>
</div>

<div id="namediv" class="stuffbox">
	<div class="inside">
		<h2 class="edit-comment-author"><?php _e( 'Submission', 'happyforms' ); ?></h2>
		<fieldset>
			<legend class="screen-reader-text"><?php _e( 'Submission', 'happyforms' ); ?></legend>
			<table class="form-table editcomment happyforms-edit-message-table" role="presentation">
				<tbody>
				<?php
				$conditional_controller = happyforms_get_conditional_controller();

				if ( $conditional_controller->has_conditions( $form ) ) {
					$form = $conditional_controller->get( $form, $message['request'] );
				}

				foreach ( $form['parts'] as $p => $part ) {
					$value = $message['parts'][$part['id']];
					do_action( 'happyforms_message_edit_field', $value, $part, $message, $form );
				}
				?>
				<?php if ( intval( $form['unique_id'] ) ): ?>
				<?php do_action( 'happyforms_message_edit_field', $message['tracking_id'], array(
					'type' => 'tracking_id',
					'label' => __( 'Tracking number', 'happyforms' ),
				), $message, $form ); ?>
				<?php endif; ?>
				</tbody>
			</table>
		</fieldset>
	</div>
</div>

<?php do_action( 'happyforms_message_edit_screen_after' ); ?>
