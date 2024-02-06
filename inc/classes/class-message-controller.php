<?php

class HappyForms_Message_Controller {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Message_Controller
	 */
	private static $instance;

	/**
	 * The message post type slug.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $post_type = 'happyforms-message';

	/**
	 * The parameter name used to identify a
	 * submission form
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $form_parameter = 'happyforms_form_id';

	/**
	 * The parameter name used to identify a
	 * submission form
	 *
	 * @var string
	 */
	public $form_step_parameter = 'happyforms_step';

	/**
	 * The action name used to identify a
	 * message submission request.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $submit_action = 'happyforms_message';

	/**
	 * The send-user-email action name.
	 */
	public $send_email_action = 'happyforms_send_user_email';
	public $email_notification = 'happyforms_email_notification';
	public $email_confirmation = 'happyforms_email_confirmation';

	public $mark_action = 'happyforms_mark_response';
	public $action_mark_spam = 'happyforms_mark_spam';
	public $action_mark_not_spam = 'happyforms_mark_not_spam';
	public $action_mark_read = 'happyforms_mark_read';
	public $action_mark_unread = 'happyforms_mark_unread';
	public $action_trash = 'happyforms_trash';
	public $action_restore = 'happyforms_restore';
	public $action_delete = 'happyforms_delete';

	public $schedule_pending_cleanup = 'happyforms_schedule_pending_cleanup';

