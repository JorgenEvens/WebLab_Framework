<?php
		/**
		* Adapter.php
		*
		* This file contains the implementation of the WebLab_Data_PDO_Adapter class.
		* @see WebLab_Data_PDO_Adapter
		*/
	/**
		*
		* Implementation of an adapter using the PDO functions.
		*
		* @see WebLab_Data_Adapter
		* @author Jorgen Evens <jorgen@wlab.be>
		* @package WebLab
		* @subpackage Data_PDO
		*
		*/
		class WebLab_Data_PDO_Adapter extends WebLab_Data_Adapter
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
				* Constructor for WebLab_Data_PDO_Adapter.
				*
				* @param mixed $login Credentials to be used during connection setup.
				*/
				public function __construct( $login )
				{
					$dsn = $login->connectionString;
					$user = isset( $login->username ) ? $login->username : '';
					$pass = isset( $login->password ) ? $login->password : '';
					$options = isset( $login->options ) ? $login->options : array();

					try {
						$this->_resource = new PDO( $dsn, $user, $pass, $options );
						$this->_connected = true;
					} catch( PDOException $ex ) {
						throw new WebLab_Exception_Data( $ex->getMessage(), $ex->getCode() );
						$this->_connected = false;
					}
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
					return new WebLab_Data_PDO_Query( $this );
				}

				/**
				* Pass on a query to the underlying connection.
				*
				* @param WebLab_Data_MySQL_Query $query The query to execute.
				* @return WebLab_Data_MySQL_Result The result of the query.
				*/
				protected function _query( $query )
				{
					$result = $this->_resource->query( $query );
						$result = mysql_query( $query, $this->_resource );

						if( strlen( mysql_error( $this->_resource ) ) > 0 || mysql_errno( $this->_resource ) || !$result )
						{
								throw new WebLab_Exception_Data( mysql_error( $this->_resource ) . '<br /><strong>Query:</strong><br />' . $query . '<br />' );
						}elseif( strtolower(gettype($result)) != 'resource' )
						{
								return $result;
						}
						else
						{
								return new WebLab_Data_MySQL_Result( $result );
						}
				}

				/**
				* Retrieve the last inserted id
				*
				* @return int The last inserted id.
				*/
				public function insert_id()
				{
						return mysql_insert_id( $this->_resource );
				}

				/**
				* Escape a string using the database specific escape function.
				*
				* @param string $str Input to be escaped.
				* @return string Escaped version of $str.
				*/
				public function escape_string( $str )
				{
						return mysql_real_escape_string( $str, $this->_resource );
				}

				/**
				* Retrieve information about the database engine.
				*
				* @return mixed Database specification settings.
				*/
				public function getAdapterSpecs()
				{
						return (object) array(
								'escape_string'   => array( $this, 'escape_string' ),
								'wildcard'        => $this->_wildcard
						);
				}

				/**
				* Notify the database engine that a transaction is about to start.
				*
				*/
				protected function _start_transaction() {
					$this->query( 'START TRANSACTION' );
				}

				/**
				* Notify the database engine that a transaction is to be stopped.
				*
				* @param boolean $commit Should the result be saved
				*/
				protected function _quit_transaction( $commit ) {
					if( $commit ) {
						$this->query( 'COMMIT' );
					} else {
						$this->query( 'ROLLBACK' );
					}
				}

		}
