<?php
    class WebLab_Data_Field
    {
        protected $_table;

        protected $_name;
        protected $_alias;

        protected $_value;
        protected $_altered = false;

        protected $_default;
        
        protected $_criteria = array();
        protected $_function;
        
        protected $_order;
        protected $_group = false;


        public function __construct( $name, $value=null, $default='NULL' )
        {
            $this->_name = $name;
            if( isset( $criteria ) )
            {
                $this->addCriteria( $criteria );
            }
            $this->_value = $value;
            $this->_default = $default;
        }

        public function createCriteria()
        {
            return new WebLab_Data_Criteria( $this );
        }

        public function setTable( WebLab_Data_Table $table )
        {
            $this->_table = $table;

            return $this;
        }

        public function setFunction( $function )
        {
            $allowed = array(
                'YEAR',
                'MONTH',
                'COUNT'
            );

            $function = strtoupper( $function );

            if( in_array( $function, $allowed ) )
            {
                $this->_function = $function;
            }

            return $this;
        }

        public function getFunction()
        {
            return $this->_function;
        }

        public function getFullName()
        {
            if( isset( $this->_function ) )
            {
                return strtoupper( $this->_function ) . '( ' . $this->_table->getName() . '.' . $this->_name . ' )';
            }
            return $this->_table->getName() . '.' . $this->_name;
        }

        public function getName()
        {
            return $this->_name; // table.field
        }

        public function getAlias()
        {
            return $this->_alias;
        }

        public function setAlias( $alias )
        {
            if( is_string( $alias ) )
            {
                $this->_alias = $alias;
            }

            return $this;
        }

        public function setValue( $value )
        {
            $this->_value = $value;
            $this->_altered = true;

            return $this;
        }

        public function getValue()
        {
            return isset( $this->_value ) ? $this->_value : $this->_default;
        }

        public function altered()
        {
            return $this->_altered;
        }

        public function setDefault( $default )
        {
            $this->_default = $default;

            return $this;
        }

        public function getDefault()
        {
            return $this->_default;
        }

        public function setOrder( $direction='ASC' )
        {
            $this->_order = ( $direction == 'DESC' ) ? 'DESC' : 'ASC';
            $this->_order = $direction == null ? null : $this->_order;

            return $this;
        }

        public function getOrder()
        {
            return $this->_order;
        }

        public function setGroup( $group=true )
        {
            $this->_group = $group;

            return $this;
        }

        public function getGroup()
        {
            return $this->_group;
        }

        public function __toString()
        {
            return $this->getFullName();
        }

    }