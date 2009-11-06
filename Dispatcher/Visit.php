<?php
    class WebLab_Dispatcher_Visit extends WebLab_Dispatcher_Abstract
    {

        protected $_default;

        final public function __construct()
        {
            $url = WebLab_Config::getInstance()->get( 'Application.Runtime.URL' );
            
            if( $url[ 'parameters' ][0] )
            {
                $module = $this->classFromPattern( $url['parameters'][0] );
                if( class_exists( $module ) )
                {
                    return new $module( $url['parameters'] );
                }else
                {
                    return $this->_default();
                } 
            }else
            {
                return $this->_default();
            }
        }

        protected function _default()
        {
            if( !is_callable( $this->_default ) )
            {
                throw new WebLab_Exception_Dispatcher( 'Default module function not set!' );
            }
            call_user_func( $this->_default );
        }

    }