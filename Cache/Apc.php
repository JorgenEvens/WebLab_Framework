<?php

	class WebLab_Cache_Apc implements WebLab_Cache_Interface {

		protected static $_instance = null;

		public static function isAvailable() {
			return function_exists( 'apc_fetch' );
		}

		public static function open() {
			if( empty( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}

		public function exists( $key ) {
			return apc_exists( $key );
		}

		public function get( $key ) {
			if( $this->exists( $key ) )
				return apc_fetch( $key );

			return null;
		}

		public function set( $key, $value=null, $ttl=0 ) {
			if( !is_array( $key ) )
				return apc_store( $key, $value, $ttl );

			foreach( $key as $k => $v )
				$this->set( $k, $v );
		}

		public function delete( $key ) {
			if( $this->exists( $key ) )
				apc_delete( $key );
		}

	}