<?php
    abstract class WebLab_Data_Row
    {
        protected $_values = array();

        public function __construct( $data )
        {
            if( is_array( $data ) )
            {
                $this->_values = $data;
            }
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


    }