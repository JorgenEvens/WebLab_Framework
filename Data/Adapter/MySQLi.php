<?php
    class WebLab_Data_Adapter_MySQLi extends WebLab_Data_DataSource
    {

        public function __construct( $host='localhost', $username='root', $password='', $database='', $port=3306 )
        {
            $this->_datasource = new mysqli( $host, $username, $password, $database, $port );
        }

        protected function _query( WebLab_Data_Query $query )
        {
            if( !$this->isConnected() )
            {
                throw new WebLab_Exception_Data( 'Datasource is not connected.' );
            }

            $result = $this->_datasource->query( (string) $query );
            return new WebLab_Data_Result_MySQLi( $result );
        }

        public function register( $name )
        {
            WebLab_Config::getInstance()->set( 'Application.Runtime.Data.Sources.MySQLi.' . $name, $this );
        }

    }