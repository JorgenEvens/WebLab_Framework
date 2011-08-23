<?php
	/**
     *
     * Implementation of an adapter using the mySQLi module.
     *
     * @see WebLab_Data_Adapter
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage WebLab_Data_MySQLi
     *
     */
    class WebLab_Data_MySQLi_Adapter extends WebLab_Data_Adapter
    {
        
        protected $_connected = false;
        protected $_wildcard = '%';

        public function __construct( $login ) {
            $this->_resource = new mysqli( $login->host, $login->username, $login->password, $login->database, $login->port );

            if( !empty( $this->_resource->connect_error ) ) {
                $this->error = $this->_resource->connect_error;
                return;
            }

            $this->setPrefix( $login->prefix );
            $this->_connected = true;
        }

        public function isConnected() {
            return $this->_connected;
        }

        public function newQuery() {
        	return new WebLab_Data_MySQLi_Query( $this );
        }

        public function insert_id() {
            return $this->_resource->insert_id;
        }

        public function escape_string( $str ) {
            return $this->_resource->real_escape_string( $str );
        }

        public function getAdapterSpecs() {
            return (object) array(
                'escape_string'   => array( $this, 'escape_string' ),
                'wildcard'	      => $this->_wildcard
            );
        }
        
    	protected function _query( $query ) {
            $result = $this->_resource->query( $query );
			
            if( strlen( $this->_resource->error ) > 0 || $this->_resource->errno || !$result ) {
                throw new Exception( $this->_resource->error . '<br /><strong>Query:</strong><br />' . $query . '<br />' );
            } elseif( !( $result instanceof MySQLi_Result ) ) {
                return true;
            } else {
                return new WebLab_Data_MySQLi_Result( $result );
            }
        }
        
        protected function _start_transaction() {
        	$this->_resource->autocommit( false );
        }
        
        protected function _quit_transaction( $commit ) {
        	$this->_resource->autocommit( true );
        	
        	if( $commit ) {
        		$this->_resource->commit();
        	} else {
        		$this->_resource->rollback();
        	}
        }


    }