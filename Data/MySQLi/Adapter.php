<?php
    /**
    * Adapter.php
    *
    * This file contains the implementation of the WebLab_Data_MySQLi_Adapter class.
    * @see WebLab_Data_MySQLi_Adapter
    */
	/**
     *
     * Implementation of an adapter using the mySQLi module.
     *
     * @see WebLab_Data_Adapter
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data_MySQLi
     *
     */
    class WebLab_Data_MySQLi_Adapter extends WebLab_Data_Adapter
    {
        
        /**
         * Contains connected state of this adapter.
         *
         * @var boolean Is adapter connected?
         */
        protected $_connected = false;

        /**
         * Wildcard character to use for this database engine.
         *
         * @var char Wildcard symbol for engine.
         */
        protected $_wildcard = '%';

        /**
         * Charset to use on the connection
         *
         * @var string Charset to use with connection
         */
        protected $_charset = 'unknown';

        /**
         * Constructor for WebLab_Data_MySQLi_Adapter.
         *
         * @param mixed $login Credentials to be used during connection setup.
         */
        public function __construct( $login ) {
            $this->_resource = new mysqli( $login->host, $login->username, $login->password, $login->database, $login->port );

            if( !empty( $this->_resource->connect_error ) ) {
                $this->error = $this->_resource->connect_error;
                return;
            }

            if( isset( $login->charset ) ) {
                $this->_charset = $login->charset;
                $this->_resource->set_charset( $this->_charset );
            }
            $charset = $this->_resource->get_charset();
            $this->_charset = $charset->charset;
            
            $this->setPrefix( $login->prefix );
            $this->_connected = true;
        }

        /**
         * Return whether adapter is connected.
         *
         * @return boolean Is adapter connected?
         */
        public function isConnected() {
            return $this->_connected;
        }

        /**
         * Create a new query to be run on this database.
         *
         * @return WebLab_Data_MySQLi_Query
         */
        public function newQuery() {
        	return new WebLab_Data_MySQLi_Query( $this );
        }

        /**
         * Retrieve the last inserted id
         *
         * @return int The last inserted id.
         */
        public function insert_id() {
            return $this->_resource->insert_id;
        }

        /**
         * Escape a string using the database specific escape function.
         *
         * @param string $str Input to be escaped.
         * @return string Escaped version of $str.
         */
        public function escape_string( $str ) {
            return $this->_resource->real_escape_string( $str );
        }

        /**
         * Retrieve information about the database engine.
         *
         * @return mixed Database specification settings.
         */
        public function getAdapterSpecs() {
            return (object) array(
                'escape_string'   => array( $this, 'escape_string' ),
                'wildcard'	      => $this->_wildcard
            );
        }

        public function getCharset() {
            return $this->_charset;
        }
        
        /**
         * Pass on a query to the underlying connection.
         *
         * @param WebLab_Data_MySQLi_Query $query The query to execute.
         * @return WebLab_Data_MySQLi_Result The result of the query.
         */
    	protected function _query( $query ) {
            return $this->_create_result($query);
        }
        
        protected function _execute( $query ) {
            $result = $this->_resource->query( $query );
			
            if( strlen( $this->_resource->error ) > 0 || $this->_resource->errno || !$result ) {
                throw new Exception( $this->_resource->error . '<br /><strong>Query:</strong><br />' . $query . '<br />' );
            }
            
            return $result;
        }
        
        protected function _create_result( $query, $result=null ) {
            return new WebLab_Data_MySQLi_Result($query, $result);
        }
        
        /**
         * Notify the database engine that a transaction is about to start.
         *
         */
        protected function _start_transaction() {
        	$this->_resource->autocommit( false );
        }
        
        /**
         * Notify the database engine that a transaction is to be stopped.
         *
         * @param boolean $commit Should the result be saved
         */
        protected function _quit_transaction( $commit ) {
        	$this->_resource->autocommit( true );
        	
        	if( $commit ) {
        		$this->_resource->commit();
        	} else {
        		$this->_resource->rollback();
        	}
        }


    }