<?php
	/**
	 * Read data from the resource.
	 * 
	 * @author jorgen
	 *
	 */
    abstract class WebLab_Data_Result
    {
        protected $_rows = array();

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

    }