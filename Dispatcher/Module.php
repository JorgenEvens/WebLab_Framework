<?php
    abstract class WebLab_Dispatcher_Module extends WebLab_Dispatcher_Abstract
    {

        final public function __construct( $parameters )
        {
            if( $parameters[1] )
            {
                $module = $this->classFromPattern( $parameters[1] );
                if( class_exists( $module ) )
                {
                    return new $action( $url['parameters'] );
                }else
                {
                    return $this->_default();
                }
            }else
            {
                return $this->_default();
            }
        }

        abstract protected function _default();

    }