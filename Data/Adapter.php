<?php
    abstract class WebLab_Data_Adapter
    {
        public $error;
        protected $_resource;
        protected $_wildcard = '*';
        protected $_prefix = '';
        protected $_transaction_depth = 0;

        abstract public function __construct( $config );
        abstract protected function _query( $query );
        abstract public function isConnected();
        abstract public function insert_id();
        abstract public function escape_string( $str );
        abstract public function getAdapterSpecs();
        abstract protected function _start_transaction();
        abstract protected function _quit_transaction( $commit );

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
            
            $pattern = '#(\s|^)(from|into|update)\s#i';
            //$end_tabledefinition = '#([^\b\s]+)(\s+([^A][^S]|.+)\s+|\s*$)#iU';
            $end_tabledefinition = '#(.+)\s+(([^A][^S])|.+)\s+#iU';
            
            // Take everything after the FROM / INTO keyword.
            preg_match( $pattern, $query, $matches, PREG_OFFSET_CAPTURE );
            
            while( count($matches) > 0)
            {
	            $start = $matches[0][1] + strlen( $matches[0][0] );
	            
	            if( $start < 7 )
	            {
	                throw new Exception( 'The FROM / INTO Keyword was not found, so this is not a prefixable query.' );
	            }
	            
	            // explode it to get the different tables;
	            $tables = explode( ',', substr( $query, $start ) );
	            
	            // detect end of tabledefinition
	            $end = null;
	            foreach( $tables as $key => $table )
	            {
	            	if( !empty( $end ) )
	            	{
	            		// Ignore all other values, they are no valid tablenames for this part of the query
	            		unset( $tables[$key] );
	            		continue;
	            	}
	            	
		            if( preg_match( $end_tabledefinition, $table, $match ) )
	                {
	                	unset( $tables[$key] );
	                	$tables[] = $match[1];
	                	$end = preg_quote( array_shift( array_filter( explode( ' ', $match[2] ) ) ) );
	                }
	            }
	            
	            // Replace every table with it's prefixed form.
	            foreach( $tables as $table )
	            {
	                $table = trim( $table ); // Remove redundant spaces
					
	                $tbl_pattern = '#(\b)' . $table . '(\b.+' . $end . '|\.|$)#U';
	                // Otherwise just replace the table with it's prefixed form and continue;
	                while( preg_match( $tbl_pattern, $query ) )
	                {
	                	$query = preg_replace( $tbl_pattern , '$1' . $this->getPrefix() . $table . '$2', $query, -1, $count );
	                	
	                	// Move forward to the point where Start is now located.
	                	$start += $count * strlen($this->getPrefix());
	                }
	            }
	            
	            if( !preg_match( $pattern, $query, $matches, PREG_OFFSET_CAPTURE, $start ) )
	            	break;
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
        
        public function startTransaction(){
        	$this->_transaction_depth++;
        	
        	if( $this->_transaction_depth == 1 )
        		$this->_start_transaction();
        }
        
        public function quitTransaction( $commit ){
        	$this->_transaction_depth--;
        	
        	if( $this->_transaction_depth < 1 || !$commit ) {
        		$this->_quit_transaction( $commit );
        		$this->_transaction_depth = 0;
        	}
        }
    }
