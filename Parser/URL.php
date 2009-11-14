<?php
    class WebLab_Parser_URL
    {

        public function __construct( $values=null )
        {
            if( is_array( $values ) )
            {
                return $this->get( $values );
            }
        }

        public function get( $values )
        {
            if( !is_array( $values ) )
            {
                throw new WebLab_Exception_Parser( 'WebLab_Parser_URL::get() only accepts arrays' );
            }

            $array = array();
            foreach( $values as $value )
            {
                $array[ $value ] = $this->_get( $value );
            }

            return $array;
        }

        protected function _get( $method )
        {
            $method = 'get' . ucfirst( $method );
            if( method_exists( $this, $method ) )
            {
                return $this->$method();
            }

            return false;
        }

        public function getFullUrl()
        {
            $fullUrl = $this->getProtocol() . '://' .
                $_SERVER[ 'SERVER_NAME' ] .
                $this->getDirectory();

            return $fullUrl;
        }

        public function getScriptname()
        {
            return array_pop( explode( '/', $_SERVER[ 'SCRIPT_FILENAME' ] ) );
        }

        public function getDirectory()
        {
	    return array_shift( $this->getURI() );
        }

        public function getBasePath()
        {
            $localPath = array_shift( explode( $this->getScriptname(), $_SERVER[ 'SCRIPT_FILENAME' ] ) );

            $httpPath = array_pop( explode( $_SERVER[ 'DOCUMENT_ROOT' ], $localPath ) );
            return $httpPath;
        }

        public function getParameters()
        {
            $params = array_pop( str_replace( $this->getBasePath(), '', $this->getURI() ) );
            $params = explode( '/', $params );
            $params = array_values( array_filter( $params ) );

            $tmp = array();

            foreach( $params as $param )
            {
                if( !empty( $param ) )
                {
                    $tmp[ $param ] = next( $params );
                }
            }

            $tmp = array_merge( $tmp, $params );
            return array_merge( $tmp, $_GET );
        }

        public function getURI()
        {
            return explode( $this->getScriptname(), $_SERVER[ 'REQUEST_URI' ] );
        }

        public function getProtocol()
        {
            return strtolower(
                array_shift(
                    explode( '/', $_SERVER[ 'SERVER_PROTOCOL' ] )
                    )
                );
        }
        
    }