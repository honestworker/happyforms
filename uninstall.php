<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Free version specific options
delete_transient( 'happyforms_review_notice_recommend' );
delete_option( 'happyforms_modal_dismissed_onboarding' );
delete_option( 'happyforms_show_powered_by' );
delete_option( '_happyforms_received_submissions' );

// Forms
if ( ! defined( 'HAPPYFORMS_VERSION' ) ) {
    $statuses = array( 'publish', 'trash', 'any' );

    foreach( $statuses as $status ) {
        $form_ids = get_posts( array(
            'post_type' => 'happyform',
            'post_status' => $status,
            'numberposts' => -1,
            'fields' => 'ids',
        ) );

        foreach( $form_ids as $form_id ) {
            wp_delete_post( $form_id, true );
        }
    }
}

// Admin stats
delete_option( 'happyforms_stat_settings' );
delete_option( 'happyforms_stat_settings' );
delete_option( 'happyforms_stat_new_contacts' );
delete_option( 'happyforms_stat_reached_goals' );
delete_option( 'happyforms_stat_responses_abandoned' );
delete_option( 'happyforms_stat_responses_mobile' );
delete_option( 'happyforms_stat_responses_started' );
delete_option( 'happyforms_stat_responses_submitted' );
delete_option( 'happyforms_stat_settings' );
delete_option( 'happyforms_stat_validation_errors' );

// General options
delete_option( 'happyforms-data-version' );
delete_option( 'widget_happyforms_widget' );
delete_option( 'happyforms_goal_pages' );
delete_transient( '_happyforms_has_responses' );
delete_option( 'happyforms-tracking' );
delete_option( 'ttf_updates_key_happyforms' );

// User meta
$users = get_users();

foreach( $users as $user ) {
    delete_user_meta( $user->ID, 'happyforms-dismissed-notices' );
    delete_transient( 'happyforms_admin_notices_' . md5( $user->user_login ) );
    delete_user_meta( $user->ID, 'happyforms-settings-sections-states' );
}

// Blocklist
delete_option( 'happyforms_blocklist' );

// Activity
$responses = get_posts( array(
    'post_type' => 'happyforms-message',
    'post_status' => array_values( get_post_stati() ),
    'numberposts' => -1,
) );

foreach( $responses as $response ) {
    wp_delete_post( $response->ID, true );
}

delete_transient( 'happyforms_response_counters' );

// Migrations
delete_option( 'happyforms-data-version' );

// Polls
$polls = get_posts( array(
    'post_type' => 'happyforms-poll',
    'post_status' => 'any',
    'numberposts' => -1,
) );

foreach( $polls as $poll ) {
    wp_delete_post( $poll->ID, true );
}

// Privacy settings
delete_option( 'happyforms_privacy_settings' );
wp_clear_scheduled_hook( 'happyforms_schedule_privacy_cleanup' );

// Role permissions
delete_option( 'happyforms_role_permissions' );

// Validation messages
delete_option( 'happyforms-validation-messages' );

// Integrations
delete_option( '_happyforms_service_credentials' );

// Anti-spam
delete_option( '_happyforms_antispam_service_active' );

// Email
delete_option( '_happyforms_email_service_active' );

// Deactivation
delete_option( '_happyforms_cleanup_on_deactivate' );