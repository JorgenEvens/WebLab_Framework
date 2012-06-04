<?php
	abstract class WebLab_Data_QueryBuilder {
		
		protected $_query = null;
		
		abstract function select();
		
		abstract function insert();
		
		abstract function update();
		
		abstract function delete();
		
		public function setQuery( WebLab_Data_Query $query ) {
			$this->_query = $query;
			return $this;
		}
		
		/**
		 * Decompile the query into an easier to use format.
		 *
		 * @return object
		 */
		protected function _parseQuery()
		{
			$query = $this->_query;
			
			$tables = $query->getTables();
			$fields = array();
			$order = array();
			$group = array();
		
			foreach( $tables as $table ) {
				foreach( $table->getFields() as $field ) {
					$fields[] = $field;
		
					if( $field->getOrder() != null ) {
						$order[] = $field . ' ' . $field->getOrder();
					}
		
					if( $field->getGroup() ) {
						$group[] = $field;
					}
				}
			}
		
			return (object) array(
					'tables'  => $tables,
					'fields'  => $fields,
					'order'   => $order,
					'group'   => $group
			);
		}
		
	}