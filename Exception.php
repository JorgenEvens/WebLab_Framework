<?php
    /**
     *
     * Configuration Exception
     *
     * This class gets called whenever an exception occures within the
     * WebLab_Config class.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     *
     */

     /**
      * WebLab_Exception_Config gets called whenever an exception occures within
      * WebLab_Config class.
      *
      */
    abstract class WebLab_Exception extends Exception {
        
    	protected $_trace;
    	
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