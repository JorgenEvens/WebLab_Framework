<?php
    class WebLab_Parser_URL
    {
        
        protected $_url;

        public function __construct()
        {
            $this->_url = parse_url( $_SERVER['REQUEST_URI'] );
            DEFINE( 'BASE', $this->getBasePath() );
        }

        /*
         * Get a collection of URL values
         * @param   Array   $values Names of the values you want to retrieve.
         */
        public function get( $values )
        {
            if( !is_array( $values ) )
                throw new WebLab_Exception_Parser( 'WebLab_Parser_URL::get() only accepts arrays' );

            $array = array();
            foreach( $values as $value )
            {
                $array[ $value ] = $this->_get( $value );
            }

            return $array;
        }

        /*
         * Retrieves property by name
         * @param   String   $method The name of the property to get.
         */
        protected function _get( $method )
        {
            $method = 'get' . ucfirst( $method );
            if( method_exists( $this, $method ) )
            {
                return $this->$method();
            }

            return false;
        }

        /*
         * The full URL of the current page.
         */
        public function getFullURL()
        {
            $fullUrl = $this->getProtocol() . '://' .
                $this->getHostname() . ':' . $this->getPort() .
                $this->getPath();

            return $fullUrl;
        }

        /*
         * The name of the current script running.
         * This will probably be your entry page, mostly index.php
         */
        public function getScriptname()
        {
            return array_pop( explode( '/', $_SERVER[ 'SCRIPT_FILENAME' ] ) );
        }

        /*
         * The basepath for urls.
         * This path exists out of
         * document_root/folder/to/application/
         */
        public function getBasePath()
        {
            $urlParts = explode( '/', $_SERVER[ 'SCRIPT_NAME'] );
            unset( $urlParts[ count($urlParts)-1 ] );
            $httpPath = implode( '/', $urlParts ) . '/';

            return $httpPath;
        }

        private function clean( $value )
        {
            return ( empty( $value ) && !( $value === '0' ) );
        }

        /*
         * Get the parameters supplied through URL
         * This is everything behind the basepath and the $_GET parameter
         */
        public function getParameters()
        {
            $base = $this->getBasePath();
            $path = $this->_url['path'];

            if( $base != '/' )
                $params = strtr( $path, $base, '' );

            $params = explode( '/', $params );
            $params = array_values( array_filter( $params, array( $this, 'clean' ) ) );

            $tmp = array();

            for( $i=0; $i<count($params);$i++ )
            {
                $param = $params[ $i ];
                if( !empty( $param ) && !is_numeric( $param ) )
                {
                    $tmp[ $param ] = $params[ $i+1 ];
                }
            }

            $tmp = array_merge( $tmp, $params );
            return array_merge( $tmp, $_GET );
        }

        /*
         * Get the protocol used to load this page.
         */
        public function getProtocol()
        {
            return $this->_url['scheme'];
        }

        /*
         * Get the port on which the server is running.
         */
        public function getPort()
        {
            return $this->_url['port'];
        }

        /*
         * Get the hostname.
         */
        public function getHostname()
        {
            return $this->_url['host'];
        }

        /*
         * Get the path used to access this page.
         */
        public function getPath()
        {
            return $this->_url['path'];
        }
        
    }