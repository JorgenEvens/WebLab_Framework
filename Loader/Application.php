<?php
	/**
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Loader
	 *
	 */
    abstract class WebLab_Loader_Application
    {

    	/**
    	 * Contains Application Loader configuration
    	 * @var object
    	 */
		protected $_config;
	
		/**
		 * Initialize the application
		 */
		public function __construct() {
		    $this->_config = WebLab_Config::getApplicationConfig()->get( 'Application.Loader', WebLab_Config::OBJECT, false );
			$this->_include();
			$this->start();
		}
	
		/**
		 * Call method in the Loader configuration
		 * @param  string $method Methods to invoke.
		 * @return boolean	If loading succeeded.
		 */
		protected function _call( $method ) {
			if( strpos( $method, '::' ) === false ) {
				if( method_exists( $this, $method ) ) {
					$this->$method();
					return true;
			    }
			} else {
		    	$parts = explode( '::', $method );
		    	if( method_exists( $parts[0], $parts[1] ) ) {
		    		call_user_func($method);
		    		return true;
		    	}
		    }
		    
		    return false;
		}

		/**
		 * Include other application folders.
		 * 
		 */
		protected function _include() {
            $includes = array_map( array( $this, '_parse_path' ), $this->_config->includePaths );
            set_include_path( get_include_path() . PATH_SEPARATOR . implode( PATH_SEPARATOR, $includes ) );
		}

		/**
		 * Parse path, create an absolute path.
		 * @param  string $path Relative path as supplied in configuration
		 * @return string       The absolute path generated.
		 */
		public function _parse_path( $path ) {
        	if( $path[0] != '/' ) $path = getcwd() . '/' . $path;
            return realpath( $path );
        }
		
		/**
		 * Start application by running every method in the Loader configuration.
		 * 
		 */
		public function start() {
			if( empty( $this->_config ) || empty( $this->_config->load ) ) return;

		    foreach( $this->_config->load as $method => $start ) {
				if( $start && !$this->_call( $method ) ) {
			    	throw new WebLab_Exception_Loader( 'Unable to load a method. ( ' . $method . ')' );
				}
		    }
		}

    }