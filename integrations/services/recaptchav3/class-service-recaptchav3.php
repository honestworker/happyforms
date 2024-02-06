<?php

class HappyForms_Service_RecaptchaV3 extends HappyForms_Service {

	public $id = 'recaptchav3';
	public $group = 'antispam';

	public $captcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
	public $captcha_field = 'g-recaptcha-response';
	public $captcha_action = 'happyforms_submit';

	public function __construct() {
		$this->label = __( 'v3', 'happyforms' );
	}

	public function set_credentials( $credentials = array(), $raw = array() ) {
		$this->credentials = array(
			'site' => '',
			'secret' => '',
			'min_score' => '0.5',
		);

		if ( isset( $credentials['site'] ) && ! empty( $credentials['site'] ) ) {
			$this->credentials['site'] = $credentials['site'];
		}

		if ( isset( $credentials['secret'] ) && ! empty( $credentials['secret'] ) ) {
			$this->credentials['secret'] = $credentials['secret'];
		}

		if ( isset( $credentials['min_score'] ) && ! empty( $credentials['min_score'] ) ) {
			$this->credentials['min_score'] = $credentials['min_score'];
		}

		if ( ! empty( $raw ) ) {
			$this->credentials['min_score'] = ( isset( $raw['min_score'] ) ) ? $raw['min_score'] : $credentials['min_score'];
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
		$recaptcha_url = add_query_arg( 'render', $this->credentials['site'], $recaptcha_url );

		return $recaptcha_url;
	}

	public function get_frontend_script_url() {
		return happyforms_get_plugin_url() . 'integrations/services/recaptchav3/frontend.js';
	}

	public function admin_widget( $previous_credentials = array() ) {
		require_once( happyforms_get_integrations_folder() . '/services/recaptchav3/partial-widget.php' );
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
			require_once( happyforms_get_integrations_folder() . '/services/recaptchav3/class-integration-recaptchav3.php' );
		}
	}

	public function validate_submission( $form ) {
		$secret_key = $form['captcha_secret_key'];
		$captcha_value = isset ( $_REQUEST[$this->captcha_field] ) ? $_REQUEST[$this->captcha_field] : '';
		$captcha_value = sanitize_text_field( $captcha_value );
		$request_body = array(
			'secret' => $secret_key,
			'response' => $captcha_value,
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
		} else {
			if ( ! isset( $response['action'] ) || $response['action'] !== $this->captcha_action ) {
				return new WP_Error( 'captcha', 'captcha_invalid_action' );
			}

			if ( ! isset( $response['score'] ) || (float) $response['score'] < (float) $this->credentials['min_score'] ) {
				return new WP_Error( 'captcha', 'captcha_insufficient_score' );
			}
		}

		return $captcha_value;
	}

}