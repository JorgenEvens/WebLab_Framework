<?php

	class WebLab_Cache_Apc implements WebLab_Cache_Interface {

		protected static $_instance = null;

		protected $_namespace = '';

		public static function isAvailable() {
			return function_exists( 'apc_fetch' );
		}

		public function setNamespace( $ns ) {
			$this->_namespace = empty( $ns ) ? '' : $ns . '/';
		}

		public static function open() {
			if( empty( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}

		public function exists( $key ) {
			return apc_exists( $this->_namespace . $key );
		}

		public function get( $key ) {
			if( $this->exists( $key ) )
				return apc_fetch( $this->_namespace . $key );

			return null;
		}

		public function set( $key, $value=null, $ttl=0 ) {
			if( !is_array( $key ) )
				return apc_store( $this->_namespace . $key, $value, $ttl );

			foreach( $key as $k => $v )
				$this->set( $k, $v );
		}

		public function delete( $key ) {
			if( $this->exists( $key ) )
				apc_delete( $this->_namespace . $key );
		}

	}