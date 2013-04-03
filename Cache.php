<?php
	/**
	 * Allows for full page caching, straight form the configurationfile.
	 * 
	 * @author jorgen
	 * @package WebLab
	 *
	 */
	class WebLab_Cache {
	
		protected static $_cached;
		
		protected static $_url;
	
		/**
		 * Initialize cache and check if the current page has been cached.
		 */
		public static function init() {
			if( !empty( $_POST ) ) return; // Cannot cache when data is being delivered
			
			$cache_dir = WebLab_Config::getApplicationConfig()->get( 'Application.Cache.Location', WebLab_Config::OBJECT, false );
			if( $cache_dir == null ) return;
			
			self::$_url = WebLab_Parser_URL::getForRequest()->getPath();

			$hash = md5( self::$_url );
			$path = $cache_dir . DIRECTORY_SEPARATOR . $hash . '.cache';
			
			if( file_exists( $path ) ) {
				if( self::_acceptsGzip() ) {
					header( 'Content-Encoding:gzip' );
					readfile( $path );
				} else {
					readgzfile( $path );
				}
				exit();
			}
			
			self::$_cached = $path;
		}
		
		protected static function _acceptsGzip() {
			$s_key = '_gzip_support';
			
			if( !isset( $_SESSION[$s_key] ) ) { 
			
				// IIS
				$accepted = '';
				if( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
					$accepted = $_SERVER['HTTP_ACCEPT_ENCODING'];
				} // Apache
				else if( function_exists( 'getallheaders' ) ){
					$headers = getallheaders();
					$search = 'accept-encoding:';
					
					foreach( $headers as $header ) {
						if( strpos( strtolower( $header ), $search ) !== false ) {
							$accepted .= ',' . trim( substr( $header, strpos( $header, ':' )+1 ) );
						}
					}
				}
				
				$_SESSION[$s_key] = ( strpos( ',' . trim( $accepted ) . ',', ',gzip,' ) !== false );
			}
			
			return $_SESSION[$s_key];
		}
		
		public static function out() {
			$cache = WebLab_Config::getApplicationConfig()->get( 'Application.Cache.URLs', WebLab_Config::RAW, false );
			if( $cache == null ) {
				return;
			}

			$regex = '#^(' . implode( '|', array_filter( $cache, 'preg_quote' ) ) . ')#i';
			
			if( preg_match( $regex, self::$_url ) && self::$_cached != null ) {
				$file = gzopen( self::$_cached, 'w' );
				gzwrite( $file, (string)WebLab_Template::getRootTemplate() );
				gzclose( $file );
			}
		}
	
	}