	/**
	 * The singleton constructor.
	 *
	 * @since 1.0
	 *
	 * @return HappyForms_Message_Controller
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'happyforms_responses_page_url', array( $this, 'page_url' ) );
		add_action( 'parse_request', array( $this, 'admin_post' ) );
		add_action( 'admin_init', array( $this, 'admin_post' ) );
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
		add_action( 'delete_post', array( $this, 'delete_post' ) );
		add_action( 'trashed_post', array( $this, 'trashed_post' ) );
		add_action( 'untrashed_post', array( $this, 'untrashed_post' ) );
		add_action( 'wp_untrash_post_status',  array( $this, 'untrash_post_status' ), 10, 3 );
		add_action( 'happyforms_form_deleted', array( $this, 'form_deleted' ) );
		add_filter( 'happyforms_email_part_visible', array( $this, 'email_part_visible' ), 10, 4 );

		// Core multi-step hooks
		add_action( 'happyforms_step', array( $this, 'default_submission_step' ) );
		// Submission preview and review
		add_action( 'happyforms_step', array( $this, 'preview_submission_step' ) );
		add_action( 'happyforms_step', array( $this, 'review_submission_step' ) );
		// Client IP
		add_action( 'happyforms_response_created', array( $this, 'append_response_info' ), 10, 2 );
		add_action( 'happyforms_draft_created', array( $this, 'append_response_info' ), 10, 2 );
		// Unique IDs
		add_action( 'happyforms_response_created', array( $this, 'response_stamp_unique_id' ), 10, 2 );
		add_action( 'happyforms_submission_success', array( $this, 'notice_append_unique_id' ), 10, 3 );
		// Asynchronous success features
		add_action( 'happyforms_pending_submission_success', array( $this, 'pending_submission_success' ) );
		// Resend user email link
		add_action( 'wp_ajax_' . $this->send_email_action, array( $this, 'send_user_email' ) );
		add_action( 'wp_ajax_' . $this->action_mark_spam, array( $this, 'ajax_mark_spam' ) );
		add_action( 'wp_ajax_' . $this->action_mark_not_spam, array( $this, 'ajax_mark_not_spam' ) );
		add_action( 'wp_ajax_' . $this->action_mark_read, array( $this, 'ajax_mark_read' ) );
		add_action( 'wp_ajax_' . $this->action_mark_unread, array( $this, 'ajax_mark_unread' ) );
		add_action( 'wp_ajax_' . $this->action_trash, array( $this, 'ajax_trash' ) );
		add_action( 'wp_ajax_' . $this->action_restore, array( $this, 'ajax_restore' ) );
		add_action( 'wp_ajax_' . $this->action_delete, array( $this, 'ajax_delete' ) );

		add_action( 'happyforms_stale_fields_deleted', array( $this, 'delete_stale_fields' ), 10, 2 );

		add_action( $this->schedule_pending_cleanup, array( $this, 'pending_cleanup' ) );

		$this->schedule_cleanup();
	}

	public function get_post_fields() {
		$fields = array(
			'post_title' => '',
			'post_type' => $this->post_type,
			'post_status' => 'publish',
		);

		return $fields;
	}

	public function get_meta_fields() {
		$fields = array(
			'form_id' => 0,
			'read' => false,
			'tracking_id' => '',
			'request' => array(),
		);

		return $fields;
	}

	/**
	 * Get the default values of the message post object fields.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_defaults( $group = '' ) {
		$fields = array();

		switch ( $group ) {
			case 'post':
				$fields = $this->get_post_fields();
				break;
			case 'meta':
				$fields = $this->get_meta_fields();
				break;
			default:
				$fields = array_merge(
					$this->get_post_fields(),
					$this->get_meta_fields()
				);
				break;
		}

		return $fields;
	}

	/**
	 * Action: register the message custom post type.
	 *
	 * @hooked action init
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name' => __( 'Submissions', 'happyforms' ),
			'singular_name' => __( 'Submission', 'happyforms' ),
			'edit_item' => __( 'Edit Submission', 'happyforms' ),
			'view_item' => __( 'View Submission', 'happyforms' ),
			'view_items' => __( 'View Submissions', 'happyforms' ),
			'search_items' => __( 'Search Submissions', 'happyforms' ),
			'not_found' => __( 'No submissions found.', 'happyforms' ),
			'not_found_in_trash' => __( 'No submissions found in Trash.', 'happyforms' ),
			'all_items' => __( 'All Submissions', 'happyforms' ),
			'menu_name' => __( 'All Submissions', 'happyforms' ),
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_admin_bar' => false,
			'query_var' => true,
			'capability_type' => 'page',
			'has_archive' => false,
			'hierarchical' => false,
			'can_export' => false,
			'supports' => array( 'custom-fields' ),
		);

		register_post_type( $this->post_type, $args );
	}

	public function page_url( $url ) {
		$url = "edit.php?post_type={$this->post_type}";

		return $url;
	}

	public function get_session_reset_callback( $session, $form ) {
		$session_reset_callback = apply_filters(
			'happyforms_session_reset_callback',
			array(
				'function' => array( $session, 'reset_step' ),
				'args'   => array(),
			),
			$session,
			$form
		);

		return $session_reset_callback;
	}

	/**
	 * Action: handle a form submission.
	 *
	 * @hooked action parse_request
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_post() {
		// Exit early if we're not submitting any form
		if ( ! isset ( $_REQUEST['action'] ) || $this->submit_action != $_REQUEST['action'] ) {
			return;
		}

		// Check form_id parameter
		if ( ! isset ( $_REQUEST[$this->form_parameter] ) ) {
			wp_send_json_error();
		}

		$form_id = intval( $_REQUEST[$this->form_parameter] );

		$form_controller = happyforms_get_form_controller();
		$form = $form_controller->get( $form_id );

		// Check if form found
		if ( ! $form || is_wp_error( $form ) ) {
			wp_send_json_error();
		}

		// Set form step
		$step = isset( $_REQUEST[$this->form_step_parameter] ) ?
			$_REQUEST[$this->form_step_parameter] : '';

		happyforms_get_session()->set_step( $step );

		// Validate honeypot
		if ( happyforms_get_form_controller()->has_honeypot_protection( $form ) ) {
			if ( ! happyforms_validate_honeypot( $form ) && ! defined( 'HAPPYFORMS_IS_SPAMBOT' ) ) {
				define( 'HAPPYFORMS_IS_SPAMBOT', true );
			}
		}

		// Validate hash
		if ( happyforms_get_form_controller()->has_hash_protection( $form ) ) {
			if ( ! happyforms_validate_hash( $form ) && ! defined( 'HAPPYFORMS_IS_SPAMBOT' ) ) {
				define( 'HAPPYFORMS_IS_SPAMBOT', true );
			}
		}

		// Validate browser
		if ( happyforms_get_form_controller()->has_browser_protection( $form ) ) {
			if ( ! happyforms_validate_browser( $form ) && ! defined( 'HAPPYFORMS_IS_SPAMBOT' ) ) {
				define( 'HAPPYFORMS_IS_SPAMBOT', true );
			}
		}

		define( 'HAPPYFORMS_STEPPING', true );
		do_action( 'happyforms_step', $form );
	}

	public function default_submission_step( $form ) {
		if ( 'submit' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();

		// Validate submission
		$antispam = happyforms_get_antispam_integration();
		$antispam_result = '';

		if ( happyforms_is_truthy( $form['captcha'] ) && $antispam->get_active_service()->is_connected() ) {
			$antispam_result = $antispam->validate_submission( $form );

			if ( is_wp_error( $antispam_result ) ) {
				$antispam_error = new WP_Error( 'error', happyforms_get_validation_message( 'field_empty' ) );

				$session->add_error( happyforms_get_recaptcha_part_name( $form ), $antispam_error->get_error_message() );
			}
		}

		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();
		$session_reset_callback = $this->get_session_reset_callback( $session, $form );

		// If this submission is pending confirmation from asynchronous conditions.
		$is_pending = $this->submission_is_pending( $_REQUEST, $form );

		if ( false === $submission || is_wp_error( $antispam_result ) ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, html_entity_decode( $form['error_message'] ) );

			// Reset steps
			call_user_func( $session_reset_callback['function'], $session_reset_callback['args'] );

			/**
			 * This action fires upon an invalid submission.
			 *
			 * @since 1.4
			 *
			 * @param WP_Error $submission Error data.
			 * @param array    $form   Current form data.
			 *
			 * @return void
			 */
			do_action( 'happyforms_submission_error', $submission, $form );

