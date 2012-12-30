<?php
    /**
     * Represents a PHP Template file
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     *
     */
    class WebLab_Template
    {

    	/**
    	 * 
    	 * @var stdClass Contains config for templates.
    	 */
    	protected static $_config;
    	
    	/**
    	 * Contains the root template, if set.
    	 * @var WebLab_Template
    	 */
    	protected static $_root;
    	
    	/**
    	 * Lazy load config.
    	 */
    	protected static function _getConfig() {
    		if( empty( self::$_config ) ) {
            	self::$_config = WebLab_Config::getApplicationConfig()->get( 'Application.Templates', WebLab_Config::OBJECT, false );
    		}
            	
            return self::$_config;
    	}
    	
    	/**
    	 * Set the root template for current request.
    	 * @param WebLab_Template $template
    	 */
    	public static function setRootTemplate( WebLab_Template $template ) {
    		self::$_root = $template;
    	}
    	
    	/**
    	 * Get the root template for current request.
    	 */
    	public static function &getRootTemplate() {
    		return self::$_root;
    	}
    	
    	/**
    	 * @var int The maximum age in seconds of a cached template.
    	 */
    	public $max_cache_age = 0;
    	
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
         * @var String The directory of a specific theme. Defaults to 'source'.
         */
        protected $_theme = 'source';
        
        /**
         * @var string Determines if logic should be called.
         */
        protected $_setup = false;
        
        /**
         * Constructs a new Template
         * @param String $template The path to the template, relative to the configured template directory.
         * @param String $theme	The name of the theme to be used. default theme is 'source'
         * @param String $directory The directory in which to look for the template. Defaults to configuration setting.
         */
        public function __construct( $template, $theme=null, $directory=null ) {
          	$config = self::_getConfig();

            if( !empty( $directory ) && is_string( $directory ) ) {
            	$this->setTemplateDir( $directory );
            } elseif( is_string( $config->directory ) ) {
                $this->setTemplateDir( $config->directory );
            }
            
            if( $theme === null && !empty( $config->theme ) && is_string( $config->theme ) ) {
            	$this->setTheme( $config->theme );
            }
                
            if( is_string( $theme ) ) {
            	$this->setTheme( $theme );
            }
            
            $variables['variables'] = array();
            
            $this->_variables = $variables;

            if( !is_string( $template ) ) {
                throw new WebLab_Exception_Template( 'Template name shoud refer to the path within the theme.' );
            }

            $this->_template = $template;

            $this->_init();
            $this->setBasicVariables();
        }
        
        /**
         * Set some default settings for a template.
         */
        protected function setBasicVariables() {}

        /**
         * Attaches a template as a variable to this template.
         * @param WebLab_Template $template Template you would like to attach to this template.
         * @param String $moduleName Variable name for $template.
         * @return WebLab_Template This template instance.
         * @deprecated Use WebLab_Template::set now.
         */
        public function attach( WebLab_Template &$template, $moduleName ) {
            $this->_variables[ $moduleName ] = &$template;

            return $this;
        }
        
        /**
         * Set a template variable value.
         * 
         * @param string $variable
         * @param mixed $value
         * @return WebLab_Template
         */
        public function set( $variable, $value ) {
        	$this->_variables[ $variable ] = $value;
        	
        	return $this;
        }
        
        /**
         * Get a template variable.
         * 
         * @param string $variable
         * @return mixed:
         */
        public function get( $variable ) {
            if( isset( $this->_variables[ $variable ] ) ) {
        	   return $this->_variables[ $variable ];
            }
            return null;
        }
        
		/**
		 * Set the theme folder within the template directory.
		 * @param String $dir Foldername of the theme.
		 * @return WebLab_Template This template instance.
		 */
        public function setTheme( $dir ) {
        	$this->_theme = $dir;
        	
        	return $this;
        }

        /**
         * Set the location of the template directory.
         * This directory contains all of the themes.
         * @param String $dir The directory to look for templates and themes.
         */
        public function setTemplateDir( $dir ) {
            $this->_dir = $dir;

            return $this;
        }

        /**
         * Get the entire stack of variables currently attached to this template.
         * @return Array Return an array of attached variables. VariableNames as keys.
         */
        public function getVars() {
            return $this->_variables;
        }

        /**
         * Catches all the property calls to set the appropriate variable.
         * @param String $name The name of the property (variable) being set.
         * @param Object &$value The value to be set.
         * @return WebLab_Template This template instance.
         */
        public function __set( $name, $value ) {
            $this->_variables[ $name ] = &$value;

            return $this;
        }

        /**
         * Catches all the property calls to return the appropriate variable.
         * @param String $name The name of the property (variable) te get.
         * @return Object The value of this property.
         */
        public function &__get( $name ) {
            return $this->_variables[ $name ];
        }

        /**
         * Renders the template to its compiled HTML form.
         * @param bool $show Whether to send the output to the browser requesting it. Defaults to false.
         * @return String The HTML code rendered.
         */
        public function render( $show=false ) {
            $code = $this->_getCode();
            if( $show ) {
                echo $code;
            }
            
            return $code;
        }

        /**
         * Running the template file and thus obtaining the HTML code.
         * @return String The HTML code rendered.
         */
        protected function _getCode() {       	
        	if( !$this->_setup ) {
        		$this->_setup = true;
        		$this->_load( 'setup' );
        	}
        	
            ob_start();
            $this->_load();
            return ob_get_clean();
        }

        /**
         * Include a component file for current template
         * $component will be attached behind the filename, including a suffix .php
         * 
         * @param string $component
         * @param array $variables
         */
        protected function _load( $component='', $extract=true ) {
        	if( !empty( $component ) ) {
        		$component = '.' . $component;
        	}
        	
        	$path = $this->_dir . ( empty( $this->_theme ) ? '/' : '/' . $this->_theme . '/' ) . $this->_template . $component . '.php';
        	
        	$exists = @fopen( $path, 'r', true );
        	if( $exists === false ) {
        		if( empty( $component ) ) throw new WebLab_Exception_Template( 'Template (' . $this->_template . ') not found!' );
        		return;
        	}
        	fclose( $exists );
        	
        	if( isset( self::_getConfig()->extract_vars ) && self::_getConfig()->extract_vars && $extract ) {
        		extract( $this->_variables );
        	}
        	
        	include( $path );
        }
        
        /**
         * Loads the init component of a template.
         * Injecting headers, scripts or styles should be done here.
         */
        protected function _init() {
        	 $this->_load( 'init', false );
        }

        /**
         * Retrieve cached template
         * @return string|NULL
         */
        protected function _getCache( $path ) {
        	if( file_exists( $path ) && ( time() - filemtime( $path ) ) < $this->max_cache_age ) {
        		return file_get_contents( $path, true );
        	}
        	
        	return null;
        }
        
        /**
         * Put cached code
         * @param string $code
         */
        protected function _putCache( $path, $code ) {
        	$folder = dirname( $path );
        	if( !is_dir( $folder ) ) {
        		mkdir( $folder, 0755, true );
        	}
        	if( !empty( $this->max_cache_age ) ) {
        		file_put_contents( $path, $code );
        	}
        }
        
        /**
         * Returns the value of the function render.
         * @return String Returns HTML that has been rendered by the render function. If a Exception occurs the message of this exception will be returned.
         */
        final public function __toString() {
        	try {
        		/*
        		 * Hot path for uncached templates.
        		 */
	        	if( empty( $this->max_cache_age ) ) {
	        		return $this->render();
	        	} else {
	        		/*
	        		 * Slower path, cache lookup
	        		 */
	        		
	        		$cache_dir = $path = WebLab_Config::getApplicationConfig()->get( 'Application.Templates.cache_dir', WebLab_Config::RAW );
	        		$theme_dir = empty( $this->_theme ) ? '/' : '/' . $this->_theme . '/';
	        		 
	        		$param = md5( serialize( $this->_variables ) );
	        		 
	        		$cache_file = $theme_dir . $this->_template . $param . '.php';
	        		$cache_file = $_SERVER['DOCUMENT_ROOT'] . BASE . $cache_dir . '/' . $theme_dir . md5( $cache_file );
	        		 
	        		$cache = $this->_getCache( $cache_file );
	        		
	        		if( !empty( $cache ) ) {
	        			return $cache;
	        		}
	        		
	        		$code = $this->render();
	        		$this->_putCache( $cache_file, $code );
	        		return $code;
	        	}
        	} catch( Exception $ex ) {
        		return $ex->getMessage();
        	}
        }

    }