<?php
    /**
     * WebService.php
     *
     * This file contains the implementation of the WebLab_Dispatcher_WebService class.
     * @see WebLab_Dispatcher_WebService
     */
	/**
	 * A base implementation for a REST WebService.
	 *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Dispatcher
	 *
	 */
	abstract class WebLab_Dispatcher_WebService extends WebLab_Dispatcher_Module {
		
		/**
		 * Holds a reference to the template variables.
		 *
		 * @var array
		 */
		protected $data;
		
		/**
		 * Check for the correct request method
		 *
		 * @param string $method
		 * @return boolean
		 */
		protected function is( $method ) {
			return strtoupper( $_SERVER['REQUEST_METHOD'] ) == strtoupper( $method );
		}
		
		/**
		 * Answer request with an error
		 *
		 * @param string $message
		 * @param int $http_status_code
		 */
		protected function error( $message, $http_status_code=500 ) {
			WebLab_Http_Headers::getForRequest()->setResponseCode( $http_status_code );
				
			$this->data->error = true;
			$this->data->message = $message;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Dispatcher_Module::__init()
		 */
		public final function __init() {
			ini_set( 'html_errors', false );
			
			// TODO: XML template
			$t = new WebLab_Template( 'data/json' );
				
			WebLab_Template::setRootTemplate( $t );
			$this->layout = $t;
			$this->data = &$t;
				
			return true;
		}
		
		/**
		 * Write data to the template
		 *
		 * @param array $new_data
		 */
		public function put( $new_data ) {
			$data = $this->data;
			if( !is_a( $new_data, 'stdClass' ) && !is_array( $new_data ) ) {
				return;
			}
				
			foreach( $new_data as $key => $value ) {
				$data->$key = $value;
			}
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Dispatcher_Module::_default()
		 */
		public function _default(){
			return $this->error( 'Method does not exist', 501 );
		}
		
	}