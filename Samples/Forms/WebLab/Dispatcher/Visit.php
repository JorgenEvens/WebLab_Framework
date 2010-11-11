<?php
    class WebLab_Dispatcher_Visit
    {

        public function __construct( $default, $pattern )
        {
            $this->setPattern( $pattern )
                ->setDefault( $default );
        }
        
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