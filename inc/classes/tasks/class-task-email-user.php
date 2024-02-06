<?php

class HappyForms_Task_Email_User extends HappyForms_Task {

	public static $event = 'email_user';

	public function run() {
		$response = happyforms_get_message_controller()->get( $this->response_id, true );

		if ( ! $response ) {
			error_log( 'Response not found.' );
			return;
		}

		$form_id = $response['form_id'];
		$form = happyforms_get_form_controller()->get( $form_id );

		$request = happyforms_get_meta( $this->response_id, 'request', true );

		if ( $request ) {
			$form = happyforms_get_conditional_controller()->get( $form, $request );
		}

		if ( ! $form ) {
			error_log( 'Form not found.' );
			return;
		}

		$email_part_id = happyforms_get_form_property( $form, 'confirmation_email_respondent_address' );
		$email_parts = array();

		if ( 'all' !== $email_part_id ) {
			$email_part = happyforms_get_form_controller()->get_part_by_id( $form, $email_part_id );

			if ( ! $email_part ) {
				$email_part = happyforms_get_form_controller()->get_first_part_by_type( $form, 'email' );
			}

			if ( $email_part ) {
				$email_parts[] = $email_part;
			}
		} else {
			$email_parts = happyforms_get_form_controller()->get_parts_by_type( $form, 'email' );
		}

		if ( ! empty( $form['confirmation_email_subject'] )
			&& ! empty( $form['confirmation_email_content'] )
			&& ! empty( $form['confirmation_email_sender_address'] ) ) {

			// Compose an email message
			$email_message = new HappyForms_Email_Message( $response );
			$senders = happyforms_get_form_property( $form, 'confirmation_email_sender_address' );
			$senders = explode( ',', $senders );
			$name = happyforms_get_form_property( $form, 'confirmation_email_from_name' );
			$from = $senders[0];
			$reply_to = happyforms_get_form_property( $form, 'confirmation_email_reply_to' );
			$reply_to = empty( $reply_to ) ? $from : $reply_to;

			$email_message->set_from( $from );
			$email_message->set_from_name( $name );
			$email_message->set_reply_to( $reply_to );
			$email_message->set_subject( $form['confirmation_email_subject'] );

			$to = array_map( function( $email_part ) use( $response ) {
				$part_id = $email_part['id'];
				$part_value = happyforms_get_message_part_value( $response['parts'][$part_id], $email_part );

				return $part_value;
			}, $email_parts );
			$to = array_values( array_filter( array_map( 'trim', $to ) ) );

			if ( ! empty( $to ) ) {
				$email_message->set_to( $to );
			}

			ob_start();
			require_once( happyforms_user_email_template_path() );
			$content = ob_get_clean();

			$email_message->set_content( $content );
			$email_message = apply_filters( 'happyforms_email_confirmation', $email_message );

			$email_message->send();
			do_action( 'happyforms_email_confirmation_sent', $email_message );
		}
	}

}
