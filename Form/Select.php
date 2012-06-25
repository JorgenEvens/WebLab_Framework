<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Form
	 *
	 */
	class WebLab_Form_Select extends WebLab_Form_Field {
		
		/**
		 * Stores the options in the select box.
		 * 
		 * @var array
		 */
		protected $_options;
		
		/**
		 * Holds the value of the selection
		 * 
		 * @var string
		 */
		protected $_selected;
		
		/**
		 * Constructs a selection box.
		 * 
		 * @param string $name
		 * @param array $options
		 * @param array $attributes
		 */
		public function __construct( $name, $label='', $options=array(), $attributes=array() ){
			$this->_options = $options;
			$attributes['name'] = $name;
			
			parent::__construct( $attributes, $label );
		}
		
		/**
		 * Add an option to the selection box.
		 * 
		 * @param string $text
		 * @param string $value
		 * @return WebLab_Form_Select
		 */
		public function addOption( $text, $value ){
			$this->_options[] = (object)array( 'text' => $text, 'value' => $value );
			return $this;
		}
		
		/**
		 * Remote an option from the select box.
		 * 
		 * @param string $text
		 * @param string $value
		 * @return WebLab_Form_Select
		 */
		public function removeOption( $text, $value ){
			foreach( $this->_options as $key => $option )
				if( $option->text == $text && $option->value == $value )
					unset( $this->_options[ $key ] );
			
			return $this;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::__toString()
		 */
		public function __toString() {
			$html = '<select';
			
            foreach( $this->_attributes as $key => $value ){
                $html .= ' ' . htmlentities( $key ) . '="' . htmlentities( $value ) . '"';
            }

            $html .= '>';
            $value = $this->getValue();
            
            foreach( $this->_options as $option ){
            	$html .= '<option value="' . htmlentities( $option->value ) . '"';
            	if( $value == $option->value ) $html .= ' selected="selected"';
            	$html .= '>' . htmlentities( $option->text ) . '</option>';
            }
            
            $html .= '</select>';
            return $html;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::getValue()
		 */
		public function getValue(){
			return $this->_selected;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::setValue()
		 */
		public function setValue( $value ) {
			$this->_selected = $value;
			return $this;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::update()
		 */
        public function update(){
        	$this->_selected = $this->_form->getValue($this);
        }
		
	}