<?php

class HappyForms_Service {

	protected $credentials = array();
	protected $data = array();

	public $id = '';
	public $label = '';
	public $group = '';

	public function set_credentials( $credentials = array(), $raw = array() ) {
		$this->credentials = $credentials;
	}

	public function get_credentials() {
		return $this->credentials;
	}

	public function is_connected() {
		return false;
	}

	public function admin_widget( $previous_credentials = array() ) {
		// Noop
	}

	public function configure() {
		// Noop
	}

}
