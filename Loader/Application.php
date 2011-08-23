<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Loader
	 *
	 */
    abstract class WebLab_Loader_Application
    {

		protected $_config;
	
		public function __construct() {
		    $this->_config = config( 'Application.Loader' );
		    $this->start();
		}
	
		protected function _call( $method ) {
			if( strpos( $method, '::' ) === false ) {
				if( method_exists( $this, $method ) ) {
					$this->$method();
					return true;
			    }
			} else {
		    	$parts = explode( '::', $method );
		    	if( method_exists( $parts[0], $parts[1] ) ) {
		    		call_user_func($method);
		    		return true;
		    	}
		    }
		    
		    return false;
		}
	
		public function start() {
			if( !empty( $this->_config ) && !empty( $this->_config->load ) ) {
			    foreach( $this->_config->load as $method => $start ) {
					if( $start ) {
					    if( !$this->_call( $method ) ) {
					    	throw new WebLab_Exception_Loader( 'Unable to load a method. ( ' . $method . ')' );
					    }
					}
			    }
			}
		}

    }