			// Features that depend on asynchronous conditions should hook
			// to this action, instead of `happyforms_submission_error`.
			if ( ! $is_pending ) {
				do_action( 'happyforms_pending_submission_error', $submission, $form );
			}

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send error response
			wp_send_json_error( $response );
		} else {
			// Add a general success notice at the top
			$session->add_notice( $form_id, html_entity_decode( $form['confirmation_message'] ) );

			// Empty submitted values
			$session->clear_values();

			$form = happyforms_get_conditional_controller()->get( $form, $_REQUEST );

			// Create message post
			if ( ! happyforms_is_spambot() ) {
				$status = 'publish';

				if ( $is_pending ) {
					$status = 'pending';
				} else {
					$trash_submission = apply_filters( 'happyforms_should_trash_submission', false, $submission, $_REQUEST, $form );

					if ( $trash_submission ) {
						$status = 'trash';
					}

					$submission = apply_filters( 'happyforms_cleanup_submission_data', $submission );
				}

				$message_id = $this->create( $form, $submission, $status );

				if ( is_wp_error( $message_id ) ) {
					return;
				}

				$message = $this->get( $message_id );

				/**
				 * This action fires once a message is succesfully submitted.
				 *
				 * @since 1.4
				 *
				 * @param array $submission Submission data.
				 * @param array $form   Current form data.
				 *
				 * @return void
				 */
				do_action( 'happyforms_submission_success', $submission, $form, $message );

				// Features that depend on asynchronous conditions should hook
				// to this action, instead of `happyforms_submission_success`.
				if ( ! $is_pending && 'trash' != $status ) {
					do_action( 'happyforms_pending_submission_success', $message_id );
				}

				$response['printable_data'] = $this->printable_submission_data( $form, $message );
				$redirect_url = happyforms_get_form_property( $form, 'redirect_url' );

				if ( ! empty( $redirect_url ) ) {
				 	$response['redirect'] = $form['redirect_url'];
				 	$response['redirect_after'] = apply_filters( 'happyforms_submission_redirect_after', 5 );
				}
			}

			if ( ! empty( $submission ) ) {
				$response['hide_steps'] = true;
			}

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send success response
			$this->send_json_success( $response, $submission, $form );
		}
	}

	public function preview_submission_step( $form ) {
		if ( 'preview' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();

		// Validate ReCaptcha
		$antispam = happyforms_get_antispam_integration();
		$antispam_result = '';

		if ( happyforms_is_truthy( $form['captcha'] ) && $antispam->get_active_service()->is_connected() ) {
			$antispam_result = $antispam->validate_submission( $form );

			if ( is_wp_error( $antispam_result ) ) {
				$antispam_error = new WP_Error( 'error', happyforms_get_validation_message( 'field_empty' ) );

				$session->add_error( happyforms_get_recaptcha_part_name( $form ), $antispam_error->get_error_message() );
			}
		}

		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();
		$session_reset_callback = $this->get_session_reset_callback( $session, $form );

		if ( false === $submission || is_wp_error( $antispam_result ) ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, html_entity_decode( $form['error_message'] ) );

			// Reset steps
			call_user_func( $session_reset_callback['function'], $session_reset_callback['args'] );

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send error response
			wp_send_json_error( $response );
		} else {
			// Advance step
			$session->next_step();

			$form = happyforms_get_conditional_controller()->get( $form, $_REQUEST );

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send success response
			$this->send_json_success( $response, $submission, $form );
		}
	}

	public function review_submission_step( $form ) {
		if ( 'review' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();
		$session_reset_callback = $this->get_session_reset_callback( $session, $form );

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, html_entity_decode( $form['error_message'] ) );
		}

		// Reset steps
		call_user_func( $session_reset_callback['function'], $session_reset_callback['args'] );

		// Render the form
		$response['html'] = $form_controller->render( $form );

		if ( false === $submission ) {
			// Send error response
			wp_send_json_error( $response );
		}

		// Send success response
		$this->send_json_success( $response, $submission, $form );
	}

	public function send_json_success( $response = array(), $submission = array(), $form = array() ) {
		$response = apply_filters( 'happyforms_json_response', $response, $submission, $form );

		wp_send_json_success( $response );
	}

	public function trashed_post( $post_id ) {
		$post = get_post( $post_id );

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		do_action( 'happyforms_submission_status_changed', $post_id );
	}

	public function untrashed_post( $post_id ) {
		$post = get_post( $post_id );

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		do_action( 'happyforms_submission_status_changed', $post_id );
	}

	public function untrash_post_status( $new_status, $post_id, $previous_status ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		$new_status = 'publish';

		return $new_status;
	}

	public function before_delete_post( $post_id ) {
		$post = get_post( $post_id );

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		do_action( 'happyforms_before_delete_response', $post_id );
	}

	/**
	 * Action: update the unread badge upon message deletion.
	 *
	 * @since 1.1
	 *
	 * @hooked action delete_post
	 *
	 * @param int|string $post_id The ID of the message object.
	 *
	 * @return void
	 */
	public function delete_post( $post_id ) {
		$post = get_post( $post_id );

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		do_action( 'happyforms_response_deleted', $post_id );
	}

	public function form_deleted( $form_id ) {
		$responses = $this->get_by_form( $form_id );

		foreach ( $responses as $response ) {
			wp_delete_post( $response['ID'], true );
		}
	}

	public function validate_part( $form, $part, $request ) {
		$part_class = happyforms_get_part_library()->get_part( $part['type'] );

		if ( false !== $part_class ) {
			$part_id = $part['id'];
			$part_name = happyforms_get_part_name( $part, $form );
			$sanitized_value = $part_class->sanitize_value( $part, $form, $request );
			$validated_value = $part_class->validate_value( $sanitized_value, $part, $form );
			$validated_value = apply_filters( 'happyforms_validate_part_submission', $validated_value, $part, $form, $request );

			$session = happyforms_get_session();
			$session->add_value( $part_name, $sanitized_value );

			if ( ! is_wp_error( $validated_value ) ) {
				return $validated_value;
			} else {
				do_action( 'happyforms_validation_error', $form, $part );

				$part_field = $part_name;
				$error_data = $validated_value->get_error_data();

				if ( ! empty( $error_data ) && isset( $error_data['components'] ) ) {
					foreach ( $error_data['components'] as $component ) {
						$session->add_error( $part_field, $validated_value->get_error_message(), $component );
					}
				} else {
					$session->add_error( $part_field, $validated_value->get_error_message() );
				}
			}
		}

		return false;
	}

	public function validate_submission( $form, $request = array() ) {
		$submission = array();
		$is_valid = true;

		// Apply conditional logic
		$form = happyforms_get_conditional_controller()->get( $form, $request );
		$blocklist_controller = happyforms_get_message_blocklist();

		foreach( $form['parts'] as $part ) {
			$part_id = $part['id'];
			$validated_value = $this->validate_part( $form, $part, $request );

			if ( false !== $validated_value ) {
				$string_value = happyforms_stringify_part_value( $validated_value, $part, $form );
				$submission[$part_id] = $string_value;

				$submission = $blocklist_controller->validate_part_disallowed_keys( $submission, $validated_value, $part, $form );
			} else {
				$is_valid = false;
			}
		}

		$is_valid = apply_filters( 'happyforms_validate_submission', $is_valid, $request, $form );

		if ( $is_valid ) {
			$submission = $blocklist_controller->validate_submission_disallowed_keys( $submission, $form );
		}

		return $is_valid ? $submission : false;
	}

	public function get_raw_request( $form, $submission ) {
		$request = array();

		foreach( $form['parts'] as $part_id => $part ) {
			$part_name = happyforms_get_part_name( $part, $form );

			if ( ! isset( $_REQUEST[$part_name] ) ) {
				continue;
			}

			$part_class = happyforms_get_part_library()->get_part( $part['type'] );
			$value = $part_class->sanitize_value( $part, $form, $_REQUEST );
			$request[$part_name] = $value;
		}

		return $request;
	}

	public function get_insert_post_data( $form, $submission ) {
		$defaults = $this->get_post_fields();
		$defaults_meta = $this->get_meta_fields();
		$raw_request = $this->get_raw_request( $form, $submission );
		$message_meta = wp_parse_args( array(
			'form_id' => $form['ID'],
			'request' => $raw_request,
		), $defaults_meta );
		$message_meta = array_merge( $message_meta, $submission );
		$message_meta = happyforms_prefix_meta( $message_meta );
		$post_data = array_merge( $defaults, array(
			'meta_input' => $message_meta
		) );

		return $post_data;
	}

	/**
	 * Create a new message post object.
	 *
	 * @since 1.0
	 *
	 * @param array $form       The message form data.
	 * @param array $submission The message form data.
	 *
	 * @return int|boolean
	 */
	public function create( $form, $submission, $status = 'publish' ) {
		$post_data = $this->get_insert_post_data( $form, $submission );
		$message_id = wp_insert_post( wp_slash( $post_data ), true );

		wp_update_post( array(
			'ID' => $message_id,
			'post_title' => happyforms_get_message_title( $message_id ),
			'post_status' => $status,
		) );

		do_action( 'happyforms_response_created', $message_id, $form );

		return $message_id;
	}

	public function append_response_info( $response_id, $form ) {
		$client_referer = (
			isset( $_REQUEST['happyforms_client_referer'] ) ?
			$_REQUEST['happyforms_client_referer'] : ''
		);

		$current_post_id = (
			isset( $_REQUEST['happyforms_current_post_id'] ) ?
			$_REQUEST['happyforms_current_post_id'] : ''
		);

		happyforms_update_meta( $response_id, 'client_referer', $client_referer );
		happyforms_update_meta( $response_id, 'current_post_id', $current_post_id );

		if ( happyforms_capture_client_ip() ) {
			happyforms_update_meta( $response_id, 'client_ip', happyforms_get_client_ip() );
		}
	}

	public function response_stamp_unique_id( $response_id, $form ) {
		if ( intval( $form['unique_id'] ) ) {
			$increment = $form['unique_id_start_from'];
			$prefix = $form['unique_id_prefix'];
			$suffix = $form['unique_id_suffix'];
			$tracking_id = "{$prefix}{$increment}{$suffix}";

			happyforms_update_meta( $response_id, 'tracking_id', $tracking_id );
		}
	}

	public function notice_append_unique_id( $submission, $form, $message ) {
		if ( intval( $form['unique_id'] ) ) {
			$tracking_id = $message['tracking_id'];
			$notice = $form['confirmation_message'];
			$label = __( 'Tracking number', 'happyforms' );
			$notice = "{$notice}<span>{$label}: {$tracking_id}</span>";
			$notice = html_entity_decode( $notice );

			happyforms_get_session()->add_notice( $form['ID'], $notice );
		}
	}

	public function submission_is_pending( $request, $form ) {
		$is_pending = apply_filters( 'happyforms_submission_is_pending', false, $request, $form );

		return $is_pending;
	}

	public function pending_submission_success( $submission_id ) {
		$form_id = happyforms_get_meta( $submission_id, 'form_id', true );
		$form = happyforms_get_form_controller()->get( $form_id );

		if ( ! happyforms_is_spambot() ) {
			if ( 1 === intval( $form['receive_email_alerts'] ) ) {
				happyforms_get_task_controller()->add( 'HappyForms_Task_Email_Owner', $submission_id );
			}

			if ( 1 === intval( $form['send_confirmation_email'] ) ) {
				happyforms_get_task_controller()->add( 'HappyForms_Task_Email_User', $submission_id );
			}

			$save_entries = apply_filters( 'happyforms_save_entries', true, $form );

			if ( ! $save_entries && ! $is_pending ) {
				wp_delete_post( $submission_id );
			}
		}
	}

	public function process_pending_submission( $submission_id ) {
		$form_controller = happyforms_get_form_controller();
		$form_id = happyforms_get_meta( $submission_id, 'form_id', true );
		$form = $form_controller->get( $form_id );
		$submission = happyforms_get_message_controller()->get( $submission_id );

		if ( ! $this->submission_is_pending( $submission['request'], $form ) ) {
			return;
		}

		$is_success = apply_filters( 'happyforms_pending_submission_succeeded', true, $submission_id );

		if ( ! $is_success ) {
			do_action( 'happyforms_pending_submission_error', $submission, $form );

			return;
		}

		wp_update_post( array(
			'ID' => $submission_id,
			'post_status' => 'publish',
		) );

		do_action( 'happyforms_pending_submission_success', $submission_id );
	}

	/**
	 * Get one or more message post objects.
	 *
	 * @since 1.0
	 *
	 * @param string $post_ids The IDs of the messages to retrieve.
	 *
	 * @return array
	 */
	public function do_get( $post_ids = '' ) {
		$query_params = array(
			'post_type' => $this->post_type,
			'post_status' => array( 'publish', 'pending', 'draft', 'trash' ),
			'posts_per_page' => -1,
		);

		if ( ! empty( $post_ids ) ) {
			if ( is_numeric( $post_ids ) ) {
				$query_params['p'] = $post_ids;
			} else if ( is_array( $post_ids ) )  {
				$query_params['post__in'] = $post_ids;
			}
		}

		$messages = get_posts( $query_params );
		$message_entries = array_map( array( $this, 'to_array'), $messages );

		if ( is_numeric( $post_ids ) ) {
			if ( count( $message_entries ) > 0 ) {
				return $message_entries[0];
			} else {
				return false;
			}
		}

		return $message_entries;
	}

	public function get( $post_ids, $force = false ) {
		$args = md5( serialize( func_get_args() ) );
		$key = "_happyforms_cache_responses_get_{$args}";
		$found = false;
		$result = happyforms_cache_get( $key, $found );

		if ( false === $found || $force ) {
			$result = $this->do_get( $post_ids );
			happyforms_cache_set( $key, $result );
		}

		return $result;
	}

	/**
	 * Get all messages relative to a form.
	 *
	 * @since 1.0
	 *
	 * @param string $form_id The ID of the form.
	 *
	 * @return array
	 */
	public function get_by_form( $form_id, $ids_only = false, $count = -1 ) {
		if ( $ids_only ) {
			global $wpdb;

			$query = $wpdb->prepare( "
				SELECT p.ID FROM $wpdb->posts p
				JOIN $wpdb->postmeta m ON p.ID = m.post_id
				WHERE p.post_type = 'happyforms-message'
				AND m.meta_key = '_happyforms_form_id'
				AND m.meta_value = %d;
			", $form_id );

			$results = $wpdb->get_col( $query );

			return $results;
		}

		$query_params = array(
			'post_type'   => $this->post_type,
			'post_status' => 'any',
			'posts_per_page' => $count,
			'meta_query' => array( array(
				'key' => '_happyforms_form_id',
				'value' => $form_id,
			) )
		);

		$messages = get_posts( $query_params );
		$message_entries = array_map( array( $this, 'to_array'), $messages );

		return $message_entries;
	}

	/**
	 * Get messages by a list of meta fields.
	 *
	 * @param string $metas An array of meta fields.
	 *
	 * @return array
	 */
	public function get_by_metas( $metas ) {
		$metas = happyforms_prefix_meta( $metas );
		$meta_query = array();

		foreach ( $metas as $field => $value ) {
			$meta_query[] = array(
				'field' => $field,
				'value' => $value,
			);
		}

		$query_params = array(
			'post_type'   => $this->post_type,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'meta_query' => $meta_query,
		);

		$messages = get_posts( $query_params );
		$message_entries = array_map( array( $this, 'to_array'), $messages );

		return $message_entries;
	}

	/**
	 * Turn a message post object into an array.
	 *
	 * @since 1.0
	 *
	 * @param WP_Post $message The message post object.
	 *
	 * @return array
	 */
	public function to_array( $message ) {
		$message_array = $message->to_array();
		$message_meta = happyforms_unprefix_meta( get_post_meta( $message->ID ) );
		$form_id = $message_meta['form_id'];
		$form = happyforms_get_form_controller()->get( $form_id );
		$meta_defaults = $this->get_meta_fields();
		$message_array = array_merge( $message_array, wp_parse_args( $message_meta, $meta_defaults ) );
		$message_array['parts'] = array();

		if ( $form ) {
			foreach ( $form['parts'] as $part_data ) {
				$part = happyforms_get_part_library()->get_part( $part_data['type'] );

				if ( $part ) {
					$part_id = $part_data['id'];
					$part_value = $part->get_default_value( $part_data );

					if ( isset( $message_meta[$part_id] ) ) {
						$part_value = $message_meta[$part_id];
					}

					$message_array['parts'][$part_id] = $part_value;
					unset( $message_array[$part_id] );
				}
			}
		}

		return $message_array;
	}

	public function email_part_visible( $visible, $part, $form, $response ) {
		$required = happyforms_is_truthy( $part['required'] );
		$value = happyforms_get_email_part_value( $response, $part, $form );

		if ( false === $required && empty( $value ) ) {
			$visible = false;
		}

		if ( isset( $part['use_as_subject'] ) && $part['use_as_subject'] ) {
			$visible = false;
		}

		return $visible;
	}

	public function do_search_metas( $term ) {
		global $wpdb;

		$sql = "
		SELECT m.post_id, m.meta_key, m.meta_value
		FROM $wpdb->postmeta m JOIN $wpdb->posts p ON m.post_id = p.ID
		WHERE p.post_type = %s AND m.meta_value LIKE %s
		GROUP BY m.post_id;
		";

		$term = '%' . $wpdb->esc_like( $term ) . '%';
		$post_type = happyforms_get_message_controller()->post_type;
		$query = $wpdb->prepare( $sql, $post_type, $term );
		$metas = $wpdb->get_results( $query );

		return $metas;
	}

	public function search_metas( $term ) {
		$args = md5( serialize( func_get_args() ) );
		$key = "__happyforms_cache_responses_metas_search_{$args}";
		$found = false;
		$result = happyforms_cache_get( $key, $found );

		if ( false === $found ) {
			$result = $this->do_search_metas( $term );
			happyforms_cache_set( $key, $result );
		}

		return $result;
	}

	public function send_user_email() {
		if ( ! check_ajax_referer( $this->send_email_action ) ) {
			wp_send_json_error();
		}

		if ( ! isset( $_REQUEST['response_id'] ) || ! isset( $_REQUEST['email'] ) ) {
			wp_send_json_error();
		}

		$error_message = __( 'Invalid email.', 'happyforms' );
		$success_message = __( 'Email sent.', 'happyforms' );

		$emails = explode( ',', $_REQUEST['email'] );
		$emails = array_map( 'trim', $emails );
		$emails = array_filter( $emails );
		$email_type = $_REQUEST['type'];

		foreach( $emails as $email ) {
			if ( ! happyforms_is_email( $email ) ) {
				wp_send_json_error( array(
					'message' => $error_message,
				) );
			}
		}

		if ( empty( $emails ) ) {
			wp_send_json_error( array(
				'message' => $error_message,
			) );
		}

		$response_id = $_REQUEST['response_id'];
		$response = $this->get( $response_id );

		if ( ! $response ) {
			wp_send_json_error( array(
				'message' => $error_message,
			) );
		}

		if ( $this->email_notification === $email_type ) {
			add_filter( 'happyforms_email_alert', function( $email_message ) use( $emails ) {
				$email_message->set_to( $emails );

				return $email_message;
			} );
			happyforms_get_task_controller()->add( 'HappyForms_Task_Email_Owner', $response_id );
		}

		if ( $this->email_confirmation === $email_type ) {
				add_filter( 'happyforms_email_confirmation', function( $email_message ) use( $emails ) {
				$email_message->set_to( $emails );

				return $email_message;
			} );
			happyforms_get_task_controller()->add( 'HappyForms_Task_Email_User', $response_id );
		}

		wp_send_json_success( array(
			'message' => $success_message,
		) );
	}

	public function get_admin_title() {
		$before_title = __( 'Submissions', 'happyforms' );
		$after_title = sprintf( __( '&lsaquo; %s &#8212; WordPress' ), get_bloginfo( 'name' ) );
		$title = "{$before_title} {$after_title}";
		$count = happyforms_submission_counter()->get_total_unread();

		if ( ! empty( $count ) ) {
			$title = "{$before_title} ({$count}) {$after_title}";
		}

		return $title;
	}

	public function get_admin_edit_title() {
		$before_title = __( 'Edit Submission', 'happyforms' );
		$after_title = sprintf( __( '&lsaquo; %s &#8212; WordPress' ), get_bloginfo( 'name' ) );
		$title = "{$before_title} {$after_title}";

		return $title;
	}

	public function get_ajax_mark_response( $activity_id ) {
		$page_title = html_entity_decode( happyforms_get_message_controller()->get_admin_title() );
		$form_id = happyforms_get_meta( $activity_id, 'form_id', true );
		$read_unread_badge = happyforms_read_unread_badge( $form_id );
		$response = array(
			'counters' => happyforms_submission_counter()->get_totals(),
			'pageTitle' => $page_title,
			'formID' => $form_id,
			'badge' => $read_unread_badge,
		);

		return $response;
	}

	public function ajax_mark_spam() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_mark_spam . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];
		$current_status = happyforms_get_meta( $post_id, 'read', true );

		if ( 'trash' == get_post_status( $post_id ) ) {
			happyforms_update_meta( $post_id, 'previously_trash', true );
		} else {
			happyforms_update_meta( $post_id, 'previously_trash', false );
		}

		if ( 2 !== $current_status ) {
			happyforms_update_meta( $post_id, 'previously_read', $current_status );
		}

		happyforms_update_meta( $post_id, 'read', 2 );
		wp_untrash_post( $post_id );

		do_action( 'happyforms_submission_status_changed', $post_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function ajax_mark_not_spam() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_mark_not_spam . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];
		$current_status = '';

		if ( happyforms_meta_exists( $post_id, 'previously_read' ) ) {
			$current_status = happyforms_get_meta( $post_id, 'previously_read', true );
		}

		if ( happyforms_meta_exists( $post_id, 'previously_trash' ) && happyforms_get_meta( $post_id, 'previously_trash', true ) ) {
			wp_trash_post( $post_id );
		}

		happyforms_update_meta( $post_id, 'read', $current_status );

		do_action( 'happyforms_submission_status_changed', $post_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function ajax_mark_read() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_mark_read . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];

		happyforms_update_meta( $post_id, 'read', 1 );

		do_action( 'happyforms_submission_status_changed', $post_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function ajax_mark_unread() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_mark_unread . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];

		happyforms_update_meta( $post_id, 'read', '' );

		do_action( 'happyforms_submission_status_changed', $post_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function ajax_trash() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_trash . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];

		wp_trash_post( $post_id );

		do_action( 'happyforms_submission_status_changed', $post_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function ajax_restore() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_restore . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];

		wp_untrash_post( $post_id );

		do_action( 'happyforms_submission_status_changed', $post_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function ajax_delete() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error();
		}

		$action = $this->action_delete . '-' . $_REQUEST['post'];

		if ( ! check_ajax_referer( $action ) ) {
			wp_send_json_error();
		}

		$post_id = $_REQUEST['post'];

		wp_untrash_post( $post_id );

		$form_id = happyforms_get_meta( $post_id, 'form_id', true );

		wp_delete_post( $post_id, true );

		happyforms_submission_counter()->update_form_counters( $form_id );

		wp_send_json_success( $this->get_ajax_mark_response( $post_id ) );
	}

	public function delete_stale_fields( $fields, $form_id ) {
		global $wpdb;

		$field_placeholder = implode( ', ', array_fill( 0, count( $fields ), '%s' ) );
		$sql = "
			DELETE f
			FROM $wpdb->postmeta f
			JOIN $wpdb->postmeta p ON f.post_id = p.post_id
			WHERE p.meta_key = '_happyforms_form_id'
			AND p.meta_value = %d
			AND f.meta_key IN ({$field_placeholder});
		";

		$query = call_user_func_array(
			array( $wpdb, 'prepare' ),
			array_merge( array( $sql ), array( $form_id ), $fields )
		);

		$wpdb->query( $query );
	}

	public function schedule_cleanup() {
		// Pending submissions
		if ( ! wp_next_scheduled( $this->schedule_pending_cleanup ) ) {
			wp_schedule_event( time(), 'daily', $this->schedule_pending_cleanup );
		}
	}

	public function pending_cleanup() {
		$post_ids = get_posts( array(
			'post_type' => $this->post_type,
			'post_status' => 'pending',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'date_query' => array(
				'before' => '-1 day'
			),
		) );

		foreach( $post_ids as $post_id ) {
			wp_delete_post( $post_id );
		}
	}

	public function printable_submission_data( $form, $message ) {
		ob_start();
		require_once( happyforms_printable_submission_template() );
		$submission_html = ob_get_clean();

		return $submission_html;
	}

}

if ( ! function_exists( 'happyforms_get_message_controller' ) ):
/**
 * Get the HappyForms_Message_Controller class instance.
 *
 * @since 1.0
 *
 * @return HappyForms_Message_Controller
 */
function happyforms_get_message_controller() {
	return HappyForms_Message_Controller::instance();
}

endif;

/**
 * Initialize the HappyForms_Message_Controller class immediately.
 */
happyforms_get_message_controller();
