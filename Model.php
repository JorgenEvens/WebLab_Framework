<?php
    abstract class WebLab_Model
    {
        private static $_db;
        
        public final function __construct()
        {
            $this->_loadDatabases();

            $args = func_get_args();
            call_user_func_array( array( $this, '__init' ), $args );
        }

        protected static function getDb()
        {
            if( !isset( self::$_db ) )
            {
                self::_loadDatabases();
            }
            return self::$_db;
        }

        private static function _loadDatabases()
        {
            $databases = WebLab_Config::getInstance()->get( 'Application.Data' )->toArray();

            if( !isset( self::$_db ) )
            {
                self::$_db = new stdClass();
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
                    self::$_db->$name = new $adapterClass( $configuration );
                }
            }

            return true;
        }
    }