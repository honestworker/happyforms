<?php
class HappyForms_Settings_Page_Controller {

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_settings_page_method', array( $this, 'set_admin_page_method' ) );
		add_filter( 'happyforms_settings_page_url', array( $this, 'set_admin_page_url' ) );
		add_action( 'happyforms_add_meta_boxes', array( $this, 'set_metaboxes' ) );
	}

	public function permissions_metabox_callback(  ) {
		require( happyforms_get_include_folder() . '/templates/admin-settings-role-permissions.php' );
	}

	public function set_metaboxes( ) {
		$screen = get_plugin_page_hookname( plugin_basename( $this->set_admin_page_url() ), 'happyforms' );

		if ( current_user_can( 'manage_options' ) ) {
			add_meta_box( 
				'happyforms-role_permissions-section',
				__( 'Role Capabilities', 'happyforms' ),
				array( $this, 'permissions_metabox_callback' ),
				$screen, 'normal'
			);
		}
	}

	public function set_admin_page_method() {
		return array( $this, 'settings_page' );
	}

	public function set_admin_page_url() {
		return 'happyforms-settings';
	}

	public function settings_page() {
		wp_enqueue_script('dashboard');
		add_filter( 'admin_footer_text', 'happyforms_admin_footer' );

		require_once( happyforms_get_include_folder() . '/templates/admin-settings.php' );
	}

}

if ( ! function_exists( 'happyforms_get_settings_page_controller' ) ):

function happyforms_get_settings_page_controller() {
	return HappyForms_Settings_Page_Controller::instance();
}

endif;

happyforms_get_settings_page_controller();
