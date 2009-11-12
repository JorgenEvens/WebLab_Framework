<?php
    abstract class WebLab_Dispatcher_Abstract
    {
        
        protected $_parameters = array();
        protected $_pattern = '{*}Controller';
        protected $_default;

        public function __construct( $default, $pattern )
        {
            $this->setPattern( $pattern )
                ->setDefault( $default );
        }

        public function classFromPattern( $variable )
        {
            return str_replace( '{*}', ucfirst( $variable ), $this->_pattern );
        }

        final public function setPattern( $pattern )
        {
            $this->_pattern = $pattern;
            return $this;
        }

        final protected function setDefault( $class )
        {
            $this->_default = $class;
            return $this;
        }

    }