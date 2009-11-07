<?php
    abstract class WebLab_Data_Result
    {

        protected $_data;
        protected $_resource;
        protected $_id;
        protected $_position;
        
        public function __construct( $data, $id )
        {
            $this->_data = $data;
            $this->_id = $id;
            $this->_read();
        }

        public function fetch()
        {
            return next( $this->_data );
        }

        public function reset()
        {
            reset( $this->_data );
        }

        public function fetchAll()
        {
            return $this->_data;
        }

        protected function _add( $data )
        {
            if( isset( $this->_id ) )
            {
                $this->data[ $data[ $this->_id ] ] = $this->_wrap( $data, $this->_id );
            }else
            {
                $this->_data[] = $this->_wrap( $data );
            }
        }

        protected function count()
        {
            return count( $this->_data );
        }

        public function getId()
        {
            return $this->_id;
        }

        abstract protected function _read();
        abstract protected function _wrap();

    }