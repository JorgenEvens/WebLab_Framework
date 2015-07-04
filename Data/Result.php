<?php
	/**
	 * Read data from the resource.
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data
	 *
	 */
    abstract class WebLab_Data_Result implements ArrayAccess, Iterator, Countable
    {
        protected $_rows = null;
        protected $_total = -1;
        protected $_query = null;

        abstract protected function _read( $result );

        public function __construct( $query, $result=null )
        {
            $this->_query = $query;
            if( $result !== null )
                $this->_rows = $this->_read($result);
        }
        
        public function getQuery()
        {
            return $this->_query;
        }
        
        protected function ensureLoaded()
        {
            if( $this->_rows !== null )
                return;
            
            $this->_rows = $this->_read( $this->getQuery()->execute() );
        }
        
        protected function _ensureCounted() {
            if( $this->_total > -1 )
                return;
                
            if( is_string( $this->_query ) )
                throw new Exception('Unable to count the total results for a string query.');
                
            $rows = $this->_read( $this->getQuery()->count() );
            $this->_total = current($rows)->count;
        }
        
        /**
         * Fallback for old code that calls this to get results.
         * 
         * @return WebLab_Data_Result
         */
        public function fetch_all()
        {
            return $this;
        }
        
        public function previous()
        {
            return previous( $this->data() );
        }
        
        public function pop()
        {
            return array_pop( $this->data() );
        }
        
        public function shift() {
            return array_shift( $this->data() );
        }

        /**
         * Trigger query execution
         */
        public function getTotalRows(){
            $this->_ensureCounted();
        	return $this->_total;
        }
        
        public function fetch( $id )
        {
            $this->ensureLoaded();
            return $this->_rows[ $id ];
        }
        
        public function &data()
        {
            $this->ensureLoaded();
            return $this->_rows;
        }
        
        /**
         * Countable
         */
        public function count()
        {
            return count( $this->data() );
        }
        
        /**
         * Iterator
         */
        public function current()
        {
            return current( $this->data() );
        }
        
        public function key()
        {
            return key( $this->data() );
        }
        
        public function next()
        {
            return next( $this->data() );
        }
        
        public function rewind()
        {
            return reset( $this->data() );
        }
        
        public function valid()
        {
            return $this->current() !== false;
        }
        
        /**
         * ArrayAccess
         */
        public function offsetExists( $offset )
        {
            return $offset < count($this->data());
        }
        
        public function offsetGet( $offset )
        {
            return $this->fetch($offset);
        }
        
        public function offsetSet( $offset, $value )
        {
            throw new Exception('Cannot modify result.');
        }
        
        public function offsetUnset( $offset )
        {
            throw new Exception('Cannot unset record.');
        }

    }