<?php

class HappyForms_Integration_RecaptchaV3 {

	private static $instance;

	private $service;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function __construct() {
		require_once( happyforms_get_integrations_folder() . '/services/antispam/helpers-recaptcha.php' );
	}

	public function hook() {
		$this->service = happyforms_get_integrations()->get_service( 'recaptchav3' );

		add_filter( 'happyforms_form_has_captcha', array( $this, 'has_captcha' ), 10, 2 );
		add_action( 'happyforms_parts_after', 'happyforms_recaptcha' );
	}

	public function has_captcha( $has_captcha, $form ) {
		$has_captcha = $form['captcha'] || happyforms_is_preview();

		return $has_captcha;
	}

}

if ( ! function_exists( 'happyforms_get_integration_recaptchav3' ) ):

function happyforms_get_integration_recaptchav3() {
	return HappyForms_Integration_RecaptchaV3::instance();
}

endif;

happyforms_get_integration_recaptchav3();
