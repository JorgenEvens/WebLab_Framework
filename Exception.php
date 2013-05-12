<?php
    /**
     * A base implementation for exceptions thrown by the framework.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Exception
     *
     */
    abstract class WebLab_Exception extends Exception {
        
    	protected $_trace;
    	
        public static function reporting() {
            $error_reporting = WebLab_Config::getApplicationConfig()->get( 'Application.Error.reporting' );
            if( is_string( $error_reporting ) ) {
                $error_reporting = constant( $error_reporting );
            }
            error_reporting( $error_reporting );
        }

    	protected function _updateTrace() {
    		if( empty( $this->_trace ) )
    			$this->_trace = $this->getTrace();
    	}
    	
    	public function getClass() {
    		$this->_updateTrace();
    		return $this->_trace[0]['class'];
    	}
    	
    	public function getMethod() {
    		$this->_updateTrace();
    		return $this->_trace[0]['function'];
    	}
    	
    }