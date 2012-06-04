<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Form
	 *
	 */
	class WebLab_Form_TextArea extends WebLab_Form_Field {

		public $value;
		
		/**
		 * Constructs new textarea
		 * 
		 * @param string $name
		 * @param string $value
		 * @param array $attributes
		 */
        public function __construct( $name, $label='', $value=null, $attributes=array() ){
        	$attributes['name'] = $name;
        	
        	if( !empty( $value ) )
        		$this->value = $value;
        		
        	parent::__construct( $attributes, $label );
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::update()
         */
        public function update(){
            if( empty( $this->_form ) )
                    return;
            
            $this->value = $this->_form->getValue($this);
        }

        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::__toString()
         */
        public function __toString(){
            $html = '<textarea';

            foreach( $this->_attributes as $key => $value ) {            		
                $html .= ' ' . htmlentities( $key ) . '="' . htmlentities( $value ) . '"';
            }

            $html .= '>' . $this->value . '</textarea>';
            return $html;
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::getValue()
         */
        public function getValue() {
        	return $this->value;
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::setValue()
         */
        public function setValue( $value ) {
        	$this->value = $value;
        	return $this;
        }

	}