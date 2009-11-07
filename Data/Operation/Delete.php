<?php
    class WebLab_Data_Operation_Delete
    {
        public function __toString()
        {
            $q = 'DELETE * ';

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