<?php
class HappyForms_Integration_Google_Geocoding {

	private static $instance;

	private $ajax_action_geocode = 'happyforms_address_geocode';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_filter( 'happyforms_frontend_settings', array( $this, 'frontend_settings' ) );
		add_action( 'happyforms_part_customize_address_after_options', array( $this, 'add_part_controls' ) );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );
		add_action( 'wp_ajax_' . $this->ajax_action_geocode, array( $this, 'ajax_geocode' ) );
		add_action( 'wp_ajax_nopriv_' . $this->ajax_action_geocode, array( $this, 'ajax_geocode' ) );
	}

	public function add_part_controls() {
		require( happyforms_get_integrations_folder() . '/services/google-geocoding/templates/partial-part-controls.php' );
	}

	public function customize_enqueue_scripts( $deps = array() ) {
		wp_enqueue_script(
			'google-part-geocoding',
			happyforms_get_plugin_url() . 'integrations/services/google-geocoding/assets/js/parts/part-google-geocoding.js',
			$deps, happyforms_get_version(), true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$service = happyforms_get_integrations()->get_service( 'google-geocoding' );

		if ( ! $service->is_connected() ){
			return $deps;
		}

		$form_controller = happyforms_get_form_controller();
		$type = 'address';
		$has_address_geolocation = false;

		foreach ( $forms as $form ) {
			$parts = array_filter( $form['parts'], function( $part ) use( $type ) {
				return $part['type'] === $type && $part['has_geolocation'] == 1;
			} );

			if ( ! empty( $parts ) ){
				$has_address_geolocation = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $has_address_geolocation ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-google-geocoding',
			happyforms_get_plugin_url() . 'integrations/services/google-geocoding/assets/js/frontend/google-geocoding.js',
			array( 'happyforms-part-address' ), happyforms_get_version(), true
		);

		$deps[] = 'happyforms-google-geocoding';

		return $deps;
	}

	public function frontend_settings( $settings ) {
		$settings['googleGeocoding'] = array(
			'url' => admin_url( 'admin-ajax.php' ),
			'actionGeocode' => $this->ajax_action_geocode,
		);

		return $settings;
	}

	public function ajax_geocode() {
		$results = [];
		$service = happyforms_get_integrations()->get_service( 'google-geocoding' );

		if ( $service->is_connected() && isset( $_GET['latitude'] ) && isset( $_GET['longitude'] ) ) {
			$latitude = sanitize_text_field( $_GET['latitude'] );
			$longitude = sanitize_text_field( $_GET['longitude'] );

			$results = $service->geolocate_address( $latitude, $longitude );
		}

		wp_send_json( $results );
	}

}

if ( ! function_exists( 'happyforms_get_google_geocoding_integration' ) ):

function happyforms_get_google_geocoding_integration() {
	return HappyForms_Integration_Google_Geocoding::instance();
}

endif;

happyforms_get_google_geocoding_integration();
