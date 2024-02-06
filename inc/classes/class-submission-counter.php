<?php

class Happyforms_Submission_Counter {

	private static $instance;

	public $counters_transient = 'happyforms_response_counters';

	public $key_count_submission_read = 'count_submissions_read';
	public $key_count_submission_unread = 'count_submissions_unread';
	public $key_count_submission_spam = 'count_submissions_spam';
	public $key_count_submission_trash = 'count_submissions_trash';
	public $key_count_submission_total = 'count_submissions_total';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_action( 'init', array( $this, 'migrate_submission_count_transients' ) );
		add_action( 'happyforms_response_created', array( $this, 'response_created' ), 10, 2 );
		add_action( 'happyforms_submission_status_changed', array( $this, 'submission_status_changed' ) );
		add_action( 'happyforms_form_updated', array( $this, 'form_updated') );
		add_action( 'happyforms_form_duplicated', array( $this, 'form_duplicated') );

	}

	public function response_created( $submission, $form ) {
		$this->update_form_counters( $form['ID'] );
	}

	public function submission_status_changed( $post_id ) {
		$form_id = happyforms_get_meta( $post_id, 'form_id', true );

		$this->update_form_counters( $form_id );
	}

	public function update_form_counters( $id ) {
		global $wpdb;

		$query = $wpdb->prepare("
			SELECT CASE r.meta_value
			WHEN 1 THEN 'read'
			WHEN 2 THEN 'spam'
			ELSE 'unread'
			END AS status,
			COUNT(r.meta_id) AS amount
			FROM $wpdb->posts p, $wpdb->postmeta f, $wpdb->postmeta r
			WHERE f.meta_key = '_happyforms_form_id'
			AND f.meta_value = %d
			AND r.meta_key = '_happyforms_read'
			AND f.post_id = r.post_id
			AND p.ID = f.post_id
			AND p.post_status != 'pending'
			AND p.post_status != 'trash'
			GROUP BY status
		", $id );

		$result_counters = $wpdb->get_results( $query, ARRAY_A );

		$counters = $this->get_default_counters();
		$count_total = 0;

		foreach ( $result_counters as $count ) {
			$status = $count['status'];
			$amount = $count['amount'];

			$counters[ $status ] = $amount;

			if ( 'spam' !== $status ) {
				$counters['total'] += $amount;
			}
		}

		$trash_query = $wpdb->prepare( "
			SELECT COUNT(p.ID)
			FROM $wpdb->posts p
			JOIN $wpdb->postmeta m
			ON p.ID = m.post_id
			AND m.meta_key = '_happyforms_form_id'
			AND m.meta_value = %d
			WHERE p.post_status = 'trash'
		", $id );

		$count_trash = $wpdb->get_var( $trash_query );

		$counters['trash'] = $count_trash;

		foreach ( $counters as $status => $count ) {
			$meta_key = 'count_submissions_' . $status;

			happyforms_update_meta( $id, $meta_key, $count );
		}
	}

	public function update_counters() {
		global $wpdb;

		$status_results = $wpdb->get_results( "
			SELECT f.meta_value AS form_id,
			CASE r.meta_value
			WHEN 1 THEN 'read'
			WHEN 2 THEN 'spam'
			ELSE 'unread'
			END AS status,
			COUNT(r.meta_id) AS amount
			FROM $wpdb->posts p, $wpdb->postmeta f, $wpdb->postmeta r
			WHERE f.meta_key = '_happyforms_form_id'
			AND r.meta_key = '_happyforms_read'
			AND f.post_id = r.post_id
			AND p.ID = f.post_id
			AND p.post_status != 'pending'
			AND p.post_status != 'trash'
			GROUP BY form_id, status
		", ARRAY_A );

		$counter_total = [];
		$counters = [];

		foreach ( $status_results as $result ) {
			$form_id = $result['form_id'];
			$status = $result['status'];
			$amount = $result['amount'];

			if ( ! isset( $counters[ $form_id ] ) ) {
				$counters[ $form_id ] = $this->get_default_counters();
			}

			$counters[ $form_id ][ $status ] = $amount;

			if ( 'spam' !== $status ) {
				$counters[ $form_id ]['total'] += $amount;
			}
		}

		$form_ids = happyforms_get_form_controller()->get( array(), true );

		$trash_results = $wpdb->get_results( "
			SELECT m.meta_value AS form_id, COUNT(p.ID) AS amount
			FROM $wpdb->posts p JOIN $wpdb->postmeta m
			ON p.ID = m.post_id AND m.meta_key = '_happyforms_form_id'
			WHERE p.post_status = 'trash'
			GROUP BY form_id
		", ARRAY_A );

		foreach ( $form_ids as $form_id ) {
			if ( ! isset( $counters[ $form_id ] ) ) {
				$counters[ $form_id ] = $this->get_default_counters();
			}
		}

		foreach ( $trash_results as $result ) {
			$form_id = $result['form_id'];
			$amount = $result['amount'];
			$counters[ $form_id ]['trash'] = $amount;
		}

		foreach ( $counters as $form_id => $counts ) {
			foreach ( $counts as $status => $amount ) {
				$meta_key = 'count_submissions_' . $status;

				happyforms_update_meta( $form_id, $meta_key, $amount );
			}
		}
	}

	public function get_totals() {
		global $wpdb;
		$result = $wpdb->get_results( "
			SELECT CASE meta_key
			WHEN '_happyforms_{$this->key_count_submission_unread}' THEN 'unread'
			WHEN '_happyforms_{$this->key_count_submission_read}' THEN 'read'
			WHEN '_happyforms_{$this->key_count_submission_spam}' THEN 'spam'
			WHEN '_happyforms_{$this->key_count_submission_trash}' THEN 'trash'
			ELSE 'total'
			END AS status,
			SUM(meta_value) AS amount
			FROM $wpdb->postmeta
			WHERE meta_key IN (
				'_happyforms_{$this->key_count_submission_unread}',
				'_happyforms_{$this->key_count_submission_read}',
				'_happyforms_{$this->key_count_submission_spam}',
				'_happyforms_{$this->key_count_submission_trash}',
				'_happyforms_{$this->key_count_submission_total}'
			)
			group by meta_key;
		", ARRAY_A );

		$counters = [];

		foreach ( $result as $count ) {
			$counters[ $count['status'] ] = $count['amount'];
		}

		return $counters;
	}

	public function get_total_unread() {
		global $wpdb;

		$total = $wpdb->get_var("
			SELECT SUM(meta_value) AS amount
			FROM $wpdb->postmeta
			WHERE meta_key = '_happyforms_{$this->key_count_submission_unread}'
		");

		return $total;
	}

	public function get_total_submissions( $form_id ) {
		$total = happyforms_get_meta( $form_id, $this->key_count_submission_total, true );

		if ( empty( $total ) ) {
			$total = 0;
		}

		return $total;
	}

	public function get_default_counters() {
		$counters = array(
			'read' => 0,
			'unread' => 0,
			'spam' => 0,
			'trash' => 0,
			'total' => 0,
		);

		return $counters;
	}

	public function form_updated( $form ) {
		$this->update_form_counters( $form['ID'] );
	}

	public function form_duplicated( $form ) {
		$this->update_form_counters( $form['ID'] );
	}

	public function migrate_submission_count_transients() {
		$counters = get_transient( $this->counters_transient );

		if ( false !== $counters ) {
			foreach ( $counters as $form_id => $counts ) {
				if ( '' != $form_id ) {
					foreach ( $counts as $key => $count ) {
						$meta_key = 'count_submissions_' . $key;
						happyforms_update_meta( $form_id, $meta_key, $count );
					}
				}
			}

			delete_transient( $this->counters_transient );
		}
	}
}

if ( ! function_exists( 'happyforms_submission_counter' ) ):

function happyforms_submission_counter() {
	return Happyforms_Submission_Counter::instance();
}

endif;

happyforms_submission_counter();
