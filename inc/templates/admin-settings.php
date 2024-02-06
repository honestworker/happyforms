<?php $screen = get_current_screen()->id; ?>
<div class="wrap" id="happyforms-settings-screen">
	<h1><?php _e( 'Settings', 'happyforms' ); ?></h1>

	<div id="dashboard-widgets-wrap" class="happyforms-admin-widgets">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container">
			<?php do_meta_boxes( $screen, 'normal', '' ); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container">
			<?php do_meta_boxes( $screen, 'side', '' ); ?>
			</div>
			<div id="postbox-container-3" class="postbox-container">
			<?php do_meta_boxes( $screen, 'column3', '' ); ?>
			</div>
			<div id="postbox-container-4" class="postbox-container">
			<?php do_meta_boxes( $screen, 'column4', '' ); ?>
			</div>
		</div>
		<?php
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		?>
	</div>
</div>
