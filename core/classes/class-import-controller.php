<?php

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) ) {
		require $class_wp_importer;
	}
}


class HappyForms_Import_Controller extends WP_Importer {

	var $file_id;

	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_import_page_method', array( $this, 'set_admin_page_method' ) );
		add_filter( 'happyforms_import_page_url', array( $this, 'set_admin_page_url' ) );
	}

	public function set_admin_page_method() {
		return array( $this, 'dispatch' );
	}

	public function set_admin_page_url() {
		return 'happyforms-import';
	}

	public function dispatch() {
		$this->header();

		if ( ! isset( $_FILES['import']['name'] ) ) {
			$this->upload_form();
		} else {
			if ( $this->handle_upload() ) {
				check_admin_referer( 'import-upload' );
				require_once( happyforms_get_include_folder() . '/classes/class-importer-xml.php' );

				$file = get_attached_file( $this->id );
				$importer = new HappyForms_Importer_XML();
				$result = $importer->import( $file );

				$messages = [];

				if ( is_wp_error( $result ) ) {
					$messages = $result->get_error_messages();
				} else {
					$messages = $importer->get_success_messages();
				}

				if ( ! empty( $messages ) ) {
					$this->print_messages( $messages );
				}

				wp_import_cleanup( $this->id );
			}
		}

		$this->footer();
	}

	function header() {
		echo '<div class="wrap">';
		echo '<h2>' . __( 'Import Forms', 'happyforms' ) . '</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function upload_form() {
		echo '<div class="narrow">';
		echo '<p>' . __( 'Choose a forms export (.xml) file to upload, then click Upload file and import.', 'happyforms' ) . '</p>';
		wp_import_upload_form( 'admin.php?page=happyforms-import' );
		echo '</div>';
	}

	public function handle_upload() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'happyforms' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';

			return false;
		} else if ( ! file_exists( $file['file'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'happyforms' ), esc_html( $file['file'] ) );
			echo '</p>';

			return false;
		}

		$this->id = (int) $file['id'];

		return true;
	}

	private function print_messages( $messages ) {
		$output = array_map( function( $message ) {
			return "<p>$message</p>";
		}, $messages );

		$output = implode( '', $output );

		echo $output;
	}

}

if ( ! function_exists( 'happyforms_get_import_controller' ) ):

function happyforms_get_import_controller() {
	return HappyForms_Import_Controller::instance();
}

endif;

happyforms_get_import_controller();
