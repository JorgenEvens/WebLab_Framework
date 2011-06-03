<?php
    class WebLab_Form_Input extends WebLab_Form_Field
    {

        protected $_filters = array();
        protected $_isPostback = false;

        public function __construct( $name, $type, $value=null, $properties=array() ){
        	$properties['name'] = $name;
        	$properties['type'] = $type;
        	if( !empty( $value ) )
        		$properties['value'] = $value;
        		
        	parent::__construct( $properties );
        }
        
        public function update(){
            if( empty( $this->_form ) )
                    return;
            
            $response = ( $this->_form->getMethod() == WebLab_Form_Wrap::POST ) ? $_POST : $_GET;
			$this->_isPostback = isset( $response[ $this->name ] );
            
            switch( $this->_properties['type'] ){
                case 'checkbox':
                    $this->checked = ( $response[ $this->name ] === $this->value ) ? 'checked' : '';
                    break;

                case 'radio':
                    $this->selected = ( $response[ $this->name ] === $this->value ) ? 'selected' : '';
                    break;

                default:
                    $this->value = $response[ $this->name ];
                    break;
            };
        }

        public function __toString(){
            $html = '<input';

            foreach( $this->_properties as $key => $value ){
                $html .= ' ' . $key . '="' . addslashes( $value ) . '"';
            }

            $html .= ' />';
            return $html;
        }

        public function addFilter( WebLab_Filter_Filter $filter, $errorMessage ){
            $this->_filters[] = (object)array(
                'filter' => $filter,
                'errorMessage' => $errorMessage
            );
            return $this;
        }

        public function isValid(){
            $errors = array();
            
            foreach( $this->_filters as $filter ){
                if( !$filter->filter->test( $this->value ) )
                        $errors[] = $filter->errorMessage;
            }

            if( !count( $errors ) )
                return true;
            
            return $errors;
        }
        
        public function isPostback(){
        	return $this->_isPostback;
        }

    }