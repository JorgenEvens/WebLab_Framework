<?php
    abstract class WebLab_Data_Operation
    {
        protected $_criteria;
        protected $_limit;
        protected $_fields;
        protected $_tables;

        protected function _procesEquation( $data )
        {
            $tmp = array();
            foreach( $data as $field => $value )
            {
                if( !is_string( $field ) )
                {
                    throw new WebLab_Exception_Data( 'Fieldnames should be strings.' );
                }elseif( is_numeric( $value ) )
                {
                    $tmp[] = $field . ' = ' . $value;
                }else
                {
                    $tmp[] = $field . ' = \'' . $value . '\'';
                }
            }

            return $tmp;
        }

        protected function _procesCriteria()
        {
            return $this->_procesEquation( $this->_criteria );
        }

        public function select( $fields=null, $append=false )
        {
            if( !isset( $fields ) )
            {
                return $this->_fields;
            }

            if( !is_array( $fields ) )
            {
                throw new WebLab_Exception_Data( 'Expecting an array of fields.' );
            }

            if( $append )
            {
                $this->_fields = array_merge( $this->_fields, $fields );
            }else
            {
                $this->_fields = $fields;
            }

            return $this;
        }


        public function criteria( $criteria=null, $append=false )
        {
            if( !isset( $criteria ) )
            {
                return $this->_criteria;
            }

            if( !is_array( $criteria ) )
            {
                throw new WebLab_Exception_Data( 'Expecting an array of criterium.' );
            }

            if( $append )
            {
                $this->_criteria = array_merge( $this->_criteria, $criteria );
            }else
            {
                $this->_criteria = $criteria;
            }

            return $this;
        }

        public function tables( $tables=null, $append=false )
        {
            if( !isset( $tables ) )
            {
                return $this->_tables;
            }

            if( !is_array( $tables ) )
            {
                throw new WebLab_Exception_Data( 'Expecting an array of tables.' );
            }

            if( $append )
            {
                $this->_tables = array_merge( $this->_tables, $tables );
            }else
            {
                $this->_tables = $tables;
            }

            return $this;
        }

        public function limit( $start=null, $count )
        {
            if( !isset( $start ) )
            {
                return $this->_limit;
            }

            $this->_limit = array( 'start' => $start, 'count' => $count );

            return $this;
        }

        protected function _procesAlias( $array )
        {
            $tmp = array();
            foreach( $array as $field => $alternative )
            {
                if( !is_numeric( $field ) && is_string( $alternative ) )
                {
                    $tmp[] = $field . ' AS ' . $alternative;
                }elseif( is_string( $alternative ) )
                {
                    $tmp[] = $alternative;
                }else
                {
                    throw new WebLab_Exception_Data( 'Fieldnames should be strings.' );
                }
            }

            return $tmp;
        }

        abstract public function __toString();
    }