<?php
    class WebLab_Data_mySQLi_Result extends WebLab_Data_Result
    {

        protected function _read( $result )
        {
            while( $row = $result->fetch_object() )
            {
                $this->_rows[] = $row;
            }
        }

    }