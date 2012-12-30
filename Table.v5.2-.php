<?php
    /**
     * Table.v5.2-.php
     *
     * This file contains the implementation of the WebLab_Table class.
     * This version of WebLab_Table provides support for PHP 5.2.5 and older.
     * @see WebLab_Table
     */
    /**
     * An abstraction to retrieve data form a database.
     * This version of WebLab_Table provides support for PHP 5.2.5 and older.
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
		 * Contains all the available static properties of all subclasses of WebLab_Table.
		 *
		 * @var mixed Collection of static properties per subclass.
		 */
		private static $_properties = array();
		
		/**
		 * Retrieve the static properties of a specific class using reflection.
		 *
		 * @param string $class_name The name of the class to inspect.
		 * @return mixed An array of available static properties.
		 */
		private static function _getProperties( $class_name ) {
			$properties = &self::$_properties[$class_name];
			if( empty( $properties ) ) {
				$class = new ReflectionClass( $class_name  );
				$properties = $class->getStaticProperties();
			}
			
			return $properties;
		}
		
		/**
		 * Retrieve the value of a specifc static property of a class.
		 *
		 * @param string $class The name of the class to retrieve property from.
		 * @param string $name The name of the property without the $-sign.
		 * @return mixed The value of the property
		 */
		private static function &_getProperty( $class, $name ) {
			$props = self::_getProperties( $class );
			
			return $props[$name];
		}
		
		/**
		 * Singleton method to retrieve an instance.
		 *
		 * @return WebLab_Table
		 */
		public static function getInstance(){
			$class_name = get_called_class();
			$instance = &self::_getProperty( get_called_class(), '_instance' );
			
			if( empty( $instance ) ){
				$instance = new $class_name();
			}
				
			return $instance;
		}
		
		/**
		 * Start a transaction on the database.
		 *
		 */
		public static function startTransaction() {
			db(self::getProperty( get_called_class(), '_database'))->startTransaction();
		}
		
		/**
		 * End the transaction on the database.
		 *
		 * @param boolean $commit Should changes be committed or discarded.
		 */
		public static function quitTransaction( $commit=true ) {
			db(self::getProperty( get_called_class(), '_database'))->quitTransaction( $commit );
		}

		/**
		 * Retrieve the name of the table using the prefix set for this database.
		 *
		 * @see WebLab_Database
		 * @return string The full table name
		 */
		public static function table() {
			$db = self::getProperty( get_called_class(), '_database');
			$table = self::getProperty( get_called_class(), '_table');

			return db($db)->getPrefix() . $table;
		}
		
		/**
		 * Detect if a field is in the column list.
		 * 
		 * @param $field The name of the column to test for.
		 * @return boolean True if field is present.
		 */
		protected static function _hasField( $field ) {
			return in_array( $field, $this->_getProperty('_fields') );
		}

		/**
		 * Get a static property of the current class.
		 *
		 * @param string $name The name of the property without the $-sign.
		 * @return mixed The value of the property.
		 */
		public function _getProperty( $name ) {
			return self::getProperty( get_called_class(), $name );
		}
		
		/**
		 * Create an instance of a WebLab_Data_Table to be used internally.
		 *
		 * @return WebLab_Data_Table An instance of the abstraction layer Table class.
		 */
		public function createTable(){
			$t = new WebLab_Data_Table( self::table() );
			return $t->addFields( $this->_getProperty( '_fields' ) );
		}
		
		/**
		 * Update a record if it already exists, insert it otherwise.
		 *
		 * @param mixed &$object The object to insert as a record.
		 */
		public function save( &$object ){
			$q = db($this->_getProperty('_database'))->newQuery();
				
			$table = $q->addTable( $this->createTable() );
				
			if( !isset( $object['online'] ) && self::_hasField( 'online' ) )
				$object['online'] = 1;
		
			if( !isset( $object['deleted'] ) && self::_hasField( 'deleted' ) )
				$object['deleted'] = 0;
				
			foreach( $object as $key => &$value )
				if( in_array( $key, $this->_getProperty('_fields') ) )
				$table->getField($key)->setValue( $value );
		
			$q->insert( true, array( $table->getField( 'id' ) ) );
				
			$object['id'] = $q->getAdapter()->insert_id();
		}

		/**
		 * Create a record for this table.
		 * The id of the new record will be set in $object
		 *
		 * @param mixed &$object The object to insert as a record.
		 */
		public function create( &$object ){
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			if( !isset( $object['online'] ) )
				$object['online'] = 1;
				
			if( !isset( $object['deleted'] ) )
				$object['deleted'] = 0;
			
			foreach( $object as $key => &$value )
				if( in_array( $key, $this->_getProperty('_fields') ) )
					$table->getField($key)->setValue( $value );
				
			$q->insert();
			
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
		/**
		 * Delete a record from the table.
		 * If soft-delete is used it will only update the deleted and online field.
		 * 
		 * @param mixed &$object The object to delete from the table.
		 */
		public function delete( &$object ){
			$has_deleted = self::_hasField( 'deleted' );
			$has_online = self::_hasField( 'online' );
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( self::table() )->addFields( 'id' );
			
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
		 * @param mixed &$object The new values for this record, including the unaltered primary key.
		 */
		public function update( &$object ){
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( self::table() )->addFields( 'id' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			foreach( $object as $key => &$value )
				if( $key != 'id' && in_array( $key, $this->_getProperty('_fields') ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
		/**
		 * Find a record by its primary key.
		 *
		 * @param int $id ID of the record to find.
		 * @return mixed The record as an object.
		 */
		public function find( $id ) {
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			$criteria = $q->getCriteria();
			$criteria->addAnd( $table->id->eq( $id ) );

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

			$q = db($this->_getProperty('_database'))->newQuery();
			
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
			$q = db($this->_getProperty('_database'))->newQuery();

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
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( self::table() )
				->addFields( 'id' );				
				
			$table->id->setFunction( 'COUNT' )
				->setAlias( 'count' );
			
			$criteria = $q->getCritera();
			if( self::_hasField( 'online' ) ) {
				$table->addField('online')->setSelect( false );
				$criteria->addAnd( $table->online->eq( 1 ) );
			}
			
			if( self::_hasField( 'deleted' ) ) {
				$table->addField('deleted')->setSelect( false );
				$criteria->addAnd( $table->deleted->eq( 0 ) );
			}
			
			return $q->select()->current()->count;
		}
	}
	
	if(!function_exists('get_called_class')) { 
	/**
	 * A replacement for get_called_class on systems where the function is not available.
	 * 
	 * @param mixed $bt A backtrace to use.
	 * @param int $l The position the read the classname from.
	 * @return string The classname which was called.
	 */
	function get_called_class($bt = false,$l = 1) { 
	    if (!$bt) $bt = debug_backtrace(); 
	    if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep."); 
	    if (!isset($bt[$l]['type'])) { 
	        throw new Exception ('type not set'); 
	    } 
	    else switch ($bt[$l]['type']) { 
	        case '::': 
	            $lines = file($bt[$l]['file']); 
	            $i = 0; 
	            $callerLine = ''; 
	            do { 
	                $i++; 
	                $callerLine = $lines[$bt[$l]['line']-$i] . $callerLine; 
	            } while (stripos($callerLine,$bt[$l]['function']) === false); 
	            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', 
	                        $callerLine, 
	                        $matches); 
	            if (!isset($matches[1])) { 
	                // must be an edge case. 
	                throw new Exception ("Could not find caller class: originating method call is obscured."); 
	            } 
	            switch ($matches[1]) { 
	                case 'self': 
	                case 'parent': 
	                    return get_called_class($bt,$l+1); 
	                default: 
	                    return $matches[1]; 
	            } 
	            // won't get here. 
	        case '->': switch ($bt[$l]['function']) { 
	                case '__get': 
	                    // edge case -> get class of calling object 
	                    if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object."); 
	                    return get_class($bt[$l]['object']); 
	                default: return $bt[$l]['class']; 
	            } 
	
	        default: throw new Exception ("Unknown backtrace method type"); 
	    } 
	} 
	} 