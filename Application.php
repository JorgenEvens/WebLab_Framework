<?php

    /**
     * The main class to start an application using WebLab Framework
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     */
    class WebLab_Application
    {
    	/**
    	 * Holds the main application currently running.
    	 * 
    	 * @var WebLab_Application
    	 */
    	protected static $_self;
    	
    	/**
    	 * Returns the application currently running.
    	 * 
    	 * @return WebLab_Application
    	 */
    	public static function getApplication() {
    		return self::$_self;
    	}
    	
        /**
         * Application constructor
         * 
         * @param string $config Path to configuration file.
         */
		public function __construct( $config )
		{
		    WebLab_Config::setApplicationConfig( $config );
		    if( empty( self::$_self ) ) {
		    	self::$_self = $this;
		    }
		}

        /**
         * Starts the application
         */
		public function run()
		{
		    $loader = config( 'Application.Loader' );
	            
		    if( isset( $loader ) )
		    {
				$this->acquire( $loader->location );
				$loader = $loader->name;
				$loader = new $loader();
		    }
		}

        /**
         * Require a php file.
         * 
         * @param string $location The file to require.
         */
		protected function acquire( $location )
		{
		    require_once( $location );
		}

    }