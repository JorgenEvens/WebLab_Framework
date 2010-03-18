<?php
    class WebLab_Filter_StringFilter extends WebLab_Filter_Filter
    {

        private $_value;
        private $_test;

        public static function Equals( $string )
        {
            return self::_CreateFilter( $string, 'equals' );
        }

        public static function Contains( $string )
        {
            return self::_CreateFilter( $string, 'contains' );
        }

        public static function NotEquals( $string )
        {
            return self::_CreateFilter( $string, 'notEquals' );
        }

        public static function In( $array )
        {
            return self::_CreateFilter( $array, 'inArray' );
        }

        protected static function _CreateFilter( $value, $test )
        {
            $instance = new self();
            $instance->_value = $value;
            $instance->_test = array( '$this', $test );

            return $instance;
        }

        protected function inArray( $value )
        {
            return in_array( $value, $this->_value );
        }

        protected function notEquals( $value )
        {
            return !$this->equals( $value );
        }

        protected function equals( $value )
        {
            return $value == $this->_value;
        }

        protected function contains( $value )
        {
            return ( strpos( $this->_value, $value )  > -1 );
        }

    }