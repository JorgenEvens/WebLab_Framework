<?php
	/**
	 * Base logic for a database adapter.
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Data
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
            $query = $this->_prefixTables( $query );
            
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
    
        /**
         * Prefixes the tables in the provided query.
         * 
         * @param string $query
         * @throws Exception
         * @return string
         */
		protected function _prefixTables( $query ) {
            if( empty( $this->_prefix ) ) {
                return $query;
            }

            if( !is_string( $query ) ) {
                throw new Exception( 'Expecting that the query supplied is a string.' );
            }
            
            $string_pattern = '#[^\\\\]("|\').+[^\\\\]("|\')#iU';
            $string_placeholder = '#\$(\d+)#';
            
            $this->_original_strings = array();
            
            $query = preg_replace_callback( $string_pattern, array( $this, '_stripStrings' ), $query );
            
            $pattern = '#(\s|^)(from|into|update)\s#i';
            //$end_tabledefinition = '#([^\b\s]+)(\s+([^A][^S]|.+)\s+|\s*$)#iU';
            $end_tabledefinition = '#(.+)\s+(([^A][^S])|.+)\s+#iU';
            
            // Take everything after the FROM / INTO / UPDATE keyword.
            preg_match( $pattern, $query, $matches, PREG_OFFSET_CAPTURE );
            
            while( count($matches) > 0) {
	            $start = $matches[0][1] + strlen( $matches[0][0] );
	            
	            if( $start < 7 ) {
	                throw new Exception( 'The FROM / INTO Keyword was not found, so this is not a prefixable query.' );
	            }
	            
	            // explode it to get the different tables;
	            $tables = explode( ',', substr( $query, $start ) );
	            
	            // detect end of tabledefinition
	            $end = null;
	            foreach( $tables as $key => $table ) {
	            	if( !empty( $end ) ) {
	            		// Ignore all other values, they are no valid tablenames for this part of the query
	            		unset( $tables[$key] );
	            		continue;
	            	}
	            	
		            if( preg_match( $end_tabledefinition, $table, $match ) ) {
	                	unset( $tables[$key] );
	                	$tables[] = $match[1];
	                	$end = preg_quote( array_shift( array_filter( explode( ' ', $match[2] ) ) ) );
	                }
	            }
	            
	            // Replace every table with it's prefixed form.
	            foreach( $tables as $table ) {
	                $table = trim( $table ); // Remove redundant spaces
					
	                $tbl_pattern = '#(\b)' . $table . '(\b.+' . $end . '|\.|$)#U';
	                // Otherwise just replace the table with it's prefixed form and continue;
	                while( preg_match( $tbl_pattern, $query ) ) {
	                	$query = preg_replace( $tbl_pattern , '${1}' . $this->getPrefix() . $table . '${2}', $query, -1, $count );
	                	
	                	// Move forward to the point where Start is now located.
	                	$start += $count * strlen($this->getPrefix());
	                }
	            }
	            
	            if( !preg_match( $pattern, $query, $matches, PREG_OFFSET_CAPTURE, $start ) ) {
	            	break;
	            }
            }
            
            $query = preg_replace_callback( $string_placeholder, array( $this, '_setStrings' ), $query );

            return $query;
        }
        
        protected function _stripStrings( $match ) {
        	$index = count( $this->_original_strings );
        	
        	$this->_original_strings[] = $match[0];
        	
        	return '$' . $index;
        }
        
        protected function _setStrings( $match ) {
        	return $this->_original_strings[$match[1]];
        }
    }
