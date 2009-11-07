<?php
    class WebLab_Data_Row
    {
        protected $_values = array();
        protected $_original = array();
        protected $_id=null;

        public function __construct( $data, $id=null )
        {
            if( is_array( $data ) )
            {
                $this->_values = $data;
                $this->_original = $data;
            }
        }

        public function getId()
        {
            return $this->_id;
        }

        public function original()
        {
            return $this->_original;
        }

        public function toArray()
        {
            return $this->_values;
        }

        public function __get( $var )
        {
            $method = 'get' . ucfirst( $var );
            if( method_exists( $this, $method ) )
            {
                return $this->$method();
            }

            if( !isset( $this->_values[ $var ] ) )
            {
                throw new WebLab_Exception_Data( 'This property (' . htmlentities( $var ) . ') was not found!' );
            }

            return $this->_values[ $var ];
        }

        public function __set( $var, $value )
        {
            $method = 'set' . ucfirst( $var );
            if( method_exists( $this, $method ) )
            {
                return $this->$method( $value );
            }

            if( !isset( $this->_values[ $var ] ) )
            {
                throw new WebLab_Exception_Data( 'This property (' . htmlentities( $var ) . ') was not found!' );
            }

            $this->_values[ $var ] = $value;

            return $this;
        }


    }