<?php
	// TODO: Review entire WebLab_Form tree for cleanup & optimizations
    abstract class WebLab_Form
    {
        const POST = 'POST';
        const GET = 'GET';

        private $_fields = array();
        protected $_method = self::POST;
        protected $_action = '';
        private $_errors = array();
        public $postback = null;
        
        public function __construct( $action='', $method = self::POST ){
            $this->_action = $action;
            $this->_method = $method;
            
            $this->_setupPostback();
        }
        
        protected function _setupPostback(){
        	$this->postback = new WebLab_Form_Input( $this->getFormId(), 'hidden', 'postback' );
        }

        public function add( WebLab_Form_Field $field ){
            if( empty( $field ) )
                throw new WebLab_Exception_Form( 'Field not set' );

            if( empty( $field->name ) )
                    throw new WebLab_Exception_Form( 'Cannot add a field to a form without the field having a name.' );

            if( array_key_exists( $field->name, $this->_fields ) )
                    throw new WebLab_Exception_Form( 'Duplicate name attribute for fields.' );

            $field->setForm( $this );
            $this->_fields[$field->name] = $field;

            return $this;
        }

        public function remove( $field ){
            if( !$field instanceof WebLab_Form_Field && !is_string( $field ) )
                throw new WebLab_Exception_Form( 'The field to remove should be either of type WebLab_Form_Field or string.' );

            if( $field instanceof WebLab_Form_Field ){
                if( empty( $field->name ) )
                    throw new WebLab_Exception_Form( 'Cannot remove a field from a form without the field having a name.' );

                $field = $field->name;
            }

            unset( $this->_fields[ $field ] );
            return $this;
        }
        
        public function getFormId(){
        	return substr( md5( $this->_action . '-' . $this->_method ), 0, 6 );
        }
        
        public function isPostback(){
        	$postback_code = $this->getFormId();
        	$response = $this->_getResponse();
        	return isset( $response[$postback_code] );
        }
        
        public function _getResponse(){
        	return ( $this->_method == self::POST ) ? $_POST : $_GET;
        }

        public function isValid(){
            foreach( $this->_fields as $key => $field ){
                $response = $field->isValid();
                if( $response !== true ){
                    $this->_errors[$key] = $response;
                }
            }
            return ( count( $this->_errors ) == 0 );
        }

        public function getMethod(){
            return $this->_method;
        }

        public function setMethod( $method ){
            if( !is_string( $method ) )
                throw new WebLab_Exception_Form( 'Method should be either POST or GET');

            if( $method == 'POST' || $method == 'GET' )
                $this->_method = $method;

            return $this;
        }

        public function getAction(){
            return $this->_action;
        }

        public function setAction( $action ){
            if( !is_string( $action ) )
                throw new Exception( 'Action should be a string, pointing to the result page' );

            $this->_action = $action;

            return $this;
        }
        
        public function getFields(){
        	return $this->_fields;
        }

        public function update(){
            foreach( $this->_fields as $field ){
                $field->update();
            }
        }

        public function getErrors(){
            return $this->_errors;
        }

        public function __get( $name ){
            return $this->_fields[ $name ];
        }
        
        public function __isset( $name ) {
        	return isset( $this->_fields[$name] );
        }
        
        // TODO: allow string
        public function getValue( WebLab_Form_Field $field ){
        	$response = $this->_getResponse();
        	if( isset( $response[ $field->name ] ) )
        		return $response[ $field->name ];
        	else
        		return null;
        }
        
        public function getResults(){
        	$fields = array_keys( $this->_fields );
        	$response = $this->_getResponse();
        	$data = array();
        	
        	foreach( $response as $key => $value )
        		if( in_array( $key, $fields) )
        			$data[$key] = $value;
        			
        	return $data;
        }

    }