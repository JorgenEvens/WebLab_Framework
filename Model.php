<?php
    /**
     *
     * Abstract Model
     *
     * Implements basic model functions
     * Relieves coder from having to connect to his database.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     * @package WebLab
     *
     */
    abstract class WebLab_Model
    {
        /**
         * Static instance of all databases configured
         * @var Array   Contains all configured instances of the databaseconnection.
         */
        private static $_db;

        /**
         * Only here for backward compatibility
         */
        public function __construct()
        {
            $args = func_get_args();
            call_user_func_array( array( $this, '__init' ), $args );
        }

        /**
         * __init() used instead of __construct because database loading happens in __construct.
         * @deprecated __construct is to be used freely again.
         */
        public function __init()
        {}
        
        /**
         * Initialises database connection if not yet created.
         * @return Array Contains all configured instances of the databaseconnection.
         */
        protected static function getDb()
        {
            if( !isset( self::$_db ) )
                self::_loadDatabases();

            return self::$_db;
        }

        /**
         * Load the databases from the configuration.
         * @return bool Indicating whether loading of database was successful.
         */
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
                    continue;

                $adapterClass = 'WebLab_Data_' . $configuration->type . '_Adapter';
                if( class_exists( $adapterClass ) )
                {
                    self::$_db->$name = new $adapterClass( $configuration );
                }
            }

            return true;
        }
    }