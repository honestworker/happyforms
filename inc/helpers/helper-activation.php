<?php

if ( ! function_exists( 'happyforms_schedule_remove_unassigned_attachments' ) ) :

function happyforms_schedule_remove_unassigned_attachments() {
	require_once( happyforms_get_include_folder() . '/classes/class-attachment-controller.php' );

	$controller = happyforms_get_attachment_controller();

	if ( ! wp_next_scheduled( $controller->schedule_remove_unassigned ) ) {
		wp_schedule_event( time(), 'hourly', $controller->schedule_remove_unassigned );
	}
}

endif;

add_action( 'happyforms_activate', 'happyforms_schedule_remove_unassigned_attachments' );

if ( ! function_exists( 'happyforms_reset_license' ) ) :

function happyforms_reset_license() {
	delete_option( Happyforms()->updater->product->option );
}

endif;

add_action( 'happyforms_deactivate', 'happyforms_reset_license' );