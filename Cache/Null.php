<?php

	class WebLab_Cache_Null implements WebLab_Cache_Interface {

		protected static $_instance = null;

		public function setNamespace( $ns ) {}

		public static function isAvailable() {
			return true;
		}

		public static function open() {
			if( empty( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}

		public function __construct() {
			header( 'X-Warning: Using NullCache' );
		}

		public function exists( $key ) {
			return false;
		}

		public function get( $key ) {
			return null;
		}

		public function set( $key, $value=null, $ttl=0 ) { }

		public function delete( $key ) { }

	}