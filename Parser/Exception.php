<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Parser
	 *
	 */
    class WebLab_Parser_Exception
    {

        protected $_template = 'error';
        protected $_log = false;
        protected $_exception;

        public function __construct( $register, $log )
        {
            error_reporting(0);
            $this->_template = new Exception( 'No error occurred.' );

            if( $register )
                $this->registerHook();

            if( isset( $log ) )
            {
                $this->_log = $log;
            }
        }

        public function getErrorTemplate()
        {
            return $this->_template;
        }

        public function setErrorTemplate( $template )
        {
            $this->_template    =   $template;
        }

        

        public function triggerException( $exception )
        {
            $this->_exception = $exception;
            
            $this->_logException()
                ->_viewException();
        }

        protected function _logException()
        {
            if( !$log )
            {
                return $this;
            }
            
            $log = fopen( $this->_log, 'a' );

            $time = date( DateTime::COOKIE );
            $exception = $time . ":\t" . $this->_exception->getMessage();

            fwrite( $log, $exception );
            fclose( $log );

            return $this;
            
        }

        protected function _viewException()
        {
            $template = new WebLab_Template( $this->_template );
            $template->exception    =   $exception;
            $template->render();
        }

        public function registerHook()
        {
            set_exception_handler( array( $this, 'triggerException' ) );
        }

        public function unregisterHook()
        {
            restore_exception_handler();
        }

    }