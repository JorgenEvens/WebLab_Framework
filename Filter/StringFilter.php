<?php
    class WebLab_Filter_StringFilter extends WebLab_Filter_Filter
    {

        protected function inArray( $testValue, $value )
        {
            return in_array( $value, $testValue );
        }

        protected function notEquals( $testValue, $value )
        {
            return !$this->equals( $testValue, $value );
        }

        protected function equals( $testValue, $value )
        {
            return $value == $testValue;
        }

        protected function contains( $testValue, $value )
        {
            return ( strpos( $value, $testValue )  > -1 );
        }

        protected function maxLength( $testValue, $value )
        {
            return !( strlen( $value ) > $testValue );
        }

        protected function minLength( $length, $value )
        {
            return !( strlen( $value ) < $testValue );
        }

    }