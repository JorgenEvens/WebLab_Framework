<?php
    require_once( 'MG_FieldAnalyser.php' );
    
    class MG_TableAnalyser
    {

        protected $_link;
        protected $_tableName;
        protected $_fields;

        public function __construct( MG_Database $link, $tableName )
        {
            $this->_link = $link;
            $this->_tableName = $tableName;

            $this->_validateInformation();
        }

        protected function _validateInformation()
        {
            if( !$this->_link->isConnected() )
            {
                throw new Exception( 'The database connection is invalid.' );
            }

            if( !in_array( $this->_tableName, $this->_link->getTables() ) )
            {
                throw new Exception( 'This table does not exist.' );
            }
        }

        public function getFields()
        {
            $q = 'SHOW FIELDS FROM ' . $this->_tableName;

            $fields = $link->query( $q );

            for( $i=0; $i<count( $fields ); $i++ )
            {
                $fields[$i] = new MG_FieldAnalyser( $fields[$i], $this->_link->TYPE_DEF );
            }

            return $fields;
        }

        public function getName()
        {
            return $this->_tableName;
        }

    }