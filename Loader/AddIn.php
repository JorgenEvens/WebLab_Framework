<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Loader
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
            $includes = config( 'Application.Loader.includePaths', WebLab_Config::RAW );

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
            require_once( strtr( $className, '_', PATH_SEPARATOR ) . '.php' );
        }

        public function addIncludePath( $path )
        {
            set_include_path( get_include_path() . PATH_SEPARATOR . getcwd() . '/' . $path );
        }

        public function deleteIncludePath( $path )
        {
            $includes = explode( ':' . $path . ':', get_include_path() );
            set_include_path( implode( ':', $includes ) );
        }

    }