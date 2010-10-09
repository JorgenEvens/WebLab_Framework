<?php
    /**
     *
     * Template
     *
     * Represents a PHP Template file
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     * @package WebLab_Framework
     *
     */
    class WebLab_Template
    {

        /**
         *
         * @var String Contains the path to the template file.
         */
        protected $_template;

        /**
         *
         * @var Array All variables assigned to this template.
         */
        protected $_variables;

        /**
         *
         * @var String The directory in which to search for the templates.
         */
        protected $_dir;

        /**
         * Constructs a new Template
         * @param String $template The path to the template, relative to the configured template directory.
         */
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

            $this->setBasicVariables();
        }

        protected function setBasicVariables()
        {
            $this->url = (object) WebLab_Config::getInstance()->get( 'Application.Runtime.URL' )->toArray();
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
            include( $this->_dir . '/source/' . $this->_template );
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