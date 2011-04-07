<?php
    class WebLab_Form_Wrap
    {
        const POST = 'POST';
        const GET = 'GET';

        private $_fields = array();
        protected $_method = self::POST;
        protected $_action = '';
        private $_errors = array();

        public function __construct( $action='', $method = self::POST ){
            $this->_action = $action;
            $this->_method = $method;
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
        
        public function isPostback(){
        	foreach( $this->_fields as $key => $field ){
                if( $field->isPostback() )
                	return true;
            }
            return false;
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

    }