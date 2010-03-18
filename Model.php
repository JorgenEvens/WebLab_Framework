<?php
    abstract class WebLab_Model
    {
        protected $_db;
        
        public final function __construct()
        {
            $this->_loadDatabases();

            $args = func_get_args();
            call_user_func_array( array( $this, '__init' ), $args );
        }

        protected function _loadDatabases()
        {
            $databases = WebLab_Config::getInstance()->get( 'Application.Data' )->toArray();

            if( !isset( $this->_db ) )
            {
                $this->_db = new stdClass();
            }

            foreach( $databases as $name => $configuration )
            {
                $configuration = (object)$configuration;
                if( !$configuration->auto )
                {
                    continue;
                }

                $adapterClass = 'WebLab_Data_' . $configuration->type . '_Adapter';
                if( class_exists( $adapterClass ) )
                {
                    $this->_db->$name = new $adapterClass( $configuration );
                }
            }

            return true;
        }
    }