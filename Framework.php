<?php
    /**
     *
     * Framework Initialisation
     *
     * Initialisation of the WebLab Framework.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     * @package WebLab
     *
     */


     /**
      * Autoloader, loads the classes for u.
      * This is the default, if you wish to override this you should register a AddIn.
      * 
      * @param  string  $className  The class to load
      */
    function __autoload( $className )
    {
        $c = strtr( $className, '_', '/' ) . '.php';
        $cHandle = @fopen( $c, 'r', true );
        if( !empty( $cHandle ) ){
        	fclose( $cHandle );
        	return include_once( $c );
        }
        return false;
    }