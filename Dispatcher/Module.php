<?php
    abstract class WebLab_Dispatcher_Module extends WebLab_Dispatcher_Abstract
    {

        public function execute()
        {
            if( $parameters[1] )
            {
                $module = $this->classFromPattern( $parameters[1] );
                if( class_exists( $module ) )
                {
                    return new $action( $url['parameters'] );
                }else
                {
                    $module = $this->classFromPattern( $this->_default );
                    return new $module( $url['parameters'] );
                }
            }else
            {
                $module = $this->classFromPattern( $this->_default );
                return new $module( $url['parameters'] );
            }
        }

    }