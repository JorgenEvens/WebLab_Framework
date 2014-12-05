<?php

	class WebLab_Cache_CacheFactory {

		protected static $_cache = null;

		public static function getCache() {
			if( empty( self::$_cache ) )
				self::$_cache = self::initCache();

			return self::$_cache;
		}

		public static function initCache() {
			if( WebLab_Config::isLoaded() )
				$preference = WebLab_Config::getApplicationConfig()->get( 'Application.Cache.Type', WebLab_Config::OBJECT, false );
			
			if( !empty( $preference ) ) {
				$preference = 'WebLab_Cache_' . ucfirst( $preference );
			
				if( class_exists( $preference ) )
					return $preference::open();
			}

			if( extension_loaded('apc') || extension_loaded('acpu') )
				return WebLab_Cache_Apc::open();

			if( WebLab_Cache_File::isAvailable() )
				return WebLab_Cache_File::open();

			return WebLab_Cache_Null::open();
		}

	}