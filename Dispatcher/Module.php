<?php
    abstract class WebLab_Dispatcher_Module
    {
        protected $param;
        protected $layout;

        public final function __construct( $parameters )
        {
            $this->layout = &WebLab_Config::getInstance()->get( 'Application.Runtime.Environment.template' );
            
            $this->param = !empty( $parameters ) ? $parameters : array();

            if( $this->__init() )
            {
                $this->execute();
            }
        }

        protected function __init()
        { return true; }

        public function execute()
        {
            if( $this->param[1] )
            {
                $action = $this->param[1];
                if( method_exists( $this, $action ) )
                {
                    return $this->$action( $this->param );
                }else
                {
                    $this->_default( $this->param );
                }
            }else
            {
                $this->_default( $this->param );
            }
        }

        abstract protected function _default();

    }