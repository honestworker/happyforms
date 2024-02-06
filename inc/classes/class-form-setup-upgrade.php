<?php

class HappyForms_Form_Setup_Upgrade {

	private static $instance;

	public $action_abandon = 'happyforms-form-abandon';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		// Common form extensions
		add_filter( 'happyforms_get_steps', array( $this, 'steps_add_preview' ), 10, 2 );
		add_filter( 'happyforms_meta_fields', array( $this, 'meta_fields' ) );
		add_filter( 'happyforms_frontend_settings', array( $this, 'frontend_settings' ), 10, 1 );
		add_action( 'wp_ajax_' . $this->action_abandon, array( $this, 'form_abandoned' ) );
		add_action( 'wp_ajax_nopriv_' . $this->action_abandon, array( $this, 'form_abandoned' ) );
		add_filter( 'happyforms_after_submission_links_template', array( $this, 'submission_links_template' ), 10 );
		add_filter( 'happyforms_get_form_attributes', array( $this, 'form_attributes' ), 10, 2 );

		// Reviewable form display
		add_filter( 'happyforms_get_submit_template_path', array( $this, 'submit_preview_template' ), 10, 2 );
		add_filter( 'happyforms_get_submit_template_path', array( $this, 'confirm_preview_partial' ), 20, 2 );
		add_filter( 'happyforms_form_class', array( $this, 'form_html_class_preview' ), 10, 2 );
		add_action( 'happyforms_parts_before', array( $this, 'form_open_preview' ) );
		add_filter( 'happyforms_part_attributes', array( $this, 'part_attributes_preview' ), 10, 4 );
		add_action( 'happyforms_part_before', array( $this, 'part_before_preview' ), 10, 2 );
		add_action( 'happyforms_part_after', array( $this, 'part_after_preview' ), 10, 2 );

		add_filter( 'happyforms_setup_controls', array( $this, 'setup_controls' ) );
		add_filter( 'happyforms_email_controls', array( $this, 'email_controls' ) );
		add_action( 'happyforms_messages_controls', array( $this, 'messages_controls' ) );
		add_filter( 'happyforms_messages_fields', array( $this, 'get_messages_fields' ) );

