<?php
	/**
     *
     * Implementation of a QueryBuilder, Generates MySQL specific SQL.
     *
     * @see WebLab_Data_Query
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data_MySQLi
     *
     */
	class WebLab_Data_MySQL_QueryBuilder extends WebLab_Data_QueryBuilder {
		
		public function select() {

			$query = $this->_query;
			$parse = $this->_parseQuery();

			if( count( $parse->tables ) == 0 ) {
				throw new Exception( 'No tables given' );
			}

			$q = 'SELECT ';

			if( count( $parse->fields ) == 0 ) {
				$q .= '* ';
			} else {
				$tmp = array();
				foreach( $parse->fields as $field ) {
					if( !$field->getSelect() ) continue;

					$alias = $field->getAlias();
					$tmp[] = empty( $alias ) ? $field : $field->__toString() . ' AS ' . $alias;
				}
				$q .= implode( ', ', $tmp ) . ' ';
			}

			$q .= 'FROM ' . implode( ', ', $parse->tables ) . ' ';

			$criteria = $query->getCriteria();
			if( !empty( $criteria ) && $criteria->hasCriteria() ) {
				$q .= ' WHERE ' . $criteria->get( $query->getAdapter()->getAdapterSpecs() ) . ' ';
			}

			if( count( $parse->group ) > 0 ) {
				$q .= 'GROUP BY ' . implode( ', ', $parse->group ) . ' ';
			}

			if( count( $parse->order ) > 0 ) {
				$q .= 'ORDER BY ' . implode( ', ', $parse->order ) . ' ';
			}

			$limit = $query->getLimit();
			if( !empty( $limit ) ) {
				$q .= 'LIMIT ' . $limit->start . ', ' . $limit->count;
			}

			return $q;
		}

		public function count() {

			$query = $this->_query;
			$parse = $this->_parseQuery();

			if( count( $parse->tables ) == 0 ) {
				throw new Exception( 'No tables given' );
			}

			$q = 'SELECT COUNT(1) AS count ';

			$q .= 'FROM ' . implode( ', ', $parse->tables ) . ' ';

			$criteria = $query->getCriteria();
			if( !empty( $criteria ) && $criteria->hasCriteria() ) {
				$q .= ' WHERE ' . $criteria->get( $query->getAdapter()->getAdapterSpecs() ) . ' ';
			}

			if( count( $parse->group ) > 0 ) {
				$q .= 'GROUP BY ' . implode( ', ', $parse->group ) . ' ';
			}

			return $q;
		}

		public function update() {

			$query = $this->_query;
			$parse = $this->_parseQuery();

			if( count( $parse->tables ) == 0 ) {
				throw new Exception( 'No tables given' );
			}

			$q = 'UPDATE ' . implode( ', ', $parse->tables ) . ' ';

			$fields = array();
			foreach( $parse->fields as $field ) {
				if( $field->isAltered() ) {
					$fields[] = $field . ' = ' . $this->_escape( $field->getValue(), $query->getAdapter() );
				}
			}

			if( count( $fields ) == 0 ) {
				throw new Exception( 'No fields to alter.' );
			}

			$q .= 'SET ' . implode( ', ', $fields ) . ' ';

			$criteria = $query->getCriteriaChain();
			if( !empty( $criteria ) && $criteria->hasCriteria() ) {
				$q .= ' WHERE ' . $criteria->get( $query->getAdapter()->getAdapterSpecs() ) . ' ';
			}

			$limit = $query->getLimit();
			if( !empty( $limit ) ) {
				$q .= 'LIMIT ' . $limit->start . ', ' . $limit->count;
			}

			return $q;
		}

		public function insert( $update=false, $ignoreInUpdate=array() ) {

			$query = $this->_query;
			$parse = $this->_parseQuery();

			if( count( $parse->tables ) == 0 ) {
				throw new Exception( 'No tables given' );
			}

			if( count( $parse->fields ) == 0 ) {
				throw new Exception( 'No fields given' );
			}

			$q = 'INSERT INTO ' . implode( ', ', $parse->tables ) . ' ';

			$q .= '( ' . implode( ', ', $parse->fields ) . ' ) ';

			$values = array();
			foreach( $parse->fields as $field ) {
				$values[] = $this->_escape( $field->getValue(), $query->getAdapter() );
			}

			$q .= 'VALUES( ' . implode( ', ', $values ) . ' )';

			if( $update ) {
				$q .= ' ON DUPLICATE KEY UPDATE ';
				$values = array();
				foreach( $parse->fields as $field ) {
					if( in_array( $field, $ignoreInUpdate ) || !$field->isAltered() ) {
						continue;
					}

					$values[] = $field . ' = ' . $this->_escape( $field->getValue(), $query->getAdapter() );
				}

				$q .= implode(', ', $values ) . ' ';
			}

			return $q;
		}

		public function delete() {

			$query = $this->_query;
			$parse = $this->_parseQuery();

			if( count( $parse->tables ) == 0 ) {
				throw new Exception( 'No tables given' );
			}

			$q = 'DELETE FROM ' . implode( ', ', $parse->tables ) . ' ';

			$criteria = $query->getCriteriaChain();
			if( !empty( $criteria ) && $criteria->hasCriteria() ) {
				$q .= ' WHERE ' . $criteria->get( $query->getAdapter()->getAdapterSpecs() ) . ' ';
			}

			$limit = $query->getLimit();
			if( !empty( $limit ) ) {
				$q .= 'LIMIT ' . $limit->start . ', ' . $limit->count;
			}

			return $q;
		}

		protected function _escape( $value, $adapter ) {
			if( $value === 'NULL' ) {
				return 'NULL';
			} else if( is_numeric( $value ) && !preg_match( '#^00+#', $value ) ) {
				return $value;
			}

			return '\'' . $adapter->escape_string( $value ) . '\'';

		}


	}
