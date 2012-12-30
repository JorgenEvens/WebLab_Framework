<?php
    /*
     * Disable error reporting as soon as the framework is loaded.
     * Can be overwritten by loading WebLab_Exception::reporting in the configuration file
     * and defining the key Error.reporting in the configuration file.
     */
    error_reporting( 0 );
    
    /**
     * Initialisation of the WebLab Framework.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
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