<?php
	abstract class WebLab_Table {
		
		protected static $_database = 'main';
		protected static $_table;
		protected static $_fields;
		protected static $_instance;
		
		public static function getInstance(){
			if( empty( static::$_instance ) ){
				static::$_instance = new static();
			}
				
			return static::$_instance;
		}
		
		public static function startTransaction() {
			db(static::$_database)->startTransaction();
		}
		
		public static function quitTransaction( $commit=true ) {
			db(static::$_database)->quitTransaction( $commit );
		}
		
		public static function table() {
			return db(static::$_database)->getPrefix() . static::$_table;
		}
		
		protected static function _hasField( $field ) {
			return in_array( $field, static::$_fields );
		}

		public function createTable(){
			$t = new WebLab_Data_Table( static::table() );
			return $t->addFields( static::$_fields );
		}
		
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
		
			$q->insert( true, array( $table->getField( 'id' ) ) );
				
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
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
			
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
		public function delete( &$object ){
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
		
		public function update( &$object ){
			$q = db(static::$_database)->newQuery();
			
			$table = $q->addTable( static::table() )->addFields( 'id' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			foreach( $object as $key => &$value )
				if( $key != 'id' && in_array( $key, static::$_fields ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
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

		public function findBy( $field, $value=null, &$result_count=0 ) {
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
			$result_count = $result->getTotalRows();

			return $result->fetch_all();
		}
		
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