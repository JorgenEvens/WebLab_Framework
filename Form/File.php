<?php
    /**
     * File.php
     *
     * This file contains the implementation of the WebLab_Form_File class.
     * @see WebLab_Form_Field
     */
	/**
	 * Field representation of an input field in HTML with type set to File.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Form
	 *
	 */
    class WebLab_Form_File extends WebLab_Form_Field
    {
        /**
         * Constructs a new input field.
         * 
         * @param string $name
         * @param string $label
         * @param array $attributes
         */
        public function __construct( $name, $label='', $attributes=array() ){
        	$attributes['name'] = $name;
        	$attributes['type'] = 'file';
        	
        	parent::__construct( $attributes, $label );
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::update()
         */
        public function update(){
            // Nothing to update, value is pulled straight from $_FILES
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::__toString()
         */
        public function __toString(){
            $html = '<input';
            
            foreach( $this->_attributes as $key => $value ) {
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
            $name = $this->attr( 'name' );
            if( !isset( $_FILES[$name] ) )
                return null;

            return $_FILES[ $name ];
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::setValue()
         * @param string $value Set the value of a field to this.
         */
        public function setValue( $value ) {
        	throw new WebLab_Exception_Form( 'Cannot set value of a file input' );
        }

        public function move( $target ) {
            $file = $this->getValue();

            if( empty( $file ) )
                return false;

            if( !file_exists( $file['tmp_name'] ) )
                return false;

            move_uploaded_file( $file['tmp_name'], $target );

            return true;
        }

    }