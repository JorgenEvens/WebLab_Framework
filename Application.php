<?php

    /**
     * The main class to start an application using WebLab Framework
     *
     * @package WebLab_Framework
     */
    class WebLab_Application
    {
        /**
         * Application Configuration.
         * @var WebLab_Config   Holds an instance of WebLab_Config
         */
	protected $_config;

        /**
         * Application constructor
         * @param string $config Path to configuration file.
         */
	public function __construct( $config )
	{
	    $this->_config = WebLab_Config::getInstance()->import( $config );
	}

        /**
         * Starts the application
         */
	public function run()
	{
	    $loader = $this->_config->get( 'Application.Loader' );
            
	    if( isset( $loader ) )
	    {
                $this->acquire( $loader->get( 'location' ) );
		$loader = $loader->get( 'name' );
		$loader = new $loader();
	    }


	}

        /**
         * Require a php file.
         * @param string $location The file to require.
         */
	protected function acquire( $location )
	{
	    require_once( $location );
	}

    }