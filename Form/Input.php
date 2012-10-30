<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Form
	 *
	 */
    class WebLab_Form_Input extends WebLab_Form_Field
    {
        /**
         * Constructs a new input field.
         * 
         * @param string $name
         * @param string $type
         * @param string $label
         * @param string $value
         * @param array $attributes
         */
        public function __construct( $name, $type, $label='', $value=null, $attributes=array() ){
        	$attributes['name'] = $name;
        	$attributes['type'] = $type;
        	if( !empty( $value ) )
        		$attributes['value'] = $value;
        	
        	parent::__construct( $attributes, $label );
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::update()
         */
        public function update(){
            if( empty( $this->_form ) )
				return;

            if( !$this->_form->isPostback() )
                return;
            
            $attr = &$this->_attributes;
            $type = $attr[ 'type' ];
            $value = $this->_form->getValue($this);
            
            
            if( $type == 'checkbox' ) {
            	if( empty( $value ) ) {
            		$attr['checked'] = false;
            	} else {
            		$attr['checked'] = 'checked';
            		$attr['value'] = $value;
            	}
            } elseif( $type == 'radio' ) {
            	if( empty( $value ) ) {
            		$attr['selected'] = false;
            	} else {
            		$attr['selected'] = 'selected';
            		$attr['value'] = $value;
            	}
            } else {
            	$attr['value'] = $value;
            }
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::__toString()
         */
        public function __toString(){
            $html = '<input';
			
            $type = $this->_attributes[ 'type' ];
            
            foreach( $this->_attributes as $key => $value ) {
            	if( $type == 'password' && $key == 'value' )
            		continue;

            	if( is_array( $value ) )
            		$value = implode( ' ', $value );

                $html .= ' ' . $key . '="' . htmlentities( $value ) . '"';
            }
            
            $html .= ' />';
            return $html;
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::getValue()
         */
        public function getValue() {
        	$this->update();
        	
        	$attr = &$this->_attributes;
        	$type = $attr[ 'type' ];
        	//$value = $this->_form->getValue($this);
        	
        	if( $type == 'checkbox' ) {
        		if( empty( $attr[ 'checked' ] ) ) {
        			return '';
        		}
        		return $attr['value'];
        	} elseif( $type == 'radio' ) {
        		if( empty( $attr[ 'selected' ] ) ) {
        			return '';
        		}
        		return $attr[ 'value' ];
        	} else {
                if( empty( $attr[ 'value' ] ) ) {
                    return '';
                }
        		return $attr[ 'value' ];
        	}
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::setValue()
         */
        public function setValue( $value ) {
        	$this->_attributes['value'] = $value;
        }

    }