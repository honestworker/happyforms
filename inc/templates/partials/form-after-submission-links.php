<div class="happyforms-form__part happyforms-part happyforms-form-links">
	<?php if ( empty( $form['redirect_url'] ) ) : ?>
	<button type="button" class="happyforms-text-button happyforms-print-submission"><?php echo $form['print_submission_link'];?></button>
	<?php else: ?>
	<p class='happyforms-redirect-notice'><?php echo $form['submission_redirect_notice']; ?></p>
	<button type="button" class="happyforms-text-button happyforms-redirect-to-page" data-url="<?php echo $form['redirect_url'];?>"><?php echo $form['redirect_now_link']; ?></button>
	<?php endif; ?>
</div>