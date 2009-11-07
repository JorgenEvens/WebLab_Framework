<?php
    class WebLab_Data_Table
    {

        protected $_fields;
        protected $_select;
        protected $_name;
        protected $_database;
        protected $_id;


        public function __construct( $name, $fields, $db )
        {
            if( !is_string( $name ) )
            {
                throw new WebLab_Exception_data( 'The tablename should be a string.' );
            }

            $this->_name = $name;

            if( !is_array( $fields ) )
            {
                throw new WebLab_Exception_Data( 'Fields are not in array.' );
            }

            $this->_fields = $fields;

            if( !( $a instanceof WebLab_Data_DataSource ) )
            {
                throw new WebLab_Exception_Data( 'Database should be of type WebLab_DataSource or any extend of this.' );
            }
            $this->_database = $db;
        }

        public function addField( $fieldName )
        {
            $this->_fields[] = $fieldName;

            $this->_updateSelect();
        }

        public function removeField( $fieldName )
        {
            for( $i=0; $i<count( $this->_fields );$i++ )
            {
                if( $this->_fields[ i ] == $fieldName )
                {
                    unset( $this->_fields[i] );
                }
            }

            $this->_updateSelect();
        }

        protected function _updateSelect()
        {
            $this->_select->select( $this->_fields );
        }

        public function &select()
        {
            return $this->_select;
        }

        public function fetch()
        {
            return $this->_database->query( $this->_select );
        }

        public function setTableName( $name )
        {
            $this->_tableName = $name;
            return $this;
        }

        public function getTableName()
        {
            return $this->_tableName;
        }

    }