		add_action( 'happyforms_do_setup_control', array( $this, 'do_control' ), 10, 3 );
		add_action( 'happyforms_do_email_control', array( $this, 'do_control' ), 10, 3 );
		add_action( 'happyforms_do_setup_control', array( $this, 'do_deprecated_control' ), 10, 3 );

		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_styles' ) );
		add_filter( 'happyforms_part_customize_template_path_table', array( $this, 'part_table_set_customize_template_path' ) );
		add_action( 'happyforms_response_created', array( $this, 'increment_unique_id' ), 10, 2 );
	}

	public function get_fields() {
		$fields = array(
			'unique_id' => array(
				'default' => 0,
				'sanitize' => 'happyforms_sanitize_checkbox',
			),
			'unique_id_start_from' => array(
				'default' => 1,
				'sanitize' => 'intval',
			),
			'unique_id_prefix' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field',
			),
			'unique_id_suffix' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field',
			),
			'confirmation_email_respondent_address' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field',
			)
		);

		return $fields;
	}

	public function get_messages_fields( $fields ) {
		$messages_fields = array(
			'review_step_message' => array(
				'default' => __( 'Please double-check for mistake.', 'happyforms' ),
				'sanitize' => 'esc_html',
			),
			'review_button_label' => array(
				'default' => __( 'Review reply', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'edit_button_label' => array(
				'default' => __( 'Edit', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'multi_step_current_page_label' => array(
				'default' => __( 'Step', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'multi_step_back_label' => array(
				'default' => __( 'Back', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'submission_redirect_notice' => array(
				'default' => __( "In a few seconds, you'll be automatically redirected to another page.", 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'redirect_now_link' => array(
				'default' => __( 'Continue to redirected page now', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'print_submission_link' => array(
				'default' => __( 'Print my submission', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
		);

		$fields = array_merge( $fields, $messages_fields );

		return $fields;
	}


	public function meta_fields( $fields ) {
		$fields = array_merge( $fields, $this->get_fields() );

		return $fields;
	}

	public function setup_controls( $controls ) {
		// unset upsell for "Redirect to web address"
		unset( $controls[11] );

		$setup_controls = array(
			19 => array(
				'type' => 'url',
				'label' => __( 'Redirect to this page address (URL) after submission', 'happyforms' ),
				'placeholder' => __( 'Search or type URL', 'happyforms' ),
				'field' => 'redirect_url',
			),
			51 => array(
				'type' => 'redirect-checkbox',
				'label' => __( 'Open web address in new tab', 'happyforms' ),
				'field' => 'redirect_blank',
			),
			1700 => array(
				'type' => 'identifier-checkbox',
				'label' => __( 'Add submission identifier', 'happyforms' ),
				'field' => 'unique_id',
			),
			1701 => array(
				'type' => 'group_start',
				'trigger' => 'unique_id'
			),
			1702 => array(
				'type' => 'identifier-number',
				'label' => __( 'Start counter from', 'happyforms' ),
				'field' => 'unique_id_start_from',
				'min' => 0
			),
			1703 => array(
				'type' => 'identifier-text',
				'label' => __( 'Prefix', 'happyforms' ),
				'field' => 'unique_id_prefix',
				'autocomplete' => 'off',
			),
			1704 => array(
				'type' => 'identifier-text',
				'label' => __( 'Suffix', 'happyforms' ),
				'field' => 'unique_id_suffix',
				'autocomplete' => 'off',
			),
			1705 => array(
				'type' => 'group_end'
			),
			1800 => array(
				'type' => 'checkbox',
				'label' => __( 'Require submitters to review a submission', 'happyforms' ),
				'field' => 'preview_before_submit',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $setup_controls );

		return $controls;
	}

	public function email_controls( $controls ) {
		$email_controls = array(
			630 => array(
				'type' => 'email-parts-list',
				'label' => __( 'To email address', 'happyforms' ),
				'field' => 'confirmation_email_respondent_address',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $email_controls );

		return $controls;
	}

	public function messages_controls( $controls ) {
		$message_controls = array(
			41 => array(
				'type' => 'text',
				'label' => __( 'Form redirected after submission', 'happyforms' ),
				'field' => 'submission_redirect_notice',
			),
			140 => array(
				'type' => 'escaped_text',
				'label' => __( 'Submitter is viewing review page', 'happyforms' ),
				'field' => 'review_step_message',
			),
			2263 => array(
				'type' => 'text',
				'label' => __( 'Review reply', 'happyforms' ),
				'field' => 'review_button_label',
				'autocomplete' => 'off',
			),
			2262 => array(
				'type' => 'text',
				'label' => __( 'Edit reply', 'happyforms' ),
				'field' => 'edit_button_label',
				'autocomplete' => 'off',
			),
			6231 => array(
				'type' => 'text',
				'label' => __( 'Current page', 'happyforms' ),
				'field' => 'multi_step_current_page_label',
			),
			2266 => array(
				'type' => 'text',
				'label' => __( 'Previous page', 'happyforms' ),
				'field' => 'multi_step_back_label',
			),
			2267 => array(
				'type' => 'text',
				'label' => __( 'Redirect to page', 'happyforms' ),
				'field' => 'redirect_now_link',
			),
			2023 => array(
				'type' => 'text',
				'label' => __( 'Print user submission', 'happyforms' ),
				'field' => 'print_submission_link',
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $message_controls );

		return $controls;
	}

	public function do_control( $control, $field, $index ) {
		$type = $control['type'];

		switch( $type ) {
			case 'parts-list':
				require( happyforms_get_include_folder() . '/templates/customize-controls/parts-list.php' );
				break;
			case 'url':
				require( happyforms_get_include_folder() . '/templates/customize-controls/url.php' );
				break;
			break;
		}
	}

	public function do_deprecated_control( $control, $field, $index ) {
		$type = $control['type'];

		switch( $type ) {
			case 'identifier-checkbox':
			case 'identifier-number':
			case 'identifier-text':
				$form = happyforms_customize_get_current_form();

				if ( $form[ 'unique_id' ] == 1 ) {
					$path = happyforms_get_core_folder() . '/templates/customize-controls/setup';
					$type = str_replace( 'identifier-', '', $type );

					require( "{$path}/{$type}.php" );
				}

				break;
			case 'redirect-checkbox':
				$form = happyforms_customize_get_current_form();
				$path = happyforms_get_core_folder() . '/templates/customize-controls/setup';

				if ( empty( $form['redirect_blank'] ) ) {
					break;
				}

				require( "{$path}/checkbox.php" );
				break;
		}
	}

	public function form_attributes( $attrs, $form ) {
		if ( isset( $form['redirect_blank'] ) && '1' == $form['redirect_blank'] ) {
			$attrs['data-happyforms-redirect-blank'] = '';
		}

		return $attrs;
	}

	public function increment_unique_id( $response_id, $form ) {
		if ( intval( $form['unique_id'] ) ) {
			$increment = intval( $form['unique_id_start_from'] );
			happyforms_update_meta( $form['ID'], 'unique_id_start_from', $increment + 1 );
		}
	}

	public function requires_confirmation( $form ) {
		return ( 1 === intval( $form['preview_before_submit'] ) );
	}

	public function part_table_set_customize_template_path( $template_path ) {
		$template_path = happyforms_get_include_folder() . '/templates/parts/customize-table.php';

		return $template_path;
	}

	public function part_before_preview( $part, $form ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'review' === happyforms_get_current_step( $form ) ) ) {
			require( happyforms_get_include_folder() . '/templates/partials/part-preview.php' );
		}
	}

	public function part_after_preview( $part, $form ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'review' === happyforms_get_current_step( $form ) ) ) {
			?>
			</div></div>
			<?php
		}
	}

	public function part_attributes_preview( $attributes, $part, $form, $component ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'review' === happyforms_get_current_step( $form ) ) ) {
			$attributes[] = 'readonly';
		}

		return $attributes;
	}

	public function form_open_preview( $form ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'review' === happyforms_get_current_step( $form ) ) ) {
			?>
			<div>
				<?php echo wpautop( html_entity_decode( happyforms_get_form_property( $form, 'review_step_message' ) ) ); ?>
			</div>
			<?php
		}
	}

	public function form_html_class_preview( $classes, $form ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'review' === happyforms_get_current_step( $form ) ) ) {
			$classes[] = 'happyforms-form-preview';
		}

		return $classes;
	}

	public function confirm_preview_partial( $path, $form ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'review' === happyforms_get_current_step( $form ) ) ) {
			$path = happyforms_get_include_folder() . '/templates/partials/form-confirm-preview.php';
		}

		return $path;
	}

	public function submit_preview_template( $path, $form ) {
		if ( happyforms_get_form_property( $form, 'preview_before_submit' )
			&& ( 'preview' === happyforms_get_current_step( $form ) ) ) {
			$path = happyforms_get_include_folder() . '/templates/partials/form-submit-preview.php';
		}

		return $path;
	}

	public function steps_add_preview( $steps, $form ) {
		if ( $this->requires_confirmation( $form ) ) {
			$steps[100] = 'preview';
			$steps[200] = 'review';
		}

		return $steps;
	}

	public function customize_enqueue_styles() {
		wp_enqueue_style(
			'happyforms-customize-upgrade',
			happyforms_get_plugin_url() . 'inc/assets/css/customize.css',
			array(), happyforms_get_version()
		);
	}

	public function frontend_settings( $data ) {
		$data['actionAbandon'] = $this->action_abandon;

		return $data;
	}

	public function form_abandoned() {
		do_action( 'happyforms_form_abandoned' );
	}

	public function submission_links_template( $template ) {
		$template = happyforms_get_include_folder() . '/templates/partials/form-after-submission-links.php';

		return $template;
	}

}

if ( ! function_exists( 'happyforms_get_form_setup_upgrade' ) ):
/**
 * Get the HappyForms_Form_Controller class instance.
 */
function happyforms_get_form_setup_upgrade() {
	return HappyForms_Form_Setup_Upgrade::instance();
}

endif;

/**
 * Initialize the HappyForms_Form_Controller class immediately.
 */
happyforms_get_form_setup_upgrade();
