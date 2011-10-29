<?php
	/**
	 * Defines a criteria used in the WHERE statement.
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Data
	 *
	 */
    class WebLab_Data_Criteria {
    	/**
    	 * Holds the field to which the criteria applies.
    	 * 
    	 * @var WebLab_Data_Field
    	 */
        protected $_field;
        
        /**
         * Defines the operator used.
         * When not an operator is used but a keyword, this will also be stored in this property.
         * 
         * @var string
         */
        protected $_action;
        
        /**
         * The value to which the field should match using $_action.
         * 
         * @see $_action
         * @var mixed
         */
        protected $_value;

        /**
         * Generate the criteria and set the field to which it applies.
         * 
         * @param WebLab_Data_Field $field
         */
        public function __construct( WebLab_Data_Field $field ) {
            $this->setField( $field );
        }

        /**
         * Sets the field to which this criteria applies.
         * 
         * @param WebLab_Data_Field $field
         */
        public function setField( WebLab_Data_Field $field ) {
            $this->_field = $field;
        }

        /**
         * Sets the criteria to use a greater than operator, using $value as value.
         * 
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function greaterThan( $value ) {
            return $this->gt( $value );
        }

        /**
         * Shorthand for greaterThan
         * 
         * @see greaterThan( $value )
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function gt( $value ) {
            $this->_action = '>';
            $this->_value = $value;

            return $this;
        }

        /**
         * Sets the criteria to use a less than operator, using $value as value.
         * 
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function lessThan( $value ) {
            return $this->lt( $value );
        }

        /**
         * Shorthand for lessThan
         * 
         * @see lessThan( $value )
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function lt( $value ) {
            $this->_action = '<';
            $this->_value = $value;

            return $this;
        }

        /**
         * Sets the criteria to use the equals operator, using $value as value.
         * 
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function equals( $value ) {
            return $this->eq( $value );
        }

        /**
         * Shorthand for equals
         * 
         * @see equals( $value )
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function eq( $value ) {
            $this->_action = '=';
            $this->_value = $value;

            return $this;
        }

        /**
         * Sets the criteria to use the not equals operator, using $value as value.
         * 
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function different( $value ) {
            return $this->diff( $value );
        }

        /**
         * Shorthand for different
         * 
         * @see different( $value )
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function diff( $value ) {
            $this->_action = '<>';
            $this->_value = $value;

            return $this;
        }

        /**
         * Sets the criteria to use the LIKE keyword, using $value as value.
         * 
         * @param mixed $value
         * @return WebLab_Data_Criteria
         */
        public function like( $value ) {
            $this->_action = 'LIKE';
            $this->_value = $value;

            return $this;
        }

        /**
         * Sets the criteria to use the BETWEEN keyword, using $value as value.
         * 
         * @param int $low
         * @param int $high
         * @return WebLab_Data_Criteria
         */
        public function between( $low, $high ) {
            if( !is_numeric( $low ) || !is_numeric( $high ) )
            {
                throw new Exception( 'between( $low, $high ) only accepts numeric values.' );
            }

            $this->_action = 'BETWEEN';
            $this->_value = array( $low, $high );

            return $this;
        }

        /**
         * Sets the criteria to use the NOT NULL keyword.
         * 
         * @return WebLab_Data_Criteria
         */
        public function notNull() {
            $this->_action = 'IS NOT NULL';
            $this->_value = '';
            
            return $this;
        }

        /**
         * Sets the criteria to use the IS NULL keyword.
         * 
         * @return WebLab_Data_Criteria
         */
        public function isNull() {
            $this->_action = 'IS NULL';
            $this->_value = '';
            
            return $this;
        }
        
        /**
         * Sets the criteria to use the IN keyword.
         * 
         * @param array $collection
         * @return WebLab_Data_Criteria
         */
        public function in( $collection ) {
			$this->_action = 'IN';
			$this->_value = $collection;
			
			return $this;
		}
		
		/**
         * Sets the criteria to use the NOT IN keyword.
         * 
         * @param array $collection
         * @return WebLab_Data_Criteria
         */
        public function notIn( $collection ) {
			$this->_action = 'NOT IN';
			$this->_value = $collection;
			
			return $this;
		}

        /**
         * Returns a text representation of the criteria, based on the adapter specifications.
         * 
         * @param object $adapterSpecs
         * @return string
         */
        public function get( $adapterSpecs ) {
            $action = $this->_action;
            $value = $this->_value;

            if( !isset( $action ) ) {
                return '';
            }

            // Means that there are 2 values AKA -> BETWEEN x AND y
            if( is_array( $value ) && $action == 'BETWEEN' ) {
                $action = $action . ' ' . $value[0] . ' AND';
                $value = $value[1];
            }

            if( is_numeric( $value ) ) {
                $action .= ' ' . $value;
            } elseif( is_string( $value ) ) {
                
                if( $action == 'LIKE' ) {
                    $value = strtr( $value, '*', $adapterSpecs->wildcard );
                }

                if( !empty( $value ) ) {
                    $action .= ' \'' . call_user_func( $adapterSpecs->escape_string, $value ) . '\'';
                }
            } elseif( is_array( $value ) ){
				$value = array_map($adapterSpecs->escape_string, $value);
				$action .=' (' . implode( ', ', $value ) . ')';
            } elseif( $value instanceof WebLab_Data_Field ) {
                $action .= $value->getFullName();
            } elseif( empty( $value ) ) {
            	$action = 'IS NULL';
            }

            $action = $this->_field->getFullName() . ' ' . $action;

            return $action;
        }
    }
