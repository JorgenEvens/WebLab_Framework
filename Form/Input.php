<?php
    class WebLab_Form_Input extends WebLab_Form_Field
    {

        protected $_filters = array();

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
            
            $value = $this->_form->getValue($this);
            switch( $this->_properties['type'] ){
                case 'checkbox':
                    $this->checked = ( $value === $this->value ) ? 'checked' : '';
                    break;

                case 'radio':
                    $this->selected = ( $value === $this->value ) ? 'selected' : '';
                    break;

                default:
                    $this->value = $value;
                    break;
            };
        }

        public function __toString(){
        	$this->_prepare();
        	
            $html = '<input';

            foreach( $this->_properties as $key => $value ){
                $html .= ' ' . $key . '="' . addslashes( $value ) . '"';
            }

            $html .= ' />';
            return $html;
        }

        public function addFilter( WebLab_Filter $filter, $errorMessage ){
            $this->_filters[] = (object)array(
                'filter' => $filter,
                'errorMessage' => $errorMessage
            );
            return $this;
        }

        public function isValid(){
            $errors = array();
            
            foreach( $this->_filters as $filter ){
                if( !$filter->filter->test( trim( $this->value ) ) )
                        $errors[] = $filter->errorMessage;
            }
			
            if( !count( $errors ) )
                return true;
            
            return $errors;
        }

    }