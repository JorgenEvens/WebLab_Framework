<?php
    abstract class WebLab_Data_DataSource
    {

        private $_datasource;

        abstract public function __construct();

        public function isConnected()
        {
            return isset( $this->_datasource );
        }

        public function query( $query )
        {
            return $this->_query( $query );
        }

        abstract protected function _query( $query );
    }