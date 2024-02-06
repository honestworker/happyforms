<?php
class HappyForms_Integrations {

	private static $instance;
	private static $hooked = false;

	private $option_name = '_happyforms_service_credentials';
	private $services = array();
	private $data = array();
	private $credentials = array();

	public $action_update = 'happyforms-service-update';
	public $integrations_action = 'happyforms-integrations-update';
	public $nonce_update = 'happyforms_update_nonce';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		require_once( happyforms_get_integrations_folder() . '/classes/class-service.php' );

	 	// Google Places
	 	require_once( happyforms_get_integrations_folder() . '/services/google-places/class-service-google-places.php' );
	 	$this->register_service( 'HappyForms_Service_Google_Places' );

	 	// Google Geocoding
	 	require_once( happyforms_get_integrations_folder() . '/services/google-geocoding/class-service-google-geocoding.php' );
	 	$this->register_service( 'HappyForms_Service_Google_Geocoding' );

		// reCaptcha
		require_once( happyforms_get_integrations_folder() . '/services/recaptcha/class-service-recaptcha.php' );
		$this->register_service( 'HappyForms_Service_Recaptcha' );

		// reCaptcha V3
		require_once( happyforms_get_integrations_folder() . '/services/recaptchav3/class-service-recaptchav3.php' );
		$this->register_service( 'HappyForms_Service_RecaptchaV3' );

