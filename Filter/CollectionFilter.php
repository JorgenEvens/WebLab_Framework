<?php
    class WebLab_Filter_CollectionFilter extends WebLab_Filter_Filter
    {

        public function contains( $testValue, $value )
        {
            return in_array( $value, $testValue );
        }

        public function keyExists( $testValue, $value )
        {
            $keys = array_keys( $testValue );
            return in_array( $value, $keys );
        }

        public function notContains( $testValue, $value )
        {
            return !$this->contains( $testValue, $value );
        }

        public function notKeyExists( $testValue, $value )
        {
            return !$this->keyExists( $testValue, $value );
        }

        public function hasElements( $testValue, $value )
        {
            return ( count( $value ) > 0 );
        }

        public function notHasElements( $testValue, $value )
        {
            return !$this->hasElements( $testValue, $value );
        }

    }