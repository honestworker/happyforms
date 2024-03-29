<?php

if ( ! function_exists( 'happyforms_previous_message_edit_link' ) ):

function happyforms_previous_message_edit_link( $post_id, $text ) {
	global $happyforms_message_nav;

	if ( array_search( $post_id, $happyforms_message_nav ) > 0 ) {
		edit_post_link( $text, '', '', $happyforms_message_nav[0] );
	}
}

endif;

if ( ! function_exists( 'happyforms_next_message_edit_link' ) ):

function happyforms_next_message_edit_link( $post_id, $text ) {
	global $happyforms_message_nav;

	if ( array_search( $post_id, $happyforms_message_nav )
		=== count( $happyforms_message_nav ) - 2 ) {
		edit_post_link( $text, '', '', $happyforms_message_nav[2] );
	}
}

endif;

if ( ! function_exists( 'happyforms_get_pdf_part_value' ) ):

function happyforms_get_pdf_part_value( $value, $part = array(), $form = array() ) {
	$value = happyforms_get_message_part_value( $value, $part, 'pdf' );
	$value = apply_filters( 'happyforms_get_pdf_part_value', $value, $part, $form );

	// Interpolate line breaks
	$value = str_replace( '\n', "\n", $value );
	// Replace html line breaks with plain line breaks
	$value = preg_replace( '/<br(\s+)?\/?>/i', "\n", $value );
	// Strip all tags
	$value = wp_strip_all_tags( $value, false );
	// Strip tabs and redundant whitespace
	$value = preg_replace( "/[ \t]+/", " ", $value );
	// Decode HTML entities
	$value = htmlspecialchars_decode( $value, ENT_QUOTES );
	// Unescape
	$value = wp_unslash( $value );

	return $value;
}

endif;

if ( ! function_exists( 'happyforms_get_response_mark_link' ) ):

function happyforms_get_response_mark_link( $response_id ) {
	$url = admin_url( 'admin-ajax.php' );
	$action = happyforms_get_message_controller()->mark_action;
	$url = wp_nonce_url( $url, "{$action}-{$response_id}" );
	$url = add_query_arg( 'action', $action, $url );
	$url = add_query_arg( 'post', $response_id, $url );

	return $url;
}

endif;

if ( ! function_exists( 'happyforms_get_form_status_link' ) ):

	function happyforms_get_form_status_link( $form_ids = array() ) {
		$url = admin_url( 'edit.php' );
		$form_ids = join( ',', $form_ids );
		$action = happyforms_get_form_status()->status_action;
		$url = wp_nonce_url( $url, "{$action}-{$form_ids}" );
		$url = add_query_arg( 'action', $action, $url );
		$url = add_query_arg( 'form_ids', $form_ids, $url );

		return $url;
	}

endif;

if ( ! function_exists( 'happyforms_get_activity_status_link' ) ):

	function happyforms_get_activity_status_link( $activity_ids = array() ) {
		$url = admin_url( 'edit.php' );
		$activity_ids = join( ',', $activity_ids );
		$action = happyforms_get_message_controller()->mark_action;
		$url = wp_nonce_url( $url, "{$action}-{$activity_ids}" );
		$url = add_query_arg( 'action', $action, $url );
		$url = add_query_arg( 'activity_ids', $activity_ids, $url );

		return $url;
	}

endif;


if ( ! function_exists( 'happyforms_unread_messages_badge' ) ):
/**
 * Outputs the unread messages badge, if there are any.
 *
 * @since 1.1
 *
 * @return void
 */
function happyforms_unread_messages_badge() {
	$unread = happyforms_submission_counter()->get_total_unread();

	$badge = sprintf(
		' <span class="happyforms-pending-count awaiting-mod count-1" %1$s><span class="pending-count">%2$s</span></span>',
		$unread > 0 ? '' : 'style="display: none;"', $unread
	);

	return $badge;
}

endif;

if ( ! function_exists( 'happyforms_unregistered_badge' ) ):
/**
 * Outputs the unregistered badge, if Happyforms isn't registered.
 *
 * @return void
 */
function happyforms_unregistered_badge() {
	$badge = '';

	if ( ! HappyForms()->is_registered() ) {
		$badge = ' <span class="happyforms-unregistered-badge awaiting-mod count-1"><span class="pending-count">1</span></span>';
	}

	return $badge;
}

endif;

if ( ! function_exists( 'happyforms_read_unread_badge' ) ):

