<?php
    abstract class WebLab_Data_Adapter
    {
        public $error;
        protected $_resource;
        protected $_wildcard = '*';
        protected $_prefix = '';

        abstract public function __construct( $config );
        abstract protected function _query( $query );
        abstract public function isConnected();
        abstract public function insert_id();
        abstract public function escape_string( $str );
        abstract public function getAdapterSpecs();

        public final function query( $query )
        {
            $query = $this->_prefixTables( $query );
            
            return $this->_query( $query );
        }

        protected function _prefixTables( $query )
        {
            if( empty( $this->_prefix ) )
            {
                return $query;
            }

            if( !is_string( $query ) )
            {
                throw new Exception( 'Expecting that the query supplied is a string.' );
            }

            // Take everything after the FROM keyword.
            //$query = strtolower( $query );
            preg_match( "/(from |into )/i", $query, $start, PREG_OFFSET_CAPTURE );
            $start = $start[0][1] + 5;



            if( $start < 6 )
            {
                throw new Exception( 'The FROM / INTO Keyword was not found, so this is not a valid query.' );
            }

            // explode it to get the different tables;
            $tables = explode( ',', substr( $query, $start ) );

            // Replace every table with it's prefixed form.
            foreach( $tables as $table )
            {
                $table = trim( $table ); // Remove redundant spaces.

                // If the tablename contains a space then this is the end of the table listing.
                $emptyChar = strpos( $table, ' ' );
                if( $emptyChar > -1 )
                {
                    $table = substr( $table, 0, $emptyChar );
                    $query = preg_replace( '/\b' . $table . '\b/', $this->getPrefix() . $table, $query );
                    break;
                }

                // Otherwise just replace the table with it's prefixed form and continue;
                $query = preg_replace( '/\b' . $table . '\b/', $this->getPrefix() . $table, $query );
            }

            return $query;
        }

        public function getPrefix()
        {
            return $this->_prefix;
        }

        public function setPrefix( $prefix )
        {
            $this->_prefix = $prefix;
            return $this;
        }

        public function getAdapterSpecification()
        {
            return $this->getAdapterSpecs();
        }
    }
