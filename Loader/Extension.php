<?php
	class WebLab_Loader_Extension {

		protected static $_instance;
    	
    	public static function init() {
    		if( empty( self::$_instance ) ) {
    			self::$_instance = new self();
    		}
    	}

		public function __construct() {
			$config = WebLab_Config::getApplicationConfig()->get( 'Application.Extensions', WebLab_Config::RAW, false );
			$dir = ( $config->Location[0] != '/' ) ? realpath( getcwd() . '/' . $path ) : $path;

			$modules = array();
			foreach( $config->Active as $module ) {
				$modules[] = $dir . '/' . $module;
			}
			set_include_path( get_include_path() . PATH_SEPARATOR . implode( PATH_SEPARATOR, $modules ) );
        }
	}