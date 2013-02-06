<?php
    /**
     * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Loader
     *
     */
    class WebLab_Loader_AddIn
    {
    	
    	protected static $_self;
    	
    	public static function init() {
    		if( empty( self::$_self ) ) {
    			self::$_self = new self( false );
    		}
    	}
		// TODO: Rewrite.
        public function __construct( $register=false )
        {
            $includes = WebLab_Config::getApplicationConfig()->get( 'Application.Loader.includePaths', WebLab_Config::RAW, false );

            array_map( array( $this, 'addIncludePath' ), $includes );

            if( $register ){ $this->register(); };
        }

        // TODO: why does this break the application?
        public function register()
        {
            spl_autoload_register( array($this, 'acquire') );
        }

        public function unregister()
        {
            spl_autoload_unregister( array($this, 'acquire') );
        }

        public function acquire( $className )
        {
            return @include_once( $this->_parseName( $className ) );
        }

        protected function _parseName( $className )
        {
            require_once( strtr( $className, '_', DIRECTORY_SEPARATOR ) . '.php' );
        }

        public function addIncludePath( $path )
        {
        	if( $path[0] != '/' ) {
        		$path = getcwd() . '/' . $path;
        	}
            $path = realpath( $path );
            set_include_path( $path . PATH_SEPARATOR . get_include_path() );
        }

        public function deleteIncludePath( $path )
        {
            $includes = explode( PATH_SEPARATOR . $path . PATH_SEPARATOR, get_include_path() );
            set_include_path( implode( PATH_SEPARATOR, $includes ) );
        }

    }