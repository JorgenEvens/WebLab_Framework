<?php
    abstract class WebLab_Dispatcher_Module
    {
        protected $_parameters = array();

        public function __construct( $parameters )
        {
            $this->_parameters = $parameters;
            $this->execute();
        }

        public function execute()
        {
            if( $this->_parameters[1] )
            {
                $action = $this->_parameters[1];
                if( method_exists( $this, $action ) )
                {
                    return $this->$action( $this->_parameters );
                }else
                {
                    $this->_default( $this->_parameters );
                }
            }else
            {
                $this->_default( $this->_parameters );
            }
        }

        abstract protected function _default( $parameters );

    }