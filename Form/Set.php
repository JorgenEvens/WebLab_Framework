<?php
    /**
     * Set.php
     *
     * This file contains the implementation of the WebLab_Form_Set class.
     * @see WebLab_Form_Set
     */
	/**
	 * A group of multiple fields, used as an array ( name[] ).
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Form
	 *
	 */
	class WebLab_Form_Set extends WebLab_Form_Field {
		
		/**
		 * Contains generated fields
		 * 
		 * @var array
		 */
		public $fields = array();
		
		/**
		 * Contains reference field
		 * 
		 * @var WebLab_Form_Field
		 */
		protected $_type;
		
		/**
		 * Generates a new set of the same fields.
		 * 
		 * @param WebLab_Form_Field $type The reference field.
		 * @param int $size The initial size of the set.
		 */
		public function __construct( $type, $size=1 ) {
			parent::__construct( array( 'name' => $type->getAttribute( 'name' ) ) );
			
			$this->_type = $type;
			
			for( $i=0; $i<$size; $i++ ) {
				$this->add( $i );
			}
		}
		
		/**
		 * Returns the size of the set.
		 */
		public function size() {
			return count( $this->fields );
		}
		
		/**
		 * Returns the keys of the fields
		 */
		public function keys() {
			return array_keys( $this->fields );
		}
		
		/**
		 * Add an additional field with specified key.
		 * 
		 * @param string $key
		 */
		public function add( $key=null ) {
			$item = clone $this->_type;
			$key = empty( $key ) ? count( $this->fields ) : $key;
			$name = $this->getAttribute( 'name' ) . '[' . $key . ']';
			
			$item->setAttribute( 'name', $name );
			$item->setAttribute( 'id', $name );
			
			if( isset( $this->_form ) ) {
				$item->setForm( $this->_form );
			}
			
			$this->fields[ $key ] = $item;
			
			return $item;
		}
		
		/**
		 * Remove a field by it's key.
		 * 
		 * @param string $key
		 */
		public function remove( $key ) {
			$item = $this->fields[ $key ];
			if( empty( $item ) ) return false;
			
			$item->setForm( null );
			unset( $this->fields[ $key ] );
		}
		
		public function setForm( WebLab_Form $form ) {
			parent::setForm( $form );
			
			foreach( $this->fields as $field ) {
				$field->setForm( $form );
			}
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::isValid()
		 */
		public function isValid() {
			$errors = array();
			
			foreach( $this->fields as $field ) {
				$field_errors = $field->isValid();
				if( $field_errors !== true ) {
					$errors = array_merge( $errors, $field_errors );
				}
			}
			
			return empty( $errors ) ? true : $errors;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::addFilter()
		 */
		public function addFilter( WebLab_Filter $filter, $error_message=NULL ) {
			parent::addFilter( $filter, $error_message );
			
			$this->_type->addFilter( $filter, $error_message );
			
			foreach( $this->fields as $field ) {
				$field->addFilter( $filter, $error_message );
			}
		}
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Form_Field::__toString()
		 */
        public function __toString() {
        	return implode( '', $this->fields );
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::update()
         */
        public function update() {
        	$response = $this->_form->getResponse();
        	$response = $response[ $this->getAttribute( 'name' ) ];
        	$keys = array_keys( $response );
        	$fields = &$this->fields;
        	
        	foreach( $keys as $key ) {
        		if( !isset( $fields[ $key ] ) ) {
        			$this->add( $key );
        		}
        	}
        	
        	foreach( $this->fields as $field ) {
        		$field->update();
        	}
        }

        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::getValue()
         */
        public function getValue() {
        	if( $this->_form->isPostback() )
    			$this->update();
        	
        	return $this->fields;
        }

        public function getField( $key ) {
        	if( isset( $this->fields[$key] ) )
        		return $this->fields[$key];
        	return null;
        }
        
        /**
         * (non-PHPdoc)
         * @see WebLab_Form_Field::setValue()
         */
        public function setValue( $values ) {
        	$f = &$this->fields;
        	
        	foreach( $values as $key => $value ) {
        		if( isset( $f[ $key ] ) ) $f[$key]->setValue( $value );
        	}
        }
		
	}