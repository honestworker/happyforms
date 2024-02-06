<?php

class HappyForms_Role_Permissions {

	private static $instance;
	private static $hooked = false;

	public $main_capability = 'happyforms_manage';
	public $form_capability = 'happyforms_manage_forms';
	public $activity_capability = 'happyforms_manage_activity';
	public $settings_capability = 'happyforms_manage_settings';
	public $import_capability = 'happyforms_manage_import';
	public $export_capability = 'happyforms_manage_export';
	public $coupons_capability = 'happyforms_manage_coupons';
	public $integrations_capability = 'happyforms_manage_integrations';

	public $save_action = 'happyforms_save_role_permissions';
	public $save_nonce = 'happyforms-role-permissions';
	public $option = 'happyforms_role_permissions';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		if ( self::$hooked ) {
			return;
		}

		self::$hooked = true;

		add_action( 'wp_ajax_' . $this->save_action, array( $this, 'save_settings' ) );
		add_action( 'init', array( $this, 'set_admin_role_capabilities' ) );
		add_filter( 'happyforms_main_page_capabilities', array( $this, 'get_main_page_capabilities' ) );
		add_filter( 'happyforms_forms_page_capabilities', array( $this, 'get_forms_page_capabilities' ) );
		add_filter( 'happyforms_responses_page_capabilities', array( $this, 'get_responses_page_capabilities' ) );
		add_filter( 'happyforms_settings_page_capabilities', array( $this, 'get_settings_page_capabilities' ) );
		add_filter( 'happyforms_import_page_capabilities', array( $this, 'get_import_page_capabilities' ) );
		add_filter( 'happyforms_export_page_capabilities', array( $this, 'get_export_page_capabilities' ) );
		add_filter( 'happyforms_integrations_page_capabilities', array( $this, 'get_integrations_page_capabilities' ) );
		add_filter( 'happyforms_coupons_page_capabilities', array( $this, 'get_coupons_page_capabilities' ) );
		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ), 10, 4 );
		add_action( 'current_screen', array( $this, 'current_screen' ) );
	}

	public function get_defaults() {
		$roles = $this->get_roles();
		$defaults = array();

		foreach( $roles as $role_id => $role ) {
			$defaults[$role_id] = array(
				'allow' => 0,
				'allow_forms' => 0,
				'allow_activity' => 0,
				'allow_settings' => 0,
				'allow_import' => 0,
				'allow_export' => 0,
				'allow_coupons' => 0,
				'allow_integrations' => 0,
			);
		}

		return $defaults;
	}

	public function read() {
		$permissions = get_option( $this->option, '' );
		$permissions = wp_parse_args( $permissions, $this->get_defaults() );

		return $permissions;
	}

	public function write( $roles ) {
		$permissions = $this->get_defaults();

		foreach( $roles as $role_id => $role_permissions ) {
			foreach( $role_permissions as $permission => $allowed ) {
				$permissions[$role_id][$permission] = $allowed;
			}
		}

		update_option( $this->option, $permissions );

		foreach( $permissions as $role_id => $role_permissions ) {
			$role = get_role( $role_id );

			if ( ! $role ) {
				continue;
			}

			$allowed_permissions = $permissions[ $role_id ];
			$allow = $allowed_permissions['allow'];

			foreach ( $allowed_permissions as $permission => $value ) {
				if ( 'allow' == $permission ) {
					continue;
				}

				$capability = '';
				switch( $permission ) {
					case 'allow_forms':
						$capability = $this->form_capability;
						break;
					case 'allow_activity':
						$capability = $this->activity_capability;
						break;
					case 'allow_coupons':
						$capability = $this->coupons_capability;
						break;
					case 'allow_integrations':
						$capability = $this->integrations_capability;
						break;
					case 'allow_import':
						$capability = $this->import_capability;
						break;
					case 'allow_export':
						$capability = $this->export_capability;
						break;
					case 'allow_settings':
						$capability = $this->settings_capability;
						break;
					default:
						break;
				}

				if ( '' == $capability ) {
					continue;
				}

				if ( $allow & $allowed_permissions[ $permission ] ) {
					$role->add_cap( $capability );
				} else {
					$role->remove_cap( $capability );
				}
			}

		}
	}

	public function get_core_roles() {
		$roles = array( 'editor', 'author', 'contributor', 'subscriber' );

		return $roles;
	}

	public function get_roles() {
		$roles = get_editable_roles();
		unset( $roles['administrator'] );

		$extended_roles = apply_filters( 'happyforms_extended_privacy_roles', false );

		if ( ! $extended_roles ) {
			$roles = array_intersect_key( $roles, array_flip( $this->get_core_roles() ) );
		}

		return $roles;
	}

	public function save_settings() {
		if ( ! check_ajax_referer( $this->save_action, $this->save_nonce ) ) {
			return;
		}

		$permissions = isset( $_REQUEST['happyforms_role_permissions'] ) ? $_REQUEST['happyforms_role_permissions'] : '';
		$this->write( $permissions );

		ob_start();
		require_once( happyforms_get_include_folder() . '/templates/admin-settings-role-permissions.php' );
		$response = ob_get_clean();

		wp_send_json_success( array(
			'html' => $response,
			'message' => __( 'Changes saved.', 'happyforms' ),
		) );
	}

	public function set_admin_role_capabilities() {
		$role = get_role( 'administrator' );

		$role->add_cap( $this->form_capability );
		$role->add_cap( $this->activity_capability );
		$role->add_cap( $this->settings_capability );
		$role->add_cap( $this->coupons_capability );
		$role->add_cap( $this->import_capability );
		$role->add_cap( $this->export_capability );
		$role->add_cap( $this->integrations_capability );
	}

	public function get_main_page_capabilities() {
		return $this->main_capability;
	}

	public function get_forms_page_capabilities() {
		return $this->form_capability;
	}

	public function get_responses_page_capabilities() {
		return $this->activity_capability;
	}

	public function get_settings_page_capabilities() {
		return $this->settings_capability;
	}

	public function get_import_page_capabilities() {
		return $this->import_capability;
	}

	public function get_export_page_capabilities() {
		return $this->export_capability;
	}

	public function get_coupons_page_capabilities() {
		return $this->coupons_capability;
	}

	public function get_integrations_page_capabilities() {
		return $this->integrations_capability;
	}

	public function map_meta_cap( $caps, $cap, $user_id, $args ) {
		if ( 'edit_pages' === $cap && $this->is_forms_screen() ) {
			$caps = array( $this->form_capability );
		} else if ( 'edit_pages' === $cap && $this->is_activity_screen() ) {
			$caps = array( $this->activity_capability );
		} else if ( 'edit_post' === $cap && $this->is_forms_screen() ) {
			$caps = array( $this->form_capability );
		} else if ( 'edit_post' === $cap && $this->is_activity_screen() ) {
			$caps = array( $this->activity_capability );
		} else if ( 'edit_others_pages' === $cap && $this->is_activity_edit_screen() ) {
			$caps = array( $this->activity_capability );
		} else if ( 'edit_post' === $cap && $this->is_activity_edit_screen() ) {
			$caps = array( $this->activity_capability );
		} else if ( 'delete_post' === $cap && $this->is_form( $args[0] ) ) {
			$caps = array( $this->form_capability );
		} else if ( 'delete_post' === $cap && $this->is_activity( $args[0] ) ) {
			$caps = array( $this->activity_capability );
		} else if ( 'customize' === $cap && $this->is_form_edit_screen() ) {
			$caps = array( $this->form_capability );
		} else if ( 'customize' === $cap && $this->is_form_preview_frame() ) {
			$caps = array( $this->form_capability );
		} else if ( $this->main_capability === $cap ) {
			if ( current_user_can( $this->form_capability ) ) {
				$caps = array( $this->form_capability );
			} else if ( current_user_can( $this->activity_capability ) ) {
				$caps = array( $this->activity_capability );
			} else if ( current_user_can( $this->settings_capability ) ) {
				$caps = array( $this->settings_capability );
			} else {
				$caps = array();
			}
		}

		return $caps;
	}

	public function current_screen( $screen ) {
		$has_access = true;

		if ( $this->is_forms_screen() && ! current_user_can( $this->form_capability ) ) {
			$has_access = false;
		} else if ( $this->is_form_edit_screen() && ! current_user_can( $this->form_capability ) ) {
			$has_access = false;
		} else if ( $this->is_activity_screen() && ! current_user_can( $this->activity_capability ) ) {
			$has_access = false;
		} else if ( $this->is_activity_edit_screen() && ! current_user_can( $this->activity_capability ) ) {
			$has_access = false;
		} else if ( $this->is_settings_screen() && ! current_user_can( $this->settings_capability ) ) {
			$has_access = false;
		} else if ( $this->is_coupons_screen() && ! current_user_can( $this->coupons_capability ) ) {
			$has_access = false;
		} else if ( $this->is_integrations_screen() && ! current_user_can( $this->integrations_capability ) ) {
			$has_access = false;
		} else if ( $this->is_import_screen() && ! current_user_can( $this->import_capability ) ) {
			$has_access = false;
		} else if ( $this->is_export_screen() && ! current_user_can( $this->export_capability ) ) {
			$has_access = false;
		}

		if ( ! $has_access ) {
			wp_die( 'Sorry, you are not allowed to view this item.' );
		}
	}

	public function is_forms_screen() {
		global $pagenow, $typenow;

		$form_post_type = happyforms_get_form_controller()->post_type;

		if ( 'edit.php' === $pagenow && $form_post_type === $typenow ) {
			return true;
		}

		return false;
	}

	public function is_form( $post_id ) {
		$form_post_type = happyforms_get_form_controller()->post_type;
		$is_form = $form_post_type === get_post_type( $post_id );

		return $is_form;
	}

	public function is_activity( $post_id ) {
		$message_post_type = happyforms_get_message_controller()->post_type;
		$is_activity = $message_post_type === get_post_type( $post_id );

		return $is_activity;
	}

	public function is_form_edit_screen() {
		global $pagenow;

		if ( 'customize.php' === $pagenow && HappyForms()->is_customize_mode() ) {
			return true;
		}

		return false;
	}

	public function is_form_preview_frame() {
		if ( isset( $_REQUEST['post_type'] ) 
			&& 'happyform' === $_REQUEST['post_type'] ) {

			return true;
		}

		if ( isset( $_REQUEST['happyform'] )
			&& isset( $_REQUEST['customize_messenger_channel'] ) ) {

			return true;
		}

		if ( isset( $_REQUEST['happyforms'] )
			&& isset( $_REQUEST['wp_customize'] ) ) {

			return true;
		}

		return false;
	}

	public function is_activity_screen() {
		global $pagenow, $typenow;

		$message_post_type = happyforms_get_message_controller()->post_type;

		if ( 'edit.php' === $pagenow && $message_post_type === $typenow ) {
			return true;
		}

		return false;
	}

	public function is_activity_edit_screen() {
		global $pagenow, $typenow;

		$message_post_type = happyforms_get_message_controller()->post_type;
		
		if ( isset( $_POST['post_type'] ) && $message_post_type === $_POST['post_type'] ) {
			return true;
		}

		if ( 'post.php' === $pagenow && $message_post_type === $typenow ) {
			return true;
		}

		return false;
	}

	public function is_settings_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		if ( happyforms_is_admin_screen( 'happyforms-settings' ) ) {
			return true;
		}

		return false;
	}

	public function is_coupons_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		if ( happyforms_is_admin_screen( 'happyforms-coupon' ) ) {
			return true;
		}

		return false;
	}

	public function is_integrations_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		if ( happyforms_is_admin_screen( 'happyforms-integrations' ) ) {
			return true;
		}

		return false;
	}

	public function is_import_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		if ( happyforms_is_admin_screen( 'happyforms-import' ) ) {
			return true;
		}

		return false;
	}

	public function is_export_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		if ( happyforms_is_admin_screen( 'happyforms-export' ) ) {
			return true;
		}

		return false;
	}
	
}

if ( ! function_exists( 'happyforms_get_role_permissions' ) ):

function happyforms_get_role_permissions() {
	return HappyForms_Role_Permissions::instance();
}

endif;

happyforms_get_role_permissions();
