<?php

class HappyForms_Message_Admin_Edit {

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		$controller = happyforms_get_message_controller();
		$post_type = $controller->post_type;

		add_action( 'load-post.php', array( $this, 'handle_action' ) );
		add_action( 'admin_head', array( $this, 'screen_title' ) );
		add_action( 'admin_head', array( $this, 'highlight_menu' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_screen' ) );
		add_action( 'do_meta_boxes', array( $this, 'setup_metaboxes' ) );
		add_filter( 'admin_footer_text', 'happyforms_admin_footer' );
		add_action( 'happyforms_message_edit_field', array( $this, 'edit_field' ), 10, 4 );
		add_action( 'post_submitbox_minor_actions', array( $this, 'publish_box_meta_minor' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'publish_box_meta_misc' ) );
		add_filter( 'screen_options_show_screen', array( $this, 'show_screen_options' ) );
		add_action( 'save_post_' . $post_type, array( $this, 'save_post' ) );
		add_action( 'transition_post_status',  array( $this, 'transition_post_status' ), 10, 3 );
		add_filter( 'happyforms_message_sanitize_field', array( $this, 'sanitize_field' ), 10, 3 );
		add_filter( 'happyforms_message_field_is_editable', array( $this, 'field_is_editable' ), 10, 3 );
		add_filter( 'mce_external_plugins', array( $this, 'mce_plugin' ) );
		add_filter( 'happyforms_dashboard_data', array( $this, 'dashboard_data' ) );
		add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function handle_action() {
		$controller = happyforms_get_message_controller();
		$action = $controller->mark_action;

		if ( ! isset( $_GET['action'] )
			|| $action !== $_GET['action']
			|| ! isset( $_GET['_wpnonce'] )
			|| ! isset( $_GET['status'] ) ) {

			return;
		}

		$nonce = $_GET['_wpnonce'];
		$status = $_GET['status'];

		if ( ! isset( $_GET['post'] ) ) {
			return;
		}

		$response_id = $_GET['post'];

		if ( ! wp_verify_nonce( $nonce, "{$action}-{$response_id}" ) ) {
			return;
		}

		$response = $controller->get( $response_id );

		if ( ! $response ) {
			return;
		}

		if ( ! in_array( $status, array( 'read', 'unread', 'spam', 'not_spam' ) ) ) {
			return;
		}

		wp_untrash_post( $response_id );

		$status_value = '';

		if ( in_array( $status, array( 'read', 'unread', 'spam' ) ) ) {
			$status_values = array( 'read' => 1, 'unread' => '', 'spam' => 2 );
			$status_value = $status_values[$status];
			$current_status = happyforms_get_meta( $response_id, 'read', true );

			if ( 2 !== $current_status && 2 === $status_value ) {
				happyforms_update_meta( $response_id, 'previously_read', $current_status );
			}
		} elseif( happyforms_meta_exists( $response_id, 'previously_read' ) ) {
			$status_value = happyforms_get_meta( $response_id, 'previously_read', true );
		}

		happyforms_update_meta( $response_id, 'read', $status_value );

		do_action( 'happyforms_submission_status_changed', $response_id );

		if ( wp_redirect( wp_get_referer() ) ) {
			exit;
		}
	}

	public function screen_title() {
		global $post, $title;

		$title = __( 'Edit Submission', 'happyforms-ugprade' );
	}

	public function highlight_menu() {
		global $parent_file, $submenu_file, $submenu;

		$parent_file = 'happyforms';
		$submenu_file = 'edit.php?post_type=happyforms-message';
	}

	/**
	 * Action: output custom markup for the
	 * Message Edit admin screen.
	 *
	 * @since 1.0
	 *
	 * @hooked action edit_form_after_title
	 *
	 * @param WP_Post $post The message post object.
	 *
	 * @return void
	 */
	public function edit_screen( $post ) {
		global $message, $form;

		$message = happyforms_get_message_controller()->get( $post->ID );
		$form = happyforms_get_form_controller()->get( $message['form_id'] );

		require_once( happyforms_get_include_folder() . '/templates/admin-message-edit.php' );
	}

	public function setup_metaboxes( $post_type ) {
		global $wp_meta_boxes;


		$client_ip = happyforms_get_meta( get_the_ID(), 'client_ip', true );
		$publish_metabox = $wp_meta_boxes[$post_type]['side']['core']['submitdiv'];
		$publish_metabox['title'] = __( 'Save', 'happyforms' );

		$wp_meta_boxes[$post_type] = array(
			'side' => array(
				'core' => array(
					'submitdiv' => $publish_metabox
				)
			)
		);

		if ( happyforms_capture_client_ip() && ! empty( $client_ip ) ) {
			add_meta_box(
				'happyforms-message-details',
				__( 'Details', 'happyforms' ),
				array( $this, 'metabox_message_details' ),
				$post_type,
				'side',
				'low'
			);
		}

		add_meta_box(
			'happyforms-email-notification',
			__( 'Email Notification', 'happyforms' ),
			array( $this, 'metabox_email_notification' ),
			$post_type,
			'side',
			'low'
		);

		add_meta_box(
			'happyforms-email-confirmation',
			__( 'Email Confirmation', 'happyforms' ),
			array( $this, 'metabox_email_confirmation' ),
			$post_type,
			'side',
			'low'
		);
	}

	public function metabox_message_details( $post, $metabox ) {
		?>
		<div class="misc-pub-section">
			<span>
				<i class="dashicons dashicons-admin-home"></i>
				<?php _e( 'IPv4/IPv6', 'happyforms' ); ?>: <b><?php echo happyforms_get_meta( get_the_ID(), 'client_ip', true ); ?></b>
			</span>
		</div>
		<?php
	}

	public function metabox_email_notification( $post, $metabox ) {
		$action = happyforms_get_message_controller()->send_email_action;
		$type = happyforms_get_message_controller()->email_notification;
		$url = admin_url( 'admin-ajax.php' );
		$url = wp_nonce_url( $url, $action );
		$url = add_query_arg( array(
			'action' => $action,
			'response_id' => $post->ID,
			'type' => $type,
		), $url );
		?>
		<div class="happyforms-metabox-content">
			<p class="post-attributes-label-wrapper">
				<label for="happyforms-send-notification-email-field"><?php _e( 'Email address', 'happyforms' ); ?></label>
			</p>
			<input type="text" name="" id="happyforms-send-notification-email-field" class="widefat">
			<p class="post-attributes-help-text"><?php _e( 'Send to multiple email addresses by separating each with a comma.' ); ?></p>
		</div>
		<div class="happyforms-metabox-actions">
			<span class="spinner"></span>
			<input type="button" class="button button-primary button-large" id="happyforms-send-notification-email-submit" value="<?php _e( 'Send Email', 'happyforms' ); ?>" data-url="<?php echo $url; ?>" />
			<div class="clear"></div>
		</div>
		<?php
	}

	public function metabox_email_confirmation( $post, $metabox ) {
		$action = happyforms_get_message_controller()->send_email_action;
		$type = happyforms_get_message_controller()->email_confirmation;
		$url = admin_url( 'admin-ajax.php' );
		$url = wp_nonce_url( $url, $action );
		$url = add_query_arg( array(
			'action' => $action,
			'response_id' => $post->ID,
			'type' => $type,
		), $url );
		?>
		<div class="happyforms-metabox-content">
			<p class="post-attributes-label-wrapper">
				<label for="happyforms-send-confirmation-email-field"><?php _e( 'Email address', 'happyforms' ); ?></label>
			</p>
			<input type="text" name="" id="happyforms-send-confirmation-email-field" class="widefat">
			<p class="post-attributes-help-text"><?php _e( 'Email submitter a copy of their reply. Send to multiple email addresses by separating each with a comma.' ); ?></p>
		</div>
		<div class="happyforms-metabox-actions">
			<span class="spinner"></span>
			<input type="button" class="button button-primary button-large" id="happyforms-send-confirmation-email-submit" value="<?php _e( 'Send Email', 'happyforms' ); ?>" data-url="<?php echo $url; ?>" />
			<div class="clear"></div>
		</div>
		<?php
	}

	public function edit_field( $value, $part, $message, $form ) {
		$visible = apply_filters( 'happyforms_message_part_visible', true, $part );

		if ( ! $visible ) {
			return;
		}

		switch( $part['type'] ) {
			case 'attachment':
			case 'payments':
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-text.php' );
				break;
			case 'signature':
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-signature.php' );
				break;
			case 'legal':
			case 'email_integration':
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-input-text-disabled.php' );
				break;
			case 'table':
			case 'multi_line_text':
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-input-textarea.php' );
				break;
			case 'rich_text':
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-input-editor.php' );
				break;
			case 'scrollable_terms':
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-scrollable-terms.php' );
				break;
			default:
				include( happyforms_get_include_folder() . '/templates/partials/admin-message-edit-input-text.php' );
				break;
		}
	}

	public function publish_box_meta_minor() {
		global $post, $form;

		$status = happyforms_get_meta( $post->ID, 'read', true );
		$status_label = '';

		switch ( $status ) {
			case 1:
				$status_label = __( 'Read', 'happyforms' );
				break;
			case 2:
				$status_label = __( 'Spam', 'happyforms' );
				break;
			default:
				$status_label = __( 'Unread', 'happyforms' );
				break;
		}
		?>
		<div class="misc-pub-section misc-pub-comment-status" id="comment-status">
			<span class="happyforms-submission-status"><?php _e( 'Status: ', 'happyforms' ); ?><b><?php echo $status_label ?></b></span>
			<fieldset id="comment-status-radio">
				<legend class="screen-reader-text"><?php _e( 'Submission status', 'happyforms' ); ?></legend>
				<label>
					<input type="radio" name="response_status" value="read" <?php checked( $status, 1 ); ?>><?php _e( 'Read', 'happyforms' ); ?>
				</label><br>
				<label>
					<input type="radio" name="response_status" value="unread" <?php checked( $status, '' ); ?>><?php _e( 'Unread', 'happyforms' ); ?>
				</label><br>
				<label>
					<input type="radio" name="response_status" value="spam" <?php checked( $status, 2 ); ?>><?php _e( 'Spam', 'happyforms' ); ?>
				</label>
			</fieldset>
		</div>
		<?php
	}

	public function publish_box_meta_misc( $post ) {
		global $form;

		$submitted = sprintf(
			__( '%1$s at %2$s', 'happyforms' ),
			date_i18n( __( 'M j, Y' ), strtotime( $post->post_date ) ),
			date_i18n( __( 'H:i' ), strtotime( $post->post_date ) )
		);
		?>
		<div class="misc-pub-section curtime misc-pub-curtime happyforms-submission-timestamp">
			<span id="happyforms-timestamp">
				<?php _e( 'Submitted on', 'happyforms' ); ?>: <b><?php echo $submitted; ?></b>
			</span>
			<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js">
				<span aria-hidden="true"><?php _e( 'Edit', 'happyforms' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit date and time', 'happyforms' ); ?></span>
			</a>
			<fieldset id="happyforms-timestampdiv" class="hide-if-js">
				<?php touch_time( true, 1 ); ?>
			</fieldset>
		</div>

		<div class="misc-pub-section misc-pub-response-to">
			<?php _e( 'Submitted to:', 'happyforms' ); ?>

			<?php
			if ( 'publish' === $form['post_status'] && current_user_can( 'happyforms_manage_forms' ) ) {
				printf(
					'<b><a href="%s">%s</a></b>',
					happyforms_get_form_edit_link( $form['ID'] ),
					happyforms_get_form_title( $form )
				);
			} else {
				echo "<b>" . happyforms_get_form_title( $form ) . "</b>";
			}
			?>
		</div>
		<?php
	}

	public function show_screen_options() {
		$screen = get_current_screen();
		$post_type = happyforms_get_message_controller()->post_type;

		if ( $screen->id === $post_type ) {
			return false;
		}
	}

	private function get_field_allowed_tags() {
		$tags = array(
			'br' => array(),
			'b' => array(),
			'strong' => array(),
			'i' => array(),
			'em' => array(),
			'ul' => array(),
			'ol' => array(),
			'li' => array(),
			'p' => array(),
			'a' => array( 'href' => array() ),
			'pre' => array(),
			'hr' => array(),
			'u' => array(),
			'strike' => array(),
			'del' => array(),
			'blockquote' => array(),
		);

		return $tags;
	}

	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['action'] ) || ( 'editpost' !== $_POST['action'] ) ) {
			return;
		}

		if ( isset( $_POST['parts'] ) ) {
			$parts = $_POST['parts'];

			foreach( $parts as $part_id => $part ) {
				$type = $part['type'];
				$value = $part['value'];
				$is_editable = apply_filters( 'happyforms_message_field_is_editable', true, $value, $type, $post_id );

				if ( ! $is_editable ) {
					continue;
				}

				$class = happyforms_get_part_library()->get_part( $type );
				$value = apply_filters( 'happyforms_message_sanitize_field', $value, $value, $type, $post_id );

				happyforms_update_meta( $post_id, $part_id, $value );
			}
		}

		if ( isset( $_POST['response_status'] )
			&& in_array( $_POST['response_status'], array( 'read', 'unread', 'spam' ) ) ) {

			$status = $_POST['response_status'];
			$status_values = array( 'read' => 1, 'unread' => '', 'spam' => 2 );
			$status_value = $status_values[$status];
			$current_status = happyforms_get_meta( $post_id, 'read', true );

			if ( 2 !== $current_status && 2 === $status_value ) {
				happyforms_update_meta( $post_id, 'previously_read', $current_status );
			}

			happyforms_update_meta( $post_id, 'read', $status_value );

			do_action( 'happyforms_submission_status_changed', $post_id );

			wp_redirect( wp_get_original_referer() );
			exit;
		}
	}

