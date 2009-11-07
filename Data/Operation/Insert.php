<?php
    class WebLab_Data_Operation_Insert
    {
        protected $_row;
        protected $_table;

        public function __construct( $row )
        {
            if( $row instanceof WebLab_Data_Row )
            {
                $this->_row = $row;
            }else
            {
                throw new WebLab_Exception_Data( 'The row should be an instance of WebLab_Data_Row' );
            }
        }

        public function row( $row=null )
        {
            if( !($row instanceof WebLab_Data_Row) )
            {
                throw new WebLab_Exception_Data( 'The row should be an instance of WebLab_Data_Row' );
            }

            $this->_row = $row;
            return $this;
        }

        public function __toString()
        {
            if( !is_string( $table ) )
            {
                throw new WebLab_Exception_Data( 'No table given.' );
            }

            $q = 'INSERT INTO ' . $table;

            $fields = $this->_row->toArray();
            if( !count( $fields ) )
            {
                throw new WebLab_Exception_Data( 'No fields to update.' );
            }

            $keys = array_keys( $fields );
            $q .= '(' . implode( ', ', $keys ) . ')';

            $q .= ' VALUES(';

            $tmp = array();
            foreach( $keys as $key )
            {
                $tmp[] = $fields[ $key ];
            }
            $q .= implode( ', ', $fields ) . ')';

            return $q;
        }
    }