<?php
    class Control_IndexController extends WebLab_Dispatcher_Module
    {

        public function _default()
        {
            $template = new WebLab_Template( 'home/home.php' );
            $template->form = new Form_DemoForm();
            $this->layout->content = $template;
        }

    }
