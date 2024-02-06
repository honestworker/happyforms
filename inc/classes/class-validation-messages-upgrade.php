<?php

class HappyForms_Validation_Messages_Upgrade {
	private $save_action = 'happyforms_save_validation_messages';

	public $save_nonce = 'happyforms-validation-messages-nonce';
	public $messages_option_name = 'happyforms-validation-messages';
	public $validation_messages_controller = '';

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Form_Controller
	 */
	private static $instance;

	/**
	 * The singleton constructor.
	 *
	 * @since 1.0
	 *
	 * @return HappyForms_Form_Controller
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		$this->validation_messages_controller = happyforms_validation_messages();

		add_filter( 'happyforms_default_validation_messages', array( $this, 'add_messages' ) );

		add_filter( 'happyforms_messages_fields', array( $this, 'meta_messages_fields' ) );
		add_filter( 'happyforms_messages_controls', array( $this, 'messages_controls' ) );
	}

	/**
	 * Adds messages applicable to paid version only.
	 *
	 * @hooked filter `happyforms_default_validation_messages`
	 *
	 * @param array $messages Array of default messages.
	 *
	 * @return array Messages array with new items added.
	 */
	public function add_messages( $messages ) {
		$upgrade_messages = wp_list_pluck( $this->get_validation_fields(), 'default' );
		$messages = array_merge( $messages, $upgrade_messages );

		return $messages;
	}


	public function get_validation_fields() {
		$fields = array(
			'file_not_uploaded' => array(
				'default' => __( 'Please upload a file.', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'file_size_too_big' => array(
				'default' => __( 'This file size is too big.', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'file_invalid' => array(
				'default' => __( 'This file type isn’t allowed.', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'file_duplicate' => array(
				'default' => __( 'A file with this name has already been uploaded.', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'file_min_count' => array(
				'default' => __( 'Too few files have been uploaded.', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
		);

		return $fields;
	}

	public function messages_controls( $controls ) {
		$message_controls = array(
			4060 => array(
				'type' => 'text',
				'label' => __( "Required file isn't uploaded", 'happyforms' ),
				'field' => 'file_not_uploaded',
			),
			4220 => array(
				'type' => 'text',
				'label' => __( "This file's size is too big", 'happyforms' ),
				'field' => 'file_size_too_big',
			),
			4240 => array(
				'type' => 'text',
				'label' => __( "This file's type not allowed", 'happyforms' ),
				'field' => 'file_invalid',
			),
			4241 => array(
				'type' => 'text',
				'label' => __( 'A file with this name has already been uploaded', 'happyforms' ),
				'field' => 'file_duplicate',
			),
			4242 => array(
				'type' => 'text',
				'label' => __( 'User uploaded too few files', 'happyforms' ),
				'field' => 'file_min_count',
			)
		);

		$controls = happyforms_safe_array_merge( $controls, $message_controls );

		return $controls;
	}

	public function meta_messages_fields( $fields ) {
		$fields = array_merge( $fields, $this->get_validation_fields() );

		return $fields;
	}
}

if ( ! function_exists( 'happyforms_validation_messages_upgrade' ) ):
/**
 * Get the HappyForms_Validation_Messages_Upgrade class instance.
 *
 * @since 1.0
 *
 * @return HappyForms_Validation_Messages_Upgrade
 */
function happyforms_validation_messages_upgrade() {
	return HappyForms_Validation_Messages_Upgrade::instance();
}

endif;

/**
 * Initialize the HappyForms_Validation_Messages_Upgrade class immediately.
 */
happyforms_validation_messages_upgrade();
