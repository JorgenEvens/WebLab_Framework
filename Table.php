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
			db(self::$_database)->startTransaction();
		}
		
		public static function quitTransaction( $commit=true ) {
			db(self::$_database)->quitTransaction( $commit );
		}
		
		public function createTable(){
			$t = new WebLab_Data_Table( static::$_table );
			return $t->addFields( static::$_fields );
		}
		
		public function create( &$object ){
			$q = db(self::$_database)->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			if( !isset( $object['online'] ) )
				$object['online'] = 1;
				
			if( !isset( $object['deleted'] ) )
				$object['deleted'] = 0;
			
			foreach( $object as $key => &$value )
				if( in_array( $key, static::$_fields ) )
					$table->getField($key)->setValue( $value );
				
			$q->insert();
			
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
		public function delete( &$object ){
			$q = db(self::$_database)->newQuery();
			
			$table = $q->addTable( static::$_table )->addFields( 'id', 'online', 'deleted' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			$table->deleted->setValue( 1 );
			$table->online->setValue( 0 );
			
			$q->update();
		}
		
		public function update( &$object ){
			$q = db(self::$_database)->newQuery();
			
			$table = $q->addTable( static::$_table )->addFields( 'id' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			foreach( $object as $key => &$value )
				if( $key != 'id' && in_array( $key, static::$_fields ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
		public function find( $id ){
			$q = db(self::$_database)->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			$q->getCriteria()->addAnd( $table->id->eq( $id ) )
				->addAnd( $table->online->eq( 1 ) )
				->addAnd( $table->deleted->eq( 0 ) );
			
			return $q->select()->current();
		}
		
		public function findAll( $count=null, $start=0, &$result_count=0 ){
			$q = db(self::$_database)->newQuery();
			$q->countLimitless( true );
			
			$table = $q->addTable( $this->createTable() );
			
			$q->getCriteria()->addAnd( $table->online->eq( 1 ) )
				->addAnd( $table->deleted->eq( 0 ) );
			
			if( $count != null )
				$q->setLimit( $count, $start );
				
			$result = $q->select();
			$result_count = $result->getTotalRows();
				
			return $result->fetch_all();
		}
		
		public function countAll(){
			$q = db(self::$_database)->newQuery();
			
			$table = $q->addTable( static::$_table )
				->addFields( 'id', 'online', 'deleted' );
			$table->online->setSelect( false );
			$table->deleted->setSelect( false );
				
				
			$table->id->setFunction( 'COUNT' )
				->setAlias( 'count' );
			
			$q->getCriteria()->addAnd( $table->online->eq( 1 ) )
				->addAnd( $table->deleted->eq( 0 ) );
			
			return $q->select()->current()->count;
		}
	}