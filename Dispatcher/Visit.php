<?php
    class WebLab_Dispatcher_Visit extends WebLab_Dispatcher_Abstract
    {

        public function execute()
        {
            $url = WebLab_Config::getInstance()->get( 'Application.Runtime.URL' )->toArray();

            if( $url[ 'parameters' ][0] )
            {
                $module = $this->classFromPattern( $url['parameters'][0] );
                if( class_exists( $module ) )
                {
                    return new $module( $url['parameters'] );
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