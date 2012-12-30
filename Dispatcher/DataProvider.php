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
		
			if( !is_numeric( $id ) ) {
				return $this->error( 'Method does not exist', 501 );
			}
				
			$p = $this->getTable()->find( $id );
				
			$this->put( $p );
		}
		
		/**
		 * Performs a search on the table using the equivalent find function.
		 */
		public function find() {
			if( !$this->is( 'GET' ) ) return $this->error( 'This call only supports GET.');
				
			$id = isset( $this->param[ 'find' ] ) ? $this->param[ 'find' ] : null;
				
			if( !is_numeric( $id ) && $id !== 'all' ) {
				return $this->error( 'Method does not exist', 501 );
			}
				
			$table = $this->getTable();
				
			if( $id == 'all' ) {
				$p = $table->findAll();
			} else {
				$p = $table->find( $id );
			}
				
			$this->put( $p );
		}
		
		/**
		 * Performs a create on the table using the equivalent create function.
		 */
		public function create() {
			if( !$this->is( 'POST' ) ) return $this->error( 'This call only supports POST.' );
				
			$table = $this->getTable();
			$obj = $_POST;
				
			$table->create( $obj );
				
			$this->put( $obj );
		}
		
		/**
		 * Performs an update on the table using the equivalent update function.
		 */
		public function update() {
			if( !$this->is( 'POST' ) ) return $this->error( 'This call only supports POST.' );
				
			$table = $this->getTable();
			$obj = $_POST;
				
			$table->update( $obj );
				
			$this->put( $obj );
		}
		
		/**
		 * Performs a delete on the table using the equivalent delete function.
		 */
		public function delete() {
			if( !$this->is( 'POST' ) ) return $this->error( 'This call only supports POST.' );
				
			$table = $this->getTable();
				
			$table->delete( $_POST );
				
			$this->put( array('succes' => true ) );
		}
		
	}