<?php

class HappyForms_Upgrade extends HappyForms_Core {

	public $updater;

	public function initialize_plugin() {
		$this->load_translations();

		parent::initialize_plugin();

		add_filter( 'happyforms_show_welcome_page', '__return_false' );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_preview' ) );
		add_action( 'happyforms_customize_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ) );
		add_filter( 'happyforms_frontend_stylesheets_url', array( $this, 'set_frontend_stylesheets_url' ) );

		// Helpers
		require_once( happyforms_get_include_folder() . '/helpers/helper-misc.php' );

		// Updates
		require_once( happyforms_get_updater_folder() . '/updater.php' );
		$this->updater = new TTF_Product_Rest_Updater( happyforms_get_plugin_metadata() );

		// License checks
		add_action( 'admin_init', array( $this, 'register_subscribe_modal' ) );
		add_action( 'ttf_product_updater_license_check', array( $this, 'updater_license_check' ), 10, 2 );
		add_filter( 'happyforms_dashboard_modal_settings', [ $this, 'get_dashboard_modal_settings' ] );

		add_filter( 'ttf_product_updater_transition_message', array( $this, 'get_updater_transition_message' ), 10, 2 );
		add_filter( 'ttf_product_updater_show_transition_notice', array( $this, 'updater_show_transition_notice' ), 10, 2 );
		add_filter( 'ttf_product_updater_membership_required_message', array( $this, 'get_updater_membership_required_message' ), 10, 2 );
		add_filter( 'ttf_product_updater_banners', array( $this, 'get_updater_banners' ), 10, 2 );
		add_filter( 'ttf_product_updater_icons', array( $this, 'get_updater_icons' ), 10, 2 );
		add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
		add_action( 'after_plugin_row_' . happyforms_plugin_name(), array( $this, 'after_plugin_row' ), 10, 3 );

		// Form extensions
		require_once( happyforms_get_include_folder() . '/classes/class-submission-counter.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-form-setup-upgrade.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-form-messages-upgrade.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-form-styles-upgrade.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-form-shuffle-upgrade.php' );

		$part_library = happyforms_get_part_library();

		// Website url part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-website-url.php' );
		$part_library->register_part( 'HappyForms_Part_WebsiteUrl', 4 );

		// Attachment part
		require_once( happyforms_get_include_folder() . '/helpers/helper-upload.php' );
		require_once( happyforms_get_include_folder() . '/helpers/helper-form-templates.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-attachment-controller.php' );
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-attachment.php' );
		$part_library->register_part( 'HappyForms_Part_Attachment', 7 );

		// Phone part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-phone.php' );
		$part_library->register_part( 'HappyForms_Part_Phone', 10 );

		// Date part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-date.php' );
		$part_library->register_part( 'HappyForms_Part_Date', 11 );

		// Scale part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-scale.php' );
		$part_library->register_part( 'HappyForms_Part_Scale', 12 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-signature.php' );
		$part_library->register_part( 'HappyForms_Part_Signature', 13 );

		// Rating part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-rating.php' );
		$part_library->register_part( 'HappyForms_Part_Rating', 14 );

		// Terms part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-scrollable-terms.php' );
		$part_library->register_part( 'HappyForms_Part_Scrollable_Terms', 16 );


		// Table part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-table.php' );
		$part_library->register_part( 'HappyForms_Part_Table', 18 );

		// Address part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-address.php' );
		$part_library->register_part( 'HappyForms_Part_Address', 19 );

		// Rank order part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-rank-order.php' );
		$part_library->register_part( 'HappyForms_Part_Rank_Order', 20 );

		// Likert scale part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-likert-scale.php' );
		$part_library->register_part( 'HappyForms_Part_Likert_Scale', 21 );

		// Title part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-title.php' );
		$part_library->register_part( 'HappyForms_Part_Title', 22 );

		// Legal part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-legal.php' );
		$part_library->register_part( 'HappyForms_Part_Legal', 23 );

		// Narrative part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-narrative.php' );
		$part_library->register_part( 'HappyForms_Part_Narrative', 24 );

		// Toggletip part
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-toggletip.php' );
		$part_library->register_part( 'HappyForms_Part_Toggletip', 107 );

		// Modals
		require_once( happyforms_get_include_folder() . '/helpers/helper-modal.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-form-modals.php' );

		// Restrict entries
		require_once( happyforms_get_include_folder() . '/classes/class-form-restrict.php' );

		// Form scheduling
		require_once( happyforms_get_include_folder() . '/classes/class-form-schedule.php' );

		// Password protection
		require_once( happyforms_get_include_folder() . '/classes/class-form-password-protection.php' );

		// Turn off styles
		require_once( happyforms_get_include_folder() . '/classes/class-form-mute-styles.php' );

		// Privacy settings
		require_once( happyforms_get_include_folder() . '/classes/class-privacy-settings.php' );

		// Form archive
		require_once( happyforms_get_include_folder() . '/classes/class-form-status.php' );

		// Submission sessions
		require_once( happyforms_get_include_folder() . '/classes/class-form-sessions.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-session-controller.php' );

		// Multi step
		require_once( happyforms_get_include_folder() . '/classes/class-form-stepper.php' );
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-page-break.php' );
		$part_library->register_part( 'HappyForms_Part_PageBreak', 109 );


		if ( is_admin() ) {
			require_once( happyforms_get_include_folder() . '/classes/class-admin-pages-controller.php' );
			require_once( happyforms_get_include_folder() . '/classes/class-settings-page-controller.php' );
			require_once( happyforms_get_integrations_folder() . '/classes/class-integrations-page-controller.php' );
			require_once( happyforms_get_include_folder() . '/classes/class-export-controller-upgrade.php' );
			happyforms_get_export_controller_upgrade();
		}

		// Polls
		require_once( happyforms_get_include_folder() . '/classes/class-polls-controller.php' );
		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-poll.php' );
		$part_library->register_part( 'HappyForms_Part_Poll', 25 );

		// PDFs
		require_once( happyforms_get_include_folder() . '/classes/class-pdf-controller.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-pdf.php' );

		// Tasks
		require_once( happyforms_get_include_folder() . '/classes/tasks/class-task.php' );
		require_once( happyforms_get_include_folder() . '/classes/tasks/class-task-email-owner.php' );
		require_once( happyforms_get_include_folder() . '/classes/tasks/class-task-email-user.php' );
		require_once( happyforms_get_include_folder() . '/classes/tasks/class-task-email-abandonment.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-task-controller.php' );

		// Integrations
		require_once( happyforms_get_integrations_folder() . '/classes/class-integrations.php' );

		// Conditional logic
		require_once( happyforms_get_include_folder() . '/classes/class-condition.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-conditional-controller.php' );
		require_once( happyforms_get_include_folder() . '/classes/class-conditionals-ui-controller.php' );

		// Validation messages management
		require_once( happyforms_get_include_folder() . '/classes/class-validation-messages-upgrade.php' );

		// Message blocklist
		require_once( happyforms_get_include_folder() . '/classes/class-message-blocklist.php' );

		// Role permissions
		require_once( happyforms_get_include_folder() . '/classes/class-role-permissions.php' );

		// Answer limiting
		require_once( happyforms_get_include_folder() . '/classes/class-form-answer-limiter.php' );

		// Email test
		require_once( happyforms_get_include_folder() . '/classes/class-email-test.php' );
	}

	public function load_translations() {
		$domains = array( 'happyforms', 'happyforms-upgrade' );

		foreach( $domains as $domain ) {
			$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
			$mofile = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';
			load_textdomain( 'happyforms-upgrade', $mofile );
			load_textdomain( 'happyforms', $mofile );
		}
	}

	public function admin_menu() {
		parent::admin_menu();

		global $menu, $submenu;

		if ( ! isset( $submenu['happyforms'] ) ) {
			return;
		}


		$menu_links = wp_list_pluck( $menu, 2 );
		$forms_menu_index = array_search( 'happyforms', $menu_links );

		if ( false !== $forms_menu_index ) {
			$menu[$forms_menu_index][0] .= happyforms_unregistered_badge();
		}

		$submenu_links = wp_list_pluck( $submenu['happyforms'], 2 );
		$activity_index = array_search( 'edit.php?post_type=happyforms-message', $submenu_links );

		if ( false === $activity_index ) {
			return;
		}

		$submenu['happyforms'][$activity_index][0] .= happyforms_unread_messages_badge();
	}

	public function admin_screens() {
		parent::admin_screens();

		global $pagenow;

		$form_post_type = happyforms_get_form_controller()->post_type;
		$response_post_type = happyforms_get_message_controller()->post_type;
		$post_types = array( $form_post_type, $response_post_type );
		$current_post_type = get_current_screen()->post_type;

		if ( ! in_array( $pagenow, array( 'edit.php', 'post.php' ) )
			|| ! in_array( $current_post_type, $post_types ) ) {

			return;
		}

		switch( $current_post_type ) {
			case $form_post_type:
				require_once( happyforms_get_include_folder() . '/classes/class-form-admin-upgrade.php' );
				break;
			case $response_post_type:
				if ( 'edit.php' === $pagenow ) {
					require_once( happyforms_get_include_folder() . '/classes/class-message-admin.php' );
				} else {
					require_once( happyforms_get_include_folder() . '/classes/class-message-admin-edit.php' );
				}
				break;
		}
	}

	public function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();

		wp_enqueue_style(
			'happyforms-admin-upgrade',
			happyforms_get_plugin_url() . 'inc/assets/css/admin.css',
			array( 'happyforms-admin' ), happyforms_get_version()
		);

		wp_enqueue_script(
			'happyforms-admin-upgrade',
			happyforms_get_plugin_url() . 'inc/assets/js/admin/dashboard.js',
			array( 'happyforms-admin' ), happyforms_get_version(), true
		);

		if ( ! $this->is_registered() && ( happyforms_is_admin_screen() ) ) {
			$this->display_admin_subscribe_modal();

			return;
		}
	}

	private function display_admin_subscribe_modal() {
		$script = "
		( function( $ ) {

		var dashboardInit = happyForms.dashboard.init;

		happyForms.dashboard.init = function() {
			dashboardInit.apply( this, arguments );

			happyForms.modals.openSubscribeModal();
		}

		} )( jQuery );
		";

		wp_add_inline_script( 'happyforms-admin-upgrade', $script );
	}

	public function is_registered() {
		$registered = $this->updater->get_license_key();
		$registered = apply_filters( 'happyforms_is_registered', $registered );

		return $registered;
	}

	public function get_dashboard_modal_settings( $settings ) {
		$settings['subscribeModalActionRequestKey'] = $this->updater->action_request_key;
		$settings['subscribeModalActionAuthorize'] = $this->updater->action_authorize;
		$settings['subscribeModalNonceRequestKey'] = wp_create_nonce( $this->updater->action_request_key );
		$settings['subscribeModalNonceAuthorize'] = wp_create_nonce( $this->updater->action_authorize );
		$settings['subscribeModalProductPlan'] = $this->updater->product->plan;
		$settings['dashboardURL'] = admin_url();

		return $settings;
	}

	public function enqueue_styles_preview() {
		parent::enqueue_styles_preview();

		if ( ! happyforms_is_preview() ) {
			return;
		}

		wp_enqueue_style(
			'happyforms-upgrade-preview',
			happyforms_get_plugin_url() . 'inc/assets/css/preview.css',
			array(), happyforms_get_version()
		);
	}

	public function customize_enqueue_scripts() {
		if ( ! $this->is_registered() && $this->is_customize_mode() ) {
			$this->display_customize_subscribe_modal();
		}
	}

	private function display_customize_subscribe_modal() {
		$script = "
		( function( $ ) {

		var happyFormsStart = happyForms.start;

		happyForms.start = function() {
			happyFormsStart.apply( this, arguments );
			happyForms.modals.openSubscribeModal();
		};

		} )( jQuery );
		";

		wp_add_inline_script( 'happyforms-customize', $script );
	}

	public function set_frontend_stylesheets_url( $url ) {
		$url = happyforms_get_plugin_url() . 'inc/assets/css/frontend';

		return $url;
	}

	public function get_updater_membership_required_message( $message, $product_id ) {
		if ( $product_id !== $this->updater->product->id ) {
			return $message;
		}

		$message = __( 'To keep updated, please register Happyforms', 'ttf-product-updater' );

		return $message;
	}

	public function get_updater_transition_message( $message, $product_id ) {
		if ( $product_id !== $this->updater->product->id ) {
			return $message;
		}

		$message = sprintf(
			'<strong>%1s</strong>: %2s. <a href="%3s">%4s</a>.',
			'Happyforms',
			__( 'The roll out of our new updater reset your registration', 'ttf-product-updater' ),
			admin_url( 'admin.php?page=happyforms-settings' ),
			__( 'Please register again', 'ttf-product-updater' )
		);

		return $message;
	}

	public function updater_show_transition_notice( $show, $product_id ) {
		if ( $product_id !== $this->updater->product->id ) {
			return $show;
		}

		if ( happyforms_is_admin_screen( 'happyforms-settings' ) ) {
			$show = false;
		}

		return $show;
	}

	public function get_updater_banners( $banners, $product_id ) {
		if ( $product_id !== $this->updater->product->id ) {
			return $banners;
		}

		if ( ! $banners ) {
			$banners = array(
				'low' => 'https://happyforms.io/assets/img/plugin/banner-772x250.png',
				'high' => 'https://happyforms.io/assets/img/plugin/banner-1544x500.png',
			);
		}

		return $banners;
	}

	public function get_updater_icons( $icons, $product_id ) {
		if ( $product_id !== $this->updater->product->id ) {
			return $icons;
		}

		if ( ! $icons ) {
			$icons = array(
				'2x' => 'https://happyforms.io/assets/img/plugin/icon-256x256.png',
				'1x' => 'https://happyforms.io/assets/img/plugin/icon-128x128.png',
			);
		}

		return $icons;
	}

	public function admin_print_styles() {
		if ( ! $this->is_registered() ) : ?>
		<style type="text/css" media="screen">
		.plugins tr[data-slug="happyforms"] th,
		.plugins tr[data-slug="happyforms"] td {
			box-shadow: none;
		}

		@media screen and (max-width: 782px) {
			.plugins tr.happyforms-upgrade__notice-unregistered:before {
				background-color: #f0f6fc;
				border-left: 4px solid #72aee6;
			}
		}
		</style>
		<?php endif;
	}

	public function after_plugin_row( $plugin_file, $plugin_data, $status ) {
		if ( $this->is_registered() ||
			( isset( $plugin_data['update'] ) && $plugin_data['update'] ) ) {

			return;
		} ?>
		<tr class="plugin-update-tr active happyforms-upgrade__notice-unregistered">
			<td colspan="4" class="plugin-update colspanchange">
				<div class="update-message notice inline notice-error notice-alt">
					<p><?php printf(
						'%1$s. <a href="%2$s">%3$s</a>.',
						__( 'You\'re unregistered', 'happyforms' ),
						admin_url( '/edit.php?post_type=happyform' ),
						__( 'Register now', 'happyforms' )
					); ?></p>
				</div>
			</td>
		</tr>
		<?php
	}

	public function register_subscribe_modal() {
		happyforms_get_dashboard_modals()->register_modal( 'subscribe' );
	}

	public function updater_license_check( $product_id, $response ) {
		if ( $this->updater->product->id !== $product_id ) {
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( $body ) {
			$body = json_decode( $body );

			if ( isset( $body->code ) && 'error' === $body->code ) {
				delete_option( $this->updater->product->option );
			}
		}
	}

}
