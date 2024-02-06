<?php

class HappyForms_Part_Video extends HappyForms_Form_Part {

	public $type = 'video';

	public function __construct() {
		$this->label = __( 'Video', 'happyforms' );
		$this->description = __( 'For adding a single video.', 'happyforms' );

		add_filter( 'happyforms_message_part_visible', array( $this, 'message_part_visible' ), 10, 2 );
		add_filter( 'happyforms_email_part_visible', array( $this, 'email_part_visible' ), 10, 3 );
		add_filter( 'happyforms_email_part_label', array( $this, 'email_part_label' ), 10, 4 );
		add_filter( 'happyforms_email_part_value', array( $this, 'email_part_value' ), 10, 4 );
		add_filter( 'happyforms_csv_part_visible', array( $this, 'csv_part_visible' ), 10, 2 );
	}

	public function get_customize_fields() {
		$fields = array(
			'type' => array(
				'default' => $this->type,
				'sanitize' => 'sanitize_text_field',
			),
			'label' => array(
				'default' => __( '', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'label_placement' => array(
				'default' => 'show',
				'sanitize' => 'sanitize_text_field'
			),
			'description' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'width' => array(
				'default' => 'full',
				'sanitize' => 'sanitize_key'
			),
			'css_class' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'attachment' => array(
				'default' => 0,
				'sanitize' => 'intval',
			),
			'required' => array(
				'default' => 0,
				'sanitize' => 'intval',
			),
		);

		return happyforms_get_part_customize_fields( $fields, $this->type );
	}

	public function customize_templates() {
		$template_path = happyforms_get_core_folder() . '/templates/parts/customize-video.php';
		$template_path = happyforms_get_part_customize_template_path( $template_path, $this->type );

		require_once( $template_path );
	}

	public function frontend_template( $part_data = array(), $form_data = array() ) {
		$part = wp_parse_args( $part_data, $this->get_customize_defaults() );
		$form = $form_data;

		include( happyforms_get_core_folder() . '/templates/parts/frontend-video.php' );
	}

	public function get_message_definitions() {
		return array();
	}

	public function sanitize_value( $part_data = array(), $form_data = array(), $request = array() ) {
		$sanitized_value = $this->get_default_value( $part_data );
		$part_name = happyforms_get_part_name( $part_data, $form_data );

		if ( isset( $request[$part_name] ) ) {
			$sanitized_value = sanitize_text_field( $request[$part_name] );
		}

		return $sanitized_value;
	}

	public function validate_value( $value, $part = array(), $form = array() ) {
		$validated_value = esc_attr( $value );

		return $validated_value;
	}

	public function message_part_visible( $visible, $part ) {
		if ( $this->type === $part['type'] ) {
			$visible = false;
		}

		return $visible;
	}

	public function email_part_visible( $visible, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$visible = true;
		}

		return $visible;
	}

	public function csv_part_visible( $visible, $part ) {
		if ( $this->type === $part['type'] ) {
			$visible = false;
		}

		return $visible;
	}

	public function email_part_value( $value, $message, $part, $form ) {
		if ( $this->type !== $part['type'] ) {
			return $value;
		}

		if ( 0 === $part['attachment'] ) {
			return $value;
		}

		$attachment = get_posts( array(
			'post_type' => 'attachment',
			'p' => $part['attachment'],
		) );

		if ( ! empty( $attachment ) ) {
			$attachment = $attachment[0];
			$src = wp_get_attachment_url( $attachment->ID );
			$filename = basename( $src );

			$value = "<a href={$src}>{$filename}</a>";
		}


		return $value;
	}

	public function email_part_label( $label, $message, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( '' === $part['label'] ) {
				$label = '';
			}
		}

		return $label;
	}

	public function customize_enqueue_scripts( $deps = array() ) {
		wp_enqueue_media();

		wp_enqueue_script(
			'happyforms-video-handle',
			happyforms_get_plugin_url() . 'core/assets/js/customize/video.js',
			array( 'happyforms-customize' ),
			happyforms_get_version(),
			true
		);

		wp_enqueue_script(
			'part-video',
			happyforms_get_plugin_url() . 'core/assets/js/parts/part-video.js',
			$deps, happyforms_get_version(), true
		);
	}
}
