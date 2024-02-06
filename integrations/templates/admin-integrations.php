<?php $screen = get_current_screen()->id; ?>
<div class="wrap" id="happyforms-integrations-screen">
	<h1><?php _e( 'Integrations', 'happyforms' ); ?></h1>

	<div id="happyforms-integration-toolbar" class="media-toolbar wp-filter">

		<div class="media-toolbar-secondary">
			<select id="happyforms-integration-filters" class="attachment-filters">
				<option value=""><?php _e( 'All integrations', 'happyforms' ); ?></option>
				<option value="antispam"><?php _e( 'Anti-spam and validation', 'happyforms' ); ?></option>
			</select>
		</div>

		<div class="media-toolbar-primary search-form">
			<label for="media-search-input" class="media-search-input-label"><?php _e( 'Search', 'happyforms' ); ?></label>
			<input type="search" id="happyforms-search-input" class="search">
		</div>
	</div>

	<div id="dashboard-widgets-wrap" class="happyforms-admin-widgets">
		<div id="happyforms-integrations-results-wrap">
			<p id="happyforms-no-integrations-found"><?php _e( 'No integrations found.', 'happyforms' ); ?></p>
			<div id="happyforms-integrations-results" class="metabox-holder">
				<div id="postbox-container-1" class="postbox-container"></div>
				<div id="postbox-container-2" class="postbox-container"></div>
				<div id="postbox-container-3" class="postbox-container"></div>
				<div id="postbox-container-4" class="postbox-container"></div>
			</div>
		</div>
		
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