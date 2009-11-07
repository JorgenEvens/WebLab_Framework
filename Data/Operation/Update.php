<?php
    class WebLab_Data_Operation_Update extends WebLab_Data_Operation
    {
        protected $_row;

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
            $q = 'UPDATE ';
            if( !count( $this->_tables ) )
            {
                throw new WebLab_Exception_Data( 'No table given.' );
            }

            $q .= implode( ', ', $this->_procesAlias( $this->_tables ) );
            $q .= ' SET ';

            if( !count( $this->_fields ) )
            {
                throw new WebLab_Exception_Data( 'No fields to update.' );
            }

            $q .= implode( ', ', $this->_procesEquation( $this->_fields ) );

            if( count( $this->_criteria ) )
            {
                $q .= ' WHERE ';
                $q .= implode( ' AND ', $this->_procesEquation( $this->_row->original() ) );
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

    }