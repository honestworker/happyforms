<?php

class HappyForms_Importer_XML {

	private $entity_id = false;
	private $form_id = false;
	private $meta_key = false;
	private $cdata = '';
	private $error = false;
	private $counters = array();

	public function import( $path ) {
		if ( ! is_file( $path ) ) {
			return new WP_Error( 'Invalid file.' );
		}

		$stream = fopen( $path, 'r' );
		$parser = xml_parser_create( 'UTF-8' );

		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_set_object( $parser, $this );
		xml_set_character_data_handler( $parser, 'cdata' );
		xml_set_element_handler( $parser, 'tag_open', 'tag_close' );

		while( ( $data = fread( $stream, 16384 ) ) ) {
			if ( ! xml_parse( $parser, $data ) ) {
				$this->error_parse( $parser );
			}

			if ( $this->error ) {
				return $this->error;
			}
		}

		xml_parse( $parser, '', true );
		xml_parser_free( $parser );
		fclose( $stream );

		return true;
	}

	public function tag_open( $parse, $tag, $attr ) {
		switch( $tag ) {
			case 'hf:form':
				$this->create_post( 'happyform' );
				$this->form_id = $this->entity_id;
				break;

			case 'hf:response':
				$this->create_post( 'happyforms-message' );
				break;

			case 'hf:poll':
				$this->create_post( 'happyforms-poll' );
				break;

			case 'hf:meta':
				$this->meta_key = (
					isset( $attr['name'] ) ?
					$attr['name'] : false
				);
				break;
			default:
				break;
		}
	}

	function cdata( $parser, $cdata ) {
		$this->cdata .= $cdata;
	}

	public function tag_close( $parse, $tag ) {
		switch( $tag ) {
			case 'hf:post_title':
				$this->update_post_title();
				break;
			case 'hf:post_name':
				$this->update_post_name();
				break;
			case 'hf:post_status':
				$this->update_post_status();
				break;
			case 'hf:meta':
				$this->update_post_meta();
				break;
			case 'hf:attachment':
				$this->create_attachment();
				break;
		}

		$this->cdata = '';
	}

	public function create_post( $post_type ) {
		$this->entity_id = wp_insert_post( array(
			'post_type' => $post_type,
			'post_status' => 'publish',
		) );

		if ( ! $this->entity_id ) {
			$this->error_insert( $post_type );
		}

		$this->increment_counter( $post_type );
	}

	public function create_attachment() {
		$attachment = maybe_unserialize( $this->cdata );

		$attachment_id = wp_insert_attachment( array(
			'guid' => $attachment['url'],
			'post_mime_type' => $attachment['mime'],
			'post_content' => '',
			'post_status' => 'inherit',
		), $this->entity_id );

		update_post_meta( $attachment_id, '_happyforms_hash_id', '' );
	}

	public function update_post_title() {
		$this->entity_id = wp_update_post( array(
			'ID' => $this->entity_id,
			'post_title' => $this->cdata,
		) );
	}

	public function update_post_name() {
		$post_name = $this->cdata;
		$post_name = explode( '-', $post_name );
		$post_name[0] = $this->form_id;
		$post_name = implode( '-', $post_name );

		$this->entity_id = wp_update_post( array(
			'ID' => $this->entity_id,
			'post_name' => $post_name,
		) );
	}

	public function update_post_status() {
		$this->entity_id = wp_update_post( array(
			'ID' => $this->entity_id,
			'post_status' => $this->cdata,
		) );
	}

	public function update_post_meta() {
		$meta_value = maybe_unserialize( $this->cdata );

		if ( '_happyforms_form_id' === $this->meta_key ) {
			$meta_value = $this->form_id;
		}

		update_post_meta( $this->entity_id, $this->meta_key, $meta_value );
	}

	public function get_entity_label( $post_type, $count ) {
		$label = '';

		switch ( $post_type ) {
			case 'happyform':
				$label = __( 'form', 'happyforms' );

				if ( $count > 1 ) {
					$label = __( 'forms', 'happyforms' );
				}

				break;

			case 'happyforms-message':
				$label = __( 'submission', 'happyforms' );

				if ( $count > 1 ) {
					$label = __( 'submissions', 'happyforms' );
				}

				break;

			case 'happyforms-poll':
				$label = __( 'poll', 'happyforms' );

				if ( $count > 1 ) {
					$label = __( 'polls', 'happyforms' );
				}

				break;
		}

		return $label;
	}

	public function increment_counter( $post_type ) {
		$this->counters[$post_type] = (
			isset( $this->counters[$post_type] ) ?
			$this->counters[$post_type] + 1 : 1
		);
	}

	public function get_success_messages() {
		$messages = array();

		foreach( $this->counters as $post_type => $count ) {
			$label = $this->get_entity_label( $post_type, $count );
			$message = sprintf(
				__( '%s %s imported successfully.', 'happyforms' ),
				$count, $label
			);
			$messages[] = $message;
		}

		$messages[] = __( 'All done!', 'happyforms' );

		return $messages;
	}

	public function error_parse( $parser ) {
		$current_line = xml_get_current_line_number( $parser );
		$current_column = xml_get_current_column_number( $parser );
		$error_code = xml_get_error_code( $parser );
		$error_string = xml_error_string( $error_code );
		$code = 'import-parse';
		$message = __( 'An error occured while parsing import file.', 'happyforms' );

		$this->add_error( $code, $message );
	}

	public function error_insert( $post_type ) {
		$code = 'import-insert';
		$label = $this->get_entity_label( $post_type );
		$message = sprintf(
			__( 'An error occured while importing %s.', 'happyforms' ),
			$label
		);

		$this->add_error( $code, $message );
	}

	private function add_error( $code, $message, $data = array() ) {
		if ( ! is_wp_error( $this->error ) ) {
			$this->error = new WP_Error();
		}

		$this->error->add( $code, $message, $data );
	}

}
