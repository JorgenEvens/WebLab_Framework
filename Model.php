<?php
    abstract class WebLab_Model
    {
        protected $_db = array();
        
        public function __construct()
        {
            $this->_loadDatabases();
        }

        protected static function _loadDatabases()
        {
            $databases = WebLab_Config::getInstance()->get( 'Application.Data' )->toArray();

            foreach( $config as $name => $configuration )
            {
                $configuration = (object)$configuration;
                if( !$configuration->auto )
                {
                    continue;
                }

                $adapterClass = 'WebLab_Data_' . $configuration->type . '_Adapter';
                if( class_exists( $adapterClass ) )
                {
                    $this->_db[ $name ] = new $adapterClass( $configuration );
                }
            }

            return true;
        }
    }