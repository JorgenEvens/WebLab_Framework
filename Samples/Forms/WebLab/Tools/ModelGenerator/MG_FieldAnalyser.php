<?php

    require_once( 'MG_Field_Const.php' );
    class MG_FieldAnalyser
    {

        protected $_name;
        protected $_type;
        protected $_length;
        protected $_null;
        protected $_key;
        protected $_default;
        protected $_autoIncrement;

        public function __construct( $field, $types )
        {
            $this->_analyse( $field, $types );
        }

        public function _analyse()
        {
            // Fieldname
            $this->_name = $field->Field;

            // Type
            // bigint(20)
            $type = explode( '(', $field->Type );

            if( count( $type ) > 1 )
            {
                $this->_type = $types[ $type[0] ];
                $this->_length = substr( 0, strlen( $type[1] )-1, $type[1] );
            }else
            {
                $this->_type = $field->Type;
            }

            // Nullable
            $this->_null = ( $field->Null == 'YES' );

            // Key
            $this->_key = ( $field->Key == 'PRI' );

            // Default
            $this->_default = $field->Default;

            // Extra
            $this->_autoIncrement = ( $field->Extra == 'auto_increment' );

            return $this;
        }

        public function getName()
        {
            return $this->_name;
        }

        public function getType()
        {
            return $this->_type;
        }

        public function getLength()
        {
            return $this->_length;
        }

        public function getNull()
        {
            return $this->_null;
        }

        public function getKey()
        {
            return $this->_key;
        }

        public function getDefault()
        {
            return $this->_default;
        }

        public function getAutoIncrement()
        {
            return $this->_autoIncrement;
        }

    }