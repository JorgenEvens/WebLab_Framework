<?php
    class WebLab_Data_Result_MySQLi extends WebLab_Data_Result
    {

        public function __construct( $result )
        {
            $this->_resource = $result;
        }

        protected function _read()
        {
            while( $row = $this->_resource->fetch_assoc() )
            {
                $this->_add( $row );
            }

            return $this;
        }

        protected function _wrap( $data, $id=null )
        {
            return new WebLab_Data_Row_MySQLi( $data, $id );
        }

    }