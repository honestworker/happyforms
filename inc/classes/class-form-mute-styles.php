<?php
class HappyForms_Form_Mute_Styles {
	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_style_fields', array( $this, 'add_fields' ), 10, 1 );
		add_filter( 'happyforms_style_controls', array( $this, 'add_style_controls' ), 10, 1 );
		add_filter( 'happyforms_form_class', array( $this, 'form_html_class' ), 999, 2 );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );
		add_action( 'happyforms_do_style_control', array( $this, 'do_deprecated_control' ), 10, 3 );
	}

	public function are_styles_muted( $form ) {
		if ( happyforms_get_form_property( $form, 'mute_styles' ) ) {
			return true;
		}
	}

	public function form_html_class( $class, $form ) {
		if ( $this->are_styles_muted( $form ) ) {
			$styles_class = array_search( 'happyforms-styles', $class );

			if ( false !== $styles_class ) {
				unset( $class[$styles_class] );
			}
		}

		return $class;
	}

	public function add_fields( $fields ) {
		$fields['mute_styles'] = array(
			'default'  => 0,
			'value'   => 1,
			'target' => '',
			'sanitize' => 'happyforms_sanitize_checkbox'
		);

		return $fields;
	}

	public function add_style_controls( $controls ) {
		$style_controls = array(
			101 => array(
				'field' => 'mute_styles',
				'label' => __( 'Use theme styles', 'happyforms' ),
				'type' => 'mute-style-checkbox',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $style_controls );

		return $controls;
	}

	public function do_deprecated_control( $control, $field, $index ) {
		$type = $control['type'];

		switch( $control['type'] ) {
			case 'mute-style-checkbox':
				$form = happyforms_customize_get_current_form();

				if ( happyforms_is_falsy( $form['mute_styles'] ) ) {
					break;
				}

				$path = happyforms_get_core_folder() . '/templates/customize-controls/style';

				require( "{$path}/checkbox.php" );
				break;
			default:
				break;
		}
	}

	public function customize_enqueue_scripts( $deps ) {
		$form = happyforms_customize_get_current_form();

		if ( happyforms_is_truthy( $form['mute_styles'] ) ) {
			wp_enqueue_script(
				'happyforms-mute-styles',
				happyforms_get_plugin_url() . 'inc/assets/js/customize/mute-styles.js',
				$deps, happyforms_get_version(), true
			);
			
			wp_enqueue_style(
				'happyforms-mute-styles',
				happyforms_get_plugin_url() . 'inc/assets/css/customize-mute-styles.css',
				array(), happyforms_get_version()
			);
		}
	}

}

if ( ! function_exists( 'happyforms_upgrade_get_mute_styles' ) ) :

	function happyforms_upgrade_get_mute_styles() {
		return HappyForms_Form_Mute_Styles::instance();
	}

endif;

happyforms_upgrade_get_mute_styles();
