<?php
	/**
	 * Read data from the resource.
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Data
	 *
	 */
    abstract class WebLab_Data_Result
    {
        protected $_rows = array();
        protected $_total = 0;

        abstract protected function _read( &$result );

        public function __construct( $result )
        {
            $this->_read( $result );
        }

        public function next()
        {
            return next( $this->_rows );
        }

        public function previous()
        {
            return previous( $this->_rows );
        }

        public function current()
        {
            return current( $this->_rows );
        }

        public function fetch( $id )
        {
            return $this->_rows[ $id ];
        }

        public function fetch_all()
        {
            return $this->_rows;
        }

        public function count()
        {
            return count( $this->_rows );
        }
        
        public function setTotalRows( $total ){
        	$this->_total = $total;
        }
        
        public function getTotalRows(){
        	return $this->_total;
        }

    }