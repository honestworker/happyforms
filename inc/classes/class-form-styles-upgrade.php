<?php

class HappyForms_Form_Styles_Upgrade {

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_style_fields', array( $this, 'get_fields' ) );
		add_filter( 'happyforms_style_controls', array( $this, 'get_controls' ) );
	}

	public function get_fields( $fields ) {
		$styles_fields = array(
			'color_rating_star' => array(
				'default' => '#cccccc',
				'sanitize' => 'sanitize_text_field',
				'target' => 'css_var',
				'variable' => '--happyforms-color-rating',
			),
			'color_rating_star_hover' => array(
				'default' => '#000000',
				'sanitize' => 'sanitize_text_field',
				'target' => 'css_var',
				'variable' => '--happyforms-color-rating-hover',
			),
		);

		$fields = array_merge( $fields, $styles_fields );

		return $fields;
	}

	public function get_controls( $controls ) {
		$styles_controls = array(
      4800 => array(
        'type' => 'divider',
        'label' => __( 'Rating', 'happyforms' ),
        'id' => 'rating',
      ),
			4900 => array(
				'type' => 'color',
				'label' => __( 'Rating star color', 'happyforms' ),
				'field' => 'color_rating_star',
			),
			5000 => array(
				'type' => 'color',
				'label' => __( 'Rating star color on hover', 'happyforms' ),
				'field' => 'color_rating_star_hover',
			)
		);

		$controls = happyforms_safe_array_merge( $controls, $styles_controls );

		return $controls;
	}

}

if ( ! function_exists( 'happyforms_get_styles_upgrade' ) ):

function happyforms_get_styles_upgrade() {
	return HappyForms_Form_Styles_Upgrade::instance();
}

endif;

happyforms_get_styles_upgrade();
