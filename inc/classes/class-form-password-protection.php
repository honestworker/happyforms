<?php
class HappyForms_Form_Password_Protection {
	private static $instance;

	private $password_meta_key = 'password';

	private $frontend_styles = false;

	private $form = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_meta_fields', array( $this, 'add_fields' ), 10, 1 );
		add_filter( 'happyforms_setup_controls', array( $this, 'add_setup_controls' ), 10, 1 );
		add_action( 'happyforms_do_setup_control', array( $this, 'do_control' ), 10, 3 );
		add_filter( 'happyforms_form_template_path', array( $this, 'set_password_template_path'), 10, 2 );
		add_filter( 'happyforms_get_steps', array( $this, 'steps_add_password' ), 10, 2 );
		add_action( 'happyforms_step', array( $this, 'add_password_step' ) );
		add_filter( 'happyforms_get_form_data', array( $this, 'get_password_field' ), 10, 1 );
		add_filter( 'happyforms_update_form_data', array( $this, 'hash_password_field' ), 10, 1 );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );
		add_filter( 'happyforms_style_dependencies', array( $this, 'style_dependencies' ), 10, 2 );
		add_filter( 'happyforms_session_reset_callback', array( $this, 'set_session_reset_callback' ), 10, 3 );
	}

	public function add_fields( $fields ) {
		$fields['password_protect'] = array(
			'default'  => 0,
			'sanitize' => 'happyforms_sanitize_checkbox'
		);

		$fields[$this->password_meta_key] = array(
			'default'  => '',
			'sanitize' => ''
		);

		$fields['password_input_placeholder'] = array(
			'default'  => __( 'Enter your password', 'happyforms' ),
			'sanitize' => 'sanitize_text_field'
		);

		$fields['password_submit_button_label'] = array(
			'default'  => __( 'Access Form', 'happyforms' ),
			'sanitize' => 'sanitize_text_field'
		);

		$fields['password_error_message'] = array(
			'default'  => __( 'The password you entered is incorrect. Please try again.', 'happyforms' ),
			'sanitize' => 'esc_html',
		);

		return $fields;
	}

	public function add_setup_controls( $controls ) {
		$setup_controls = array(
			1550 => array(
				'field' => 'password_protect',
				'label' => __( 'Require password', 'happyforms' ),
				'type' => 'password-checkbox'
			),
			1551 => array(
				'type' => 'group_start',
				'trigger' => 'password_protect'
			),
			1552 => array(
				'field' => '',
				'type' => 'section_start',
				'id' => 'happyforms-password-protect-settings',
				'show_when' => 'password_protect',
			),
			1553 => array(
				'field' => $this->password_meta_key,
				'label' => __( 'Password', 'happyforms' ),
				'label_filled' => __( 'New password', 'happyforms' ),
				'type' => 'password'
			),
			1554 => array(
				'field' => 'password_input_placeholder',
				'label' => __( 'Input placeholder', 'happyforms' ),
				'type'  => 'password-text',
				'autocomplete' => 'off',
			),
			1555 => array(
				'field' => 'password_submit_button_label',
				'label' => __( 'Submit password button label', 'happyforms' ),
				'type'  => 'password-text',
				'autocomplete' => 'off',
			),
			1556 => array(
				'field' => 'password_error_message',
				'label' => __( 'Error message', 'happyforms' ),
				'type' => 'password-editor',
			),
			1557 => array(
				'type'  => 'group_end'
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $setup_controls );

		return $controls;
	}

	public function set_password_template_path( $template_path, $form ) {
		if ( $this->requires_password( $form ) && 'password' === happyforms_get_current_step( $form ) ) {
			$template_path = happyforms_get_include_folder() . '/templates/single-form-password.php';
		}

		return $template_path;
	}

	public function requires_password( $form ) {
		return ( 1 === intval( happyforms_get_form_property( $form, 'password_protect' ) ) );
	}

	/**
	 * Add password step to form's step system.
	 *
	 * @hooked filter `happyforms_get_steps`
	 *
	 * @param array $steps Array of steps.
	 * @param array $form  Form data.
	 *
	 * @return array Steps with password step.
	 */
	public function steps_add_password( $steps, $form ) {
		if ( $this->requires_password( $form ) ) {
			$steps[0] = 'password';
		}

		return $steps;
	}

	public function customize_enqueue_scripts() {
		wp_enqueue_script(
			'happyforms-password-protect',
			happyforms_get_plugin_url() . 'inc/assets/js/customize/password-protect.js',
			array( 'happyforms-customize' ),
			happyforms_get_version(),
			true
		);
	}

	/**
	 * Handle password step actions.
	 *
	 * @param array $form Form data.
	 *
	 * @hooked action `add_password_step`
	 *
	 * @return void
	 */
	public function add_password_step( $form ) {
		// Return early if not password step
		if ( 'password' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$session = happyforms_get_session();
		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$message_controller = happyforms_get_message_controller();
		$submission = $this->validate_form_password( $form, $_REQUEST );

		$response = array();

		// On password validation failure, display error and reset step
		if ( false === $submission ) {
			$error_message = $form['password_error_message'];
			$error_message = html_entity_decode( $error_message );
			$error_message = wp_unslash( $error_message );

			$session->add_error( $form_id, $error_message );
			$session->reset_step();

			$response['html'] = $form_controller->render( $form );

			// Send error response
			wp_send_json_error( $response );
		// On success, proceed to next step
		} else {
			$session->next_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			$message_controller->send_json_success( $response, $submission, $form );
		}

		// Send success response
		$message_controller->send_json_success( $response, array(), $form );
	}

	/**
	 * Validates entered password with one stored in form's settings.
	 *
	 * @param array $form    Form data.
	 * @param array $request Request data ($_REQUEST).
	 *
	 * @return boolean
	 */
	public function validate_form_password( $form, $request = array() ) {
		if ( ! isset( $request['happyforms_password'] ) || empty( $request['happyforms_password'] ) ) {
			return false;
		}

		/**
		 * `wp_check_password` function allows to check plain text password which is what we get from $_REQUEST
		 * against password hash. More info: https://developer.wordpress.org/reference/functions/wp_check_password/
		 *
		 * If validation passes, we immediately return true.
		 */
		if ( wp_check_password( $request['happyforms_password'], happyforms_get_meta( $form['ID'], 'password', true ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Since we store password hash in form meta, we don't want this hash to appear in password control in form builder.
	 * That's why we reset it to empty string. The method hooked to update routine then takes care of either updating the
	 * password hash or leaving it at current value.
	 *
	 * @hooked filter `happyforms_get_form_data`
	 *
	 * @param array $form_array Form data before passing it to form builder.
	 *
	 * @return array Filtered form data.
	 */
	public function get_password_field( $form_array ) {
		if ( isset( $form_array[$this->password_meta_key] ) ) {
			$form_array[$this->password_meta_key] = '';
		}

		return $form_array;
	}

	/**
	 * Hash plain text password entered by user before saving form data to database.
	 *
	 * @hooked filter `happyforms_update_form_data`
	 *
	 * @param array $update_data Form update data before storing to database.
	 *
	 * @return array Filtered form update data.
	 */
	public function hash_password_field( $update_data ) {
		$prefixed_meta = '_happyforms_' . $this->password_meta_key;

		if ( isset( $update_data[$prefixed_meta] ) ) {
			$value = $update_data[$prefixed_meta];

			/**
			 * If password was updated, there will be an array index in `$update_data`.
			 * If it's not empty, it means it was updated so we hash password and update value in array.
			 */
			if ( ! empty( $value ) ) {
				$hashed_password = wp_hash_password( $value );
				$update_data[$prefixed_meta] = $hashed_password;
			/**
			 * If it's empty, the password was not updated and to prevent storing empty string, we unset
			 * it from `$update_data`.
			 */
			} else {
				unset( $update_data[$prefixed_meta] );
			}
		}

		return $update_data;
	}

	/**
	 * Password control in form builder is a bit special, so it has its own template specified here.
	 *
	 * @hooked action `happyforms_do_setup_control`
	 *
	 * @param array $control Control data.
	 * @param array $field   Meta field data.
	 * @param int   $index   Numeric index of the control.
	 *
	 * @return void
	 */
	public function do_control( $control, $field, $index ) {
		$type = $control['type'];

		switch( $control['type'] ) {
			case 'password':
				$form = happyforms_customize_get_current_form();

				if ( $form[ 'password_protect' ] == 1 ) {
					require( happyforms_get_include_folder() . '/templates/customize-controls/password.php' );
				}

				break;
			case 'password-checkbox':
			case 'password-text':
			case 'password-editor':
				$form = happyforms_customize_get_current_form();

				if ( $form[ 'password_protect' ] == 1 ) {
					$path = happyforms_get_core_folder() . '/templates/customize-controls/setup';
					$type = str_replace( 'password-', '', $type );

					require( "{$path}/{$type}.php" );
				}

				break;
			default:
				break;
		}
	}

	public function style_dependencies( $deps, $forms ) {
		$requires_password = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $this->requires_password( $form ) ) {
				$requires_password = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() &&! $requires_password ) {
			return $deps;
		}

		wp_register_style(
			'happyforms-password',
			happyforms_get_plugin_url() . 'inc/assets/css/frontend/password.css',
			array(), happyforms_get_version()
		);

		$deps[] = 'happyforms-password';

		return $deps;
	}

	/**
	 * Prevents the form from resetting to password step when validation errors are shown
	 * and form can't be submitted. We direct the form stepper to first step instead in this case.
	 *
	 * @hooked filter `happyforms_session_reset_callback`
	 *
	 * @param array  $params  Parameters for callback function and arguments.
	 * @param object $session Session class instance.
	 * @param array  $form    Form data.
	 *
	 * @return array Filtered params.
	 */
	public function set_session_reset_callback( $params, $session, $form ) {
		if ( ! $this->requires_password( $form ) ) {
			return $params;
		}

		$params['function'] = array( $session, 'set_step' );
		$params['args']     = array( 1 );

		return $params;
	}

}

if ( ! function_exists( 'happyforms_upgrade_get_password_protection' ) ) :

	function happyforms_upgrade_get_password_protection() {
		return HappyForms_Form_Password_Protection::instance();
	}

endif;

happyforms_upgrade_get_password_protection();
