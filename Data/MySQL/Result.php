<?php
	/**
     *
     * Implementation of a result using mySQL resources.
     *
     * @see WebLab_Data_Result
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage WebLab_Data_MySQL
     *
     */
    class WebLab_Data_MySQL_Result extends WebLab_Data_Result
    {

        protected function _read( &$result )
        {
           if( mysql_num_rows( $result ) == 0 )
           {
               $this->_rows = array();
               return;
           }
           
           $this->_rows = array_map( create_function( '$result', 'return mysql_fetch_object( $result );' ), array_fill( 0, mysql_num_rows( $result ), $result ) );
        }

    }