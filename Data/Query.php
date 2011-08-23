<?php
    /**
     * Abstract representation of a query to the database.
     * 
     * @author jorgen
     * @package WebLab
	 * @subpackage WebLab_Data
     */
    abstract class WebLab_Data_Query
    {
    	/**
    	 * Holds the lastly generated query.
    	 * 
    	 * @var string
    	 */
    	public $_last_query;
    	
    	/**
    	 * The adapter to be used when performing a query.
    	 * 
    	 * @var WebLab_Data_Adapter
    	 */
    	protected $_adapter;
    	
    	/**
    	 * Tables used in this query.
    	 * 
    	 * @var WebLab_Data_Table[]
    	 */
        protected $_tables = array();
        
        /**
         * The criteriachain used in this query.
         * This is used to build the WHERE statement.
         * 
         * @var WebLab_Data_CriteriaChain
         */
        protected $_criteriaChain;
        
        /**
         * Holds limit information that is set for this query.
         * 
         * @var object
         */
        protected $_limit;
        
        /**
         * Determines if the total amount of rows should be counted if a limit is set.
         * 
         * @var boolean
         */
        protected $_count_limitless = false;
        
		/**
		 * Execute this query with a SELECT statement.
		 * 
		 * @return WebLab_Data_Result
		 */
        abstract public function select();
        
        /**
		 * Execute this query with a INSERT statement.
		 * 
		 * @return WebLab_Data_Result
		 */
        abstract public function insert();
        
        /**
		 * Execute this query with a DELETE statement.
		 * 
		 * @return WebLab_Data_Result
		 */
        abstract public function delete();
        
        /**
		 * Execute this query with a UPDATE statement.
		 * 
		 * @return WebLab_Data_Result
		 */
        abstract public function update();
        
        /**
         * Default constructor.
         * Sets the optional database adapter if supplied.
         * 
         * @param WebLab_Data_Adapter $adapter
         */
        public function __construct( WebLab_Data_Adapter $adapter=null )
        {
            if( !empty( $adapter ) ) {
                $this->setAdapter( $adapter );
            }
        }

        /**
         * Set the database adapter to use.
         * 
         * @param WebLab_Data_Adapter $adapter
         */
        public function setAdapter( WebLab_Data_Adapter $adapter ) {
            $this->_adapter = $adapter;
        }
        
        /**
         * Get the database adapter currently in use.
         * 
         * @return WebLab_Data_Adapter
         */
        public function getAdapter() {
        	return $this->_adapter;
        }
        
        /**
         * Gets the criteriachain for this query.
         * Criteria chain equals the WHERE statement in a query.
         * 
         * @see getCriteria()
         * @return WebLab_Data_CriteriaChain
         * @deprecated
         */
        public function getCriteriaChain() {
            return $this->getCriteria();
        }

        /**
         * Gets the criteriachain for this query.
         * Criteria chain equals the WHERE statement in a query.
         * 
         * @return WebLab_Data_CriteriaChain
         */
        public function getCriteria()
        {
            if( !isset( $this->_criteriaChain ) ) {
                $this->_criteriaChain = new WebLab_Data_CriteriaChain();
            }
            return $this->_criteriaChain;
        }

		/**
		 * Add multiple tables to the query.
		 * This equals the FROM statement.
		 * 
		 * @see addTable()
		 * @return WebLab_Data_Query
		 */
        public function addTables() {
            $tables = func_get_args();
            
            if( count( $tables ) == 1 && is_array( $tables[0] ) ) {
            	$tables = $tables[0];
            }

            array_map( array( $this, 'addTable' ), $tables );

            return $this;
        }

        /**
         * Add a table to the query.
         * 
         * @param WebLab_Data_Table|String $table
         * @return WebLab_Data_Table
         */
        public function addTable( $table ) {
            if( is_string( $table ) ) {
                $table = new WebLab_Data_Table( $table );
            }

            $this->_tables[ $table->getName() ] = $table;

            return $table;
        }

        /**
         * Remove a table from the query.
         * 
         * @param WebLab_Data_Table|String $table
         * @return WebLab_Data_Query
         */
        public function removeTable( $table ) {
            if( is_string( $table ) ) {
                unset( $this->_tables[ $table ] );
            } elseif( $table instanceof WebLab_Data_Table ) {
                unset( $this->_tables[ $table->getName() ] );
            }

            return $this;
        }

        /**
         * Get a table instance by it's name.
         * 
         * @param String $table
         * @return WebLab_Data_Table
         */
        public function getTable( $table ) {
            return $this->_tables[ $table ];
        }

        /**
         * Get all table instances in this query.
         * 
         * @param String $table
         * @return WebLab_Data_Table[]
         */
        public function getTables() {
            return $this->_tables;
        }

        
        /**
         * Set a limit on the query.
         * MySQL: LIMIT
         * MSSQL: TOP
         * 
         * @param int $count
         * @param int $start
         * @throws WebLab_Exception_Data
         */
        public function setLimit( $count, $start=0 ) {
            if( !( is_integer( $count ) && is_integer( $start ) ) ) {
                throw new WebLab_Exception_Data( 'Count and Start must be numeric.' );
            }

            $this->_limit = (object) array( 'count' => $count, 'start' => $start );
        }

        /**
         * Remove limit from query.
         */
        public function clearLimit() {
            unset( $this->_limit );
        }
        
        /**
         * Force query to calcuate the length of the result without a limit.
         * Special option, might not work in every implementation.
         * 
         * @param boolean $count
         */
        public function countLimitless( $count ) {
        	$this->_count_limitless = $count;
        }
        
        
        /**
         * Returns if this query is forced to load the entire length.
         * 
         * @see countLimitLess
         * @throws WebLab_Exception_Data
         * @return boolean
         */
        public function getCountLimitless() {
        	return $this->_count_limitless;
        }

        /**
         * Decompile the query into an easier to use format.
         * 
         * @return object
         */
        protected function _parseQuery()
        {
            $fields = array();
            $order = array();
            $group = array();

            foreach( $this->_tables as $table ) {
                foreach( $table->getFields() as $field ) {
                    $fields[] = $field;

                    if( $field->getOrder() != null ) {
                        $order[] = $field . ' ' . $field->getOrder();
                    }

                    if( $field->getGroup() ) {
                        $group[] = $field;
                    }
                }
            }

            return (object) array(
                'fields'  => $fields,
                'order'   => $order,
                'group'   => $group
            );
        }

        /**
         * Check if the query has access to an active database connection.
         * 
         * @throws WebLab_Exception_Data
         * @return boolean
         */
        protected function _isConnected() {
            if( !isset( $this->_adapter ) ) {
                throw new WebLab_Exception_Data( 'Adapter is not set!' );
            }

            if( !$this->_adapter->isConnected() ) {
                throw new WebLab_Exception_Data( 'Adapter is not connected!' );
            }
            
            return true;
        }

    }