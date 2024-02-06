<?php
class Happyforms_Email_Tester {
    private static $instance;
    public static $hooked = false;

    public $send_action = 'happyforms_send_test_email';

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

        add_action( 'wp_ajax_' . $this->send_action, array( $this, 'send_test_email' ) );
        add_action( 'happyforms_add_meta_boxes', array( $this, 'set_metaboxes' ) );
    }

    public function set_metaboxes() {
        $screen = get_plugin_page_hookname( plugin_basename( happyforms_get_settings_page_controller()->set_admin_page_url() ), 'happyforms' );

        if ( current_user_can( 'manage_options' ) ) {
            add_meta_box(
                'happyforms-email-test-tool',
                __( 'Email Test Tool', 'happyforms' ),
                function() {
                    require( happyforms_get_include_folder() . '/templates/admin-settings-email-test.php' );
                },
                $screen, 'side'
            );
        }
    }

    public function send_test_email() {
        check_admin_referer( $this->send_action );
        $is_valid = true;
        $message = '';

        $recipient_email = sanitize_email( $_REQUEST['email-recepient'] );

        if ( ! filter_var( $recipient_email, FILTER_VALIDATE_EMAIL ) ) {
          $is_valid = false;
          $message = 'Invalid email address.';
        }

        if ( $is_valid ) {
            $subject = 'A test email from Happyforms and WordPress.';
            $message = 'Hey there 👋' .
                        '<br/><br/>This is a test email from the Happyforms email testing tool. Everything is working and no action is required.' .
                        '<br/><br/>Questions or need help? Contact our friendly support team: ask@happyforms.io.' .
                        '<br/><br/>We\'re here for you!' .
                        '<br/><br/>Cheers,<br/>Happyforms';
            $success = true;

            try {
                add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
                if ( wp_mail( $recipient_email, $subject, $message ) ) {
                    $message = 'Test email sent!';
                } else {
                    $is_valid = false;
                    $message = 'Oops! Error occured, test email can\' be sent.';
                }
            } catch ( Exception $e ) {
                $is_valid = false;
                $message = 'Oops! Error occured, test email can\' be sent.';
            }
        }

        wp_send_json_success( array(
            'success' => $is_valid,
            'message' => $message
        ) );
    }

    public function get_content_type() {
        return 'text/html';
    }
}

if ( ! function_exists( 'happyforms_get_email_tester' ) ):

function happyforms_get_email_tester() {
    return Happyforms_Email_Tester::instance();
}

endif;

happyforms_get_email_tester();