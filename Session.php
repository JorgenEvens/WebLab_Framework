<?php
	
	/**
	 * Manages the initialization of PHP session.
	 */
	class WebLab_Session {

		/**
		 * Flag that remembers if a session has been started.
		 * 
		 * @var boolean
		 */
		protected static $_initialized = false;

		/**
		 * Set key to a specified value, and initialize the session when
		 * it has not been started yet.
		 * 
		 * @param string $key   The key to store the value under.
		 * @param mixed  $value The value to store under the key.
		 */
		public static function set( $key, $value ) {
			self::init();
			$_SESSION[$key] = $value;
		}

		/**
		 * Get the value stored under this key, initialize the session when
		 * it has not been started yet.
		 * 
		 * @param  string $key The key to retrieve the value for.
		 * @return mixed       The value stored under this key.
		 */
		public static function get( $key ) {
			self::init();
			return isset( $_SESSION[$key] ) : $_SESSION[$key] : null;
		}

		/**
		 * Initialize the session if it has not been started yet.
		 * 
		 */
		public static function init() {
			if( !self::$_initialized ) {
				session_start();
				self::$_initialized = true;
			}
		}

		/**
		 * Destroy the session and reset this class.
		 * 
		 */
		public static function reset() {
			self::$_initialized = false;
			session_destroy();
		}

		/**
		 * Retrieves current status, true if the session has been started.
		 * 
		 * @return boolean Has session been started
		 */
		public static function isReady() {
			return self::$_initialized;
		}

	}