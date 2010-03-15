<?php
   class WebLab_Data_Table
    {

        protected $_name;
        protected $_alias;
        protected $_fields = array();

        public function __construct( $name, $fields=array() )
        {
            $this->_name = $name;
            $this->addFields( $fields );
            
        }

        public function __get( $name )
        {
            return $this->getField( $name );
        }

        public function addFields(  $fields=array() )
        {
            foreach( $fields as $field )
            {
                $this->addField( $field );
            }

            return $this;
        }

        public function addField( $field=null )
        {
            if( is_string( $field ) )
            {
                $field = new WebLab_Data_Field( $field );
            }

            $field->setTable( $this );
            $this->_fields[ $field->getName() ] = $field;

            return $field;
        }

        public function removeField( $name )
        {
            unset( $this->_fields[ $name ] );

            return $this;
        }

        public function getField( $name )
        {
            if( !isset( $this->_fields[ $name ] ) )
            {
                throw new Exception( 'Field not in table.' );
            }
            return $this->_fields[ $name ];
        }

        public function getFields()
        {
            return $this->_fields;
        }

        public function getName()
        {
            return $this->_name;
        }

        public function setName( $name )
        {
            $this->_name = $name;
        }

        public function getAlias()
        {
            return $this->_alias;
        }

        public function setAlias( $alias )
        {
            if( !is_string( $alias ) )
            {
                return $this;
            }
            $this->_alias = $alias;

            return $this;
        }

        public function __toString()
        {
            return $this->getName();
        }

    }