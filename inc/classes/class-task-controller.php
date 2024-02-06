<?php

class HappyForms_Task_Controller {

	private static $instance;

	private $tasks = array();

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_action( 'shutdown', array( $this, 'ping' ) );

		$this->register( 'HappyForms_Task_Email_Owner' );
		$this->register( 'HappyForms_Task_Email_User' );
		$this->register( 'HappyForms_Task_Email_Abandonment' );
	}

	public function register( $task_class ) {
		add_action( $task_class::get_event(), array( $this, 'run' ), 10, 2 );
	}

	public function add( $task_class, $response_id ) {
		$async = apply_filters( 'happyforms_use_async_tasks', false );
		
		if ( ! $async ) {
			$this->run( $task_class, $response_id );
			return;
		}

		$task = array( $task_class, $response_id );
		$this->tasks[] = $task;
		wp_schedule_single_event( time(), $task_class::get_event(), $task );
	}

	public function run( $task_class, $response_id ) {
		$task = new $task_class( $response_id );
		$task->run();
	}

	public function ping() {
		if ( 0 === count( $this->tasks ) ) {
			return;
		}

		wp_remote_get( site_url(), array(
			'sslverify' => false,
			'blocking' => false,
		) );
	}

}

if ( ! function_exists( 'happyforms_get_task_controller' ) ):

function happyforms_get_task_controller() {
	return HappyForms_Task_Controller::instance();
}

endif;

happyforms_get_task_controller();