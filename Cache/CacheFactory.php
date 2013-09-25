<?php

	class WebLab_Cache_CacheFactory {

		protected static $_cache = null;

		public static function getCache() {
			if( empty( self::$_cache ) )
				self::$_cache = self::initCache();

			return self::$_cache;
		}

		public static function initCache() {
			if( function_exists( 'apc_fetch' ) )
				return WebLab_Cache_Apc::open();

			if( WebLab_Cache_File::isAvailable() )
				return WebLab_Cache_File::open();

			return WebLab_Cache_Null::open();
		}

	}