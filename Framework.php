<?php
    /**
     *
     * Framework Initialisation
     *
     * Initialisation of the WebLab Framework.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     * @package WebLab_Framework
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
        require_once( strtr( $className, '_', '/' ) . '.php' );
    }