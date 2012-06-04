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
    	/**
    	 * Label for this field.
    	 * 
    	 * @var string
    	 */
        protected $_label = '';
    	
        /**
         * The form of which this field is part.
         * 
         * @var WebLab_Form
         */
        protected $_form = null;

        /**
         * Attributes for this field.
         * 
         * @var array
         */
        protected $_attributes = array();
        
        /**
         * Filters that can be applied for validation.
         *
         * @var array
         */
        protected $_filters = array();
        
        /**
         * Constructs a new field.
         * 
         * @param array $attributes
         * @param string $label
         */
        public function __construct( $attributes = null, $label='' ){
            if( empty( $attributes ) ){
                $this->_attributes = array(
                    'name'      =>      ''
                );
            }else{
                $this->_attributes = $attributes;
            }
            
            $this->_label = $label;
        }

        /**
         * Retrieve the value of an attribute.
         * 
         * @param string $attribute
         * @return string
         * @deprecated
         */
        public function __get( $attribute ){
        	return $this->getAttribute( $attribute );
        }
        
        /**
         * Retrieve the value of an attribute.
         *
         * @param string $attribute
         * @return string
         */
        public function getAttribute( $attribute ) {
        	if( isset( $this->_attributes[$attribute] ) )
        		return $this->_attributes[ $attribute ];
        	 
        	return null;
        }

        /**
         * Set the value of an attribute.
         * 
         * @param string $attribute
         * @param string $value
         * @return WebLab_Form_Field
         * @deprecated
         */
        public function __set( $attribute, $value ){
            return $this->setAttribute( $attribute, $value );
        }
        
        /**
         * Set the value of an attribute.
         *
         * @param string $attribute
         * @param string $value
         * @return WebLab_Form_Field
         */
        public function setAttribute( $attribute, $value ) {
        	$this->_attributes[ $attribute ] = $value;
        	return $this;
        }

        /**
         * Check if an attribute has been set.
         * 
         * @param string $attribute
         * @return boolean
         */
        public function __isset( $attribute ){
            return isset( $this->_attributes[ $attribute ] );
        }

        /**
         * Set the to which this field belongs.
         * 
         * @param WebLab_Form $form
         * @return WebLab_Form_Field
         */
        public function setForm( WebLab_Form $form ){
            if( !empty( $this->_form ) )
                    $this->_form->remove( $this );

            $this->_form = $form;
            return $this;
        }

        /**
         * Return the form to which this field belongs.
         * 
         * @return WebLab_Form
         */
        public function getForm(){
            return $this->_form;
        }
        
        /**
         * Return the label of this field.
         * 
         * @return string
         */
    	public function getLabel(){
        	return $this->_label;
        }
        
        /**
         * Set the label of this field.
         * 
         * @param string $label
         */
    	public function setLabel( $label ){
        	$this->_label = $label;
        }
        
        /**
         * Add a validation filter
         *
         * @param WebLab_Filter $filter
         * @param unknown_type $errorMessage
         */
        public function addFilter( WebLab_Filter $filter, $errorMessage=null ){
        	$this->_filters[] = (object)array(
        			'filter' => $filter,
        			'errorMessage' => empty( $errorMessage ) ? 'Unkown error' : $errorMessage
        	);
        	return $this;
        }
        
        /**
         * Determine if field is correct.
         *
         * @return boolean|array
         */
        public function isValid(){
        	$errors = array();
        	$value = $this->getValue();
        	
        	foreach( $this->_filters as $filter ) {
				if( !$filter->filter->test( $value ) )
					$errors[] = $filter->errorMessage;
        	}
        		
        	if( !count( $errors ) )
        		return true;
        
        	return $errors;
        }

        /**
         * Returns the HTML representation of this field.
         * 
         * @return string
         */
        public abstract function __toString();
        
        /**
         * Update the field with the response.
         * 
         */
        public abstract function update();

        /**
         * Retrieve the value.
         * 
         * @return string
         */
        public abstract function getValue();
        
        /**
         * Set value of this field.
         * 
         * @param string $value
         * @return WebLab_Form_Field
         */
        public abstract function setValue( $value );
    }