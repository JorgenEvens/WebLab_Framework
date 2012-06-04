<?php
	/**
     *
     * Implementation of a query, generating mySQLi specific SQL.
     *
     * @see WebLab_Data_Query
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage WebLab_Data_MySQLi
     *
     */
	class WebLab_Data_MySQLi_Query extends WebLab_Data_Query {
		
		protected $_builder;
		
		public function builder() {
			if( empty( $this->_builder ) ) {
				$this->_builder = new WebLab_Data_MySQLi_QueryBuilder();
			}
			return $this->_builder->setQuery( $this );
		}
		
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

        public function update() {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->update();

            return $this->_adapter->query( $q );
        }

        public function insert( $update=false, $ignoreInUpdate=array() )
        {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->insert( $update, $ignoreInUpdate );

            return $this->_adapter->query( $q );
        }

        public function delete()
        {
            $this->_isConnected();

            $this->_last_query = $q = $this->builder()->delete();

            return $this->_adapter->query( $q );
        }
		
	}