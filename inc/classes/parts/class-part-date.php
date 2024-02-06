<?php

class HappyForms_Part_Date extends HappyForms_Form_Part {

	public $type = 'date';

	public function __construct() {
		$this->label = __( 'Date-Time', 'happyforms' );
		$this->description = __( 'For formatted day, month, year and or time fields.', 'happyforms' );

		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
		add_filter( 'happyforms_stringify_part_value', array( $this, 'stringify_value' ), 10, 3 );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_filter( 'happyforms_validate_part', array( $this, 'validate_part' ) );
	}

	/**
	 * Get all part meta fields defaults.
	 *
	 * @since 1.0.0.
	 *
	 * @return array
	 */
	public function get_customize_fields() {
		$fields = array(
			'type' => array(
				'default' => $this->type,
				'sanitize' => 'sanitize_text_field',
			),
			'label' => array(
				'default' => __( '', 'happyforms '),
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
			'description_mode' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'date_type' => array(
				'default' => 'date',
				'sanitize' => 'sanitize_text_field'
			),
			'time_format' => array(
				'default' => 12,
				'sanitize' => 'intval'
			),
			'default_datetime' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'min_year' => array(
				'default' => intval( date( 'Y' ) ) - 100,
				'sanitize' => array( array( $this, 'sanitize_min_year' ) ),
			),
			'max_year' => array(
				'default' => intval( date( 'Y' ) ) + 20,
				'sanitize' => array( array( $this, 'sanitize_max_year' ) ),
			),
			'years_option' => array(
				'default' => 'all',
				'sanitize' => 'sanitize_text_field'
			),
			'years_order' => array(
				'default' => 'asc',
				'sanitize' => 'sanitize_text_field'
			),
			'min_hour' => array(
				'default' => 1,
				'sanitize' => 'intval',
			),
			'max_hour' => array(
				'default' => 12,
				'sanitize' => 'intval'
			),
			'minute_step' => array(
				'default' => 1,
				'sanitize' => 'intval'
			),
			'width' => array(
				'default' => 'full',
				'sanitize' => 'sanitize_key'
			),
			'css_class' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'required' => array(
				'default' => 1,
				'sanitize' => 'happyforms_sanitize_checkbox',
			),
		);

		return happyforms_get_part_customize_fields( $fields, $this->type );
	}

	/**
	 * Get template for part item in customize pane.
	 *
	 * @since 1.0.0.
	 *
	 * @return string
	 */
	public function customize_templates() {
		$template_path = happyforms_get_include_folder() . '/templates/parts/customize-date.php';
		$template_path = happyforms_get_part_customize_template_path( $template_path, $this->type );

		require_once( $template_path );
	}

	public function sanitize_min_year( $year = '' ) {
		$year = empty( $year ) ? ( date( 'Y' ) - 100 ) : $year;
		$year = intval( $year );

		return $year;
	}

	public function sanitize_max_year( $year = '' ) {
		$year = empty( $year ) ? ( date( 'Y' ) + 20 ) : $year;
		$year = intval( $year );

		return $year;
	}

	public function sanitize_min_hour( $hour = '' ) {
		$hour = empty( $hour ) ? 1 : $hour;
		$hour = intval( $hour );

		return $hour;
	}

	public function validate_part( $part_data ) {
		if ( $this->type !== $part_data['type'] ) {
			return $part_data;
		}

		$min_hour = $part_data['min_hour'];
		$max_hour = $part_data['max_hour'];
		$minute_step = $part_data['minute_step'];
		$time_format = $part_data['time_format'];

		$min_hour = intval( $min_hour );
		$max_hour = intval( $max_hour );
		$minute_step = intval( $minute_step );
		$time_format = intval( $time_format );

		if ( 12 === $time_format ) {
			$max_hour = min( $time_format, max( 1, $max_hour ) );
		} else {
			$max_hour = min( $time_format - 1, max( 0, $max_hour ) );
		}

		$min_hour = min( $max_hour, max( 0, $min_hour ) );
		$minute_step = min( 60, max( 1, $minute_step ) );

		$part_data['min_hour'] = $min_hour;
		$part_data['max_hour'] = $max_hour;
		$part_data['minute_step'] = $minute_step;
		$part_data['time_format'] = $time_format;

		return $part_data;
	}

	/**
	 * Get front end part template with parsed data.
	 *
	 * @since 1.0.0.
	 *
	 * @param array	$part_data 	Form part data.
	 * @param array	$form_data	Form (post) data.
	 *
	 * @return string	Markup for the form part.
	 */
	public function frontend_template( $part_data = array(), $form_data = array() ) {
		$part = wp_parse_args( $part_data, $this->get_customize_defaults() );
		$form = $form_data;

		include( happyforms_get_include_folder() . '/templates/parts/frontend-date.php' );
	}

	public function get_default_value( $part_data = array() ) {
		return array(
			'date' => '',
			'datetime' => '',
			'time' => '',
			'month' => '',
			'month_year' => '',
			'year' => '',
		);
	}

	/**
	 * Sanitize submitted value before storing it.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part_data Form part data.
	 *
	 * @return string
	 */
	public function sanitize_value( $part_data = array(), $form_data = array(), $request = array() ) {
		$sanitized_value = $this->get_default_value( $part_data );
		$part_name = happyforms_get_part_name( $part_data, $form_data );

		if ( isset( $request[$part_name] ) ) {
			$sanitized_value = wp_parse_args( $request[$part_name], $sanitized_value );
			$sanitized_value = array_map( 'sanitize_text_field', $sanitized_value );
		}

		return $sanitized_value;
	}

	/**
	 * Validate value before submitting it. If it fails validation, return WP_Error object, showing respective error message.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part Form part data.
	 * @param string $value Submitted value.
	 *
	 * @return string|object
	 */
	public function validate_value( $value, $part = array(), $form = array() ) {
		$validated_value = $value;
		$components = array_keys( $validated_value );

		switch( $part['date_type'] ) {
			case 'date':
				$components = array( 'date' );
				break;
			case 'datetime':
				$components = array( 'datetime' );
				break;
			case 'time':
				$components = array( 'time' );
				break;
			case 'month_year':
				$components = array( 'date' );
				break;
			case 'month':
				$components = array( 'month' );
				break;
			case 'year':
				$components = array( 'year' );
				break;
		}

		$validated_components = array_intersect_key( $validated_value, array_flip( $components ) );
		$filled_components = array_filter( $validated_components );
		$value_is_empty = ( 0 === count( $filled_components ) );
		$value_is_complete = count( $filled_components ) === count( $validated_components );

		// Missing value components
		if ( 1 === $part['required'] && ! $value_is_complete ) {
			return new WP_Error( 'error', happyforms_get_validation_message( 'field_empty' ) );
		}

		// if incomplete, return an invalid message to frontend
		if ( ! $value_is_empty && ! $value_is_complete ) {
			return new WP_Error( 'error', happyforms_get_validation_message( 'field_invalid' ) );
		}

		// Return if completely empty
		if ( $value_is_empty ) {
			return $this->get_default_value( $part );
		}

		// Year, month, day
		if ( ! empty( $validated_value['year'] ) && 'time' !== $part['date_type'] ) {
			// Year not in range
			$year = intval( $validated_value['year'] );
			$min_year = intval( $part['min_year'] );
			$max_year = intval( $part['max_year'] );

			if ( $year > $max_year || $year < $min_year ) {
				return new WP_Error(
					'error',
					happyforms_get_validation_message( 'field_invalid' )
				);
			}
		}

		// check for mix and max hour for time
		if ( ! empty( $validated_value['time'] )
			&& ( 'time' === $part['date_type'] ) ) {
			$time = intval( $validated_value['time'] );
			$min_hour = intval( $part['min_hour'] );
			$max_hour = intval( $part['max_hour'] );

			if ( $time > $max_hour || $time < $min_hour ) {
				return new WP_Error(
					'error',
					happyforms_get_validation_message( 'field_invalid' )
				);
			}
		}

		// General format
		$year = sprintf( '%04d', intval( $validated_value['year'] ) );
		$month = sprintf( '%02d', intval( $validated_value['month'] ) );
		$hour = sprintf( '%02d', intval( $validated_value['hour'] ) );
		$minute = sprintf( '%02d', intval( $validated_value['minute'] ) );
		$period = $validated_value['period'];

		switch( $part['date_type'] ) {
			case 'month':
				$string_value = sprintf( '%s', $month );
				$format = 'm';
				break;
			case 'year':
				$string_value = sprintf( '%s', $year );
				$format = 'Y';
				break;
			default:
				$string_value = '';
				$format = '';
				break;
		}

		if ( ! date_create_from_format( $format, $string_value ) ) {
			return new WP_Error( 'error', happyforms_get_validation_message( 'field_invalid' ) );
		}

		return $validated_value;
	}

	public function stringify_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$filled_components = array_filter( $value );
			$value_is_empty = ( 0 === count( $filled_components ) );

			if ( ! $value_is_empty ) {
				$site_date_format = happyforms_get_site_date_format();

				switch ( $part['date_type'] ) {
					case 'date':
						$date = $value['date'];

						if ( 'day_first' === $site_date_format ) {
							$value = date("d F Y", strtotime($date));
						} else {
							$value = date("F d, Y", strtotime($date));
						}
						break;
					case 'time':
						$time = $value['time'];
						$formatted_time = date('g:i A', strtotime($time));
						$value = $formatted_time;
						break;
					case 'datetime':
						$datetime = $value['datetime'];

						if ( 'day_first' === $site_date_format ) {
							$value = date("d F Y, g:i A", strtotime($datetime));
						} else {
							$value = date("F d, Y, g:i A", strtotime($datetime));
						}
						break;
					case 'month_year':
						$date = $value['date'];
						$value = date("F Y", strtotime($date));
						break;
					case 'month':
						$value = "{$month}";
						break;
					case 'year':
						$year = intval( $value['year'] );
						$value = "{$year}";
						break;
					default:
						$value = '';
						break;
				}
			} else {
				$value = '';
			}
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( 1 === intval( $part['required'] ) ) {
				$class[] = 'happyforms-part-date--required';
			}

			if ( isset( $part['date_type'] ) ) {
				$class[] = 'happyforms-part-date--' . $part['date_type'];
			}

			if ( isset( $part['time_format'] ) ) {
				$class[] = 'happyforms-part-date--' . $part['time_format'];
			}
		}

		return $class;
	}

	/**
	 * Enqueue scripts in customizer area.
	 *
	 * @since 1.0.0.
	 *
	 * @param array	List of dependencies.
	 *
	 * @return void
	 */
	public function customize_enqueue_scripts( $deps = array() ) {
		wp_enqueue_script(
			'part-date',
			happyforms_get_plugin_url() . 'inc/assets/js/parts/part-date.js',
			$deps, happyforms_get_version(), true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_date = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_date = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_date ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-part-date',
			happyforms_get_plugin_url() . 'inc/assets/js/frontend/date.js',
			array(), happyforms_get_version(), true
		);

		$deps[] = 'happyforms-part-date';

		return $deps;
	}

}
