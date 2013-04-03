<?php
	/**
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Dispatcher
	 *
	 */
    class WebLab_Dispatcher_Visit implements WebLab_Dispatcher
    {
		protected static $_config;
    	protected static $_param;
    	
    	protected static function _setup() {
    		if( isset( self::$_config ) ) {
    			return;
    		}

    		self::$_config = WebLab_Config::getApplicationConfig()->get( 'Application.Dispatcher.Visit', WebLab_Config::OBJECT, false );
    		
    		if( !isset( self::$_config->default ) || !isset( self::$_config->prefix ) ) {
    			throw new WebLab_Exception_Dispatcher( 'Incomplete configuration.' );
    		}
    		self::$_param = WebLab_Parser_URL::getForRequest()->parameters;
    	}
    	
        public function __construct() {
			self::_setup();
        }
        
        public function execute() {
        	$alias = WebLab_Config::getApplicationConfig()->get( 'Application.Dispatcher.Aliasses', WebLab_Config::RAW, false );
        	$path = array_filter( array_keys( self::$_param ), create_function( '&$input', '$input=ucfirst($input); return !is_numeric($input) && !empty($input);' ) );
        	$i = count( $path );
        	
        	if( empty( $path ) && isset( $alias[""] ) ) {
        		$module = self::$_config->prefix . '_' . $alias[""];
        	} else {
        		$module = self::$_config->prefix . '_' . self::$_config->default;
        	}
        	
        	while( $i-- > 0 ) {
        		$module_name = implode( '_', $path );

        		if( isset( $alias[$module_name] ) ) {
        			$module_name = $alias[$module_name];
        		}
        		
        		$module_name = self::$_config->prefix . '_' . $module_name;

        		if( class_exists( $module_name ) ) {
        			$module = $module_name;
        			break;
        		}
                array_pop( $path );
        	}
        	
        	return new $module();
        	
        	/*
            
            $aliasses = config( 'Application.Modules.Aliasses', WebLab_Config::RAW );
            $module = isset( $this->_param[$depth] ) ? $this->_param[$depth] : '';

            if( isset( $aliasses[ $module ] ) ) {
                $module = $aliasses[ $module ];
            }

            if( $module ) {
                $module = $this->_generateClass( $module );
                if( class_exists( $module ) ) {
                    return new $module( $this->_param );
                } else {
                    $module = $this->classFromPattern( $this->_default );
                    if( !class_exists( $module ) ) {
                    	throw new WebLab_Exception_Dispatcher( 'The requested and default modules could not be found!' );
                    }
                    return new $module( $this->_param );
                }
            } else {
                $module = $this->classFromPattern( self::$_config->default );
                return new $module( $this->_param );
            }
            
            */
        }
        
        protected function _parseParam( $param ) {
        	$param = preg_replace_callback( '#-+(.)#', create_function( '$m', 'return strtoupper( $m[1] );'), $param );
        	return ucfirst( $param );
        }

        public function setPattern( $pattern ) {
            $this->_pattern = $pattern;
        }
        
        public function getPattern() {
        	return $this->_pattern;
        }

        public function setDefault( $class ) {
            $this->_default = $class;
        }
        
        public function getDefault() {
        	return $this->_default;
        }

    }