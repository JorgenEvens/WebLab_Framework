<?php
	/**
     *
     * Implementation of a result using mySQLi resources.
     *
     * @see WebLab_Data_Result
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage WebLab_Data_MySQLi
     *
     */
    class WebLab_Data_MySQLi_Result extends WebLab_Data_Result
    {

        protected function _read( &$result )
        {
           	if( $result->num_rows == 0 )
               	$this->_rows = array();
			else
           		$this->_rows = array_map( array( $this, '_parse_result' ), array_fill( 0, $result->num_rows, $result ) );
           		
           	return $this->_rows;
        }
        
        private function _parse_result( &$result ){
        	return $result->fetch_object();
        }

    }