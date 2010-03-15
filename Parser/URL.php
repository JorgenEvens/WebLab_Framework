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
            $urlParts = explode( '/', $_SERVER[ 'SCRIPT_NAME'] );
            unset( $urlParts[ count($urlParts)-1 ] );
            $httpPath = implode( '/', $urlParts ) . '/';

            return $httpPath;
        }

        public function clean( $value )
        {
            
            if( empty( $value ) && !( $value === '0' ) )
            {
                return false;
            }

            return true;
        }

        public function getParameters()
        {
            $base = $this->getBasePath();

            

            $params = ( $base != '/' ) ? str_replace( $base, '', array_pop( $this->getURI() ) ) : array_pop( $this->getURI() );
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

        public function getURI()
        {
            $url = array_shift( explode( '?', $_SERVER[ 'REQUEST_URI' ] ) );
            return explode( $this->getScriptname(), $url ) ;
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