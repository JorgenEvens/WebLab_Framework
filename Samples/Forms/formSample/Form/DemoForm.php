<?php
	class Form_DemoForm extends WebLab_Form_Wrap
	{

            public function __construct()
            {
                parent::__construct();
                $this->_initForm();
            }

            protected function _initForm(){
                // Create a filter for the name field.
                // Name must be minimum 5 characters long and maximum 10.
                $nameFilter = new WebLab_Filter_StringFilter(array(
                    'minLength'     =>      5,
                    'maxLength'     =>      10
                ));

                // Create the input field where the user will type in his name.
                $name = new WebLab_Form_Input(array(
                    'name'  =>  'name',
                    'type'  =>  'text'
                ));

                // Add the filter to the name field.
                $name->addFilter( $nameFilter, 'Your name should be between 5 and 10 characters long');

                // Create an Email field without validation.
                $email = new WebLab_Form_Input(array(
                    'name'  =>  'email',
                    'type'  =>  'text'
                ));

                // Add the fields to the form.
                $this->add($name)
                        ->add($email);
            }

        }