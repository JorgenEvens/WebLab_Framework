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
        }

        public function getScriptname()
        {
            return array_pop( explode( '/', $_SERVER[ 'SCRIPT_FILENAME' ] ) );
        }

        public function getDirectory()
        {
	    return array_shift( $this->getURI() );
        }

        public function getParameters()
        {
            $params = array_pop( $this->getURI() );
            $params = explode( '/', $param );

            $tmp = array();

            foreach( $params as $param )
            {
                $tmp[ $param ] = next( $params );
            }

            return array_merge( $tmp, $params );
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