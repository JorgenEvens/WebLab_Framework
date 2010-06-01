<?php
    class WebLab_Data_mySQLi_Result extends WebLab_Data_Result
    {

        protected function _read( $result )
        {
           if( $result->num_rows == 0 )
           {
               $this->_rows = array();
               return;
           }
           
           $this->_rows = array_map( create_function( '$result', 'return $result->fetch_object();' ), array_fill( 0, $result->num_rows, $result ) );
        }

    }