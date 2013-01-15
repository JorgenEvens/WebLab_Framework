<?php
	class WebLab_Util_ShortHands {
		
		public static function init( $activate=null ) {
			if( $activate === null ) {
				try {
					$activate = WebLab_Config::getApplicationConfig()->get( 'Util.ShortHands', WebLab_Config::RAW );
				} catch( WebLab_Exception_Config $ex ) {
					$activate = array( 'db', 'table', 'config' );
				}
			}
			
			if( !is_array( $activate ) ) {
				return;
			}
			
			foreach( $activate as $shorthand ) {
				self::$shorthand();
			}
		}
		
		public static function db() {
			function db( $name ) {
				return WebLab_Database::getDb( $name );
			}
		}
		
		public static function config() {
			function config( $path, $return_type=WebLab_Config::OBJECT ) {
				return WebLab_Config::getApplicationConfig()->get( $path, $return_type, false );
			}
		}
		
		public static function table() {
			function table( $name ) {
				$name = 'Table_' . ucfirst( $name );
				return $name::getInstance();
			}
		}
		
	}
	
	
	
	