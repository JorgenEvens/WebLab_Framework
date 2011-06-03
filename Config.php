<?php
    /**
     *
     * Configuration
     *
     * This class manages all configuration settings.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     * @package WebLab
     *
     */

     /**
      * WebLab_Config manages all configuration data.
      *
      * @package    WebLab_Framework
      */
    class WebLab_Config
    {
        /**
         * Holds configuration in an array.
         * @var array Holds the configuration in an array.
         */
		protected $_config;
		
        /**
         * Holds the root configuration instance
         * @var WebLab_Config Default configuration instance
         */
		protected static $_instance = null;
		
        /**
         * Holds the path from which configuration tree current @link $_config
         * is derived.
         * @var string The path of $_config within original configuration.
         */
		protected $_path;

        /**
         * Constructor for a new instance.
         * Only callable from within another instance.
         * @param array &$config The configuration this instance will be holding.
         * @param string $path The path where this configuration lives.
         */
		protected function __construct( &$config=array(), $path="" )
		{
		    if( empty( $path ) )
				$this->_config['Application']['Runtime'] = array();
				
		    $this->_config = &$config;
		    $this->_path = $path;
		}

        /**
         * Keeps the object from being cloned.
         */
		protected function __clone(){}

        /**
         * Returns the path from which this configuration is derived.
         * @return string
         */
		public function getPath()
		{
		    return $this->_path;
		}

        /**
         * Get a value from the configuration tree.
         * @param string $path The path to fetch from configuration.
         * @return self|WebLab_Config
         */
		public function &get( $path )
		{
		    $path = explode( '.', $path );
	
	        if( !isset( $path ) )
				return $this->_instance;
	
		    $config = &$this->_config;
	
		    foreach( $path as $value )
		    {
				$config = &$config[ $value ];
				$currentPath[] = $value;
				if( $config === null )
				    break;
		    }
	
		    if( empty( $this->_path ) )
				$path = implode( $currentPath, '.' );
			else
				$path = $this->getPath() . '.' . implode( $currentPath, '.' );
	
		    if( is_array( $config ) ) {
		    	$newInstance = new self( $config, $path );
				return $newInstance;
		    } else
				return $config;
		}

	    /**
	    * Set a property to a specific value in the configuration tree.
	    * @param string $path The path of the property to set.
	    * @param * $value The value to be set.
	    * @return WebLab_Config
	    */
		public function set( $path, $value )
		{
		    if( !( strpos( $this->getPath(), 'Application.Runtime' ) > -1 || strpos( $path, 'Application.Runtime.' ) > -1 ) )
		    {
			$this->get( 'Application.Runtime' )->set( $path, $value );
	                return $this;
		    }
		    
		    $path = explode( '.', $path );
		    $config = &$this->_config;
	
		    foreach( $path as $directory )
		    {
				$config = &$config[ $directory ];
				
				if( !isset( $config ) )
				    $config = array();
		    }
	
		    $config = $value;
	
		    return $this;
		}

		/**
		*
		* @return array The configuration as an array.
		*/
		public function toArray()
		{
		    return $this->_config;
		}
	
		/**
		*
		* @return array The configuration as an object.
		*/
		public function toObject()
		{
			return (object) $this->_config;
		}
	
		/**
		* Gets the root configuration.
		* @return WebLab_Config The root configuration.
		*/
		public static function getInstance()
		{
		    if( self::$_instance === NULL )
				self::$_instance = new self();
		    
		    return self::$_instance;
		}
	
		/**
		* Import a configuration file.
		* @param string $file The path to the configuration file.
		* @return WebLab_Config
		*/
		public function import( $file )
		{
            if( !file_exists( $file ) )
                throw new Exception( 'Could not locate config file. ( ' . $file . ' )' );
	            
		    $config = json_decode( file_get_contents( $file ), true );
		    if( !isset( $config ) )
				throw new WebLab_Exception_Config( 'There seems to be an error in your config file. ( ' . $file . ')' );
	
            $this->_config = array_merge( $this->_config, $config );
	
		    return $this;
		}
	
		/**
		* Return the string value.
		* @return string Configuration as string.
		*/
		public function __toString()
		{
			return var_export( $this->_config, true );
		}

    }