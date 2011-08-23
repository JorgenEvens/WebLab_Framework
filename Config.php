<?php
	/**
	 * Hold configuration form a specified configuration file.
	 * 
	 * @author jorgen
	 * @package WebLab
	 *
	 */
	class WebLab_Config {
		
		/**
		 * Constant that indicates you want the get function to return RAW data.
		 * In the default implementation this will be an array.
		 * 
		 * @var string
		 */
		const RAW = 'raw';
		
		/**
		 * Constant that indicates you want the get function to return the data as an object.
		 * 
		 * @var string
		 */
		const OBJECT = 'object';
		
		/**
		 * Constant that indicates you want the get function to return the data wrapped in a new instance of WebLab_Config.
		 * 
		 * @var string
		 */
		const CONFIG_WRAP = 'weblab_config';
		
		/**
		 * Holds the configuration used by the application.
		 * This location of this configuration file should be set at startup of the application.
		 * 
		 * @see WebLab_Application
		 * @see setApplicationConfig()
		 * @var WebLab_Config
		 */
		protected static $_application_config;
		
		/**
		 * Return the configuration used by the application.
		 * This function lazy loads the configuration file.
		 * 
		 * @return WebLab_Config
		 */
		public static function getApplicationConfig() {
			if( !empty( self::$_application_config ) && !( self::$_application_config instanceof self ) ) {
				self::$_application_config = new self( self::$_application_config );
			}
			
			return self::$_application_config;
		}
		
		/**
		 * Set the path to the configuration file, or set the application config to an instance of WebLab_Config.
		 * 
		 * @param string|WebLab_Config $file
		 */
		public static function setApplicationConfig( $file ) {
			self::$_application_config = $file;
		}
		
		/**
		 * Holds the configuration data of the instance.
		 * 
		 * @var mixed[]
		 */
		protected $_config;
		
		/**
		 * Constructs a new instance of WebLab_Config based on a file or an array.
		 * 
		 * @param string|mixed[] $file
		 * @throws WebLab_Exception_Config If the file could not be found.
		 * @throws WebLab_Exception_Config If there is an error in the configuration file.
		 */
		public function __construct( $file ) {
			if( is_string( $file ) ) {
				if( !file_exists( $file ) )
	                throw new WebLab_Exception_Config( 'Could not locate config file. ( ' . $file . ' )' );
		            
			    $config = json_decode( file_get_contents( $file ), true );
			    if( !isset( $config ) )
					throw new WebLab_Exception_Config( 'There seems to be an error in your config file. ( ' . $file . ')' );
			} else {
				$config = $file;
			}
			
            $this->_config = $config;
		}
		
		/**
		 * Retrieve data from the configuration tree using a '.' delimited path.
		 * If the resulting value is another branch in the configuration tree, values will be returned according to the optional $return_type parameter.
		 * All other values will be returned as the types they were interpreted as.
		 * 
		 * @see RAW
		 * @see OBJECT
		 * @see CONFIG_WRAP
		 * @param string $path
		 * @param string $return_type
		 * @throws WebLab_Exception_Config If the requested node is not available.
		 * @return mixed
		 */
		public function get( $path, $return_type=self::RAW ) {
			$properties = explode( '.', $path );
			
			$value = $this->_config;
			while( count( $properties ) > 0 ) {
				if( !isset( $value[$properties[0]] ) )
					throw new WebLab_Exception_Config( 'The node specified is not available, current node is "' . $properties[0] . '". ( ' . $path . ' )' );
				
				$value = $value[$properties[0]];
				array_splice( $properties, 0, 1 );
			}
			
			if( is_array( $value ) && $return_type != self::RAW ) {
				switch( $return_type ) {
					case self::OBJECT:
						return (object)$value;

					case self::CONFIG_WRAP:
						return new WebLab_Config( null, $value );
				}
			}
			
			return $value;
		}
		
		/**
		 * Returns whether a node exists.
		 * 
		 * @param string $path
		 * @return boolean
		 */
		public function exists( $path ) {
			try {
				$this->get( $path );
			} catch( WebLab_Exception_Config $ex ) {
				return false;
			}
			
			return true;
		}
		
	}