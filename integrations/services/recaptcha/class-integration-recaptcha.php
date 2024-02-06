<?php

class HappyForms_Compat_Recaptcha {

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
		$this->service = happyforms_get_integrations()->get_service( 'recaptcha' );

		add_filter( 'happyforms_form_has_captcha', array( $this, 'has_captcha' ), 10, 2 );
		add_action( 'happyforms_parts_after', 'happyforms_recaptcha' );
		add_filter( 'happyforms_setup_controls', array( $this, 'setup_controls' ) );
		add_filter( 'happyforms_style_fields', array( $this, 'get_style_fields' ) );
		add_filter( 'happyforms_style_controls', array( $this, 'get_style_controls' ) );
	}

	public function has_captcha( $has_captcha, $form ) {
		$has_captcha = $form['captcha'] || happyforms_is_preview();

		return $has_captcha;
	}

	public function setup_controls( $controls ) {
		$setup_controls = array(
			1501 => array(
				'type' => 'group_start',
				'trigger' => 'captcha'
			),
			1502 => array(
				'type' => 'text',
				'label' => __( 'Label', 'happyforms' ),
				'field' => 'captcha_label',
				'autocomplete' => 'off',
			),
			1503 => array(
				'type' => 'group_end'
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $setup_controls );

		return $controls;
	}

	public function get_style_fields( $fields ) {
		$fields['captcha_theme'] = array(
			'default' => 'light',
			'options' => array(
				'light' => __( 'Light color', 'happyforms' ),
				'dark' => __( 'Dark color', 'happyforms' )
			),
			'sanitize' => 'sanitize_text_field',
			'target' => 'recaptcha'
		);

		return $fields;
	}

	public function get_style_controls( $controls ) {
		$style_controls = array(
			511 => array(
				'type' => 'buttonset',
				'label' => __( 'reCAPTCHA theme', 'happyforms' ),
				'field' => 'captcha_theme'
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $style_controls );

		return $controls;
	}

}

if ( ! function_exists( 'happyforms_get_integration_recaptcha' ) ):

function happyforms_get_integration_recaptcha() {
	return HappyForms_Compat_Recaptcha::instance();
}

endif;

happyforms_get_integration_recaptcha();
