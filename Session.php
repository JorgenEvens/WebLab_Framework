<?php

	/**
	 * Provides more efficient session management.
	 * 
	 */
	class WebLab_Session {

		/**
         * Flag that remembers if a session has been started.
         * 
         * @var boolean
         */
		protected static $_started = false;

		/**
		 * Check whether a key is contained within the current session.
		 * This will start the session if it was not started before.
		 * 
		 * @param  string $key
		 * @return boolean
		 */
		public static function contains( $key ) {
			self::start();

			return isset( $_SESSION[$key] );
		}

		/**
		 * Set the value of a key in the session.
		 * This will start the session if it was not started before.
		 * 
		 * @param array|string $key
		 * @param null|mixed $value
		 */
		public static function set( $key, $value=null ) {
			self::start();

			if( !is_array( $key ) ) {
				$_SESSION[$key] = $value;
				return;
			}

			foreach( $key as $name => $value )
				self::set( $name, $value );
		}

		/**
		 * Retrieve the value of a key in the current session.
		 * This will start the session if it was not started before.
		 * 
		 * @param  array|string $key
		 * @return array|mixed
		 */
		public static function get( $key ) {
			self::start();

			if( !is_array( $key ) )
				return self::contains($key) ? $_SESSION[$key] : null;

			$result = array();
			foreach( $key as $name )
				$result[$name] = $_SESSION[$name];

			return $result;
		}

		/**
		 * Remove a key from the current session.
		 * This will start the session if it was not started before.
		 * 
		 * @param  string $key
		 */
		public static function remove( $key ) {
			self::start();

			if( !is_array( $key ) ) {
				unset( $_SESSION[$key] );
				return;
			}

			foreach( $key as $name )
				unset( $_SESSION[$name] );
		}

		/**
		 * This will start the session if it was not started before.
		 * 
		 */
		public static function start() {
			if( !self::$_started )
				session_start();

			self::$_started = true;
		}

		/**
		 * This will destroy the current session.
		 * This will start a session before destroying it.
		 * 
		 */
		public static function destroy() {
			self::start();

			session_destroy();
		}

	}