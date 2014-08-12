<?php

	class WebLab_Cache_File {

		protected static $_instance = null;

		public function setNamespace( $ns ) {}

		public static function isAvailable() {
			$cache_dir = config( 'Application.Cache' );
			return is_dir( $cache_dir );
		}

		public static function open() {
			if( empty( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}

		protected $_cache_dir = null;

		public function __construct() {
			$this->_cache_dir = config( 'Application.Cache' );
		}

		public function getLocation( $key ) {
			return $this->_cache_dir . '/' . md5( $key );
		}

		public function exists( $key ) {
			return file_exists( $this->getLocation( $key ) );
		}

		public function get( $key ) {
			if( $this->exists( $key ) )
				return unserialize( file_get_contents( $this->getLocation( $key ) ) );

			return null;
		}

		public function set( $key, $value=null, $ttl=0 ) {
			if( !is_array( $key ) )
				return file_put_contents( $this->getLocation( $key ), serialize( $value ) );

			foreach( $key as $k => $v )
				$this->set( $k, $v );
		}

		public function delete( $key ) {
			if( $this->exists( $key ) )
				unlink( $this->getLocation( $key ) );
		}

	}