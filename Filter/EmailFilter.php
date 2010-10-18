<?php
    class WebLab_Filter_EmailFilter extends WebLab_Filter_Filter
    {
        protected $_rule = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/';

        public function isValid( $testValue, $value )
        {
            if( preg_match( $this->_rule, $value ) )
                    return true;
            return false;
        }

    }