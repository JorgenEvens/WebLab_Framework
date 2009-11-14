<?php
    class WebLab_Dispatcher_Visit extends WebLab_Dispatcher_Abstract
    {

        public function execute()
        {
            $url = WebLab_Config::getInstance()->get( 'Application.Runtime.URL' )->toArray();
            $moduleAliasses = WebLab_Config::getInstance()->get( 'Application.Modules.Aliasses' )->toArray();

            $module = $url[ 'parameters' ][0];

            if( isset( $moduleAliasses[ $module ] ) )
            {
                $module = $moduleAliasses[ $module ];
            }

            if( $module )
            {
                $module = $this->classFromPattern( $module );
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