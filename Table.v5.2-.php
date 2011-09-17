<?php
	abstract class WebLab_Table {
		
		protected static $_database = 'main';
		protected static $_table;
		protected static $_fields;
		protected static $_instance;
		
		private static $_properties = array();
		
		private static function getProperties( $class_name ) {
			$properties = &self::$_properties[$class_name];
			if( empty( $properties ) ) {
				$class = new ReflectionClass( $class_name  );
				$properties = $class->getStaticProperties();
			}
			
			return $properties;
		}
		
		private static function &getProperty( $class, $name ) {
			$props = self::getProperties( $class );
			
			return $props[$name];
		}
		
		public static function getInstance(){
			$class_name = get_called_class();
			$instance = &self::getProperty( get_called_class(), '_instance' );
			
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
		
		public function createTable(){
			$t = new WebLab_Data_Table( self::getProperty( get_class( $this ), '_table') );
			return $t->addFields( self::getProperty( get_class( $this ), '_fields') );
		}
		
		public function create( &$object ){
			$q = db(self::getProperty( get_class( $this ), '_database'))->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			if( !isset( $object['online'] ) )
				$object['online'] = 1;
				
			if( !isset( $object['deleted'] ) )
				$object['deleted'] = 0;
			
			foreach( $object as $key => &$value )
				if( in_array( $key, self::getProperty( get_class( $this ), '_fields') ) )
					$table->getField($key)->setValue( $value );
				
			$q->insert();
			
			$object['id'] = $q->getAdapter()->insert_id();
		}
		
		public function delete( &$object ){
			$q = db(self::getProperty( get_class( $this ), '_database'))->newQuery();
			
			$table = $q->addTable( self::getProperty( get_class( $this ), '_table') )->addFields( 'id', 'online', 'deleted' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			$table->deleted->setValue( 1 );
			$table->online->setValue( 0 );
			
			$q->update();
		}
		
		public function update( &$object ){
			$q = db(self::getProperty( get_class( $this ), '_database'))->newQuery();
			
			$table = $q->addTable( self::getProperty( get_class( $this ), '_table') )->addFields( 'id' );
			
			$q->getCriteria()->addAnd( $table->id->eq( $object['id'] ) );
			
			foreach( $object as $key => &$value )
				if( $key != 'id' && in_array( $key, self::getProperty( get_class( $this ), '_fields') ) )
					$table->addField($key)->setValue( $value );
			
			$q->update();
		}
		
		public function find( $id ) {
			$q = db(self::getProperty( get_class( $this ), '_database'))->newQuery();
			
			$table = $q->addTable( $this->createTable() );
			
			$q->getCriteria()->addAnd( $table->id->eq( $id ) )
				->addAnd( $table->online->eq( 1 ) )
				->addAnd( $table->deleted->eq( 0 ) );
			
			return $q->select()->current();
		}
		
		public function findAll( $count=null, $start=0, &$result_count=0 ){
			$q = db(self::getProperty( get_class( $this ), '_database'))->newQuery();
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
			$q = db(self::getProperty( get_class( $this ), '_database'))->newQuery();
			
			$table = $q->addTable( self::getProperty( get_class( $this ), '_table') )
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