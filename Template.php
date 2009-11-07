<?php
    class WebLab_Template
    {

        protected $_template;
        protected $_variables;
        protected $_dir;

        public function __construct( $template )
        {
            $config = WebLab_Config::getInstance()->get( 'Application.Templates.directory' );

            if( $config )
            {
                $this->setTemplateDir( $config );
            }

            $this->_variables = array();

            if( !is_string( $template ) )
            {
                throw new WebLab_Exception_Template();
            }

            $this->_template = $template;
        }

        public function attach( WebLab_Template &$template, $moduleName )
        {
            $this->_variables[ $moduleName ] = &$template;

            return $this;
        }

        public function setTemplateDir( $dir )
        {
            $this->_dir = $dir;

            return $this;
        }

        public function getVars()
        {
            return $this->_variables;
        }

        public function __set( $name, $value )
        {
            $this->_variables[ $name ] = $value;

            return $this;
        }

        public function &__get( $name )
        {
            return $this->_variables[ $name ];
        }

        public function render( $show=false )
        {
            ob_start();
            require_once( $this->_dir . '/source/' . strtr( $this->_template, '_', '/' ) );
            $code = ob_get_clean();
            if( $show )
            {
                echo $code;
            }
            return $code;
        }

        final public function __toString()
        {
            return $this->render();
        }

    }