<?php
    /**
    * Query.php
    *
    * This file contains the implementation of the WebLab_Data_MySQL_Query class.
    * @see WebLab_Data_MySQL_Query
    */
	/**
     *
     * Implementation of a query, specific to the MySQL driver.
     *
     * @see WebLab_Data_Query
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data_MySQL
     *
     */
	class WebLab_Data_MySQL_Query extends WebLab_Data_Query {
		
        /**
         * A builder to be used for this query instance.
         *
         * @var WebLab_Data_MySQLi_QueryBuilder A builder to be used for this query instance.
         */
		protected $_builder;
		
        /**
         * Retrieve a builder instance for this query, this method ensures lazy loading.
         *
         * @return WebLab_Data_MySQLi_QueryBuilder A builder to be used for this query instance.
         */
		public function builder() {
			if( empty( $this->_builder ) ) {
				$this->_builder = new WebLab_Data_MySQL_QueryBuilder();
			}
			return $this->_builder->setQuery( $this );
		}
		
        /**
         * Performs a SELECT query on the underlying database.
         * The query is created using a QueryBuilder that converts this 
         * query object into a statement.
         *
         * @return WebLab_Data_MySQLi_Result
         */
		public function select() {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->select();

            $result = $this->_adapter->query( $q );
            
            if( $this->getCountLimitless() ) {
            	$row_count = $this->_adapter->query( 'SELECT FOUND_ROWS() AS count' );
            	$result->setTotalRows( $row_count->current()->count );
            }
            
            return $result;
        }

        /**
         * Performs an UPDATE query on the underlying database.
         * The query is created using a QueryBuilder that converts this 
         * query object into a statement.
         *
         * @return WebLab_Data_MySQLi_Result
         */
        public function update() {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->update();

            return $this->_adapter->query( $q );
        }

        /**
         * Performs an INSERT query on the underlying database.
         * The query is created using a QueryBuilder that converts this 
         * query object into a statement.
         *
         * @param boolean $update Should the record be updated if it already exists. ( uses ON DUPLICATE KEY syntax )
         * @param mixed $ignoreInUpdate Fields not to update if $update is set to true.
         * @return WebLab_Data_MySQLi_Result
         */
        public function insert( $update=false, $ignoreInUpdate=array() )
        {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->insert( $update, $ignoreInUpdate );

            return $this->_adapter->query( $q );
        }

        /**
         * Performs a DELETE query on the underlying database.
         * The query is created using a QueryBuilder that converts this 
         * query object into a statement.
         *
         * @return WebLab_Data_MySQLi_Result
         */
        public function delete()
        {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->delete();

            return $this->_adapter->query( $q );
        }
		
	}