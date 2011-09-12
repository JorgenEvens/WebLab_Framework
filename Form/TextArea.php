<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Form
	 *
	 */
	class WebLab_Form_TextArea extends WebLab_Form_Field {
		
		protected $_filters = array();
        protected $_isPostback = false;

        public function __construct( $name, $value=null, $properties=array() ){
        	$properties['name'] = $name;
        	if( !empty( $value ) )
        		$properties['value'] = $value;
        		
        	parent::__construct( $properties );
        }
        
        public function update(){
            if( empty( $this->_form ) )
                    return;
            
            $response = ( $this->_form->getMethod() == WebLab_Form::POST ) ? $_POST : $_GET;
			$this->_isPostback = isset( $response[ $this->name ] );
            $this->value = isset( $response[ $this->name ] ) ? $response[$this->name] : '';
        }

        public function __toString(){
        	$this->_prepare();
        	
            $html = '<textarea';

            foreach( $this->_properties as $key => $value ){
            	if( $key == 'value' )
            		continue;
            		
                $html .= ' ' . $key . '="' . addslashes( $value ) . '"';
            }

            $html .= '>' . $this->value . '</textarea>';
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
                if( !$filter->filter->test( $this->value ) )
                        $errors[] = $filter->errorMessage;
            }

            if( !count( $errors ) )
                return true;
            
            return $errors;
        }
	}