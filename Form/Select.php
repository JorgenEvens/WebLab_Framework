<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Form
	 *
	 */
	class WebLab_Form_Select extends WebLab_Form_Field {
		
		protected $_options;
		protected $_selected;
		protected $_filters = array();
		
		public function __construct( $name, $options=array(), $properties=array() ){
			$this->_options = $options;
			$properties['name'] = $name;
			
			parent::__construct( $properties );
		}
		
		public function addOption( $text, $value ){
			$this->_options[] = (object)array( 'text' => $text, 'value' => $value );
		}
		
		public function removeOption( $text, $value ){
			foreach( $this->_options as $key => $option )
				if( $option->text == $text && $option->value == $value )
					unset( $this->_options[ $key ] );
		}
		
		public function __toString(){
			$this->_prepare();
        	
			$html = '<select';

            foreach( $this->_properties as $key => $value ){
                $html .= ' ' . $key . '="' . addslashes( $value ) . '"';
            }

            $html .= '>';
            
            foreach( $this->_options as $option ){
            	$html .= '<option value="' . $option->value . '">' . $option->text . '</option>';
            }
            
            $html .= '</select>';
            return $html;
		}
		
		public function getSelectedValue(){
			return $this->_selected;
		}
		
        public function update(){
        	$this->_selected = $this->_form->getValue($this);
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
            	if( !$filter->filter->test( trim( $this->_selected ) ) )
            		$errors[] = $filter->errorMessage;
            }
			
            if( !count( $errors ) )
                return true;
            
            return $errors;
        }
		
	}