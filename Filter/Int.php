<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Filter
	 *
	 */
    class WebLab_Filter_Int extends WebLab_Filter
    {
    	
    	public function isNumeric( $testValue, $value ) {
    		return is_numeric( $value );
    	}

        public function equals( $testValue, $value )
        {
            return $testValue == $value;
        }

        public function notEquals( $testValue, $value )
        {
            return $testValue != $value;
        }

        public function greaterThan( $testValue, $value )
        {
            return $testValue > $value;
        }

        public function lessThan( $testValue, $value )
        {
            return $testValue < $value;
        }

        public function greaterThanOrEqual( $testValue, $value )
        {
            return $testValue >= $value;
        }

        public function lessThanOrEqual( $testValue, $value )
        {
            return $testValue <= $value;
        }

        public function between( $testValue, $value )
        {
            if( !is_array( $value ) )
                return false;

            if( count( $value ) != 2 )
                return false;

            return _between( $testValue, $value[0], $value[1] ) ||
                    _between( $testValue, $value[1], $value[0] );
        }

        protected function _between( $testValue, $high, $low )
        {
            return ( $testValue > $low && $testValue < $high );
        }

        public function notBetween( $testValue, $value )
        {
            return !$this->between( $testValue, $value );
        }

    }