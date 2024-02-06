<?php

class HappyForms_Exporter_XML {

	public $form_id;
	public $filename;
	public $with_responses;


	public function __construct( $form_id, $filename, $with_responses ) {
		$this->form_id = $form_id;
		$this->filename = $filename;
		$this->with_responses = $with_responses;
	}

	public function export() {
		global $wpdb;

		$form = $wpdb->get_row( $wpdb->prepare( "
			SELECT * FROM $wpdb->posts WHERE ID = %d AND post_type = 'happyform';
		", $this->form_id ) );

		if ( ! $form ) {
			$error = __( 'Form not found', 'happyforms' );
			return new WP_Error( $error );
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $this->filename );
		header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

		?><?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?><root xmlns:hf="happyforms"><?php

		$this->export_form( $form );

		if ( $this->with_responses ) {
			$this->export_responses( $form );
			$this->export_polls( $form );
		}

		?></root><?php
	}

	private function export_form( $form ) {
		?><hf:form><?php
		?><hf:post_title><?php echo $this->cdata( $form->post_title ); ?></hf:post_title><?php
		?><hf:post_status><?php echo $this->cdata( $form->post_status ); ?></hf:post_status><?php

		$this->export_metas( $form );

		?></hf:form><?php
	}

	private function export_responses( $form ) {
		global $wpdb;

		$responses = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.*
			FROM $wpdb->posts p
			JOIN $wpdb->postmeta m ON p.ID = m.post_id
			AND m.meta_key = '_happyforms_form_id'
			AND m.meta_value = %d
			WHERE p.post_type = 'happyforms-message';
		", $form->ID ) );

		foreach ( $responses as $response ) {
			$this->export_response( $response );
		}
	}

	private function export_response( $response ) {
		?><hf:response><?php
		?><hf:post_title><?php echo $this->cdata( $response->post_title ); ?></hf:post_title><?php
		?><hf:post_status><?php echo $this->cdata( $response->post_status ); ?></hf:post_status><?php

		$this->export_metas( $response );
		$this->export_attachments( $response );

		?></hf:response><?php
	}

	private function export_attachments( $response ) {
		global $wpdb;

		$attachments = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.ID, p.guid
			FROM $wpdb->posts p
			WHERE p.post_type = 'attachment'
			AND p.post_parent = %d
			GROUP BY p.guid;
		", $response->ID ) );

		foreach( $attachments as $attachment ) {
			$mime = get_post_mime_type( $attachment->ID );
			$value = serialize( array(
				'mime' => $mime,
				'url' => $attachment->guid,
			) );
			?><hf:attachment><?php echo $this->cdata( $value ); ?></hf:attachment><?php
		}
	}

	private function export_polls( $form ) {
		global $wpdb;

		$polls = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.*
			FROM $wpdb->posts p
			JOIN $wpdb->postmeta m ON p.ID = m.post_id
			AND m.meta_key = '_happyforms_form_id'
			AND m.meta_value = %d
			WHERE p.post_type = 'happyforms-poll';
		", $form->ID ) );

		foreach ( $polls as $poll ) {
			$this->export_poll( $poll );
		}
	}

	private function export_poll( $poll ) {
		?><hf:poll><?php
		?><hf:post_title><?php echo $this->cdata( $poll->post_title ); ?></hf:post_title><?php
		?><hf:post_name><?php echo $this->cdata( $poll->post_name ); ?></hf:post_name><?php

		$this->export_metas( $poll );

		?></hf:poll><?php
	}

	private function export_metas( $post ) {
		global $wpdb;

		$metas = $wpdb->get_results( $wpdb->prepare( "
			SELECT m.meta_key, m.meta_value
			FROM $wpdb->postmeta m
			JOIN $wpdb->posts p ON p.ID = m.post_id
			AND m.meta_key LIKE '_happyforms%%'
			WHERE p.ID = %d;
		", $post->ID ) );

		$excluded_meta = [
			'_happyforms_count_submissions_unread',
			'_happyforms_count_submissions_read',
			'_happyforms_count_submissions_spam',
			'_happyforms_count_submissions_trash',
			'_happyforms_count_submissions_total',
		];

		foreach( $metas as $meta ) {
			if ( ! $this->with_responses && in_array( $meta->meta_key, $excluded_meta ) ) {
				continue;
			}

			$meta_value = $this->value( $meta->meta_value );
			?><hf:meta name="<?php echo $meta->meta_key; ?>"><?php echo $meta_value; ?></hf:meta><?php
		}
	}

	private function value( $value ) {
		$value = (
			'string' === gettype( $value ) ?
			$this->cdata( $value ) : $value
		);

		return $value;
	}

	private function cdata( $str ) {
		if ( ! seems_utf8( $str ) ) {
			$str = utf8_encode( $str );
		}

		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

}