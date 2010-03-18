<?php
    abstract class WebLab_Filter_Filter
    {

        protected function __construct()
        {}

        public function test( $value )
        {
            return call_user_func( $this->_test, $value );
        }

    }