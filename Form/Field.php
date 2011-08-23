<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Form
	 *
	 */
    abstract class WebLab_Form_Field
    {
        protected $_label = '';
    	
        protected $_form = null;

        protected $_properties = array();
        
        public function __construct( $properties = null, $label='' ){
            if( empty( $properties ) ){
                $this->_properties = array(
                    'name'      =>      '',
                    'type'      =>      'hidden'
                );
            }else{
                $this->_properties = $properties;
            }
            
            $this->_label = $label;
        }

        public function __get( $property ){
        	if( isset( $this->_properties[$property] ) )
            	return $this->_properties[ $property ];
            	
            return null;
        }

        public function __set( $property, $value ){
            $this->_properties[ $property ] = $value;
            return $this;
        }

        public function __isset( $property ){
            return isset( $this->_properties[ $property ] );
        }

        public function setForm( WebLab_Form $form ){
            if( !empty( $this->_form ) )
                    $this->_form->remove( $this );

            $this->_form = $form;
            return $this;
        }

        public function getForm(){
            return $this->_form;
        }
        
        protected function _prepare(){
        	if( isset($this->_properties['class']) && is_array( $this->_properties['class'] ) )
        		$this->_properties['class'] = implode( ' ', $this->_properties['class'] );
        }
        
    	public function getLabel(){
        	return $this->_label;
        }
        
    	public function setLabel( $label ){
        	$this->_label = $label;
        }

        public abstract function __toString();
        public abstract function update();
        public abstract function isValid();

    }