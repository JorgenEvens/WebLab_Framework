<?php
    class Loader extends WebLab_Loader_Application
    {

        protected function _initEnvironment()
        {
            session_start();

            $env = new WebLab_Template( 'default/index.php' );
            WebLab_Config::getInstance()->set( 'Environment.template', $env );

            date_default_timezone_set( 'Europe/Brussels' );
            $_POST = $this->stripslashes_deep( $_POST );

            //header( 'Expires: Sat, 16 Nov 2019 23:09:49 GMT' );
        }

        protected function _initShutdown()
        {
            $env = WebLab_Config::getInstance()->get( 'Application.Runtime.Environment.template' );
            echo $env;
        }

        protected function _initControlDispatcher()
        {
            $url = WebLab_Config::getInstance()->get( 'Application.Runtime.URL' )->toObject();

            $dispatcher = new WebLab_Dispatcher_Visit( '404', 'Control_{*}Controller' );
            $dispatcher->execute();
        }

        function stripslashes_deep($value)
        {            
            return is_array($value) ? array_map( array( $this, 'stripslashes_deep' ), $value) : stripslashes($value);
        }

}
