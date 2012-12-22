<?php
	abstract class WebLab_Table {
		
		protected static $_database = 'main';
		protected static $_table;
		protected static $_fields;
		protected static $_instance;
		
		private static $_properties = array();
		
		private static function _getProperties( $class_name ) {
			$properties = &self::$_properties[$class_name];
			if( empty( $properties ) ) {
				$class = new ReflectionClass( $class_name  );
				$properties = $class->getStaticProperties();
			}
			
			return $properties;
		}
		
		private static function &_getProperty( $class, $name ) {
			$props = self::_getProperties( $class );
			
			return $props[$name];
		}
		
		public static function getInstance(){
			$class_name = get_called_class();
			$instance = &self::_getProperty( get_called_class(), '_instance' );
			
			if( empty( $instance ) ){
				$instance = new $class_name();
			}
				
			return $instance;
		}
		
		public static function startTransaction() {
			db(self::getProperty( get_called_class(), '_database'))->startTransaction();
		}
		
		public static function quitTransaction( $commit=true ) {
			db(self::getProperty( get_called_class(), '_database'))->quitTransaction( $commit );
		}

		public static function table() {
			$db = self::getProperty( get_called_class(), '_database');
			$table = self::getProperty( get_called_class(), '_table');

			return db($db)->getPrefix() . $table;
		}
		
		protected static function _hasField( $field ) {
			return in_array( $field, $this->_getProperty('_fields') );
		}

		public function _getProperty( $name ) {
			return $this->_getProperty('_fields')
		}
		
		public function createTable(){
			$t = new WebLab_Data_Table( self::table() );
			return $t->addFields(  );
		}
		
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
		
		public function delete( &$object ){
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( self::table() )->addFields( 'id', 'online', 'deleted' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			if( self::_hasField( 'online' ) )
				$table->online->setValue( 0 );
			
			if( self::_hasField( 'deleted' ) ) {
				$table->deleted->setValue( 1 );
				$q->update();
			} else {
				$q->delete();
			}
		}
		
		public function update( &$object ){
			$q = db($this->_getProperty('_database'))->newQuery();
			
			$table = $q->addTable( self::table() )->addFields( 'id' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			foreach( $object as $key => &$value )
				if( $key != 'id' && in_array( $key, $this->_getProperty('_fields') ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
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

		public function findBy( $field, $value=null, &$result_count=0 ) {
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
			$result_count = $result->getTotalRows();

			return $result->fetch_all();
		}
		
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