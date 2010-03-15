<?php
    class WebLab_Data_Criteria
    {
        protected $_field;
        protected $_action;
        protected $_value;

        public function __construct( WebLab_Data_Field $field )
        {
            $this->setField( $field );
        }

        public function setField( WebLab_Data_Field $field )
        {
            $this->_field = $field;
        }

        // Greater Than
        public function greaterThan( $value )
        {
            return $this->gt( $value );
        }

        public function gt( $value )
        {
            $this->_action = '>';
            $this->_value = $value;

            return $this;
        }

        // Less Than
        public function lessThan( $value )
        {
            return $this->lt( $value );
        }

        public function lt( $value )
        {
            $this->_action = '<';
            $this->_value = $value;

            return $this;
        }

        // Equals
        public function equals( $value )
        {
            return $this->eq( $value );
        }

        public function eq( $value )
        {
            $this->_action = '=';
            $this->_value = $value;

            return $this;
        }

        // Different
        public function different( $value )
        {
            return $this->diff( $value );
        }

        public function diff( $value )
        {
            $this->_action = '<>';
            $this->_value = $value;

            return $this;
        }

        // Like
        public function like( $value )
        {
            $this->_action = 'LIKE';
            $this->_value = $value;

            return $this;
        }

        // Between
        public function between( $low, $high )
        {
            if( !is_numeric( $low ) || !is_numeric( $high ) )
            {
                throw new Exception( 'between( $low, $high ) only accepts numeric values.' );
            }

            $this->_action = 'BETWEEN';
            $this->_value = array( $low, $high );

            return $this;
        }

        // Not NULL
        public function notNull()
        {
            $this->_action = 'IS NOT NULL';
            $this->_value = '';
        }

        // NULL
        public function isNull()
        {
            $this->_action = 'IS NULL';
            $this->_value = '';
        }

        public function get( $adapterSpecs )
        {
            $action = $this->_action;
            $value = $this->_value;

            if( !isset( $action ) )
            {
                return '';
            }

            // Means that there are 2 values AKA -> BETWEEN x AND y
            if( is_array( $value ) )
            {
                $action = $action . ' ' . $value[0] . ' AND';
                $value = $value[1];
            }

            if( is_numeric( $value ) )
            {
                $action .= ' ' . $value;
            }elseif( is_string( $value ) )
            {
                
                if( $action == 'LIKE' )
                {
                    $value = strtr( $value, '*', $adapterSpecs->wildcard );
                }

                if( !empty( $value ) )
                {
                    $action .= ' \'' . call_user_func( $adapterSpecs->escape_string, $value ) . '\'';
                }
                
            }elseif( $value instanceof WebLab_Data_Field )
            {
                $action .= $value->getName();
            }

            $action = $this->_field->getName() . ' ' . $action;

            return $action;
        }
    }