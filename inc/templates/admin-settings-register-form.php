<?php if ( $product->is_authorized() ) : ?>
	<p><?php _e( 'It looks like you\'ve already validated your purchase.', 'happyforms' ); ?></p>
<?php else: ?>
	<p><?php _e( 'Validate this site for support and updates using the same email address and password you use to sign into', 'happyforms' ); ?> <a href="<?php echo $product->url; ?>" target="_blank"><?php echo str_replace( 'https://', '', $product->url ); ?></a>.</p>
<?php endif; ?>

<?php if ( $notice ) : ?>
<div class="notice <?php echo $notice['type']; ?>">
	<p><?php echo $notice['message']; ?></p>
</div>
<?php endif; ?>

<form action="" method="post" class="hf-ajax-submit happyforms-updater-credentials">
<?php if ( $product->is_authorized() ) : ?>
	<input type="hidden" name="action" value="happyforms-updates-deauthorize" />
	<button type="submit" class="button button-primary">
		<?php _e( 'Reset', 'happyforms' ); ?>
	</button>
<?php else: ?>
	<?php wp_nonce_field( $page ); ?>
	<input type="hidden" name="action" value="happyforms-updates-authorize" />
	<label for="happyforms-settings-register-password"><?php _e( 'License key', 'happyforms' ); ?>:</label>
	<div class="hf-pwd">
		<input type="password" name="license_key" id="happyforms-settings-register-password" class="widefat" value="" required />
		<button type="button" class="button button-secondary hf-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php _e( 'Show license key', 'happyforms' ); ?>" data-label-show="<?php _e( 'Show license key', 'happyforms' ); ?>" data-label-hide="<?php _e( 'Hide license key', 'happyforms' ); ?>">
			<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
		</button>
	</div>
	<input type="submit" class="button button-primary button-block" value="<?php _e( 'Register Site', 'happyforms' ); ?>">
<?php endif; ?>
</form>
