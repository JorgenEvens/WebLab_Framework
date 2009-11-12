<?php
    abstract class WebLab_Dispatcher_Module extends WebLab_Dispatcher_Abstract
    {

        public function execute()
        {
            if( $this->_parameters[1] )
            {
                $module = $this->classFromPattern( $this->_parameters[1] );
                echo "class that we are looking for: " . $module;
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