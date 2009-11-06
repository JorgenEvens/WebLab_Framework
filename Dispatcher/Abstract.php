<?php
    abstract class WebLab_Dispatcher_Abstract
    {

        protected $_pattern = '{*}Controller';

        abstract public function __construct();

        public function classFromPattern( $variable )
        {
            return strtr( '{*}', $variable, ucfirst( $this->_pattern ) );
        }

        final public function setPattern( $pattern )
        {
            $this->_pattern = $pattern;
        }

        abstract protected function _default();

    }