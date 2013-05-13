<?php
    /**
     * Form.php
     *
     * This file contains the implementation of the WebLab_Form class.
     * @see WebLab_Form
     */
	/**
	 * Provides form abstraction, integrating retrieval and validation into one package.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 *
	 */
    abstract class WebLab_Form
    {
    	/**
    	 * Constant for POST method
    	 * 
    	 * @var string
    	 */
        const POST = 'POST';
        
        /**
         * Constant for GET method
         * 
         * @var string
         */
        const GET = 'GET';

        /**
         * Fields that are part of this form.
         * 
         * @var array
         */
        private $_fields = array();
        
        /**
         * Method being used by the form
         * 
         * @var string
         */
        protected $_method = self::POST;
        
        /**
         * Action of the form
         * 
         * @var string
         */
        protected $_action = '?';
        
        /**
         * Validation errors that have occured.
         * 
         * @var array
         */
        private $_errors = array();
        
        /**
         * Postback detection element
         * 
         * @var WebLab_Form_Input
         */
        public $postback = null;
        
        /**
         * Construct new form
         * 
         * @param string $action
         * @param string $method
         */
        public function __construct( $action='', $method = self::POST ){
            $this->_action = $action;
            $this->_method = $method;
            
            $this->_setupFields();
            $this->_setupPostback();
            
            if( $this->isPostback() ) {
            	$this->update();
            	$this->_setupValidation();
            }
        }
        
        /**
         * Declare fields used by this form.
         * 
         */
        protected function _setupFields(){}
        
        /**
         * Declare validation rules for this form.
         * 
         */
        protected function _setupValidation(){}
        
        /**
         * Generate postback element
         * 
         */
        protected function _setupPostback(){
        	$this->postback = new WebLab_Form_Input( $this->getFormId(), 'hidden', null, 'postback' );
        }

        /**
         * Add a field to the form
         * 
         * @param WebLab_Form_Field $field
         * @throws WebLab_Exception_Form
         */
        public function add( WebLab_Form_Field $field ){
            if( empty( $field ) )
                throw new WebLab_Exception_Form( 'Field not set' );

            $field_name = $field->name;
            if( empty( $field_name ) )
                    throw new WebLab_Exception_Form( 'Cannot add a field to a form without the field having a name.' );

            $field_exists = isset( $this->_fields[ $field_name ] );
            
            if( $field_exists )
            	throw new WebLab_Exception_Form( 'Duplicate name attribute for fields.' );

            $this->_fields[ $field_name ] = $field;

            $field->setForm( $this );

            return $this;
        }

        /**
         * Remove a field from the form
         * 
         * @param string|WebLab_Form_Field $field
         * @throws WebLab_Exception_Form
         * @return WebLab_Form
         */
        public function remove( $field ){
            if( !$field instanceof WebLab_Form_Field && !is_string( $field ) )
                throw new WebLab_Exception_Form( 'The field to remove should be either of type WebLab_Form_Field or string.' );

            if( is_string( $field ) ) {
            	unset( $this->_fields[ $field ] );
            	return $this;
            }
            
            if( $field instanceof WebLab_Form_Field && empty( $field->name ) )
            	throw new WebLab_Exception_Form( 'Cannot remove a field from a form without the field having a name.' );

            $set = &$this->_fields[ $field->name ];
            if( empty( $set ) )
            	return $this;
            
            foreach( $set as $key => $value ) {
            	if( $value == $field )
            		unset( $set[ $key ] );
            }

            return $this;
        }
        
        /**
         * Generates a ID by which the form can identify itself.
         * 
         * @return string
         */
        public function getFormId(){
        	$fields = array_keys( $this->_fields );
        	$fields = implode( ',', $fields );

        	return md5( $this->_action . '-' . $this->_method . '-' . $fields );
        }
        
        /**
         * Determine if we are dealing with a postback.
         * 
         * @return boolean
         */
        public function isPostback(){
        	$postback_code = $this->getFormId();
        	$response = $this->getResponse();
        	return isset( $response[$postback_code] );
        }
        
        /**
         * Retrieve the correct response set
         * 
         * @return array
         */
        public function getResponse(){
        	return ( $this->_method == self::POST ) ? $_POST : $_GET;
        }

        /**
         * Determine if all fields are filled out correctly.
         * 
         * @return boolean
         */
        public function isValid(){
            foreach( $this->_fields as $key => $field ){
                $response = $field->isValid();
                
                if( $response !== true )
                    $this->_errors[$key] = $response;
            }
            return ( count( $this->_errors ) == 0 );
        }

        /**
         * Return method being used.
         * 
         * @return string
         */
        public function getMethod(){
            return $this->_method;
        }

        /**
         * Set method form is going to use.
         * 
         * @param string $method
         * @throws WebLab_Exception_Form
         * @return WebLab_Form
         */
        public function setMethod( $method ){
            if( !is_string( $method ) )
                throw new WebLab_Exception_Form( 'Method should be either POST or GET');

            if( $method == 'POST' || $method == 'GET' )
                $this->_method = $method;

            return $this;
        }

        /**
         * Return the action that this form is configured with.
         * 
         * @return string
         */
        public function getAction(){
            return $this->_action;
        }

        /**
         * Set the action to be used by this form
         * 
         * @param string $action
         * @throws Exception
         * @return WebLab_Form
         */
        public function setAction( $action ){
            if( !is_string( $action ) )
                throw new Exception( 'Action should be a string, pointing to the result page' );

            $this->_action = $action;

            return $this;
        }
        
        /**
         * Get a list of the fields.
         * 
         * @return array
         */
        public function getFields(){
        	return $this->_fields;
        }

        /**
         * Update fields to correspond with posted value.
         * 
         */
        public function update(){
            foreach( $this->_fields as $field ){
                $field->update();
            }
        }

        /**
         * Retrieve validation errors.
         * 
         * @return multitype:
         */
        public function getErrors(){
            return $this->_errors;
        }

        /**
         * Returns a field with $name.
         * 
         * @param string $name
         * @return WebLab_Form_Field
         * @deprecated
         */
        public function __get( $name ){
            return $this->_fields[ $name ];
        }
        
        /**
         * Returns a field with $name
         * 
         * @param string $name
         * @return WebLab_Form_Field|array
         */
        public function get( $name ) {
        	return isset( $this->_fields[ $name ] ) ? $this->_fields[ $name ] : null;
        }
        
        /**
         * Returns whether a field is set.
         * 
         * @param string $name
         * @return boolean
         * @deprecated
         */
        public function __isset( $name ) {
        	return isset( $this->_fields[$name] );
        }
        
        /**
         * Get the value of a field
         * 
         * @param unknown_type $field
         * @throws WebLab_Exception_Form
         * @return string
         */
        public function getValue( $field ) {
        	if( $field instanceof WebLab_Form_Field ) {
        		$field = $field->getAttribute( 'name' );
        	}
        	
        	// name[]
        	$name = strpos( $field, '[' );
        	if( $name !== false ) {
        		$name = substr( $field, 0, $name );
        	} else {
        		$name = $field;
        		$field = null;
        	}

        	if( !isset( $this->_fields[ $name ] ) )
        		throw new WebLab_Exception_Form( 'The field to remove should be either of type WebLab_Form_Field or string.' );
        	
        	$resp = $this->getResponse();
        	
        	if( empty( $field ) ) {
        		$value = isset( $resp[ $name ] ) ? $resp[ $name ] : null;
        	} else {
        		$value = isset( $resp[ $name ] ) ? $resp[ $name ] : null;
        		
        		preg_match_all( '#\[([^\]]+)\]#', $field, $matches, PREG_SET_ORDER );
        		
        		foreach( $matches as $match ) {
        			if( empty( $value ) ) break;
        			
        			@$value = $value[ $match[1] ];
        		}
        	}

        	return $value;
        }
        
        /**
         * Get all fields their values.
         * 
         * @return array
         */
        public function getValues() {
        	$values = array();
        	
        	foreach( $this->_fields as $field ) {
        		$values[ $field->name ] = $field->getValue();
        	}
        	
        	return $values;
        }
        
        /**
         * Set value for a fields.
         * 
         * @param array $obj
         * @return WebLab_Form
         */
        public function setValues( $obj ) {
        	foreach( $obj as $field => $value ) {
        		if( isset( $this->_fields[ $field ] ) ) {
        			$this->_fields[ $field ]->setValue( $value );
        		}
        	}
        	return $this;
        }
        
        /**
         * Get all fields their values.
         * 
         * @return array
         * @deprecated
         */
        public function getResults(){
        	return $this->getValues();
        }

    }