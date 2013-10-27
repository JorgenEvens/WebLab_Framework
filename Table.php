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
		 * Specifies the primary key fields.
		 * If there is a auto_increment field, it should be the first in this list.
		 * find( $id ) calls will be matched against the first field in this array.
		 *
		 * @var mixed Names of primary key columns
		 */
		protected static $_primary_keys = array( 'id' );

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
		public function save( &$object ){
			$q = db(static::$_database)->newQuery();
				
			$table = $q->addTable( $this->createTable() );
				
			if( !isset( $object['online'] ) && self::_hasField( 'online' ) )
				$object['online'] = 1;
		
			if( !isset( $object['deleted'] ) && self::_hasField( 'deleted' ) )
				$object['deleted'] = 0;
				
			foreach( $object as $key => &$value )
				if( in_array( $key, static::$_fields ) )
				$table->getField($key)->setValue( $value );
		
			$no_update = array();
			foreach( static::$_primary_keys as $field ) {
				$no_update[] = $table->getField( $field );
			}

			$q->insert( true, $no_update );
				
			if( count( static::$_primary_keys ) == 1 )
				$object[static::$_primary_keys[0]] = $q->getAdapter()->insert_id();
		}
		
		/**
		 * Create a record for this table.
		 * The id of the new record will be set in $object
		 *
		 * @param mixed $object The object to insert as a record.
		 */
		public function create( &$object ){
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
			
			if( count( static::$_primary_keys ) == 1 )
				$object[static::$_primary_keys[0]] = $q->getAdapter()->insert_id();
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

			$table = $q->addTable( static::table() )->addFields( static::$_primary_keys );
			foreach( static::$_primary_keys as $field ) {
				$q->getCriteria()->addAnd( $table->$field->eq( $object[$field] ) );
			}
			
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
			
			$table = $q->addTable( static::table() )->addFields( static::$_primary_keys );
			
			foreach( static::$_primary_keys as $field ) {
				$q->getCriteria()->addAnd( $table->$field->eq( $object[$field] ) );
			}
			
			foreach( $object as $key => &$value )
				if( !in_array( $key, static::$_primary_keys ) && in_array( $key, static::$_fields ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
		/**
		 * Find a record by its primary key.
		 *
		 * @param int $id ID of the record to find.
		 * @return mixed The record as an object.
		 */
		public function find( $key ){
			if( is_array( $key ) ) {
				return $this->findBy( $key );
			}

			$result = $this->findBy( static::$_primary_keys[0], $key );
			if( empty( $result ) ) return null;

			return array_pop( $result );
		}

		/**
		 * Find records by a set of filters.
		 *
		 * @param mixed $field The fieldname or a list of filters to use as $column => $value pairs.
		 * @param mixed $value If field is a fieldname, the value it should match.
		 * @param int &$result_count If different from false, will be set to the amount of records.
		 * @return mixed An array of objects representing a record.
		 */
		public function findBy( $field, $value=null, $count=null, $start=0, &$result_count=false ) {
			if( !is_array( $field ) ) {
				$field = array(
					$field => $value
				);
			}

			$q = db(static::$_database)->newQuery();

			if( $result_count !== false )
				$q->countLimitless( true );
			
			$table = $q->addTable( $this->createTable() );
			
			$criteria = $q->getCriteria();

			$defaults = array();

			if( self::_hasField( 'online' ) )
				$defaults['online'] = 1;
			
			if( self::_hasField( 'deleted' ) )
				$defaults['deleted'] = 0;

			$field = array_merge( $defaults, $field );

			foreach( $field as $field_name => $value ) {
				if( self::_hasField( $field_name ) )
					$criteria->addAnd( $table->$field_name->eq( $value ) );
			}
			
			if( $count != null )
				$q->setLimit( $count, $start );

			$result = $q->select();
			if( $result_count !== false )
				$result_count = $result->getTotalRows();

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
			return $this->findBy( array(), null, $count, $start, $result_count );
		}
		
		/**
		 * Count the number of records in a table.
		 *
		 * @return int The amount of records found.
		 */
		public function countAll(){
			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( static::table() );

			$count = empty( static::$_primary_keys ) ? static::$_fields[0] : static::$_primary_keys[0];
			$count = $table->addField( $count );

			$count->setFunction( 'COUNT' )
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