<?php
	/**
	 * Result.php
	 *
	 * This file contains the implementation of the WebLab_Data_MySQL_Result class.
	 * @see WebLab_Data_MySQL_Result
	 */
	/**
	 *
	 * Implementation of a result using mySQL resources.
	 *
	 * @see WebLab_Data_Result
	 * @author Jorgen Evens <jorgen@wlab.be>
	 * @package WebLab
	 * @subpackage Data_MySQL
	 *
	 */
	class WebLab_Data_MySQL_Result extends WebLab_Data_Result
	{

		/**
		 * Read the records from the resource into memory.
		 *
		 * @param resource &$result The MySQL resource to read from.
		 * @return mixed An array of objects representing the records.
		 */
		protected function _read( &$result )
		{
			if( mysql_num_rows( $result ) == 0 )
			{
				$this->_rows = array();
				return;
			}

			$this->_rows = array_map( create_function( '$result', 'return mysql_fetch_object( $result );' ), array_fill( 0, mysql_num_rows( $result ), $result ) );
		}

	}