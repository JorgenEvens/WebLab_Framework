<?php
	/**
     *
     * Implementation of a query, generating mySQL specific SQL.
     *
     * @see WebLab_Data_Query
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage WebLab_Data_MySQL
     *
     */
	class WebLab_Data_MySQL_Query extends WebLab_Data_Query {
		
		public function select() {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 ) {
                throw new Exception( 'No tables given' );
            }

            $q = 'SELECT ';
            
            if( $this->getCountLimitless() ) {
            	$q .= 'SQL_CALC_FOUND_ROWS ';
            }

            if( count( $query->fields ) == 0 ) {
                $q .= '* ';
            } else {
                $tmp = array();
                foreach( $query->fields as $field ) {
                	if( !$field->getSelect() ) continue;
                    $alias = $field->getAlias();
                    $tmp[] = empty( $alias ) ? $field : $field->__toString() . ' AS ' . $alias;
                }
                $q .= implode( ', ', $tmp ) . ' ';
            }

            $q .= 'FROM ' . implode( ', ', $this->_tables ) . ' ';

            if( isset( $this->_criteriaChain ) ) {
                $q .= ' WHERE ' . $this->_criteriaChain->get( $this->_adapter->getAdapterSpecs() ) . ' ';
            }

            if( count( $query->order ) > 0 ) {
                $q .= 'ORDER BY ' . implode( ', ', $query->order ) . ' ';
            }

            if( count( $query->group ) > 0 ) {
                $q .= 'GROUP BY ' . implode( ', ', $query->group ) . ' ';
            }

            if( isset( $this->_limit ) ) {
                $q .= 'LIMIT ' . $this->_limit->start . ', ' . $this->_limit->count;
            }

            $this->_last_query = $q;

            $result = $this->_adapter->query( $q );
            
            if( $this->getCountLimitless() ) {
            	$row_count = $this->_adapter->query( 'SELECT FOUND_ROWS() AS count' );
            	$result->setTotalRows( $row_count->current()->count );
            }
            
            return $result;
        }

        public function update() {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 ) {
                throw new Exception( 'No tables given' );
            }

            $q = 'UPDATE ' . implode( ', ', $this->_tables ) . ' ';

            $fields = array();
            foreach( $query->fields as $field ) {
                if( $field->isAltered() ) {
                    if( !is_numeric( $field->getValue() ) ) {
                        $fields[] = $field . ' = \'' . call_user_func( $this->_adapter->getAdapterSpecs()->escape_string, $field->getValue() ) . '\'';
                    } else {
                        $fields[] = $field . ' = ' . $field->getValue();
                    }
                }
            }

            if( count( $fields ) == 0 ) {
                throw new Exception( 'No fields to alter.' );
            }

            $q .= 'SET ' . implode( ', ', $fields ) . ' ';

            $criteria = $this->getCriteriaChain()->get( $this->_adapter->getAdapterSpecs() );
            if( !empty( $criteria ) ) {
                $q .= ' WHERE ' . $criteria . ' ';
            }

            if( isset( $this->_limit ) ) {
                $q .= 'LIMIT ' . $this->_limit->start . ', ' . $this->_limit->count;
            }

            $this->_last_query = $q;

            return $this->_adapter->query( $q );
        }

        public function insert( $update=false, $ignoreInUpdate=array() )
        {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 ) {
                throw new Exception( 'No tables given' );
            }

            if( count( $query->fields ) == 0 ) {
                throw new Exception( 'No fields given' );
            }

            $q = 'INSERT INTO ' . implode( ', ', $this->_tables ) . ' ';

            $q .= '( ' . implode( ', ', $query->fields ) . ' ) ';

            $values = array();
            foreach( $query->fields as $field ) {
                if( is_numeric( $field->getValue() ) || $field->getValue() == 'NULL' ) {
                    $values[] = $field->getValue();
                } else {
                    $values[] = '\'' . $this->_adapter->escape_string( $field->getValue() ) . '\'';
                }
            }

            $q .= 'VALUES( ' . implode( ', ', $values ) . ' )';

            if( $update ) {
                $q .= ' ON DUPLICATE KEY UPDATE ';
                $values = array();
                foreach( $query->fields as $field ) {
                    if( in_array( $field, $ignoreInUpdate ) ) {
                        continue;
                    }

                    if( is_numeric( $field->getValue() ) ) {
                         $values[] = $field->getFullName() . '=' . $field->getValue();
                    } else {
                        $values[] = $field->getFullName() . '=\'' . $this->_adapter->escape_string( $field->getValue() ) . '\'';
                    }
                }

                $q .= implode(', ', $values ) . ' ';
            }

            $this->_last_query = $q;

            return $this->_adapter->query( $q );
        }

        public function delete()
        {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 ) {
                throw new Exception( 'No tables given' );
            }

            $q = 'DELETE FROM ' . implode( ', ', $this->_tables ) . ' ';

            if( isset( $this->_criteriaChain ) ) {
                $q .= ' WHERE ' . $this->_criteriaChain->get( $this->_adapter->getAdapterSpecs() ) . ' ';
            }

            if( isset( $this->_limit ) ) {
                $q .= 'LIMIT ' . $this->_limit->start . ', ' . $this->_limit->count;
            }

            $this->_last_query = $q;

            return $this->_adapter->query( $q );
        }
		
	}