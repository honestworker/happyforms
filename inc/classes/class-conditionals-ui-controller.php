<?php
class HappyForms_Conditionals_UI_Controller {

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_setup_controls', array( $this, 'append_control_settings' ) );
		add_filter( 'happyforms_email_controls', array( $this, 'append_control_settings' ) );
		add_filter( 'happyforms_messages_controls', array( $this, 'append_control_settings' ) );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_customize_templates' ) );
		add_filter( 'happyforms_customize_part_logic_template_path', array( $this, 'set_part_logic_template_path' ) );
		add_filter( 'happyforms_part_customize_footer_template_path', array( $this, 'set_part_customize_footer_template_path' ) );
		add_filter( 'happyforms_customize_part_choice_logic_template_path', array( $this, 'set_part_choice_logic_template_path' ) );
		add_filter( 'happyforms_customize_part_choice_footer_template_path', array( $this, 'set_part_choice_footer_template_path' ) );
	}

	/**
	 * This sets the template for logic group by hooking to a filter. Free version by default shows an upsell here
	 * so to show the actual UI, we need to override it.
	 *
	 * @hooked filter `happyforms_customize_part_logic_template_path`
	 *
	 * @param string Default file path.
	 *
	 * @return string File path.
	 */
	public function set_part_logic_template_path( $path ) {
		$path = happyforms_get_include_folder() . '/templates/customize-form-part-logic.php';

		return $path;
	}

	public function set_part_choice_logic_template_path( $path ) {
		$path = happyforms_get_include_folder() . '/templates/customize-form-part-choice-logic.php';

		return $path;
	}

	public function set_part_customize_footer_template_path( $path ) {
		$path = happyforms_get_include_folder() . '/templates/customize-form-part-footer.php';

		return $path;
	}

	public function set_part_choice_footer_template_path( $path ) {
		$path = happyforms_get_include_folder() . '/templates/customize-form-part-choice-footer.php';

		return $path;
	}

	/**
	 * Get all form parts that support logic groups.
	 *
	 * @return array
	 */
	public function get_supported_part_types() {
		$part_library = happyforms_get_part_library();
		$parts = $part_library->get_parts();

		return apply_filters( 'happyforms_conditionals_supported_parts', array_keys( $parts ) );
	}

	/**
	 * Returns the list of form builder controls and the data for parsing the control. The control ID is used
	 * as array key. Required items in array are `type` and `then_text` for each control.
	 *
	 * `type` can be:
	 * - `set` – renders "Then" part as text input
	 * - `select` – renders "Then" part as a dropdown
	 * - `template` – allows you to render custom Underscore template for the whole "Then" part of logic group. This should
	 *                be the ID of that template
	 *
	 * @return void
	 */
	public function get_enabled_controls() {
		$supported_controls = array(
			'email_recipient' => array(
				'type' => 'set',
				'then_text' => __( 'Then email address is…', 'happyforms' )
			),
			'email_bccs' => array(
				'type' => 'set',
				'then_text' => __( 'Then email address is…', 'happyforms' )
			),
			'alert_email_reply_to' => array(
				'type' => 'template',
				'template' => 'email-part-lists-then-value',
				'then_text' => __( 'Email field is…', 'happyforms' )
			),
			'confirmation_email_respondent_address' => array(
				'type' => 'template',
				'template' => 'email-part-lists-then-value',
				'then_text' => __( 'Email field is…', 'happyforms' )
			),
			'confirmation_email_subject' => array(
				'type' => 'set',
				'then_text' => __( 'Then subject…', 'happyforms' )
			),
			'redirect_url' => array(
				'type' => 'template',
				'template' => 'redirect-url-then-value',
				'then_text' => __( 'Search or type URL', 'happyforms' )
			),
			'abandoned_resume_email_respondent_address' => array(
				'type' => 'template',
				'template' => 'email-part-lists-then-value',
				'then_text' => __( 'Email field is…', 'happyforms' )
			),
		);

		return apply_filters( 'happyforms_conditional_enabled_setup_controls', $supported_controls );
	}

	/**
	 * Adds logic UI to Setup and Email step controls.
	 *
	 * @hooked filter `append_control_settings`
	 *
	 * @param array $controls List of step controls.
	 *
	 * @return array
	 */
	public function append_control_settings( $controls ) {
		$conditionals = $this->get_enabled_controls();
		$field_controls = array_filter( $controls, function( $control ) {
			return isset( $control['field'] );
		} );
		$control_keys = wp_list_pluck( $field_controls, 'field' );
		$control_keys = array_flip( $control_keys );
		$control_keys = array_intersect_key( $control_keys, $conditionals );

		foreach( $control_keys as $field => $key ) {
			$controls[$key]['conditional_settings'] = $conditionals[$field];
			add_action( "happyforms_setup_control_{$field}_after", array( $this, 'customize_render_setup_logic_template' ) );
		}

		return $controls;
	}

	/**
	 * Renders logic UI template specific to control passed in arguments.
	 *
	 * @param array $control Control data.
	 *
	 * @return void
	 */
	public function customize_render_setup_logic_template( $control ) {
		require( happyforms_get_include_folder() . '/templates/customize-form-setup-logic.php' );
	}

	/**
	 * Prints Underscore templates in form builder UI.
	 */
	public function print_customize_templates() {
		require_once( happyforms_get_include_folder() . '/templates/customize-form-logic-group.php' );
		require_once( happyforms_get_include_folder() . '/templates/customize-form-logic-item.php' );
		require_once( happyforms_get_include_folder() . '/templates/customize-form-logic-email-part-lists-then-value.php' );
		require_once( happyforms_get_include_folder() . '/templates/customize-form-logic-redirect-url-then-value.php' );
	}

	public function customize_enqueue_scripts() {
		wp_enqueue_script(
			'happyforms-conditionals',
			happyforms_get_plugin_url() . 'inc/assets/js/customize/logic.js',
			array( 'happyforms-customize' ),
			happyforms_get_version(),
			true
		);

		$controls = $this->get_enabled_controls();

		wp_localize_script(
			'happyforms-conditionals',
			'_happyFormsConditionSettings',
			array(
				'constants' => HappyForms_Condition::get_constants(),
				'controls' => $controls
			)
		);
	}

}

if ( ! function_exists( 'happyforms_get_conditionals_ui_controller' ) ):

function happyforms_get_conditionals_ui_controller() {
	return HappyForms_Conditionals_UI_Controller::instance();
}

endif;

happyforms_get_conditionals_ui_controller();
