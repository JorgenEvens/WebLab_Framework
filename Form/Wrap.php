<?php
    class WebLab_Form_Wrap
    {
        const POST = 'POST';
        const GET = 'GET';

        protected $_fields = array();
        protected $_method = 'POST';
        protected $_errors = array();
        
        public function add( WebLab_Form_Field $field ){
            if( empty( $field ) )
                throw new WebLab_Exception_Form( 'Field not set' );

            if( empty( $field->name ) )
                    throw new WebLab_Exception_Form( 'Cannot add a field to a form without the field having a name.' );

            if( array_key_exists( $field->name, $this->_fields ) )
                    throw new WebLab_Exception_Form( 'Duplicate name attribute for fields.' );

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

        public function isValid(){
            foreach( $this->_fields as $key => $field ){
                $response = $field->isValid();
                if( $response !== true )
                    $this->_errors = $response;
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

        public function getErrors(){
            return $this->_errors;
        }

    }