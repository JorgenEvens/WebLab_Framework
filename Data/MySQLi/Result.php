<?php
	/**
	 * Result.php
	 *
	 * This file contains the implementation of the WebLab_Data_MySQLi_Result class.
	 * @see WebLab_Data_MySQLi_Result
	 */
	/**
	 *
	 * Implementation of a result using mySQLi resources.
	 *
	 * @see WebLab_Data_Result
	 * @author Jorgen Evens <jorgen@wlab.be>
	 * @package WebLab
	 * @subpackage Data_MySQLi
	 *
	 */
	class WebLab_Data_MySQLi_Result extends WebLab_Data_Result
	{

		/**
		 * Read the records from the resource into memory.
		 *
		 * @param resource &$result The MySQLi resource to read from.
		 * @return mixed An array of objects representing the records.
		 */
		protected function _read( $result )
		{
			if( $result->num_rows == 0 )
				return array();
			
			return array_map( array( $this, '_parse_result' ), array_fill( 0, $result->num_rows, $result ) );
		}

		/**
		 * Callback function for array_map, retrieves a record as an object.
		 *
		 * @param resource &$result The MySQLi resource to read from.
		 * @return mixed An object representing one record.
		 */
		private function _parse_result( $result ){
			return $result->fetch_object();
		}

	}