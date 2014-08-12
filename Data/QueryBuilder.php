<?php
    /**
     * QueryBuilder.php
     *
     * This file contains the implementation of the WebLab_Data_QueryBuilder class.
     * @see WebLab_Data_QueryBuilder
     */
    /**
     * Abstract representation of a QueryBuilder.
     * QueryBuilders are used to convert a WebLab_Data_Query into 
     * database specific SQL.
     * 
     * @see WebLab_Data_Query
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data
     */
	abstract class WebLab_Data_QueryBuilder {
		
		/**
		 * The query that the current instance will parse.
		 *
		 * @var WebLab_Data_Query
		 */
		protected $_query = null;
		
		/**
		 * Build a select query using _query
		 *
		 * @see $_query
		 */
		abstract function select();

		/**
		 * Build a select count(*) query using _query
		 *
		 * @see $_query
		 */
		abstract function count();
		
		/**
		 * Build an insert query using _query
		 *
		 * @see $_query
		 */
		abstract function insert();
		
		/**
		 * Build an update query using _query
		 *
		 * @see $_query
		 */
		abstract function update();
		
		/**
		 * Build a delete query using _query
		 *
		 * @see $_query
		 */
		abstract function delete();
		
		/**
		 * Alter the query to which this builder belongs.
		 *
		 * @see $_query
		 * @param WebLab_Data_Query New query
		 * @return WebLab_Data_QueryBuilder
		 */
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
