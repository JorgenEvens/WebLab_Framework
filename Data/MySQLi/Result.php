<?php
    class WebLab_Data_mySQLi_Result extends WebLab_Data_Result
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