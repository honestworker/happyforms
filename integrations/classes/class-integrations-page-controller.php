<?php

class HappyForms_Integrations_Page_Controller {

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_integrations_page_method', array( $this, 'set_admin_page_method' ) );
		add_filter( 'happyforms_integrations_page_url', array( $this, 'set_admin_page_url' ) );
		add_action( 'happyforms_add_meta_boxes', array( $this, 'set_metaboxes' ) );
	}

	public function set_metaboxes() {
		$screen = get_plugin_page_hookname( plugin_basename( $this->set_admin_page_url() ), 'happyforms' );
		$integrations = happyforms_get_integrations();

		$service = $integrations->get_service( 'recaptcha' );
		$metabox_id = "happyforms-integrations-widget-{$service->id}";

		add_meta_box(
			$metabox_id,
			__( 'reCAPTCHA', 'happyforms' ),
			array( $this, 'antispam_metabox_callback' ),
			$screen, 'normal' 
		);

		add_filter( "postbox_classes_{$screen}_{$metabox_id}", function( $classes ) use( $service ) {
			$classes[] = 'happyforms-integrations-widget';
			$classes[] = "happyforms-integrations-widget-group-{$service->group}";

			return $classes;
		} );
	}

	public function antispam_metabox_callback(  ) {
		require( happyforms_get_integrations_folder() . '/templates/admin-antispam-integrations.php' );
	}

	public function integrations_metabox_callback( $post, $metabox ) {
		$service = $metabox['args']['service'];

		$service->admin_widget();
	}

	public function set_admin_page_method() {
		return array( $this, 'integrations_page' );
	}

	public function set_admin_page_url() {
		return 'happyforms-integrations';
	}

	public function integrations_page() {
		wp_enqueue_script('dashboard');
		add_filter( 'admin_footer_text', 'happyforms_admin_footer' );

		require_once( happyforms_get_integrations_folder() . '/templates/admin-integrations.php' );
	}

}

if ( ! function_exists( 'happyforms_get_integrations_page_controller' ) ):

function happyforms_get_integrations_page_controller() {
	return HappyForms_Integrations_Page_Controller::instance();
}

endif;

happyforms_get_integrations_page_controller();
