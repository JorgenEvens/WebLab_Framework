<?php
    class WebLab_Data_Query
    {
        protected $_tables = array();
        protected $_criteriaChain;
        protected $_limit;
        protected $_count_limitless = false;
        public $_last_query;

        protected $_adapter;
        
        public function __construct( WebLab_Data_Adapter $adapter=null )
        {
            if( !empty( $adapter ) )
            {
                $this->setAdapter( $adapter );
            }
        }

        public function setAdapter( WebLab_Data_Adapter $adapter )
        {
            $this->_adapter = $adapter;
        }
        
        public function getAdapter(){
        	return $this->_adapter;
        }
        
        // Deprecated
        // Only here for compatibility.
        public function getCriteriaChain()
        {
            return $this->getCriteria();
        }
        //-----------------------------------------

        public function getCriteria()
        {
            if( !isset( $this->_criteriaChain ) )
            {
                $this->_criteriaChain = new WebLab_Data_CriteriaChain();
            }
            return $this->_criteriaChain;
        }

        public function addTables()
        {
            $tables = func_get_args();

            array_map( array( $this, 'addTable' ), $tables );

            return $this;
        }

        public function addTable( $table )
        {
            if( is_string( $table ) )
            {
                $table = new WebLab_Data_Table( $table );
            }

            $this->_tables[ $table->getName() ] = $table;

            return $table;
        }

        public function removeTable( $table )
        {
            if( is_string( $table ) )
            {
                unset( $this->_tables[ $table ] );
            }elseif( $table instanceof WebLab_Data_Table )
            {
                unset( $this->_tables[ $table->getName() ] );
            }

            return $this;
        }

        public function getTable( $table )
        {
            return $this->_tables[ $table ];
        }

        public function getTables()
        {
            return $this->_tables;
        }

        public function setLimit( $count, $start=0 )
        {
            if( !( is_integer( $count ) && is_integer( $start ) ) )
            {
                throw new Exception( 'Count and Start must be numeric.' );
            }

            $this->_limit = (object) array( 'count' => $count, 'start' => $start );
        }

        public function clearLimit()
        {
            unset( $this->_limit );
        }
        
        public function countLimitless( $count ){
        	$this->_count_limitless = $count;
        }
        
        public function getCountLimitless(){
        	return $this->_count_limitless;
        }

       public function select()
        {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 )
            {
                throw new Exception( 'No tables given' );
            }

            $q = 'SELECT ';
            
            if( $this->getCountLimitless() )
            	$q .= 'SQL_CALC_FOUND_ROWS ';

            if( count( $query->fields ) == 0 )
            {
                $q .= '* ';
            }else
            {
                $tmp = array();
                foreach( $query->fields as $field )
                {
                	if( !$field->getSelect() ) continue;
                    $alias = $field->getAlias();
                    $tmp[] = empty( $alias ) ? $field : $field->__toString() . ' AS ' . $alias;
                }
                $q .= implode( ', ', $tmp ) . ' ';
            }

            $q .= 'FROM ' . implode( ', ', $this->_tables ) . ' ';

            if( isset( $this->_criteriaChain ) )
            {
                $q .= ' WHERE ' . $this->_criteriaChain->get( $this->_adapter->getAdapterSpecs() ) . ' ';
            }

            if( count( $query->order ) > 0 )
            {
                $q .= 'ORDER BY ' . implode( ', ', $query->order ) . ' ';
            }

            if( count( $query->group ) > 0 )
            {
                $q .= 'GROUP BY ' . implode( ', ', $query->group ) . ' ';
            }

            if( isset( $this->_limit ) )
            {
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

        public function update()
        {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 )
            {
                throw new Exception( 'No tables given' );
            }

            $q = 'UPDATE ' . implode( ', ', $this->_tables ) . ' ';

            $fields = array();
            foreach( $query->fields as $field )
            {
                if( $field->isAltered() )
                {
                    if( !is_numeric( $field->getValue() ) )
                    {
                        $fields[] = $field . ' = \'' . $field->getValue() . '\'';
                    }else
                    {
                        $fields[] = $field . ' = ' . $field->getValue();
                    }
                }
            }

            if( count( $fields ) == 0 )
            {
                throw new Exception( 'No fields to alter.' );
            }

            $q .= 'SET ' . implode( ', ', $fields ) . ' ';

            $criteria = $this->getCriteriaChain()->get( $this->_adapter->getAdapterSpecs() );
            if( !empty( $criteria ) )
            {
                $q .= ' WHERE ' . $criteria . ' ';
            }

            if( isset( $this->_limit ) )
            {
                $q .= 'LIMIT ' . $this->_limit->start . ', ' . $this->_limit->count;
            }

            $this->_last_query = $q;

            return $this->_adapter->query( $q );
        }

        public function insert( $update=false, $ignoreInUpdate=array() )
        {
            $this->_isConnected();

            $query = $this->_parseQuery();

            if( count( $this->_tables ) == 0 )
            {
                throw new Exception( 'No tables given' );
            }

            if( count( $query->fields ) == 0 )
            {
                throw new Exception( 'No fields given' );
            }

            $q = 'INSERT INTO ' . implode( ', ', $this->_tables ) . ' ';

            $q .= '( ' . implode( ', ', $query->fields ) . ' ) ';

            $values = array();
            foreach( $query->fields as $field )
            {
                if( is_numeric( $field->getValue() ) || $field->getValue() == 'NULL' )
                {
                    $values[] = $field->getValue();
                }else
                {
                    $values[] = '\'' . $this->_adapter->escape_string( $field->getValue() ) . '\'';
                }
            }

            $q .= 'VALUES( ' . implode( ', ', $values ) . ' )';

            if( $update )
            {
                $q .= ' ON DUPLICATE KEY UPDATE ';
                $values = array();
                foreach( $query->fields as $field )
                {
                    if( in_array( $field, $ignoreInUpdate ) )
                    {
                        continue;
                    }

                    if( is_numeric( $field->getValue() ) )
                    {
                         $values[] = $field->getFullName() . '=' . $field->getValue();
                    }else
                    {
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

            if( count( $this->_tables ) == 0 )
            {
                throw new Exception( 'No tables given' );
            }

            $q = 'DELETE FROM ' . implode( ', ', $this->_tables ) . ' ';

            if( isset( $this->_criteriaChain ) )
            {
                $q .= ' WHERE ' . $this->_criteriaChain->get( $this->_adapter->getAdapterSpecs() ) . ' ';
            }

            if( isset( $this->_limit ) )
            {
                $q .= 'LIMIT ' . $this->_limit->start . ', ' . $this->_limit->count;
            }

            $this->_last_query = $q;

            return $this->_adapter->query( $q );
        }

        protected function _parseQuery()
        {
            $fields = array();
            $order = array();
            $group = array();

            foreach( $this->_tables as $table )
            {
                foreach( $table->getFields() as $field )
                {
                    $fields[] = $field;

                    if( $field->getOrder() != null )
                    {
                        $order[] = $field . ' ' . $field->getOrder();
                    }

                    if( $field->getGroup() )
                    {
                        $group[] = $field;
                    }
                }
            }

            return (object) array(
                'fields'  => $fields,
                'order'   => $order,
                'group'   => $group
            );
        }

        protected function _isConnected()
        {
            if( !isset( $this->_adapter ) )
            {
                throw new Exception( 'Adapter is not set!' );
            }

            if( !$this->_adapter->isConnected() )
            {
                throw new Exception( 'Adapter is not connected!' );
            }
        }

    }