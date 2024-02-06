<?php

class HappyForms_Service_Recaptcha extends HappyForms_Service {

	public $id = 'recaptcha';
	public $group = 'antispam';

	public $captcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
	public $captcha_field = 'g-recaptcha-response';

	public function __construct() {
		$this->label = __( 'v2', 'happyforms' );
	}

	public function set_credentials( $credentials = array(), $raw = array() ) {
		$this->credentials = array(
			'site' => '',
			'secret' => ''
		);

		if ( isset( $credentials['site'] ) && ! empty( $credentials['site'] ) ) {
			$this->credentials['site'] = $credentials['site'];
		}

		if ( isset( $credentials['secret'] ) && ! empty( $credentials['secret'] ) ) {
			$this->credentials['secret'] = $credentials['secret'];
		}

		if ( isset( $credentials['enabled'] ) ) {
			$this->credentials['enabled'] = $credentials['enabled'];
		}
	}

	public function is_connected() {
		$authenticated = (
			! empty( $this->credentials['site'] )
			&& ! empty( $this->credentials['secret'] )
		);

		return $authenticated;
	}

	public function get_recaptcha_script_url() {
		$recaptcha_url = 'https://www.google.com/recaptcha/api.js';
		$recaptcha_locale = happyforms_get_recaptcha_locale();

		if ( $recaptcha_locale ) {
			$recaptcha_url = add_query_arg( 'hl', $recaptcha_locale, $recaptcha_url );
		}

		return $recaptcha_url;
	}

	public function get_frontend_script_url() {
		return happyforms_get_plugin_url() . 'integrations/services/recaptcha/frontend.js';
	}

	public function admin_widget( $previous_credentials = array() ) {
		require_once( happyforms_get_integrations_folder() . '/services/recaptcha/partial-widget.php' );
	}

	public function configure() {
		$this->load();
	}

	public function load() {
		$antispam = happyforms_get_integrations()->get_service( 'antispam' );

		if ( ! $antispam->get_active_service()->is_connected() ) {
 			return;
 		}

		if ( $antispam->get_active_service() && $this->id === $antispam->get_active_service()->id ) {
			require_once( happyforms_get_integrations_folder() . '/services/recaptcha/class-integration-recaptcha.php' );
		}
	}

	public function validate_submission( $form ) {
		$secret_key = $form['captcha_secret_key'];
		$captcha_value = isset ( $_REQUEST[$this->captcha_field] ) ? $_REQUEST[$this->captcha_field] : '';
		$captcha_value = sanitize_text_field( $captcha_value );
		$request_body = array(
			'secret' => $secret_key,
			'response' => $captcha_value,
			'ip' => $_SERVER['REMOTE_ADDR'],
		);

		$request = wp_remote_post( $this->captcha_verify_url, array( 'body' => $request_body ) );
		$response = wp_remote_retrieve_body( $request );

		if ( empty( $response ) ) {
			return new WP_Error( 'captcha', 'captcha_invalid_configuration' );
		}

		$response = json_decode( $response, true );

		if ( ! $response['success'] ) {
			$configuration_errors = array_intersect( array(
				'missing-input-secret', 'invalid-input-secret', 'bad-request'
			), $response['error-codes'] );
			$value_errors = array_intersect( array(
				'missing-input-response', 'invalid-input-response'
			), $response['error-codes'] );
			if ( count( $configuration_errors ) > 0 ) {
				return new WP_Error( 'captcha', 'captcha_invalid_configuration' );
			} else if ( count( $value_errors ) > 0 ) {
				return new WP_Error( 'captcha', 'captcha_not_verified' );
			}
		}

		return $captcha_value;
	}

}