<?php
    /**
     * Create a constant that represents the namespace separator.
     * This is used throughout the application, like the dispatcer.
     */
    if( !defined( 'NAMESPACE_SEPARATOR' ) )
        define( 'NAMESPACE_SEPARATOR', '\\' );
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
        // Handle namespaces as directories.
        $className = strtr( $className, NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR );
        $error_report = error_reporting();
        $c = preg_replace( '#_(_*)#', '/$1', $className ) . '.php';
        $c = strtr( $c, '/', DIRECTORY_SEPARATOR );
        error_reporting( $error_report & ( E_ALL ^ E_WARNING ) );
        $result = !!include_once( $c );
        error_reporting( $error_report );
	   return $result;
    }
