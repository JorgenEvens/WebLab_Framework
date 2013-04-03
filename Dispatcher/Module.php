<?php
	/**
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Dispatcher
	 *
	 */
    abstract class WebLab_Dispatcher_Module implements WebLab_Dispatcher
    {
        protected $param;
        protected $layout;

        public final function __construct()
        {
            $this->layout = &WebLab_Template::getRootTemplate();
            
            $this->param = WebLab_Parser_URL::getForRequest()->parameters;

            if( $this->__init() )
            {
                $this->execute();
            }
        }

        /**
         * Called before calling the execute method.
         * Authentication or denying access can be done here.
         * If returned true, execute will be called. Otherwise it will not.
         * 
         * @return boolean
         */
        protected function __init()
        { return true; }
        
        /**
         * Define the name of the module, this makes reading the action more robust.
         * 
         * @return String
         */
        protected function _getName() {
            preg_match( '#_([a-z0-9]+)$#i', get_class( $this ), $class );
        	return isset( $class[1] ) ? strtolower( $class[1] ) : 1;
        }

        /**
         * Called if __init returns true, will do the routing to the correct action.
         * 
         * @return *
         */
        public function execute( $action=null )
        {
            $call_default = false;

            if( empty( $action ) ) {
                // If no action is supplied, detect it from URL.
                if( isset( $this->param[ $this->_getName() ] ) )
                    $action = $this->param[ $this->_getName() ];

                $call_default = true;
            }
            
            if( !empty( $action ) && method_exists( $this, $action ) ) {
                return $this->$action();
            }

            if( $call_default ) {
                return $this->_default();
            }
        }

        /**
         * Will be called when no valid action is found.
         * This acts as a catch-all.
         */
        abstract protected function _default();

    }