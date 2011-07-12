<?php
	class WebLab_Form_Select extends WebLab_Form_Field {
		
		protected $_options;
		protected $_selected;
		
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
        
        public function isValid(){
        	return true;
        }
		
	}