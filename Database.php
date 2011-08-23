<?php
	/**
	 * This class manages database connections and generation of their adapters.
	 * Lazy loading of the adapters is used.
	 * 
	 * @author jorgen
	 * @package WebLab
	 * 
	 */
	class WebLab_Database {
		
		/**
		 * Stores the already loaded database adapters.
		 * 
		 * @var WebLab_Data_Adapter[]
		 */
		protected static $_databases = array();
		
		/**
		 * Stores the database configuration read from WebLab_Config
		 * 
		 * @see WebLab_Config
		 * @var mixed[]
		 */
		protected static $_config;
		
		/**
		 * Lazy loads the configuration.
		 * 
		 * @return mixed[]
		 */
		protected static function _getConfig() {
			if( empty( self::$_config ) )
				self::$_config = config( 'Application.Data.DB', WebLab_Config::RAW );
			
			return self::$_config;
		}
		
		/**
		 * Retrieves the database with $name from the database list, or generates it's adapter and adds it to the list.
		 * 
		 * @param string $name
		 * @throws WebLab_Exception_Data If database with that name was defined.
		 * @return WebLab_Data_Adapter
		 */
		public static function getDb( $name ) {
			if( !isset( self::$_databases[$name] ) ) {
				$config = self::_getConfig();
				
				if( empty( $config ) || !isset( $config[$name] ) ) {
					throw new WebLab_Exception_Data( 'No such database found.' );
				}
				
				$db_config = (object)$config[$name];
				$adapterClass = 'WebLab_Data_' . $db_config->type . '_Adapter';
				self::$_databases[$name] = new $adapterClass( $db_config );
			}
			
			return self::$_databases[$name];
		}
		
		/**
		 * Returns all of the database adapters defined in the configuration file.
		 * All adapters that had not been loaded yet will now be loaded before returning the list.
		 * 
		 * @return WebLab_Data_Adapter[]
		 */
		public static function getAll() {
			$config = self::$_getConfig();
			
			if( empty( $config ) ) {
				return array();
			}
			
			if( count( $config ) != count( self::$_databases ) ) {
				$databases = array_keys( $config );
				foreach( $databases as $name ) {
					if( !isset( self::$_databases[$name] ) ) {
						self::getDb( $name );
					}
				}
			}
			
			return self::$_databases;
		}
	}