<?php

class HappyForms_Integration {

	public $service = '';

	private $default_credentials = array();

	public function __construct() {
		$this->integrations = happyforms_get_integrations();
	}

	public function is_enabled() {
		$credentials = $this->integrations->get_credentials();

		if ( empty( $credentials ) || ! isset( $credentials[$this->service] ) ) {
			return false;
		}

		if ( empty( $credentials[$this->service] ) ) {
			return false;
		}

		return true;
	}

	public function get_credentials() {
		$credentials = $this->integrations->get_credentials();
		$credentials = wp_parse_args( $credentials[$this->service], $this->default_credentials );

		return $credentials;
	}

	public function authorize( $credentials ) {
		return $this->integrations->authorize( $this->service, $credentials );
	}

	public function deauthorize() {
		return $this->integrations->deauthorize( $this->service );
	}

}
