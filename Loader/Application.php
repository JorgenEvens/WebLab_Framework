<?php

    abstract class WebLab_Loader_Application
    {

	protected $_config;

	public function __construct()
	{
	    $this->_config = WebLab_Config::getInstance()->get( 'Application.Loader' );
	    $this->start();
	}

	final protected function _call( $method )
	{
	    if( method_exists( $this, $method ) )
	    {
			$this->$method();
	    }
	}

	final public function start()
	{
	    foreach( $this->_config->get( 'load' )->toArray() as $method => $start )
	    {
			if( $start )
			{
			    $this->_call( '_init' . ucfirst( $method ) );
			}
	    }
	}

	protected function _initUrlParser()
	{
            $url = new WebLab_Parser_URL();
                 
            $data = array
                (
                    'scriptname',
                    'directory',
                    'parameters',
                    'fullUrl',
                    'basePath'
                );
                
	    WebLab_Config::getInstance()->set( 'Application.Runtime.URL', $url->get( $data ) );
	}

        protected function _initExceptionParser()
        {
            $log = WebLab_Config::getInstance()->get( 'Application.Parser.Exception.log' );
            $exceptionParser = new WebLab_Parser_Exception( true, $log );
        }

        protected function _initAddInLoader()
        {
            WebLab_Config::getInstance()->set( 'Application.Runtime.Loader.AddIn.WebLab', new WebLab_Loader_AddIn() );
        }

        abstract protected function _initEnvironment();
        /*{
            WebLab_Config::getInstance()->set( 'Environment.template', 'default/index' );
            WebLab_Config::getInstance()->set( 'Environment.modules.content', '' );
        }*/

        abstract protected function _initShutdown();

        abstract protected function _initControlDispatcher();

    }