function happyforms_read_unread_badge( $form_id ) {
	$messages_url = admin_url( "/edit.php?post_type=happyforms-message&form_id={$form_id}" );
	$badge_html = '—';
	$submission_counter = happyforms_submission_counter();
	$submissions_total = happyforms_get_meta( $form_id, $submission_counter->key_count_submission_total, true );
	$submissions_unread = happyforms_get_meta( $form_id, $submission_counter->key_count_submission_unread, true );
	$submissions_read = happyforms_get_meta( $form_id, $submission_counter->key_count_submission_read, true );

	if ( '' != $submissions_total && 0 < $submissions_total ) {
		$badge_html = '<div class="happyforms-responses-count-wrapper" data-form-id="' . $form_id . '">';
		$bubble_html = sprintf(
			'<span class="responses-count-read">%d</span>
			<span class="screen-reader-text">%d %s</span>',
			$submissions_read,
			$submissions_read,
			__( 'read submissions', 'happyforms' )
		);
		$bubble = sprintf(
			'<span class="happyforms-responses-count happyforms-responses-count-read">%s</span>',
			$bubble_html
		);

		if ( current_user_can( 'happyforms_manage_activity' ) ) {
			$bubble = sprintf(
				'<a href="%s" class="happyforms-responses-count happyforms-responses-count-read">%s</a>',
				$messages_url, $bubble_html
			);
		}

		$badge_html .= $bubble;

		if ( '' != $submissions_unread && 0 < $submissions_unread ) {
			$messages_url_unread = add_query_arg( 'activity_status', 'unread', $messages_url );
			$bubble_html = sprintf(
				'<span class="responses-count-unread">%d</span>
				<span class="screen-reader-text">%d %s</span>',
				$submissions_unread,
				$submissions_unread,
				__( 'unread submissions', 'happyforms' )
			);
			$bubble = sprintf(
				'<span class="happyforms-responses-count happyforms-responses-count-unread">%s</span>',
				$bubble_html
			);

			if ( current_user_can( 'happyforms_manage_activity' ) ) {
				$bubble = sprintf(
					'<a href="%s" class="happyforms-responses-count happyforms-responses-count-unread">%s</a>',
					$messages_url_unread, $bubble_html
				);
			}

			$badge_html .= $bubble;
		}

		$badge_html .= '</div>';
	}

	return $badge_html;
}

endif;

if ( ! function_exists( 'happyforms_abandonment_email_template_path' ) ):

function happyforms_abandonment_email_template_path() {
	$path = happyforms_get_include_folder() . '/templates/email-abandonment.php';
	$path = apply_filters( 'happyforms_abandonment_email_template_path', $path );

	return $path;
}

endif;

if ( ! function_exists( 'happyforms_credentials_input' ) ) :

function happyforms_credentials_input( $service_id, $key, $label, $value ) {
	$html_id = "happyforms_integrations_{$service_id}_{$key}";
	?>
	<label for="<?php echo $html_id; ?>"><?php echo $label; ?></label>
	<div class="hf-pwd">
		<input type="password" class="widefat happyforms-credentials-input connected" id="<?php echo $html_id; ?>" name="credentials[<?php echo $service_id; ?>][<?php echo $key; ?>]" value="<?php echo $value; ?>" />
		<button type="button" class="button button-secondary hf-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php _e( 'Show credentials', 'happyforms' ); ?>" data-label-show="<?php _e( 'Show credentials', 'happyforms' ); ?>" data-label-hide="<?php _e( 'Hide credentials', 'happyforms' ); ?>">
			<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
		</button>
	</div>
	<?php
}

endif;

if ( ! function_exists( 'happyforms_message_is_spam' ) ):

function happyforms_message_is_spam( $message_id ) {
	$status = happyforms_get_meta( $message_id, 'read' );
	$is_spam = 2 == $status;

	return $is_spam;
}

endif;

if ( ! function_exists( 'happyforms_message_undo_notice' ) ):

function happyforms_message_undo_notice() {
	$current_user = wp_get_current_user();
	$current_user_avatar_url = get_avatar_url( get_current_user_id() );
	$url = admin_url( 'admin-ajax.php' );
	?>
		<div class="undo unspam" id="spam-undo-holder" style="display: none;">
			<div class="spam-undo-inside">
				<?php
				echo get_avatar( $current_user->ID, 32 );;
				printf( __( 'Comment by %s marked as spam.', 'happyforms' ), '<strong>' . $current_user->user_login . '</strong>' );
				?>
				<a href="<?php echo $url; ?>"><?php _e( 'Undo', 'happyforms' ); ?></a>
			</div>
		</div>
	<?php
}

endif;

if ( ! function_exists( 'happyforms_get_max_upload_size' ) ):

function happyforms_get_max_upload_size() {
    $max_upload_size = floor( wp_max_upload_size() / 1048576 );

    return $max_upload_size;
}

endif;

if ( ! function_exists( 'happyforms_printable_submission_template' ) ):

function happyforms_printable_submission_template() {
	$path = happyforms_get_include_folder() . '/templates/printable-submission.php';
	$path = apply_filters( 'happyforms_printable_submission_template', $path );

	return $path;
}

endif;

if ( ! function_exists( 'happyforms_capture_client_ip' ) ) :

function happyforms_capture_client_ip() {
	$capture_user_ip = apply_filters( 'happyforms_capture_client_ip', '__return_true' );

	return $capture_user_ip;
}

endif;