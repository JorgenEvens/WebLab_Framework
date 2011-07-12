<?php
    abstract class WebLab_Filter
    {

        protected $_test = array();

        public function __construct( $config, $value=null )
        {
            if( is_array( $config ) )
            {
                $this->_test = $config;
            }else if( is_string( $config ) || !empty( $value ) )
            {
                $this->_test = array( $config => $value );
            }
        }

        public function test( $value )
        {
            foreach( $this->_test as $test => $testValue )
            {
                if( !$this->$test( $testValue, $value ) )
                {
                    return false;
                }
            }
            
            return true;
        }

    }