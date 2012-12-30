<?php
    /**
     * Adapter.php
     *
     * This file contains the implementation of the WebLab_Data_Adapter class.
     * @see WebLab_Data_Adapter
     */
    /**
     *
     * Implementation of an adapter using the mySQL functions.
     *
     * @see WebLab_Data_Adapter
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data
     *
     */
    abstract class WebLab_Data_Adapter
    {
    	/**
    	 * Contains any error possibly thrown by the database.
    	 * 
    	 * @var string
    	 */
        public $error;
        
        /**
         * A resource reference to the underlying connection.
         * 
         * @var resource
         */
        protected $_resource;
        
        /**
         * Default wildcard used in SQL statements.
         * 
         * @var string
         */
        protected $_wildcard = '*';
        
        /**
         * Prefix used on tables within this database.
         * 
         * @var string
         */
        protected $_prefix = '';
        
        /**
         * Holds how many times start transaction has been called.
         * This allows for transaction nesting.
         * 
         * @var int
         * @see startTransaction()
         */
        protected $_transaction_depth = 0;

        /**
         * Holds the original strings while prefixing a query
         * 
         * @var array
         */
        protected $_original_strings;
        
        /**
         * Default constructor for creating a Adapter.
         * 
         * @param mixed $config
         */
        abstract public function __construct( $config );
        
        /**
         * Returns the state of the adapter.
         * 
         * @return boolean
         */
        abstract public function isConnected();
        
        /**
         * Returns the last inserted id.
         * 
         * @return mixed
         */
        abstract public function insert_id();
        
        /**
         * Escape text using the adapters native escaping functionality.
         * 
         * @param string $str
         */
        abstract public function escape_string( $str );
        
        /**
         * Get database specific information.
         * This returns a reference to escape_string() and the wildcard.
         * 
         * @see escape_string()
         * @see $_wildcard

         * @return object
         */
        abstract public function getAdapterSpecs();
        
        /**
         * Generate a new query of the correct type.
         * 
         * @return WebLab_Data_Query
         */
        abstract public function newQuery();
        
        /**
         * Perform querying logic.
         * 
         * @param string $query
         */
        abstract protected function _query( $query );
        
        /**
         * Start a transaction using database specific commands.
         */
        abstract protected function _start_transaction();
        
        /**
         * Quit a transaction using database specific commands.
         * 
         * @param boolean $commit
         */
        abstract protected function _quit_transaction( $commit );

        /**
         * Performs a query.
         * Prefixes the query tables if a prefix is specified.
         * 
         * @param string $query
         */
        public final function query( $query ) {
            return $this->_query( $query );
        }

        /**
         * Return the prefix for this database.
         * 
         * @return string
         */
        public function getPrefix() {
            return $this->_prefix;
        }

        /**
         * Set the prefix used while querying this database.
         * 
         * @param string $prefix
         * @return WebLab_Data_Adapter
         */
        public function setPrefix( $prefix ) {
            $this->_prefix = $prefix;
            return $this;
        }

        /**
         * Get database specific information.
         * This returns a reference to escape_string() and the wildcard.
         * 
         * @see $_wildcard
         * @see escape_string()
         * @see getAdapterSpecs()
         * @return object
         */
        public function getAdapterSpecification() {
            return $this->getAdapterSpecs();
        }
        
        /**
         * Start a transaction and increase the transaction depth
         * 
         * @see $_transaction_depth
         * @see _start_transaction()
         */
        public function startTransaction() {
        	$this->_transaction_depth++;
        	
        	if( $this->_transaction_depth == 1 ) {
        		$this->_start_transaction();
        	}
        }
        
        /**
         * Decrease transaction depth.
         * When transaction depth reaches 0 transaction will be commited.
         * 
         * When performing a rollback, transaction depth will be reset to 0 and the entire tree will be rolled back.
         * 
         * @param boolean $commit
         */
        public function quitTransaction( $commit ) {
        	$this->_transaction_depth--;
        	
        	if( $this->_transaction_depth < 1 || !$commit ) {
        		$this->_quit_transaction( $commit );
        		$this->_transaction_depth = 0;
        	}
        }

    }
