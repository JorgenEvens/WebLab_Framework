<?php
    /**
     *
     * Framework Initialization
     *
     * Initialisation of the WebLab Framework.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     *
     */


     /**
      * Autoloader, loads the classes for u.
      * This is the default, if you wish to override this behaviour you should register an AddIn.
      * 
      * @param  string  $className  The class to load
      */
    function __autoload( $className )
    {
    	// TODO: place this code in a loader class.
    	// TODO: Create a loader manager.
    	
        $c = strtr( $className, '_', '/' ) . '.php';
        $cHandle = @fopen( $c, 'r', true );
        if( !empty( $cHandle ) ){
        	fclose( $cHandle );
        	return include_once( $c );
        }
        return false;
    }
    
    function db( $name ) {
		return WebLab_Database::getDb( $name );
	}
	
	function config( $path, $return_type=WebLab_Config::OBJECT ) {
		try {
			return WebLab_Config::getApplicationConfig()->get( $path, $return_type );
		} catch( WebLab_Exception_Config $ex ) {
			return null;
		}
	}