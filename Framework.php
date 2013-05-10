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
        // Handle namespaces as directories.
        $className = strtr( $className, '\\', DIRECTORY_SEPARATOR );
        $error_report = error_reporting();
        $c = preg_replace( '#_(_*)#', DIRECTORY_SEPARATOR . '$1', $className ) . '.php';
        error_reporting( $error_report & ( E_ALL ^ E_WARNING ) );
        return !!include_once( $c );
        error_reporting( $error_report );
    }