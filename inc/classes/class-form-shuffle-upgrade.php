<?php
class HappyForms_Form_Shuffle_Parts_Upgrade {
	private static $instance;

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
		add_filter( 'happyforms_get_form_parts', array( $this, 'get_form_parts' ), 10, 2 );
		add_filter( 'happyforms_parts_with_choice_shuffle', array( $this, 'get_parts_with_choice_shuffle' ) );
	}

	public function get_parts_with_choice_shuffle( $supported_parts ) {
		$parts = array(
			'table',
			'poll',
			'rank_order'
		);

		return array_merge( $supported_parts, $parts );
	}

	public function add_fields( $fields ) {
		$fields['shuffle_parts'] = array(
			'default'  => 0,
			'sanitize' => 'happyforms_sanitize_checkbox'
		);

		return $fields;
	}

	public function add_setup_controls( $controls ) {
		$setup_controls = array(
			1450 => array(
				'field' => 'shuffle_parts',
				'label' => __( 'Shuffle order of fields', 'happyforms' ),
				'type' => 'checkbox'
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $setup_controls );

		return $controls;
	}

	public function get_form_parts( $parts, $form ) {
		if ( is_customize_preview() ) {
			return $parts;
		}

		if ( ! happyforms_get_form_property( $form, 'shuffle_parts' ) ) {
			return $parts;
		}

		if ( happyforms_get_stepper()->is_multistep( $form ) ) {
			return $parts;
		}

		$parts = $this->shuffle_form_parts( $parts, $form );

		return $parts;
	}

	public function shuffle_form_parts( $parts ) {
		$form_shuffle = happyforms_get_shuffle_parts();

		$shuffled = happyforms_shuffle_array( $parts, $form_shuffle->get_random_seed() );
		$index = 0;

		foreach ( $shuffled as $key => $part ) {
			$shuffled[$key]['width'] = $parts[$index]['width'];
			$index++;
		}

		$parts = $shuffled;

		return $parts;
	}

}

if ( ! function_exists( 'happyforms_upgrade_get_shuffle_parts' ) ) :

function happyforms_upgrade_get_shuffle_parts() {
	return HappyForms_Form_Shuffle_Parts_Upgrade::instance();
}

endif;

happyforms_upgrade_get_shuffle_parts();