		// AntiSpam
		require_once( happyforms_get_integrations_folder() . '/services/antispam/class-service-antispam.php' );
		$this->register_service( 'HappyForms_Service_AntiSpam' );


	}

	public function hook() {
		if ( self::$hooked ) {
			return;
		}

		self::$hooked = true;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_' . $this->action_update, array( $this, 'ajax_service_update' ) );
		add_action( 'wp_ajax_' . $this->integrations_action, array( $this, 'ajax_integrations_update' ) );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );
		add_action( 'happyforms_integrations_print_notices', array( $this, 'print_notices' ) );

		$this->read_credentials();
		$this->configure_services();
		$this->migrate_google_geo_apis();
	}

	public function read_credentials() {
		$this->credentials = get_option( $this->option_name, array() );
	}

	public function write_credentials() {
		$this->credentials = array_map( function( $service ) {
			return $service->get_credentials();
		}, $this->services );

		update_option( $this->option_name, $this->credentials );
	}

	public function register_service( $service ) {
		$service = $service instanceof HappyForms_Service ? $service : new $service();
		$this->services[$service->id] = $service;
	}

	public function configure_services() {
		foreach( $this->services as $service ) {
			$credentials = array();

			if ( isset ( $this->credentials[$service->id] ) ) {
				$credentials = $this->credentials[$service->id];
			}

			$service->set_credentials( $credentials );
			$service->configure();
			$service->load();
		}
	}

	public function get_services() {
		return $this->services;
	}

	public function get_service( $id ) {
		if ( isset( $this->services[$id] ) ) {
			return $this->services[$id];
		}

		return false;
	}

	public function get_service_group( $group ){
		if ( empty( $this->grouped_services ) ) {
			$this->set_grouped_services();
		}

		return $this->grouped_services[ $group ];
	}

	public function set_grouped_services(){
		$grouped_services = array();

		foreach( $this->services as $service ) {
			$grouped_services[$service->group][] = $service;
		}

		$this->grouped_services = $grouped_services;
	}

	public function migrate_google_geo_apis() {
		$google_geocoding = $this->get_service( 'google-geocoding' );
		$google_places = $this->get_service( 'google-places' );

		$google_geocoding->try_migrating_keys();
		$google_places->try_migrating_keys();
	}

	public function ajax_service_update() {
		if ( ! check_ajax_referer( $this->action_update ) ) {
			wp_die();
		}

		$services = $_REQUEST['services'];
		$group = '';
		$group_service = null;

		if ( isset( $_REQUEST['group'] ) ) {
			$group = sanitize_text_field( $_REQUEST['group'] );
			$group_service = $this->services[$group];

			if ( empty( array_filter( $services ) ) ) {
				$group_service->reset_active_service();
			}
		}

		$response        = '';
		$success_message = __( 'Changes saved.', 'happyforms' );

		ob_start();

		foreach ( $services as $service ) {

			if ( ! isset( $this->services[$service] ) ) {
				continue;
			}

			$the_service = $this->services[$service];
			$service_credentials = $the_service->get_credentials();

			if ( ! isset( $_REQUEST['credentials'][$the_service->id] ) ) {
				$_REQUEST['credentials'][$the_service->id] = $service_credentials;
			}

			$credentials = wp_parse_args( $_REQUEST['credentials'][$the_service->id], $service_credentials );
			$credentials = array_intersect_key( $credentials, $service_credentials );
			$credentials = array_map( 'sanitize_text_field', $credentials );

			if ( ! empty( $group ) && ! $group_service->supports_multiple ) {
				$group_service->set_active_service( $the_service->id );
			}

			$previous_credentials = $the_service->get_credentials();
			$the_service->set_credentials( $credentials, $_REQUEST['credentials'][$the_service->id] );
			$this->write_credentials();
		}

		$this->notice = array(
				'status' => 'success',
				'message' => $success_message,
			);

		switch( $group ){
			case 'antispam':
				require( happyforms_get_integrations_folder() . '/templates/admin-antispam-integrations.php' );
				break;
			default:
				break;
		}

		$response = ob_get_clean();

		echo $response;
		die();
	}

		public function ajax_integrations_update() {
		if ( ! check_ajax_referer( $this->integrations_action ) ) {
			wp_die();
		}

		if ( ! isset( $_REQUEST['service'] ) ) {
			wp_die();
		}

		if ( ! isset( $_REQUEST['credentials'] ) ) {
			wp_die();
		}

		$service = $_REQUEST['service'];
		$response        = '';

		if ( ! isset( $this->services[$service] ) ) {
			wp_send_json_error();
		}

		ob_start();

		$the_service = $this->services[$service];
		$service_credentials = $the_service->get_credentials();
		$this->notice = [];

		if ( ! isset( $_REQUEST['credentials'][$the_service->id] ) ) {
			$_REQUEST['credentials'][$the_service->id] = $service_credentials;
		}

		$credentials = wp_parse_args( $_REQUEST['credentials'][$the_service->id], $service_credentials );
		$credentials = array_intersect_key( $credentials, $service_credentials );
		$credentials = array_map( 'sanitize_text_field', $credentials );

		$previous_credentials = $the_service->get_credentials();

		$the_service->set_credentials( $credentials, $_REQUEST['credentials'][$the_service->id] );
		$this->write_credentials();

		$this->notice = array(
				'status' => 'success',
				'message' => __( 'Changes saved.', 'happyforms' ),
			);
		$the_service->admin_widget( $previous_credentials );

		$response = ob_get_clean();

		echo $response;
		die();
	}

	public function print_notices(){
		if( empty( $this->notice ) ) {
			return;
		}
	?>
	  <div class="notice notice-<?php echo $this->notice['status']; ?>"><p><?php echo $this->notice['message']; ?></p></div>
	<?php
	}

	public function admin_enqueue_scripts() {
		if ( ! isset( $_GET['page'] ) || 'happyforms-integrations' !== $_GET['page'] ) {
			return;
		}

		wp_enqueue_style(
			'happyforms-integrations',
			happyforms_get_plugin_url() . 'integrations/assets/css/admin.css'
		);

		wp_enqueue_script(
			'happyforms-integrations',
			happyforms_get_plugin_url() . 'integrations/assets/js/dashboard.js',
			array( 'jquery' ), happyforms_get_version(), true
		);
	}

	public function customize_enqueue_scripts() {
		$services = array();

		foreach ( $this->services as $service ) {
			switch( $service->id ) {
				case 'antispam':
					$services['antispam'] = ( ! empty( $service->active_service ) ) ? $service->active_service->id : 0;
					break;
				default:
					break;
			}

			if ( empty( $service->group ) || 'antispam' === $service->group ) {
				continue;
			}

			$services[$service->id] = ( $service->is_connected() ) ? 1 : 0;
		}

		wp_localize_script(
			'happyforms-customize',
			'_happyFormsIntegrations',
			$services
		);
	}
}

if ( ! function_exists( 'happyforms_get_integrations' ) ):

function happyforms_get_integrations() {
	$instance = HappyForms_Integrations::instance();
	$instance->hook();

	return $instance;
}

endif;

happyforms_get_integrations();
