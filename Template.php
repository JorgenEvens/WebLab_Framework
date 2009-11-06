<?php
    class WebLab_Template
    {

        protected $_template;
        protected $_variables;
        protected $_dir;

        public function __constuct()
        {
            $config = WebLab_Config::getInstance()->get( 'Application.Templates.directory' );
            if( $config )
            {
                $this->setTemplateDir( $config );
            }
        }

        public function attachTo( WebLab_Template $template, $moduleName )
        {
            $template->register( $this, $moduleName );
        }

        public function setTemplateDir( $dir )
        {
            $this->_dir = $dir;
        }

        public function getVars()
        {
            return $this->_variables;
        }

        public function __set( $name, $value )
        {
            $this->_variables[ $name ] = $value;
        }

        public function __get( $name )
        {
            return $this->_variables[ $name ];
        }

        public function render( $show )
        {
            ob_start();
            require_once( $this->_dir . '/' . strtr( '_', '/', $this->_template ) );
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