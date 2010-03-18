<?php
    class WebLab_Data_MySQLi_Adapter extends WebLab_Data_Adapter
    {
        
        protected $_connected = false;
        protected $_wildcard = '%';

        public function __construct( $login )
        {
            $this->_resource = new mysqli( $login->host, $login->username, $login->password, $login->database, $login->port );

            if( $this->_resource->connect_err )
            {
                $this->error = $this->_resource->connect_err;
                return;
            }

            $this->setPrefix( $login->prefix );
            $this->_connected = true;
        }

        public function isConnected()
        {
            return $this->_connected;
        }

        protected function _query( $query )
        {
            $result = $this->_resource->query( $query ) or die( $this->_resource->error . '<br/>' . $query );
            
            if( $result !== true )
            {
                return new WebLab_Data_mySQLi_Result( $result );
            }
            return $this;
        }

        public function insert_id()
        {
            return $this->_resource->insert_id;
        }

        public function escape_string( $str )
        {
            return $this->_resource->real_escape_string( $str );
        }

        public function getAdapterSpecs()
        {
            return (object) array(
                escape_string   => array( $this, 'escape_string' ),
                wildcard        => $this->_wildcard
            );
        }

    }