<?php
    /**
     * Query.php
     *
     * This file contains the implementation of the WebLab_Data_Query class.
     * @see WebLab_Data_Query
     */
    /**
     * Abstract representation of a query to the database.
     * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data
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
         * The criteriachain used in this query.
         * This is used to build the HAVING statement.
         *
         * @var WebLab_Data_CriteriaChain
         */
        protected $_havingChain;
        
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
         * Which type of query is to be constructed.
         *
         * @var string
         */
        protected $_query_type;

        /**
         * Should we update when a key is already present.
         *
         * @var boolean
         */
        protected $_update_on_duplicate_key = false;

        /**
         * A list of fields that should not be updated
         * when a duplicate key is detected.
         *
         * @see $_on_duplicate_key_update
         * @var array
         */
        protected $_update_on_duplicate_key_ignored = array();

        /**
         * A builder to be used for this query instance.
         *
         * @var WebLab_Data_MySQLi_QueryBuilder A builder to be used for this query instance.
         */
		protected $_builder;

        /**
         * An array of PostExecuteCallbacks which should be notified
         * when a query is executed.
         *
         * @var array
         */
        protected $_post_execute_callback = array();

        /**
         * Generates a new Result object for this query.
         *
         * @return WebLab_Data_Result
         */
        abstract protected function createResult();
        
        /**
         * Returns the builder associated with this query.
         *
         * @return WebLab_Data_QueryBuilder
         */
        abstract public function builder();
        
        public function execute() {
            $q = $this->_query_type;
            $q = $this->builder()->$q();
            $result = $this->getAdapter()->execute($q);

            foreach( $this->_post_execute_callback as $cb )
                $cb->call($this);

            return $result;
        }
        
        public function count() {
            if( !$this->getCountLimitless() )
                throw new Exception('Counting is disabled for this query.');
                
            $q = $this->builder()->count();
            return $this->getAdapter()->execute($q);
        }
        
		/**
		 * Set query type to SELECT, instructs the querybuilder to generate
		 * a select query when this query is executed.
		 * 
		 * @return WebLab_Data_Result
		 */
        public function select() {
            $this->_query_type = 'select';
            return $this->createResult();
        }
        
        /**
		 * Set query type to INSERT, instructs the querybuilder to generate
		 * a select query when this query is executed.
		 * 
		 * @return WebLab_Data_Result
		 */
        public function insert( $update=false, $no_update=array() )
        {
            $this->_query_type = 'insert';
            $this->_update_on_duplicate_key = $update;
            $this->_update_on_duplicate_key_ignored = $no_update;
            return $this->createResult();
        }
        
        /**
		 * Set query type to DELETE, instructs the querybuilder to generate
		 * a select query when this query is executed.
		 * 
		 * @return WebLab_Data_Result
		 */
        public function delete()
        {
            $this->_query_type = 'delete';
            return $this->createResult();
        }
        
        /**
		 * Set query type to DELETE, instructs the querybuilder to generate
		 * a select query when this query is executed.
		 * 
		 * @return WebLab_Data_Result
		 */
        public function update()
        {
            $this->_query_type = 'update';
            return $this->createResult();
        }
        
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
         * Destructor
         * Performs execute on non-select queries when
         * the object is cleaned.
         */
        public function __destruct() {
            if( !empty($this->_query_type) && $this->_query_type != 'select' )
                $this->execute();
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

        public function addExecuteCallback( WebLab_Data_Callback $cb ) {
            $this->_post_execute_callback[] = $cb;
            return $this;
        }

        /**
         * Get if an insert query should try to update a record on
         * duplicate key.
         *
         * @return boolean
         */
        public function getUpdateOnDuplicateKey() {
            return $this->_update_on_duplicate_key;
        }

        /**
         * Fields to ignore when an insert query tries to update
         * a record that has a the same key.
         *
         * @see getUpdateOnDuplicateKey
         * @return array
         */
        public function getUpdateOnDuplicateKeyIgnoredFields() {
            return $this->_update_on_duplicate_key_ignored;
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
         * Gets the criteriachain for this query.
         * Criteria chain equals the HAVING statement in a query.
         *
         * @return WebLab_Data_CriteriaChain
         */
        public function getHaving()
        {
        	if( !isset( $this->_havingChain ) ) {
        		$this->_havingChain = new WebLab_Data_CriteriaChain();
        	}
        	return $this->_havingChain;
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
            return isset( $this->_tables[$table] ) ? $this->_tables[ $table ] : null;
        }

        /**
         * Get all table instances in this query.
         * 
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
            if( !( is_numeric( $count ) && is_numeric( $start ) ) ) {
                throw new WebLab_Exception_Data( 'Count and Start must be numeric.' );
            }

            $this->_limit = (object) array( 'count' => $count, 'start' => $start );
        }
        

        /**
         * Get the limit that has been set on this query.
         *
         * @return mixed An object with keys count and start.
         */
        public function getLimit() {
        	return $this->_limit;
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