<?php
    abstract class WebLab_Form_Field
    {
        
        protected $_form = null;

        protected $_properties = array();

        public function __construct( $properties = null ){
            if( empty( $properties ) ){
                $this->_properties = array(
                    'name'      =>      '',
                    'type'      =>      'hidden'
                );
            }else{
                $this->_properties = $properties;
            }
        }

        public function __get( $property ){
            return $this->_properties[ $property ];
        }

        public function __set( $property, $value ){
            $this->_properties[ $property ] = $value;
            return $this;
        }

        public function __isset( $property ){
            return isset( $this->_properties[ $property ] );
        }

        public function setForm( WebLab_Form_Wrap $form ){
            if( !empty( $this->_form ) )
                    $this->_form->remove( $this );

            $this->_form = $form;
            $this->update();
            return $this;
        }

        public function getForm(){
            return $this->_form;
        }

        public abstract function __toString();
        public abstract function update();
        public abstract function isValid();
        public abstract function isPostback();

    }