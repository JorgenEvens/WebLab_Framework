<?php
    abstract class WebLab_Dispatcher_Action
    {

        public function __construct( $parameters )
        {
            if( method_exists( $this, $parameters[2] ) )
            {
                $this->$parameters[2]();
            }else
            {
                $this->_default();
            }
        }

        abstract protected function _default();
    }