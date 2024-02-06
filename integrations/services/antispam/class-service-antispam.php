<?php

class HappyForms_Service_AntiSpam extends HappyForms_Service {

	public $id = 'antispam';
	public $supports_multiple = false;
	public $active_service_option_name = '';
	public $active_service = false;

	public function __construct() {
		$this->label = __( 'Anti-Spam', 'happyforms' );
		$this->active_service_option_name = "_happyforms_{$this->id}_service_active";
	}

	public function set_active_service( $service_id ) {
		update_option( $this->active_service_option_name, $service_id );
	}

	public function get_active_service() {
		$service = get_option( $this->active_service_option_name, 'recaptchav3' );

		if ( empty( $service ) ) {
			$service = 'recaptchav3';
		}

		if ( ! empty( $service ) ) {
			$service = happyforms_get_integrations()->get_service( $service );
		}

		return $service;
	}

	public function reset_active_service() {
		update_option( $this->active_service_option_name, '' );
	}

	public function configure() {
		$this->active_service = $this->get_active_service();
		$this->load();
	}

	public function load() {
		$active_service = null;

		if ( false === $this->active_service ) {
			$recaptcha_v2_service     = happyforms_get_integrations()->get_service( 'recaptcha' );
			$recaptcha_v2_credentials = $recaptcha_v2_service->get_credentials();

			if ( isset( $recaptcha_v2_credentials['enabled'] ) ) {
				if ( 1 === (int) $recaptcha_v2_credentials['enabled'] ) {
					$active_service = 'recaptcha';
				}

				if ( '' === $recaptcha_v2_credentials['enabled'] && ! empty( $recaptcha_v2_credentials['site'] ) && ! empty( $recaptcha_v2_credentials['secret'] ) ) {
					$active_service = 'recaptcha';
				}

				unset( $recaptcha_v2_credentials['enabled'] );
				$recaptcha_v2_service->set_credentials( $recaptcha_v2_credentials );

				happyforms_get_integrations()->write_credentials();
			}

			if ( ! is_null( $active_service ) ) {
				$this->set_active_service( $active_service );
				$this->active_service = $this->get_active_service();
			}
		}

		require_once( happyforms_get_integrations_folder() . '/services/antispam/class-integration-antispam.php' );

		if ( $this->active_service->is_connected() ) {
			$this->active_service->load();
		}
	}
}