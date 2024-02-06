<?php

class HappyForms_Form_Sessions {

	private static $instance;

	public $action_clear = 'happyforms-session-clear';
	public $action_alert = 'happyforms-session-alert';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_meta_fields', array( $this, 'meta_fields' ) );
		add_filter( 'happyforms_setup_controls', array( $this, 'setup_controls' ) );
		add_filter( 'happyforms_email_controls', array( $this, 'email_controls' ) );
		add_filter( 'happyforms_messages_controls', array( $this, 'messages_controls' ) );
		add_filter( 'happyforms_get_form_attributes', array( $this, 'form_attributes' ), 10, 2 );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_filter( 'happyforms_frontend_settings', array( $this, 'session_settings' ), 10, 1 );
		add_action( 'wp_ajax_' . $this->action_clear, array( $this, 'ajax_clear_session' ) );
		add_action( 'wp_ajax_nopriv_' . $this->action_clear, array( $this, 'ajax_clear_session' ) );
		add_action( 'happyforms_form_abandoned', array( $this, 'abandon_alert' ) );

		add_action( 'happyforms_form_submit_after', array( $this, 'form_submit' ) );
		add_filter( 'happyforms_messages_fields', array( $this, 'get_messages_fields' ) );

		add_filter( 'happyforms_get_form_data', array( $this, 'transition_save_draft_numeric_input' ), 99 );
	}

	public function meta_fields( $fields ) {
		global $current_user;

		$resumable_fields = array(
			'save_abandoned_responses' => array(
				'default' => 0,
				'sanitize' => 'happyforms_sanitize_checkbox',
			),
			'abandoned_response_whitelist' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field',
			),
			'abandoned_response_expire' => array(
				'default' => 'week',
				'sanitize' => array(
					'happyforms_sanitize_choice',
					array( 'day', 'week', 'month' ),
				),
			),
			'abandoned_resume_save_button' => array(
				'default' => 0,
				'sanitize' => 'happyforms_sanitize_checkbox',
			),
			'abandoned_resume_response_expire' => array(
				'default' => '',
				'sanitize' => 'happyforms_sanitize_intval_empty'
			),
			'abandoned_resume_send_alert_email' => array(
				'default' => 0,
				'sanitize' => 'happyforms_sanitize_checkbox',
			),
			'abandoned_resume_email_sender_address' => array(
				'default' => ( $current_user->user_email ) ? $current_user->user_email : '',
				'sanitize' => 'happyforms_sanitize_emails',
			),
			'abandoned_resume_email_reply_to' => array(
				'default' => ( $current_user->user_email ) ? $current_user->user_email : '',
				'sanitize' => 'happyforms_sanitize_emails',
			),
			'abandoned_resume_email_from_name' => array(
				'default' => get_bloginfo( 'name' ),
				'sanitize' => 'sanitize_text_field',
			),
			'abandoned_resume_email_respondent_address' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field',
			),
			'abandoned_resume_email_subject' => array(
				'default' => __( 'You left before completing the form', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'abandoned_resume_email_content' => array(
				'default' => __( 'The form you were filling out didn\'t get completed. Did something go wrong?', 'happyforms' ),
				'sanitize' => 'esc_html',
			),
		);

 		$fields = array_merge( $fields, $resumable_fields );

 		return $fields;
	}

	public function get_messages_fields( $fields ) {
		$messages_fields = array(
			'abandoned_resume_save_button_label' => array(
				'default' => __( 'Save draft', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'abandoned_resume_return_message' => array(
				'default' => __( "Welcome back. We've saved your partially complete reply from earlier.", 'happyforms' ),
				'sanitize' => 'esc_html',
			),
			'abandoned_resume_clear_all_label' => array(
				'default' => __( 'Clear', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
		);

		$fields = array_merge( $fields, $messages_fields );

		return $fields;
	}

	public function setup_controls( $controls ) {
		$setup_controls = array(
			1660 => array(
				'type' => 'number',
				'label' => __( 'Let submitters save a draft for set number of days', 'happyforms' ),
				'min' => 0,
				'field' => 'abandoned_resume_response_expire',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $setup_controls );

 		return $controls;
	}

	public function email_controls( $controls ) {
		$email_controls = array(
			1660 => array(
				'type' => 'checkbox',
				'label' => __( 'Send abandonment email', 'happyforms' ),
				'field' => 'abandoned_resume_send_alert_email',
			),
			1661 => array(
				'type' => 'group_start',
				'trigger' => 'abandoned_resume_send_alert_email'
			),
			1662 => array(
				'type' => 'text',
				'label' => __( 'From email address', 'happyforms' ),
				'field' => 'abandoned_resume_email_sender_address',
			),
			1663 => array(
				'type' => 'text',
				'label' => __( 'Reply email address', 'happyforms' ),
				'field' => 'abandoned_resume_email_reply_to',
			),
			1664 => array(
				'type' => 'email-parts-list',
				'label' => __( 'To email address', 'happyforms' ),
				'field' => 'abandoned_resume_email_respondent_address',
			),
			1665 => array(
				'type' => 'text',
				'label' => __( 'Email display name', 'happyforms' ),
				'field' => 'abandoned_resume_email_from_name',
			),
			1666 => array(
				'type' => 'text',
				'label' => __( 'Email subject', 'happyforms' ),
				'field' => 'abandoned_resume_email_subject',
			),
			1667 => array(
				'type' => 'editor',
				'label' => __( 'Email content', 'happyforms' ),
				'field' => 'abandoned_resume_email_content',
			),
			1668 => array(
				'type' => 'group_end'
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $email_controls );

 		return $controls;
	}

	public function messages_controls( $controls ) {
		$message_controls = array(
			2261 => array(
				'type' => 'text',
				'label' => __( 'Save draft reply', 'happyforms' ),
				'field' => 'abandoned_resume_save_button_label',
			),
			120 => array(
				'type' => 'escaped_text',
				'label' => __( 'Submitter has returned to a draft', 'happyforms' ),
				'field' => 'abandoned_resume_return_message',
			),
			2060 => array(
				'type' => 'text',
				'label' => __( 'Clear saved draft reply', 'happyforms' ),
				'field' => 'abandoned_resume_clear_all_label',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $message_controls );

		return $controls;
	}

	public function has_sessions( $form ) {
		$has_sessions = ( $this->is_resumable( $form ) );

		return $has_sessions;
	}

	public function is_abandonable( $form ) {
		$save_entries = apply_filters( 'happyforms_save_entries', true, $form );

		return $save_entries && (
			happyforms_form_is_available( $form ) &&
			happyforms_get_form_property( $form, 'save_abandoned_responses' ) &&
			! happyforms_get_form_status()->is_archived( $form )
		);
	}

	public function is_resumable( $form ) {
		$save_entries = apply_filters( 'happyforms_save_entries', true, $form );

		return $save_entries && (
			happyforms_form_is_available( $form ) &&
			! ( '' === happyforms_get_form_property( $form, 'abandoned_resume_response_expire' ) ) &&
			! happyforms_get_form_status()->is_archived( $form )
		);
	}

	public function has_abandonment_alerts( $form ) {
		return (
			$this->is_resumable( $form ) &&
			happyforms_get_form_property( $form, 'abandoned_resume_send_alert_email' )
		);
	}

	public function form_attributes( $attrs, $form ) {
		$step = happyforms_get_current_step( $form );
		$black_listed = array( 'password', 'review' );

		if ( in_array( $step, $black_listed ) ) {
			return $attrs;
		}

		if ( $this->is_resumable( $form ) ) {
			$attrs['data-happyforms-resumable'] = '';
		}

		if ( $this->has_abandonment_alerts( $form ) ) {
			$attrs['data-happyforms-abandonment-alerts'] = '';
		}

		return $attrs;
	}

	public function script_dependencies( $deps, $forms ) {
		$has_sessions = false;

 		foreach ( $forms as $form ) {
			if ( $this->has_sessions( $form ) ) {
				$has_sessions = true;
				break;
			}
		}

 		if ( happyforms_is_preview() || ! $has_sessions ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-cookies',
			happyforms_get_plugin_url() . 'inc/assets/js/lib/js.cookie.js',
			array(), happyforms_get_version(), true
		);

 		wp_register_script(
			'happyforms-sessions',
			happyforms_get_plugin_url() . 'inc/assets/js/frontend/sessions.js',
			array( 'jquery', 'happyforms-cookies' ), happyforms_get_version(), true
		);

 		$deps[] = 'happyforms-sessions';

 		return $deps;
	}

	public function session_settings( $data ) {
		$session_controller = happyforms_get_session_controller();

		$data['sessionTimeout'] = 500;
		$data['cookie'] = $session_controller->cookie;
		$data['domain'] = COOKIE_DOMAIN;
		$data['actionSession'] = $session_controller->action;
		$data['actionSessionClear'] = $this->action_clear;

		return $data;
	}

	public function ajax_clear_session() {
		$html = '';

		if ( ! isset( $_GET['form_id'] ) || empty( $_GET['form_id'] ) ) {
			wp_send_json_error();
		}

		$form_id = $_GET['form_id'];
		$form_controller = happyforms_get_form_controller();
		$form = $form_controller->get( $form_id );

		if ( ! $form ) {
			wp_send_json_error();
		}

		$html = $form_controller->render( $form );

		$response = array(
			'html' => $html,
		);

		wp_send_json_success( $response );
	}

	public function abandon_alert() {
		if ( ! isset( $_POST['sessions'] ) ) {
			return;
		}

		$sessions = json_decode( wp_unslash( $_POST['sessions'] ) );

		if ( ! $sessions ) {
			return;
		}

		$form_controller = happyforms_get_form_controller();
		$session_controller = happyforms_get_session_controller();

		foreach( $sessions as $form_id => $session_id ) {
			$form = $form_controller->get( $form_id );

			if ( ! $form || ! $this->has_abandonment_alerts( $form ) ) {
				continue;
			}

			$session_controller->send_alert( $session_id );
		}
	}

	public function form_submit( $form ) {
		if ( ! happyforms_is_preview() && ! $this->is_resumable( $form ) ) {
			return;
		}

		include( happyforms_get_include_folder() . '/templates/partials/form-save-session-button.php' );
	}

	public function transition_save_draft_numeric_input( $form ) {
		if ( isset( $form['allow_abandoned_resume'] ) && happyforms_is_falsy( $form['allow_abandoned_resume'] ) ) {
			$form['abandoned_resume_response_expire'] = '';
		}

		if ( is_string( $form['abandoned_resume_response_expire'] ) ) {
			switch( $form['abandoned_resume_response_expire'] ) {
				case 'month':
					$form['abandoned_resume_response_expire'] = 31;
					break;
				case 'week':
					$form['abandoned_resume_response_expire'] = 7;
					break;
				case 'day':
					$form['abandoned_resume_response_expire'] = 1;
					break;
			}
		}

		return $form;
	}
}

if ( ! function_exists( 'happyforms_get_form_sessions' ) ):

function happyforms_get_form_sessions() {
	return HappyForms_Form_Sessions::instance();
}

endif;

happyforms_get_form_sessions();
