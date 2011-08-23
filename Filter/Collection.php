<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Filter
	 *
	 */
    class WebLab_Filter_Collection extends WebLab_Filter
    {

        public function contains( $testValue, $value )
        {
            return in_array( $value, $testValue );
        }

        public function keyExists( $testValue, $value )
        {
        	return array_key_exists( $value, $testValue );
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