	public function transition_post_status( $new_status, $old_status, $post ) {
		if ( 'future' !== $new_status ) {
			return;
		}

		global $wpdb;

		$wpdb->update( "{$wpdb->prefix}posts", array(
			'post_status' => $old_status,
		), array(
			'ID' => $post->ID,
		) );
	}

	public function sanitize_field( $value, $original_value, $type ) {
		switch( $type ) {
			case 'table':
			case 'rich_text':
				$value = wp_kses( $original_value, $this->get_field_allowed_tags() );
				$value = str_replace( array( "\r\n", "\n", "\r" ), '<br>', $value );
				$value = wp_slash( $value );
				break;
			default:
				$value = sanitize_text_field( $original_value );
				break;
		}

		return $value;
	}

	public function field_is_editable( $is_editable, $value, $type ) {
		$not_editable = in_array( $type, array(
			'attachment',
			'signature',
			'legal',
		) );

		return ! $not_editable;
	}

	public function mce_plugin( $plugins ) {
		$plugins = array(
			'hfcode' => happyforms_get_plugin_url() . 'inc/assets/js/admin/editor-plugins.js',
		);

		return $plugins;
	}

	public function dashboard_data( $data ) {
		$data['textNoticeDismiss'] = __( 'Dismiss this notice.', 'happyforms' );

		return $data;
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script(
			'happyforms-edit-submission',
			happyforms_get_plugin_url() . 'inc/assets/js/admin/edit-submission.js',
			array(), happyforms_get_version(), true
		);
	}

	public function admin_title( $admin_title, $title ) {
		$screen = get_current_screen();
		$message_controller = happyforms_get_message_controller();

		if ( $message_controller->post_type === $screen->post_type && 'post' === $screen->base ) {
			$admin_title = happyforms_get_message_controller()->get_admin_edit_title();
		}

		return $admin_title;
	}

}

HappyForms_Message_Admin_Edit::instance();
