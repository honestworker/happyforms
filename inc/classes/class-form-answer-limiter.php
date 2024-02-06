<?php
class HappyForms_Form_Answer_Limiter {
	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_messages_fields', array( $this, 'meta_messages_fields' ) );
		add_filter( 'happyforms_messages_controls', array( $this, 'messages_controls' ) );
		add_filter( 'happyforms_validate_part_submission', array( $this, 'validate_part_submission' ), 10, 3 );


		$supported_parts = $this->get_supported_parts();

		foreach ( $supported_parts as $part ) {
			add_filter( "happyforms_part_customize_fields_{$part}", array( $this, 'add_part_fields' ) );
			add_action( "happyforms_part_customize_{$part}_before_advanced_options", array( $this, 'add_part_controls' ) );
		}

		// TODO delete after limit_answer is completely removed.
		add_filter( 'happyforms_get_form_data', array( $this, 'transition_deprecated_limit_answer' ), 99 );
	}

	public function get_supported_parts() {
		$parts = array(
			'single_line_text',
			'multi_line_text',
			'email',
			'number',
			'phone',
			'address',
			'date',
			'website_url'
		);

		return $parts;
	}

	public function add_part_fields( $fields ) {
		$fields['max_limit_answer'] = array(
			'default' => '',
			'sanitize' => 'happyforms_sanitize_intval_empty'
		);

		return $fields;
	}

	public function add_part_controls() {
		require( happyforms_get_include_folder() . '/templates/partials/customize-limit-answer.php' );
	}

	public function validate_part_submission( $value, $part, $form ) {
		if ( is_wp_error( $value ) || ! in_array( $part['type'], $this->get_supported_parts() ) ) {
			return $value;
		}

		if ( '' == $part['max_limit_answer'] ) {
			return $value;
		}

		$current_count = $this->get_count_part_answer( $form['ID'], $part['id'], $value );

		if ( $current_count >= $part['max_limit_answer'] ) {
			$value = new WP_error( 'error', happyforms_get_form_property( $form, 'max_answer_message' ) );
		}

		return $value;
	}

	public function get_count_part_answer( $form_id, $part_id, $value ) {
		global $wpdb;
		$meta_part_id = '_happyforms_' . $part_id ;

		$query = "SELECT COUNT(p.ID) as count FROM $wpdb->posts AS p
    		INNER JOIN $wpdb->postmeta AS pm1 ON ( p.ID = pm1.post_id )
			INNER JOIN $wpdb->postmeta AS pm2 ON ( p.ID = pm2.post_id )
    		INNER JOIN $wpdb->postmeta AS pm3 ON ( p.ID = pm3.post_id )
    		WHERE ( ( pm1.meta_key = '_happyforms_form_id' AND pm1.meta_value = '{$form_id}' )
					AND ( pm2.meta_key = '_happyforms_read' AND pm2.meta_value IN ( '', '1') )
  					AND ( pm3.meta_key= '{$meta_part_id}' AND pm3.meta_value = '{$value}' ) )
    			AND (p.post_type = 'happyforms-message' AND p.post_status = 'publish' )";

		$count = $wpdb->get_var( $query );

		return $count;
	}

	public function meta_messages_fields( $fields ) {
		$messages_fields = array(
			'max_answer_message' => array(
				'default' => __( 'This answer already exists.', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
		);

		$fields = array_merge( $fields, $messages_fields );

		return $fields;
	}

	public function messages_controls( $controls ) {
		$message_controls = array(
			4050 => array(
				'type' => 'text',
				'label' => __( 'Field answer reached its limit', 'happyforms' ),
				'field' => 'max_answer_message',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $message_controls );

		return $controls;
	}

	// TODO delete after limit_answer is completely removed.
	function transition_deprecated_limit_answer( $form ) {
		$supported_parts = $this->get_supported_parts();

		array_walk( $form['parts'], function( &$part, $k ) use ( $supported_parts ) {
			if ( in_array( $part['type'], $supported_parts ) && isset ( $part['limit_answer'] ) ) {
				if ( $part['limit_answer'] == 0 || $part['limit_answer'] == '' ) {
					$part['max_limit_answer'] = '';
				}
			}
		} );

		return $form;
	}

}

if ( ! function_exists( 'happyforms_answer_limiter' ) ) :

function happyforms_answer_limiter() {
	return HappyForms_Form_Answer_Limiter::instance();
}

endif;

happyforms_answer_limiter();
