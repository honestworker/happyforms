<?php
class HappyForms_Export_Controller_Upgrade extends HappyForms_Export_Controller {

	private static $instance;

	private $parent;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function __construct() {
		$this->parent = parent::instance();

		add_action( 'admin_action_happyforms_export_import', array( $this, 'handle_request' ) );
	}

	public function hook() {
		add_action( 'happyforms_csv_export_before', array( $this, 'happyforms_csv_export_before' ) );

		remove_action( 'admin_action_happyforms_export_import', array( $this->parent, 'handle_request' ), 10 );
		add_action( 'admin_action_happyforms_export_import', array( $this, 'handle_request' ) );
	}

	private function export_import_action(){
		return $this->export_import_action;
	}


	public function handle_request() {
		if ( ! isset( $_REQUEST['happyforms_export_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['happyforms_export_nonce'], $this->export_import_action() ) ) {
			return;
		}

		$action_type = sanitize_text_field( $_REQUEST['action_type'] );

		$response = '';

		switch ( $action_type ) {
			case 'export_responses':
				$form_id = ( isset( $_REQUEST['export_responses-form_id'] ) ) ? intval( $_REQUEST['export_responses-form_id'] ) : 0;

				$response = $this->export_responses( $form_id );
			break;

			case 'export_form':
				$form_id = ( isset( $_REQUEST['form_id'] ) ) ? intval( $_REQUEST['form_id'] ) : 0;
				$include_responses = false;

				if ( isset( $_REQUEST['export_form_responses'] ) ) {
					$include_responses = filter_var ( $_REQUEST['export_form_responses'], FILTER_VALIDATE_BOOLEAN );
				}

				$response = $this->export_form( $form_id, $include_responses );
			break;
		}
	}

	public function export_responses( $form_id ) {
		require_once( happyforms_get_include_folder() . '/classes/class-exporter-csv.php' );

		$filename = $this->get_file_name( $form_id, 'csv' );
		$exporter = new HappyForms_Exporter_CSV( $form_id, $filename );
		$exporter->export();
	}

	public function export_form( $form_id, $include_responses = false ) {
		require_once( happyforms_get_include_folder() . '/classes/class-exporter-xml.php' );

		$filename = $this->get_file_name( $form_id, 'xml' );
		$exporter = new HappyForms_Exporter_XML( $form_id, $filename, $include_responses );
		$exporter->export();
	}

	public function happyforms_csv_export_before( $output ) {
		fprintf( $output, chr(0xEF) . chr(0xBB) . chr(0xBF) );
	}

}

if ( ! function_exists( 'happyforms_get_export_controller_upgrade' ) ):

function happyforms_get_export_controller_upgrade() {
	return HappyForms_Export_Controller_Upgrade::instance();
}

endif;
