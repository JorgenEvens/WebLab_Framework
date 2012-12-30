<?php
    /**
     * Table.php
     *
     * This file contains the implementation of the WebLab_Table class.
     * This version of WebLab_Table provides support for PHP 5.2.6 and higher.
     * @see WebLab_Table
     */
    /**
     * An abstraction to retrieve data form a database.
     * This version of WebLab_Table provides support for PHP 5.2.6 and higher.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     *
     */
	abstract class WebLab_Table {
		
		/**
		 * Holds the name of the database as found in the configuration.
		 *
		 * @see WebLab_Config
		 * @see WebLab_Database
		 * @var string The key by which the databaseconfiguration is identified.
		 */
		protected static $_database = 'main';

		/**
		 * Holds the name of the table to use, without the prefix.
		 *
		 * @var string The key by which the databaseconfiguration is identified.
		 */
		protected static $_table;

		/**
		 * The available columns in the table.
		 *
		 * @var mixed Names of the columns.
		 */
		protected static $_fields;

		/**
		 * Holds the singleton instance.
		 *
		 * @var WebLab_Table
		 */
		protected static $_instance;
		
		/**
		 * Singleton method to retrieve an instance.
		 *
		 * @return WebLab_Table
		 */
		public static function getInstance(){
			if( empty( static::$_instance ) ){
				static::$_instance = new static();
			}
				
			return static::$_instance;
		}
		
		/**
		 * Start a transaction on the database.
		 *
		 */
		public static function startTransaction() {
			db(static::$_database)->startTransaction();
		}
		
		/**
		 * End the transaction on the database.
		 *
		 * @param boolean $commit Should changes be committed or discarded.
		 */
		public static function quitTransaction( $commit=true ) {
			db(static::$_database)->quitTransaction( $commit );
		}
		
		/**
		 * Retrieve the name of the table using the prefix set for this database.
		 *
		 * @see WebLab_Database
		 * @return string The full table name
		 */
		public static function table() {
			return db(static::$_database)->getPrefix() . static::$_table;
		}
		
		/**
		 * Detect if a field is in the column list.
		 * 
		 * @param $field The name of the column to test for.
		 * @return boolean True if field is present.
		 */
		protected static function _hasField( $field ) {
			return in_array( $field, static::$_fields );
		}

		/**
		 * Create an instance of a WebLab_Data_Table to be used internally.
		 *
		 * @return WebLab_Data_Table An instance of the abstraction layer Table class.
		 */
		public function createTable(){
			$t = new WebLab_Data_Table( static::table() );
			return $t->addFields( static::$_fields );
		}
		
		/**
		 * Update a record if it already exists, insert it otherwise.
		 *
		 * @param mixed $object The object to insert as a record.
		 */
		public function save( $object ){
			$q = db(static::$_database)->newQuery();
				
			$table = $q->addTable( $this->createTable() );
				
			if( !isset( $object['online'] ) && self::_hasField( 'online' ) )
				$object['online'] = 1;
		
			if( !isset( $object['deleted'] ) && self::_hasField( 'deleted' ) )
				$object['deleted'] = 0;
				
			foreach( $object as $key => &$value )
				if( in_array( $key, static::$_fields ) )
				$table->getField($key)->setValue( $value );
		
			$q->insert( true, array( $table->getField( 'id' ) ) );
				
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
		/**
		 * Create a record for this table.
		 * The id of the new record will be set in $object
		 *
		 * @param mixed $object The object to insert as a record.
		 */
		public function create( $object ){
			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			if( !isset( $object['online'] ) && self::_hasField( 'online' ) )
				$object['online'] = 1;
				
			if( !isset( $object['deleted'] ) && self::_hasField( 'deleted' ) )
				$object['deleted'] = 0;
			
			foreach( $object as $key => &$value )
				if( in_array( $key, static::$_fields ) )
					$table->getField($key)->setValue( $value );
				
			$q->insert();
			
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
		/**
		 * Delete a record from the table.
		 * If soft-delete is used it will only update the deleted and online field.
		 * 
		 * @param mixed $object The object to delete from the table.
		 */
		public function delete( $object ){
			$has_deleted = self::_hasField( 'deleted' );
			$has_online = self::_hasField( 'online' );
			$q = db(static::$_database)->newQuery();
			$table = $q->addTable( static::table() )->addFields( 'id' );
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			if( $has_deleted || $has_online ) {
				if( $has_deleted )
					$table->addField( 'deleted' )->setValue( 1 );
				
				if( $has_online )
					$table->addField( 'online' )->setValue( 0 );
				
				$q->update();
			} else {
				$q->delete();
			}
		}
		
		/**
		 * Update a record in the table.
		 *
		 * @param mixed $object The new values for this record, including the unaltered primary key.
		 */
		public function update( $object ){
			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( static::table() )->addFields( 'id' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			foreach( $object as $key => &$value )
				if( $key != 'id' && in_array( $key, static::$_fields ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
		/**
		 * Find a record by its primary key.
		 *
		 * @param int $id ID of the record to find.
		 * @return mixed The record as an object.
		 */
		public function find( $id ){
			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			$criteria = $q->getCriteria()->addAnd( $table->id->eq( $id ) );
			
			if( self::_hasField( 'online' ) )
				$criteria->addAnd( $table->online->eq( 1 ) );
			
			if( self::_hasField( 'deleted' ) )
				$criteria->addAnd( $table->deleted->eq( 0 ) );
			
			return $q->select()->current();
		}

		/**
		 * Find records by a set of filters.
		 *
		 * @param mixed $field The fieldname or a list of filters to use as $column => $value pairs.
		 * @param mixed $value If field is a fieldname, the value it should match.
		 * @param int &$result_count If different from false, will be set to the amount of records.
		 * @return mixed An array of objects representing a record.
		 */
		public function findBy( $field, $value=null, &$result_count=false ) {
			if( !is_array( $field ) ) {
				$field = array(
					$field => $value
				);
			}

			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			$criteria = $q->getCriteria()->addAnd( $table->id->eq( $id ) );

			foreach( $field as $field_name => $value ) {
				if( self::_hasField( $field_name ) )
					$criteria->addAnd( $table->get( $field_name )->eq( $value ) );
			}

			if( self::_hasField( 'online' ) )
				$criteria->addAnd( $table->online->eq( 1 ) );
			
			if( self::_hasField( 'deleted' ) )
				$criteria->addAnd( $table->deleted->eq( 0 ) );
			
			$result = $q->select();
			if( $result_count !== false )
				$result_count = $result->count();

			return $result->fetch_all();
		}
		
		/**
		 * Find all the records in the table from $start to $start+$count.
		 *
		 * @param int $count The amount of records to retrieve.
		 * @param int $start The offset to start retrieving from.
		 * @param int &$result_count If different from false, will be set to the total amount of records in the table.
		 * @return mixed An array of objects representing a record.
		 */
		public function findAll( $count=null, $start=0, &$result_count=false ){
			$q = db(static::$_database)->newQuery();
			
			if( $result_count !== false )
				$q->countLimitless( true );
			
			$table = $q->addTable( $this->createTable() );
			
			$criteria = $q->getCriteria();
			
			if( self::_hasField( 'online' ) )
				$criteria->addAnd( $table->online->eq( 1 ) );
			
			if( self::_hasField( 'deleted' ) )
				$criteria->addAnd( $table->deleted->eq( 0 ) );
			
			if( $count != null )
				$q->setLimit( $count, $start );
				
			$result = $q->select();

			if( $result_count !== false )
				$result_count = $result->getTotalRows();
				
			return $result->fetch_all();
		}
		
		/**
		 * Count the number of records in a table.
		 *
		 * @return int The amount of records found.
		 */
		public function countAll(){
			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( static::table() )
				->addFields( 'id' );

			$table->id->setFunction( 'COUNT' )
				->setAlias( 'count' );
			
			$criteria = $q->getCriteria();
			
			if( self::_hasField( 'online' ) ) {
				$table->addField( 'online' )->setSelect( false );
				$criteria->addAnd( $table->online->eq( 1 ) );
			}
			
			if( self::_hasField( 'deleted' ) ) {
				$table->addField( 'deleted' )->setSelect( false );
				$criteria->addAnd( $table->deleted->eq( 0 ) );
			}
			
			return $q->select()->current()->count;
		}
	}