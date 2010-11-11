<?php
    class Control_404Controller extends WebLab_Dispatcher_Module
    {
        public function __init()
        {
            $template = new WebLab_Template( 'error/404.php' );
            $layout = WebLab_Config::getInstance()->get( 'Application.Runtime.Environment.template' );
            $template->param = $parameters;
            
            $layout->attach( $template, 'content' );
        }
        
        protected function _default() {
        }
}