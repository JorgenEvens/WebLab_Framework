<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Filter
	 *
	 */
    class WebLab_Filter_String extends WebLab_Filter
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

        protected function equalsIgnoreCase( $testValue, $value )
        {
            return $this->equals( strtolower( $testValue ), strtolower( $value ) );
        }

        protected function contains( $testValue, $value )
        {
            return ( strpos( $value, $testValue )  > -1 );
        }

        protected function maxLength( $testValue, $value )
        {
            return !( strlen( $value ) > $testValue );
        }

        protected function minLength( $testValue, $value )
        {
            return !( strlen( $value ) < $testValue );
        }

    }