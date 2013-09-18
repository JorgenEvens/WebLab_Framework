<?php
	/**
	 * Parses the url and makes it accessible through an instance of this object.
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Parser
	 *
	 */
    class WebLab_Parser_URL
    {
    	/**
    	 * Contains the URL Parser for current request.
    	 * @var WebLab_Parser_URL
    	 */
    	protected static $_request_url;
    	
    	/**
    	 * Returns the URL Parser for the current request.
    	 * 
    	 * @return WebLab_Parser_URL
    	 */
    	public static function getForRequest() {
    		if( empty( self::$_request_url ) ) {
    			$url = $_SERVER['REQUEST_URI'];
    			if( empty( $url ) )
    				$url = $_SERVER['PHP_SELF'];
    				
    			self::$_request_url = new self( $url );
    			self::$_request_url->defineConstants();
    		}
    		
    		return self::$_request_url;
    	}
    	
    	/**
    	 * Escapes an url to a browser and user friendly version.
    	 * @param String $url
    	 */
    	public static function escape( $url ){
			return trim( preg_replace( '#([^a-zA-Z\d]+)#U', '-', strtolower( $url ) ), '-' );
		}
    	
        /**
         * Holds the parsed url
         * @var Array Holds the parts of the url.
         */
        protected $_url;
        
        /**
         * Holds the computed parameters once generated.
         * @var array
         */
        protected $_parameters;

        /**
         * Constructs a new URL Parser.
         */
        public function __construct( $url )
        {
        	$this->_url = parse_url( $url );
        }
        
        /**
         * Defines URL constants such as BASE and RES_BASE, basepath and resource basepath.
         */
        public function defineConstants() {
        	if( defined( 'BASE' ) )
            	return false;
            	
        	$basepath = $this->getBasePath();
      		$url_config = WebLab_Config::getApplicationConfig()->get( 'Application.Parser.URL', WebLab_Config::OBJECT, false );

            $base = $basepath;
            $resources = $basepath;

        	if( !empty( $url_config ) && !$url_config->mod_rewrite ) {
        		$base .= ( $url_config->entrypoint ? $url_config->entrypoint : 'index.php' ) . '/';
        		$resources .= ( $url_config->resources ? $url_config->resources : 'www' ) . '/';
        	}

            if( !defined( 'BASE' ) )
                define( 'BASE', $base );
            if( !defined( 'RES_BASE' ) )
                define( 'RES_BASE', $resources );
        }

        /**
         * Get a collection of URL values
         * @param Array $values Names of the values you want to retrieve.
         * @return Array Returns the computed values for the requested values.
         * @deprecated
         */
        public function get( $values ) {
            if( !is_array( $values ) )
                throw new WebLab_Exception_Parser( 'WebLab_Parser_URL::get() only accepts arrays' );

            $array = array();
            foreach( $values as $value )
                $array[ $value ] = $this->__get( $value );

            return $array;
        }

        /**
         * Retrieves property by name
         * @param String $method The name of the property to get.
         * @return Object|bool If the requested property exists its value is returned. Otherwise false is returned.
         */
        public function __get( $method ) {
            $method = 'get' . ucfirst( $method );
            if( method_exists( $this, $method ) ) {
                return $this->$method();
            }

            return false;
        }

        /**
         * The full URL of the current page.
         * @return String Returns the full URL.
         */
        public function getFullURL() {
            return $this->getBaseURL() . ltrim( $this->getPath(), '/' );
        }

        /**
         * The Full Base URL of the website.
         * @return String Returns the full base URL.
         */
        public function getBaseURL() {
            $protocol = $this->getProtocol();
            $port = $this->getPort();
            if( ( $port == 80 && $protocol == 'http' ) || ( $port == 443 && $protocol == 'https' ) ) {
                $port = '';
            } else {
                $port = ':' . $port;
            }

            $base_url = $protocol . '://' .
                $this->getHostname() . $port . '/';

            return $base_url;
        }

        /**
         * The name of the current script running.
         * This will probably be your entry page, mostly index.php
         * @return String The name of the current script running. This will probably be your entry page, mostly index.php
         */
        public function getScriptname() {
            return array_pop( explode( '/', $_SERVER[ 'SCRIPT_FILENAME' ] ) );
        }

        /**
         * The basepath for urls.
         * This path exists out of
         * document_root/folder/to/application/
         * @return String Path to the current application root.
         * @deprecated use BASE_PATH instead.
         */
        public function getBasePath() {
            $urlParts = explode( '/', $_SERVER[ 'SCRIPT_NAME'] );
            unset( $urlParts[ count($urlParts)-1 ] );
            $httpPath = implode( '/', $urlParts ) . '/';

            return $httpPath;
        }

        /**
         * Get the parameters supplied through URL
         * This is everything behind the basepath and the $_GET parameter
         * @return Array Everything behind the basepath and the $_GET parameter as an array.
         */
        public function getParameters() {
        	if( !empty( $this->_parameters ) )
        		return $this->_parameters;
        		
            //$base = $this->getBasePath();
            $base = BASE; // This conforms to the mod_rewrite status, $this->getBasePath() does not.
            $path = $this->_url['path'];

            // Original fix for IIS6 host
            // When using PHP_SELF trailing slash on the basepath may cause problems.
            if( substr( $base, strlen( $base ) -1 ) == '/' ){
            	$base = substr( $base, 0, strlen( $base ) -1 );
            }
            
            if( $base != '/' )
                $path = str_replace( $base, '', $path );

            $params = explode( '/', $path );
            $params = array_values( array_filter( $params ) );
            
            $tmp = array();

            for( $i=0; $i<count($params);$i++ )
            {
                $param = $params[ $i ];
                if( !empty( $param ) && !is_numeric( $param ) )
                {
                    $tmp[ $param ] = isset( $params[ $i+1 ] ) ? $params[ $i+1 ] : '';
                }
            }

            $tmp = array_merge( $tmp, $params );
            $this->_parameters = array_merge( $tmp, $_GET );
            
            return $this->_parameters;
        }

        /**
         * Retrieve a single parameter by $key
         *
         * @param String $key The key to retrieve.
         */
        public function getParameter( $key ) {
            $param = $this->getParameters();
            return isset( $param[ $key ] ) ? $param[ $key ] : '';
        }
		
        /**
         * Get the protocol used to load this page.
         * @return String Locked at http for now.
         */
        public function getProtocol() {
        	// Temporary fix
        	return 'http';
        	
            return $this->_url['scheme'];
        }

        /**
         * Get the port on which the server is running.
         * @return Integer Locked at 80 for now.
         */
        public function getPort() {
        	// Temporary fix
        	return '80';
        	
            return $this->_url['port'];
        }

        /**
         * Get the hostname.
         * @return String Shorthand for $_SERVER['HTTP_HOST']
         */
        public function getHostname() {
            return $_SERVER['HTTP_HOST'];
        }

        /**
         * Get the path used to access this page.
         * @return String The path of the URL that is currently being viewed.
         */
        public function getPath() {
            return $this->_url['path'];
        }
        
        /**
         * Get the relative path to the current install
         * @return String The absolute path to this application.
         */
        public function getDirectory() {
        	$start = strpos( $this->getBasePath(), $this->getPath() ) + strlen( $this->getBasePath() );
        	$dir = substr( $this->getPath(), $start );
        	if( empty( $dir ) )
        		$dir = '/';
        	
        	if( substr( $dir, 0, 1 ) != '/' )
        		$dir = '/' . $dir;
        		
        	return $dir;
        }

        public function getCurrent( $ignore = array() ) {
            $param = $this->getParameters();
            $keys = array_filter( array_keys( $param ), 'is_numeric' );
            $url = '';
            
            for( $i=0; $i<count($keys); $i++ ){
                if( !in_array( $param[$i], $ignore ) )
                    $url .= $param[$i] . '/';
                else
                    $i++;
            }
            
            return $url;
        }
    }