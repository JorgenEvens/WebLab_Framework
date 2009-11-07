<?php
    class WebLab_Data_Operation_Select extends WebLab_Data_Operation
    {
        protected $_order;

        public function __construct()
        {
            $this->_fields = array();
            $this->_criteria = array();
            $this->_limit = array( 'start' => 0, 'count' => 0);
            $this->_tables = array();
            $this->_order = array();
        }
        
        public function order( $order=null, $append=false )
        {
            if( !isset( $order ) )
            {
                return $this->_order;
            }

            if( !is_array( $order ) )
            {
                throw new WebLab_Exception_Data( 'Expecting an array of orders.' );
            }

            if( $append )
            {
                $this->_order = array_merge( $this->_order, $order );
            }else
            {
                $this->_order = $order;
            }

            return $this;
        }


        public function __toString()
        {
            $q = 'SELECT ';

            if( !count( $this->_fields ) )
            {
                throw new WebLab_Exception_Data( 'No fields to select.' );
            }

            $q .= implode( ', ', $this->_procesAlias( $this->_fields ) );

            $q .= ' FROM ';
            if( !count( $this->_tables ) )
            {
                throw new WebLab_Exception_Data( 'No table given.' );
            }

            $q .= implode( ', ', $this->_procesAlias( $this->_tables ) );

            if( count( $this->_criteria ) )
            {
                $q .= ' WHERE ';
                $q .= implode( ' AND ', $this->_procesCriteria() );
            }

            if( count( $this->_order ) )
            {
                $q .= ' ORDER BY ';
                $q .= implode( ', ', $this->_procesOrder() );
            }

            if( $this->_limit['start'] > -1 )
            {
                $q .= ' LIMIT ' . $this->_limit['start'];

                if( $this->_limit[ 'count' ] > 0 )
                {
                    $q .= ', ' . $this->_limit[ 'count' ];
                }
            }

            return $q;
        }

        protected function _procesOrder()
        {
            $tmp = array();
            foreach( $this->_order as $field => $direction )
            {
                if( is_string( $field ) && is_string( $direction ) )
                {
                    $tmp[] = $field . ' ' . $direction;
                }else
                {
                    throw new WebLab_Exception_Data( 'Fieldnames should be strings.' );
                }
            }

            return $tmp;
        }

    }