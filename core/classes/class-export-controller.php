<?php
class HappyForms_Export_Controller {

	private static $instance;

	public $export_import_action = 'happyforms_export_import';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'add_xml_mime_type' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'happyforms_export_page_method', array( $this, 'set_admin_page_method' ) );
		add_filter( 'happyforms_export_page_url', array( $this, 'set_admin_page_url' ) );
		add_action( 'admin_action_happyforms_export_import', array( $this, 'handle_request' ) );
	}

	public function add_xml_mime_type( $info, $file, $filename ) {
		if ( isset( $_REQUEST['is_happyforms_export'] ) ) {
			$extension = pathinfo( $filename, PATHINFO_EXTENSION );

			if ( 'xml' !== $extension ) {
				return $info;
			}

			if ( extension_loaded( 'fileinfo' ) ) {
				$finfo = finfo_open( FILEINFO_MIME_TYPE );
				$mime = finfo_file( $finfo, $file );
				finfo_close( $finfo );

				$xml_mimes = array(
					'text/xml', 'application/xml'
				);

				if ( ! in_array( $mime, $xml_mimes ) ) {
					return $info;
				}
			
				$info = array(
					'ext' => $extension,
					'type' => $mime,
				);
			} else {
				$info = array(
					'ext' => $extension,
					'type' => 'text/xml',
				);
			}
		}

		return $info;
	}

	public function set_admin_page_method( $method ) {
		$method = array( $this, 'exports_page' );

		return $method;
	}

	public function set_admin_page_url( $url ) {
		$url = 'happyforms-export';

		return $url;
	}

	public function exports_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.', 'happyforms' ) );
		}

		add_filter( 'admin_footer_text', 'happyforms_admin_footer' );

		require_once( happyforms_get_include_folder() . '/templates/admin-export.php' );
	}

	public function admin_enqueue_scripts() {
		if ( happyforms_is_admin_screen( 'happyforms-export' ) ) {
			wp_register_script(
				'happyforms-export',
				happyforms_get_plugin_url() . 'inc/assets/js/admin/export.js',
				array(), happyforms_get_version(), true
			);

			wp_enqueue_script( 'happyforms-export' );
		}
	}

	public function handle_request() {
		if ( ! isset( $_REQUEST['happyforms_export_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['happyforms_export_nonce'], $this->export_import_action ) ) {
			return;
		}

		$action_type = sanitize_text_field( $_REQUEST['action_type'] );

		$response = '';

		switch ( $action_type ) {
			case 'export_form':
				$form_id = ( isset( $_REQUEST['form_id'] ) ) ? intval( $_REQUEST['form_id'] ) : 0;

				$response = $this->export_form( $form_id );
			break;
		}
	}

	public function get_file_name( $form_id, $extension ) {
		$form_title = get_the_title( $form_id );
		$form_title = sanitize_title( $form_title );
		$form_title = "{$form_title}.{$extension}";

		return $form_title;
	}

	public function export_form( $form_id ) {
		require_once( happyforms_get_include_folder() . '/classes/class-exporter-xml.php' );

		$filename = $this->get_file_name( $form_id, 'xml' );
		$exporter = new HappyForms_Exporter_XML( $form_id, $filename );
		$exporter->export();
	}

}

if ( ! function_exists( 'happyforms_get_export_controller' ) ):

function happyforms_get_export_controller() {
	return HappyForms_Export_Controller::instance();
}

endif;
