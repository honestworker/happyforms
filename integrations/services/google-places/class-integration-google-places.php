<?php
class HappyForms_Integration_Google_Places {

	private static $instance;

	private $ajax_action_autocomplete = 'happyforms_address_autocomplete';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_meta_fields', array( $this, 'meta_fields' ) );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_filter( 'happyforms_frontend_settings', array( $this, 'frontend_settings' ) );
		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 2 );
		add_filter( 'happyforms_get_form_data', array( $this, 'migrate_address_mode' ) );

		add_action( 'happyforms_part_customize_address_after_options', array( $this, 'add_part_controls' ) );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );

		add_action( 'wp_ajax_' . $this->ajax_action_autocomplete, array( $this, 'ajax_autocomplete' ) );
		add_action( 'wp_ajax_nopriv_' . $this->ajax_action_autocomplete, array( $this, 'ajax_autocomplete' ) );
	}

	public function add_part_controls() {
		require( happyforms_get_integrations_folder() . '/services/google-places/templates/partial-part-controls.php' );
	}

	public function html_part_class( $class, $part ) {
		if ( 'address' === $part['type'] ) {
			$service = happyforms_get_integrations()->get_service( 'google-places' );

			if ( $service->is_connected() && 'simple' === $part['mode'] && 1 === $part['has_autocomplete'] ){
				$class[] = 'happyforms-part--address-googleplaces';
				$class[] = 'happyforms-part--with-autocomplete';
			}
		}

		return $class;
	}

	public function customize_enqueue_scripts( $deps = array() ) {
		wp_enqueue_script(
			'google-part-places',
			happyforms_get_plugin_url() . 'integrations/services/google-places/assets/js/parts/part-google-places.js',
			$deps, happyforms_get_version(), true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$service = happyforms_get_integrations()->get_service( 'google-places' );

		if ( ! $service->is_connected() ){
			return $deps;
		}

		$form_controller = happyforms_get_form_controller();
		$type = 'address';
		$has_autocomplete = false;

		foreach ( $forms as $form ) {
			$parts = array_filter( $form['parts'], function( $part ) use( $type ) {
				return $part['type'] === $type && $part['has_autocomplete'] == 1
						&& $part['mode'] === 'simple';
			} );

			if ( ! empty( $parts ) ){
				$has_autocomplete = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $has_autocomplete ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-google-places',
			happyforms_get_plugin_url() . 'integrations/services/google-places/assets/js/frontend/google-places.js',
			array( 'happyforms-part-address' ), happyforms_get_version(), true
		);

		$deps[] = 'happyforms-google-places';

		return $deps;
	}

	public function frontend_settings( $settings ) {
		$settings['googlePlaces'] = array(
			'url' => admin_url( 'admin-ajax.php' ),
			'actionAutocomplete' => $this->ajax_action_autocomplete,
		);

		return $settings;
	}

	public function ajax_autocomplete() {
		$results = [];
		$service = happyforms_get_integrations()->get_service( 'google-places' );

		if ( $service->is_connected() && isset( $_GET['term'] ) ) {
			$results = $service->get_address_suggestions( sanitize_text_field( $_GET['term'] ) );
		}

		wp_send_json( $results );
	}

	// TODO delete after support for migrating old address fields is over.
	public function meta_fields( $fields ){
		$service_fields = array(
			'address_autocomplte_migrated' => array(
				'default' => 0,
				'sanitize' => 'sanitize_text_field',
			),
		);

		$fields = array_merge( $fields, $service_fields );

		return $fields;
	}

	public function migrate_address_mode( $form ) {
		if ( 1 === $form['address_autocomplte_migrated'] ) {
			return $form;
		}

		foreach ( $form['parts'] as $p => $part ) {
			if ( 'address' === $part['type'] && 'autocomplete' === $part['mode'] ){
				$form['parts'][$p]['mode'] = 'simple';
				$form['parts'][$p]['has_autocomplete'] = 1;
			}
		}

		$form['address_autocomplte_migrated'] = 1;

		return $form;
	}

}

if ( ! function_exists( 'happyforms_get_integration_google_places' ) ):

function happyforms_get_integration_google_places() {
	return HappyForms_Integration_Google_Places::instance();
}

endif;

happyforms_get_integration_google_places();
