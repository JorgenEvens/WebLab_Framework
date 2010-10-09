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
     * @package WebLab_Framework
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
         * Constructor of each model.
         */
        public final function __construct()
        {
            $this->_loadDatabases();

            $args = func_get_args();
            call_user_func_array( array( $this, '__init' ), $args );
        }

        /**
         * Initialises database connection if not yet created.
         * @return Array    Contains all configured instances of the databaseconnection.
         */
        protected static function getDb()
        {
            if( !isset( self::$_db ) )
            {
                self::_loadDatabases();
            }
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