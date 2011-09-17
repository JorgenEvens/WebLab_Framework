<?php
	/**
     *
     * Implementation of an adapter using the mySQL functions.
     *
     * @see WebLab_Data_Adapter
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage WebLab_Data_MySQL
     *
     */
    class WebLab_Data_MySQL_Adapter extends WebLab_Data_Adapter
    {
        
        protected $_connected = false;
        protected $_wildcard = '%';

        public function __construct( $login )
        {
            if( !( $this->_resource = @mysql_connect( $login->host . ':' . $login->port, $login->username, $login->password ) ) )
            {
                throw new WebLab_Exception_Data( mysql_error(), mysql_errno() );
            }
            if( !mysql_select_db( $login->database, $this->_resource ) )
            {
            	throw new WebLab_Exception_Data( mysql_error(), mysql_errno() );
            }

            $this->setPrefix( $login->prefix );
            $this->_connected = true;
        }

        public function isConnected()
        {
            return $this->_connected;
        }
        
        public function newQuery() {
        	return new WebLab_Data_MySQL_Query( $this );
        }

        protected function _query( $query )
        {
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

        public function insert_id()
        {
            return mysql_insert_id( $this->_resource );
        }

        public function escape_string( $str )
        {
            return mysql_real_escape_string( $str, $this->_resource );
        }

        public function getAdapterSpecs()
        {
            return (object) array(
                'escape_string'   => array( $this, 'escape_string' ),
                'wildcard'        => $this->_wildcard 
            );
        }
        
        protected function _start_transaction() {
        	$this->query( 'START TRANSACTION' );
        }
        protected function _quit_transaction( $commit ) {
        	if( $commit ) {
        		$this->query( 'COMMIT' );
        	} else {
        		$this->query( 'ROLLBACK' );
        	}
        }

    }