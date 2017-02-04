<?php
    /**
     * DataProvider.php
     *
     * This file contains the implementation of the WebLab_Dispatcher_DataProvider class.
     * @see WebLab_Dispatcher_DataProvider
     */
	/**
	 * This class is a REST interface to WebLab_Table instances.
	 * 
	 * @see WebLab_Table
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Dispatcher
	 *
	 */
	abstract class WebLab_Dispatcher_DataProvider extends WebLab_Dispatcher_WebService {

		/**
		 * Returns the table on which we should invoke the action.
		 *
		 * @return WebLab_Table
		 */
		abstract protected function getTable();
		
		/**
		 * (non-PHPdoc)
		 * @see WebLab_Dispatcher_Module::_default()
		 */
		public function _default(){
			$id = isset( $this->param[ $this->_getName() ] ) ? $this->param[ $this->_getName() ] : null;
			$id_set = ( is_numeric( $id ) && !empty( $id) );
			$is_empty = ( $id === '' );

			if( !$this->is( 'POST' ) && !$this->is( 'GET' ) )
				parse_str(file_get_contents('php://input'), $_POST);

			if( $this->is( 'GET' ) )
				return $this->_find();

			if( ( $this->is( 'POST' ) || $this->is( 'PUT' ) ) && $id_set )
				return $this->_update( $id );

			if( $this->is( 'POST' ) && $is_empty )
				return $this->_create();

			if( $this->is( 'DELETE' ) && $id_set )
				return $this->_delete( $id );

			$this->error( 'Method does not exist', 501 );

		}
		
		/**
		 * Performs a search on the table using the equivalent find function.
		 */
		public function _find() {
			if( !$this->is( 'GET' ) ) return $this->error( 'This call only supports GET.');
			
			$param_name = $this->_getName();
			$id = isset( $this->param[ $param_name ] ) ? $this->param[ $param_name ] : null;
				
			if( !is_numeric( $id ) && !empty( $id ) ) {
				return $this->error( 'Method does not exist', 501 );
			}
				
			$table = $this->getTable();
				
			if( empty( $id ) ) {
				$p = $table->findAll();
			} else {
				$p = $table->find( $id );
				if( empty( $p ) )
					return $this->error( 'Not found', 404 );
			}

			$this->put( $p->data() );
		}
		
		/**
		 * Performs a create on the table using the equivalent create function.
		 */
		public function _create() {
			if( !$this->is( 'POST' ) ) return $this->error( 'This call only supports POST.' );
				
			$table = $this->getTable();
			$obj = $_POST;

			$table->save( $obj )->execute();

			$this->put( $obj );
		}
		
		/**
		 * Performs an update on the table using the equivalent update function.
		 */
		public function _update( $id ) {
			if( !$this->is( 'POST' ) && !$this->is( 'PUT' ) ) return $this->error( 'This call only supports POST.' );
				
			$table = $this->getTable();
			$item = $table->find( $id );

			if( empty( $item ) )
				return $this->error( 'Not found', 404 );

			$item = get_object_vars($item);
			$item = array_merge( $item, $_POST );

			$table->update( $item )->execute();

			$this->put( $item );
		}
		
		/**
		 * Performs a delete on the table using the equivalent delete function.
		 */
		public function _delete( $id ) {
			if( !$this->is( 'DELETE' ) ) return $this->error( 'This call only supports POST.' );
				
			$table = $this->getTable();
			$item = $table->find( $id );

			if( empty( $item ) )
				return $this->error( 'Not found', 404 );

			$table->delete( $item );
				
			$this->put( array('succes' => true ) );
		}
		
	}